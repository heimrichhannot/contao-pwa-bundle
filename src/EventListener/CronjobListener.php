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


use HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use HeimrichHannot\ContaoPwaBundle\Sender\PushNotificationSender;
use Model\Collection;
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


	/**
	 * CommandSchedulerListener constructor.
	 * @param PushNotificationSender $notificationSender
	 */
	public function __construct(PushNotificationSender $notificationSender, Logger $logger)
	{
		$this->notificationSender = $notificationSender;
		$this->logger = $logger;
	}

	public function minutely()
	{
		$this->sendPushNotifications('minutely');
	}
	public function hourly()
	{
		$this->sendPushNotifications('hourly');
	}
	public function daily()
	{
		$this->sendPushNotifications('daily');
	}
	public function weekly()
	{
		$this->sendPushNotifications('weekly');
	}
	public function monthly()
	{
		$this->sendPushNotifications('monthly');
	}

	private function sendPushNotifications(string $interval)
	{
		/** @var PwaConfigurationsModel[]|Collection|null $configurations */
		$configurations = PwaConfigurationsModel::findBy(['sendWithCron=?', 'cronIntervall=?'],["1",$interval]);
		if (!$configurations)
		{
			return;
		}

		foreach ($configurations as $configuration)
		{
			$notifications = PwaPushNotificationsModel::findUnsentNotificationsByPid($configuration->id);
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