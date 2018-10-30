<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Controller;


use HeimrichHannot\ContaoPwaBundle\Model\PwaSubscriberModel;
use Minishlink\WebPush\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/api/notifications")
 */
class NotificationController extends Controller
{
	/**
	 * @Route("/subscribe", name="push_notification_subscription")
	 */
	public function subscribeAction(Request $request)
	{
		$this->container->get('contao.framework')->initialize();
		$data = json_decode($request->getContent(), true);
		if (!isset($data['subscription']) || !isset($data['subscription']['endpoint']))
		{
			return new Response("Missing endpoint key.", 404);
		}
		$endpoint = $data['subscription']['endpoint'];

		if (!$user = PwaSubscriberModel::findByEndpoint($endpoint))
		{
			$user = new PwaSubscriberModel();
			$user->dateAdded = $user->tstamp = time();
			$user->endpoint = $data['subscription']['endpoint'];
			$user->save();
			return new Response("Subscription successfull!", 200);
		}
		return new Response("You already subscibed!", 200);
	}

	/**
	 * @Route("/send/{payload}", name="send_notification")
	 *
	 * @param Request $request
	 */
	public function sendAction(Request $request, string $payload)
	{
		$subscribers = PwaSubscriberModel::findAll();
		if (!$subscribers)
		{
			return new Request("No subscribers found.", 404);
		}

		$notifications = [];
		/** @var PwaSubscriberModel $subscriber */
		foreach ($subscribers as $subscriber)
		{
			$subscription = new Subscription($subscriber->endpoint);
		}


		$notifications = [
			[

			],
		];
	}
}