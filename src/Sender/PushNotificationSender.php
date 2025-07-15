<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\Sender;


use Contao\Model\Collection;
use HeimrichHannot\PwaBundle\DataContainer\PwaPushNotificationContainer;
use HeimrichHannot\PwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Notification\AbstractNotification;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Minishlink\WebPush\Encryption;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PushNotificationSender
{
    private ?array $bundleConfig;
    private PwaPushNotificationContainer $notificationContainer;
    private Utils $utils;

    /**
     * PushNotificationSender constructor.
     */
    public function __construct(?array $bundleConfig, PwaPushNotificationContainer $notificationContainer, Utils $utils)
    {
        $this->bundleConfig = $bundleConfig;
        $this->notificationContainer = $notificationContainer;
        $this->utils = $utils;
    }

    public function sendWithLog(
        AbstractNotification   $notification,
        PwaConfigurationsModel $config,
        LoggerInterface        $log,
        ?array                 $subscribers = null,
    ): bool {
        if (!$this->checkRequirements($log)) {
            return false;
        }

        $log->info("Requirements checked.");

        /** @var PwaPushSubscriberModel[]|Collection<PwaPushSubscriberModel>|null $subscribers */
        if (!$subscribers && !($subscribers = PwaPushSubscriberModel::findByPid($config->id)))
        {
            $log->error("No subscribers found.");
            return false;
        }

        $log->info("Found " . count($subscribers) . " subscribers.");

        if (!$webPush = $this->createPushInstance($log)) {
            return false;
        }

        try {
            $payload = $notification->toArray();
        } catch (\ReflectionException $e) {
            throw new \Exception("Could not serialize notification. Error message: " . $e->getMessage());
        }

        if ($notificationsModel = $notification->getModel()) {
            $this->notificationContainer->notificationClickEvent($notificationsModel, $payload);
        }

        $sendDate = time(); //same send time for all subscribers and the notification
        $skipped = 0;
        $errors = 0;
        $success = 0;
        $sendCount = 0;

        foreach ($subscribers as $subscriber) {
            if (!$subscriber instanceof PwaPushSubscriberModel) {
                $log->warning("Only subscribers of typ PushSubscriberModel are allowed.", [
                    'function' => __FUNCTION__,
                    'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                ]);
                $skipped++;
                continue;
            }

            try {
                $report = $webPush->sendOneNotification(
                    new Subscription(
                        $subscriber->endpoint,
                        $subscriber->publicKey,
                        $subscriber->authToken
                    ), json_encode($payload));
            } catch (\ErrorException $e) {
                $log->warning("Error while sending push notification (Subscriber Id: {$subscriber->id}): " . $e->getMessage(), [
                    'function' => __FUNCTION__,
                    'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                ]);
                $errors++;
                continue;
            }

            $sendCount++;

            if ($report->isSuccess()) {
                $subscriber->lastSuccessfulSend = $sendDate;
                $subscriber->save();
                $log->info("Push notification sent to subscriber {$subscriber->id}.", [
                    'function' => __FUNCTION__,
                    'verbosity' => OutputInterface::VERBOSITY_DEBUG,
                ]);
                $success++;
            } else {
                if ($report->isSubscriptionExpired()) {
                    // TODO
                    $reason = 'Subscription expired';
                } else {
                    $reason = $report->getReason();
                }

                $log->warning("Push notification could not be sent to subscriber {$subscriber->id}. Reason: ".$reason, [
                    'function' => __FUNCTION__,
                    'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                    'reason' => $reason,
                    'endpoint' => $report->getEndpoint(),
                    'response' => json_encode($report->getResponse()->getBody()),
                ]);
            }
        }

        if ($notification->getModel()) {
            $notification->getModel()->sent = true;
            $notification->getModel()->dateSent = $sendDate;
            $notification->getModel()->receiverCount = $success;
            $notification->getModel()->save();
        }

        $log->info("Push notification sent to {$success} subscribers. {$skipped} subscribers skipped. {$errors} errors.", [
            'function' => __FUNCTION__,
            'successCount' => $success,
            'skippedCount' => $skipped,
            'errorCount' => $errors,
            'sendCount' => $sendCount,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ]);

        return true;
    }

    private function checkRequirements(LoggerInterface $log): bool
    {
        if (!class_exists(WebPush::class)) {
            $log->error('Please install webpush using "composer require minishlink/web-push ^8.0".', ['function' => __FUNCTION__]);
            return false;
        }

        if (!$this->bundleConfig || !isset($this->bundleConfig['vapid']['subject']) || !isset($this->bundleConfig['vapid']['publicKey']) && !isset($this->bundleConfig['vapid']['privateKey'])) {
            $log->error("Bundle config not complete. Set vapid keys to use Push Notifications.", ['function' => __FUNCTION__]);
            return false;
        }

        return true;
    }

    /**
     * Send the notification to all recipients or a list of given recipients.
     *
     * @param AbstractNotification $notification
     * @param PwaConfigurationsModel $config
     * @param array|PwaPushSubscriberModel[]|Collection|null $subscribers
     * @return array|bool
     * @throws \Exception
     */
    public function send(AbstractNotification $notification, PwaConfigurationsModel $config, ?array $subscribers = null)
    {
        $log = new LocalLogger($this->utils);

        $result = $this->sendWithLog($notification, $config , $log, $subscribers);
        if (false === $result) {
            throw new \Exception($log->getLastError());
        }

        $endLog = $log->getLastInfo();

        return [
            'success' => true,
            'sentCount' => $endLog['context']['sendCount'],
            'successCount' => $endLog['context']['successCount'],
            'errors' => array_column($log->getErrors(), 'message'),
        ];
    }

    /**
     * @return WebPush
     * @throws \Exception
     */
    protected function createPushInstance(LoggerInterface $logger): ?WebPush
    {
        $auth = [
            'VAPID' => [
                'subject' => $this->bundleConfig['vapid']['subject'],
                'publicKey' => $this->bundleConfig['vapid']['publicKey'],
                'privateKey' => $this->bundleConfig['vapid']['privateKey']
            ],
        ];

        try {
            $webPush = new WebPush($auth);
        } catch (\ErrorException $e) {
            $logger->error("Web push config: Could not construct WebPush object. Error message: " . $e->getMessage(), ['function' => __FUNCTION__]);
            return null;
        }

        try {
            if (isset($this->bundleConfig['push']['automatic_padding'])) {
                $padding = $this->bundleConfig['push']['automatic_padding'];
                if (is_numeric($padding) && $padding > 0 && $padding <= Encryption::MAX_PAYLOAD_LENGTH) {
                    $webPush->setAutomaticPadding(intval($padding));
                } elseif (is_bool($padding)) {
                    $webPush->setAutomaticPadding($padding);
                } else {
                    $webPush->setAutomaticPadding(2847);
                }
            } else {
                $webPush->setAutomaticPadding(2847);
            }
        } catch (\Exception $e) {
            $logger->error("Web push config: Could not set automatic padding. Error message: " . $e->getMessage(), ['function' => __FUNCTION__]);
            return null;
        }

        return $webPush;
    }
}