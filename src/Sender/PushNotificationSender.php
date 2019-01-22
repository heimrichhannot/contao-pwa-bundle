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
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\AbstractNotification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

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

		$this->bundleConfig = $bundleConfig;
		$this->notificationContainer = $notificationContainer;
	}

	/**
	 * Send the notification to all recipients or a list of given recipients.
	 *
	 * @param AbstractNotification $notification
	 * @param PwaConfigurationsModel $config
	 * @param array|PwaPushSubscriberModel[]|null $subscribers
	 * @return array|bool
	 * @throws \ErrorException
	 */
	public function send(AbstractNotification $notification, PwaConfigurationsModel $config, ?array $subscribers = null)
	{

		if (!$subscribers)
		{
			/** @var PwaPushSubscriberModel[]|PwaPushSubscriberModel|Collection|null */
			if (!$subscribers = PwaPushSubscriberModel::findByPid($config->id)) {
				return ["success" => false, "message"=> "No subscribers"];
			}
		}

		if (!$this->bundleConfig || !isset($this->bundleConfig['vapid']['subject']) || !isset($this->bundleConfig['vapid']['publicKey']) && !isset($this->bundleConfig['vapid']['privateKey']))
		{
			return ["success" => false, "message" => "Bundle config not complete. Set vapid keys to use Push Notifications."];
		}

		$auth = [
			'VAPID' => [
				'subject' => $this->bundleConfig['vapid']['subject'],
				'publicKey' => $this->bundleConfig['vapid']['publicKey'],
				'privateKey' => $this->bundleConfig['vapid']['privateKey']
			],
		];

		try
		{
			$webPush = new WebPush($auth);
		} catch (\ErrorException $e)
		{
			return ["success" => false, "message" => $e->getMessage()];
		}

		$payload = [];
		try
		{
			$payload = $notification->toArray();
		} catch (\ReflectionException $e)
		{
			return ["sucess" => false, "message" => "Could not serialize notification. Error message: ".$e->getMessage()];
		}

		if ($notificationsModel = $notification->getSource())
		{
			$this->notificationContainer->notificationClickEvent($notificationsModel, $payload);
		}

		$validSubscribers = 0;
		$sendDate = time(); //same sendtime for all subscribers and the notification

		/** @var PwaPushSubscriberModel $subscriber */
		foreach ($subscribers as $subscriber)
		{
			if (!$subscriber instanceof PwaPushSubscriberModel)
			{
				return ["success" => false, "message" => "Only subscribers of typ PushSubscriberModel are allowed."];
			}
			try
			{
				$webPush->sendNotification(
					new Subscription($subscriber->endpoint, $subscriber->publicKey, $subscriber->authToken),
					json_encode($payload)
				);
				$validSubscribers++;
			} catch (\ErrorException $e)
			{
				return ["success" => false, "message" => $e->getMessage()];
			}
			$subscriber->lastSuccessfulSend = $sendDate;
			$subscriber->save();
		}

		$result = $webPush->flush();

		if ($notification->getSource())
		{
			$notification->getSource()->sent = "1";
			$notification->getSource()->sendDate = $sendDate;
			$notification->getSource()->receiverCount = $validSubscribers;
			$notification->getSource()->save();
		}

		return [
			'success' => true,
			'sentCount' => $validSubscribers,
			'result' => $result,
		];
	}
}