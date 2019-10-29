<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Sender;


use Contao\Model\Collection;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\AbstractNotification;

class PushNotificationSender
{
    /**
     * @var array|null
     */
    private $bundleConfig;
    /**
     * @var PwaPushNotificationContainer
     */
    private $notificationContainer;

    /**
     * PushNotificationSender constructor.
     */
    public function __construct(?array $bundleConfig, PwaPushNotificationContainer $notificationContainer)
    {

        $this->bundleConfig          = $bundleConfig;
        $this->notificationContainer = $notificationContainer;
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
        if (!class_exists('Minishlink\WebPush\WebPush')) {
            throw new \Exception('Please install webpush using "composer require minishlink/web-push ^5.0".');
        }

        if (!$subscribers) {
            /** @var PwaPushSubscriberModel[]|PwaPushSubscriberModel|Collection|null */
            if (!$subscribers = PwaPushSubscriberModel::findByPid($config->id)) {
                throw new \Exception("No subscribers found.");
            }
        }

        if (!$this->bundleConfig || !isset($this->bundleConfig['vapid']['subject']) || !isset($this->bundleConfig['vapid']['publicKey']) && !isset($this->bundleConfig['vapid']['privateKey'])) {
            throw new \Exception("Bundle config not complete. Set vapid keys to use Push Notifications.");
        }

        $auth = [
            'VAPID' => [
                'subject'    => $this->bundleConfig['vapid']['subject'],
                'publicKey'  => $this->bundleConfig['vapid']['publicKey'],
                'privateKey' => $this->bundleConfig['vapid']['privateKey']
            ],
        ];

        try {
            $webPush = new \Minishlink\WebPush\WebPush($auth);
        } catch (\ErrorException $e) {
            throw new \Exception("Web push config: Could not construct WebPush object. Error message: " . $e->getMessage());
        }

        try {
            if (isset($this->bundleConfig['push']['automatic_padding'])) {
                $padding = $this->bundleConfig['push']['automatic_padding'];
                if (is_numeric($padding) && $padding > 0 && $padding <= \Minishlink\WebPush\Encryption::MAX_PAYLOAD_LENGTH) {
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
            throw new \Exception("Web push config: Invalid padding. Error message: " . $e->getMessage());
        }

        try {
            $payload = $notification->toArray();
        } catch (\ReflectionException $e) {
            throw new \Exception("Could not serialize notification. Error message: " . $e->getMessage());
        }

        if ($notificationsModel = $notification->getModel()) {
            $this->notificationContainer->notificationClickEvent($notificationsModel, $payload);
        }

        $validSubscribers = 0;
        $sendDate         = time(); //same sendtime for all subscribers and the notification

        /** @var PwaPushSubscriberModel $subscriber */
        foreach ($subscribers as $subscriber) {
            if (!$subscriber instanceof PwaPushSubscriberModel) {
                return ["success" => false, "message" => "Only subscribers of typ PushSubscriberModel are allowed."];
            }
            try {
                $webPush->sendNotification(
                    new \Minishlink\WebPush\Subscription($subscriber->endpoint, $subscriber->publicKey, $subscriber->authToken),
                    json_encode($payload)
                );
                $validSubscribers++;
            } catch (\ErrorException $e) {
                throw new \Exception("Error while sending push notification (Subscriber Id: {$subscriber->id}): " . $e->getMessage());
            }
        }

        try {
            $successCount = 0;
            $errors       = [];

            /** @var \Minishlink\WebPush\MessageSentReport $report */
            foreach ($webPush->flush() as $index => $report) {
                $subscriber = $subscribers->offsetGet($index);
                if ($report->isSuccess()) {
                    $subscriber->lastSuccessfulSend = $sendDate;
                    $subscriber->save();
                    $successCount++;
                } else {
                    $error = [
                        'endpoint' => $report->getEndpoint(),
                        'response' => json_encode($report->getResponse()->getBody()),
                    ];
                    if ($report->isSubscriptionExpired()) {
                        // TODO
                        $error['reason'] = 'Subscription expired';
                    } else {
                        $error['reason'] = $report->getReason();
                    }
                    $errors[] = $error;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Error while sending push notification: " . $e->getMessage());
        }


        if ($notification->getModel()) {
            $notification->getModel()->sent          = true;
            $notification->getModel()->dateSent      = $sendDate;
            $notification->getModel()->receiverCount = $validSubscribers;
            $notification->getModel()->save();
        }

        return [
            'success'      => true,
            'sentCount'    => $validSubscribers,
            'successCount' => $successCount,
            'errors'       => $errors,
        ];
    }
}