<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\PwaBundle\Notification\DefaultNotification;
use HeimrichHannot\PwaBundle\Sender\PushNotificationSender;
use Model\Collection;
use Psr\Log\LoggerInterface;

readonly class CronjobListener
{
    public function __construct(
        private PushNotificationSender $notificationSender,
        private LoggerInterface        $logger,
        private ContaoFramework        $framework
    ) {}

    /**
     * @CronJob("minutely")
     */
    public function minutely(): void
    {
        $this->sendPushNotifications('minutely');
    }

    /**
     * @CronJob("hourly")
     */
    public function hourly(): void
    {
        $this->sendPushNotifications('hourly');
    }

    /**
     * @CronJob("daily")
     */
    public function daily(): void
    {
        $this->sendPushNotifications('daily');
    }

    /**
     * @CronJob("weekly")
     */
    public function weekly()
    {
        $this->sendPushNotifications('weekly');
    }

    /**
     * @CronJob("monthly")
     */
    public function monthly(): void
    {
        $this->sendPushNotifications('monthly');
    }

    private function sendPushNotifications(string $interval): void
    {
        $this->framework->initialize();

        /** @var PwaConfigurationsModel[]|Collection|null $configurations */
        if (!$configurations = PwaConfigurationsModel::findBy(['sendWithCron=?', 'cronIntervall=?'], ["1", $interval]))
        {
            return;
        }

        foreach ($configurations as $configuration)
        {
            if (!$notifications = PwaPushNotificationsModel::findUnsentPublishedNotificationsByPid($configuration->id)) {
                continue;
            }

            foreach ($notifications as $notification)
            {
                $pushNotification = new DefaultNotification($notification);

                try
                {
                    $this->notificationSender->send($pushNotification, $configuration);
                }
                catch (\ErrorException $e)
                {
                    $this->logger->error(
                        "Error while sending Push notifications (Notification ID: " . $notification->id . ", Configuration: " . $configuration->id . "): " . $e->getMessage(),
                        [
                            'trace' => $e->getTraceAsString(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ]
                    );
                    continue;
                }
            }
        }
    }
}