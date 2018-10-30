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


use Contao\Model\Collection;
use HeimrichHannot\ContaoPwaBundle\Model\PwaSubscriberModel;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
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
	 * @Route("/subscribe", name="push_notification_subscription", methods={"POST"})
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
			$user->publicKey = $data['subscription']['keys']['p256dh'];
			$user->authToken = $data['subscription']['keys']['auth'];
			$user->save();
			return new Response("Subscription successful!", 200);
		}
		return new Response("You already subscribed!", 200);
	}

	/**
	 * @Route("/unsubscribe", name="push_notification_unsubscription", methods={"POST"})
	 */
	public function unsubscribeAction(Request $request)
	{
		$this->container->get('contao.framework')->initialize();
		$data = json_decode($request->getContent(), true);
		if (!isset($data['subscription']) || !isset($data['subscription']['endpoint']))
		{
			return new Response("Missing endpoint key.", 404);
		}
		$endpoint = $data['subscription']['endpoint'];

		/** @var PwaSubscriberModel|Collection|null $user */
		if ($user = PwaSubscriberModel::findByEndpoint($endpoint))
		{
			if ($user instanceof Collection)
			{
				foreach ($user as $entry)
				{
					$entry->delete();
				}
			}
			else {
				$user->delete();
			}
			return new Response("User successful unsubscribed!", 200);
		}
		return new Response("User not found!", 404);
	}

	/**
	 * @Route("/send/{payload}", name="send_notification")
	 *
	 * @param Request $request
	 * @param string $payload
	 * @return Response
	 * @throws \ErrorException
	 */
	public function sendAction(Request $request, string $payload)
	{
		$this->container->get('contao.framework')->initialize();
		$subscribers = PwaSubscriberModel::findAll();
		if (!$subscribers)
		{
			return new Response("No subscribers found.", 404);
		}
		if (!$publicKey = $this->getPublicKey())
		{
			return new Response("No public key available", 404);
		}

		$auth = [
			'subject' => 'mailto:t.koerner@heimrich-hannot.de', //$request->getSchemeAndHttpHost(),
			'publicKey' => $publicKey,
			'privateKey' => $this->container->getParameter('huh.pwa')['vapid_keys']['private']
		];

		$webPush = new WebPush($auth);
		/** @var PwaSubscriberModel $subscriber */
		foreach ($subscribers as $subscriber)
		{

			$webPush->sendNotification(
				new Subscription($subscriber->endpoint, $subscriber->publicKey, $subscriber->authToken),
				$payload
			);
		}
//		$response = $webPush->flush();
		dump($webPush->flush());
		die();
//		return new (print_r($webPush->flush()), 200);
	}

	/**
	 * @Route("/publickey", name="huh.pwa.notification.publickey", methods={"GET"})
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function returnPublicKeyAction(Request $request)
	{
		if ($key = $this->getPublicKey())
		{
			return new Response($key);
		}
		return new Response("No public key available.", 400);
	}

	protected function getPublicKey()
	{
		$config = $this->getParameter("huh.pwa");
		if (!isset($config['vapid_keys']) || !isset($config['vapid_keys']['public']))
		{
			return false;
		}
		return $config['vapid_keys']['public'];
	}
}