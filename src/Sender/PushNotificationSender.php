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


use HeimrichHannot\ContaoPwaBundle\Model\PushSubscriberModel;
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
	 * PushNotificationSender constructor.
	 */
	public function __construct(?array $bundleConfig)
	{

		$this->bundleConfig = $bundleConfig;
	}

	/**
	 * Send the notification to all recipients or a list of given recipients.
	 *
	 * @param AbstractNotification $notification
	 * @param array|PushSubscriberModel[]|null $subscribers
	 * @return array|bool
	 * @throws \ErrorException
	 */
	public function send(AbstractNotification $notification, ?array $subscribers = null)
	{

		if (!$subscribers)
		{
			if (!$subscribers = PushSubscriberModel::findAll()) {
				return [["success" => false, "message"=> "No subscribers"]];
			}
		}

		if (!$this->bundleConfig || !isset($this->bundleConfig['vapid']['subject']) || !isset($this->bundleConfig['vapid']['publicKey']) && !isset($this->bundleConfig['vapid']['privateKey']))
		{
			return [["success" => false, "message" => "Bundle config not complete. Set vapid keys to use Push Notifications."]];
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
			return [["success" => false, "message" => $e->getMessage()]];
		}

		try
		{
			$payload = $notification->jsonSerialize();
		} catch (\ReflectionException $e)
		{
			return [["sucess" => false, "message" => "Could not serialize notification. Error message: ".$e->getMessage()]];
		}

		/** @var PushSubscriberModel $subscriber */
		foreach ($subscribers as $subscriber)
		{
			if (!$subscriber instanceof PushSubscriberModel)
			{
				return [["success" => false, "message" => "Only subscribers of typ PushSubscriberModel are allowed."]];
			}
			try
			{
				$webPush->sendNotification(
					new Subscription($subscriber->endpoint, $subscriber->publicKey, $subscriber->authToken),
					$payload
				);
			} catch (\ErrorException $e)
			{
				return [["success" => false, "message" => $e->getMessage()]];
			}
		}
		return $webPush->flush();
	}
}