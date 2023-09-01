<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener;


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use HeimrichHannot\ContaoPwaBundle\Sender\PushNotificationSender;
use Model\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;

class CronjobListener
{
	/**
	 * @var PushNotificationSender
	 */
	private $notificationSender;
	/**
	 * @var Logger
	 */
	private $logger;
    private ContaoFramework $framework;


    /**
	 * CommandSchedulerListener constructor.
	 * @param PushNotificationSender $notificationSender
	 */
	public function __construct(PushNotificationSender $notificationSender, LoggerInterface $logger, ContaoFramework $framework)
	{
		$this->notificationSender = $notificationSender;
		$this->logger = $logger;
        $this->framework = $framework;
    }

    /**
     * @CronJob("minutely")
     */
	public function minutely()
	{
		$this->sendPushNotifications('minutely');
	}
    /**
     * @CronJob("hourly")
     */
	public function hourly()
	{
		$this->sendPushNotifications('hourly');
	}
    /**
     * @CronJob("daily")
     */
	public function daily()
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
	public function monthly()
	{
		$this->sendPushNotifications('monthly');
	}

	private function sendPushNotifications(string $interval)
	{
        $this->framework->initialize();

		/** @var PwaConfigurationsModel[]|Collection|null $configurations */
		$configurations = PwaConfigurationsModel::findBy(['sendWithCron=?', 'cronIntervall=?'],["1",$interval]);
		if (!$configurations)
		{
			return;
		}

		foreach ($configurations as $configuration)
		{
			$notifications = PwaPushNotificationsModel::findUnsentPublishedNotificationsByPid($configuration->id);
			if (!$notifications)
			{
				continue;
			}
			foreach ($notifications as $notification)
			{
				$pushNotification = new DefaultNotification($notification);
				try
				{
					$this->notificationSender->send($pushNotification, $configuration);
				} catch (\ErrorException $e)
				{
					$this->logger->error("Error while sending Push notifications 
										(Notification ID: " .$notification->id.", Configuration: ".$configuration->id."): ".$e->getMessage(),
						[
							'trace' => $e->getTraceAsString(),
							'file' => $e->getFile(),
							'line' => $e->getLine(),
						]);
					continue;
				}
			}

		}
	}
}