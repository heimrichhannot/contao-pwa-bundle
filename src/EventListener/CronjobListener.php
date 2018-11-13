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
		$notifications = $this->notificationSender->findUnsendNotifications();
		foreach ($notifications as $notification)
		{
			$configuration = PwaConfigurationsModel::findByPk($notification->pid);
			if (!$configuration)
			{
				$this->logger->error('No PWA confiration found for notification with id '.$notification->id,
				[
					'trace' => debug_backtrace(),
				]);
				continue;
			}
			$pushNotification = new DefaultNotification($notification);
			$this->notificationSender->send($pushNotification, $configuration);
		}



		$pushConfigurations = PwaConfigurationsModel::findAll();
		/** @var PwaConfigurationsModel $configuration */
		foreach ($pushConfigurations as $configuration)
		{
			/** @var PwaPushNotificationsModel[]|Collection|PwaPushNotificationsModel|null $notifications */
			if (!$notifications = PwaPushNotificationsModel::findBy(['pid=?','sent=?'],[$configuration->id, '']))
			{
				continue;
			}
			if (!$subscribers = PwaPushSubscriberModel::findByPid($configuration->id))
			{
				foreach ($notifications as $notification)
				{
					$notification->receiverCount = 0;
					$notification->sent = "1";
					$notification->save();
				}
				continue;
			}

			foreach ($notifications as $notification)
			{
				$pushNotification = new DefaultNotification();
				$pushNotification->setTitle($notification->title);
				$pushNotification->setBody($notification->body);
				$this->notificationSender->send($pushNotification, $configuration);
			}

		}
	}
}