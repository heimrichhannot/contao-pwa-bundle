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


use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use Minishlink\WebPush\VAPID;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackendController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/contao", defaults={
 *     "_scope" = "backend",
 *     "_token_check" = true,
 *     "_custom_backend_view" = true,
 *     "_backend_module" = "huh_pwa"
 * })
 */
class BackendController extends Controller
{
	/**
	 * @Route("/pwa", name="huh.pwa.backend")
	 */
	public function testAction(Request $request)
	{
		$this->container->get('contao.framework')->initialize();

		$config = $this->container->getParameter('huh.pwa');

		$keys = isset($config["vapid_keys"]) ? $config['vapid_keys'] : null;
		$generatedKeys = null;

		if (!$keys)
		{
			$generatedKeys = VAPID::createVapidKeys();
		}

		$params = [];
		$params['rt'] = $this->get('security.csrf.token_manager')->getToken($this->getParameter('contao.csrf_token_name'))->getValue();
		$params['ref'] =  $request->get('_contao_referer_id');

		$unsentNotificationRoute = $this->get('router')->generate('huh_pwa_backend_pushnotification_find_unsent', $params);
		$sendNotificationRoute = $this->get('router')->generate('huh_pwa_backend_pushnotification_send', $params);

		$content = $this->container->get('twig')->render("@HeimrichHannotContaoPwa/backend/backend.html.twig", [
			"vapidkeys" => $keys,
			"generatedKeys" => $generatedKeys,
			"content" => "Content",
			"unsentNotificationRoute" => $unsentNotificationRoute,
			"sendNotificationRoute" => $sendNotificationRoute
		]);

		return new Response($content);
	}

	/**
	 * @Route("/pwa/pushnotification/unsent", name="huh_pwa_backend_pushnotification_find_unsent")
	 */
	public function findUnsentNotificationAction()
	{
		$notifications = PwaPushNotificationsModel::findUnsentNotifications();
		if (!$notifications)
		{
			return new JsonResponse(['count' => 0]);
		}
		$response = [];
		$response['count'] = $notifications->count();

		foreach ($notifications as $notification)
		{
			$response['notifications'][] = $notification->id;
		}
		return new JsonResponse($response);
	}

	/**
	 * @Route("/pwa/pushnotification/send", name="huh_pwa_backend_pushnotification_send", methods={"POST"})
	 */
	public function sendNotificationAction(Request $request)
	{
		$id = $request->get('notificationId');
		$notification = PwaPushNotificationsModel::findUnsentNotificationById($id);
		if (!$notification) {
			return new JsonResponse([
				"success" => false,
				"message" => "No unsent notification for given Id found!",
				"id" => $id,
			], 404);
		}

		if(!$config = PwaConfigurationsModel::findByPk($notification->pid))
		{
			return new JsonResponse([
				"success" => false,
				"message" => "No parent configuration for notification found!",
				"id" => $id,
				"parentId" => $notification->pid,
			], 404);
		}

		$pushNotification = new DefaultNotification($notification);
		return new JsonResponse($this->get('huh.pwa.sender.pushnotification')->send($pushNotification, $config));
	}
}