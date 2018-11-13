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


use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;
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
		$findPagesRoute = $this->get('router')->generate('huh_pwa_backend_pages', $params);
		$updatePageRoute = $this->get('router')->generate('huh_pwa_backend_page_update', $params);

		$content = $this->container->get('twig')->render("@HeimrichHannotContaoPwa/backend/backend.html.twig", [
			"vapidkeys" => $keys,
			"generatedKeys" => $generatedKeys,
			"content" => "Content",
			"unsentNotificationRoute" => $unsentNotificationRoute,
			"sendNotificationRoute" => $sendNotificationRoute,
			"findPagesRoute" => $findPagesRoute,
			"updatePageRoute" => $updatePageRoute,
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

	/**
	 * @Route("/pwa/pages", name="huh_pwa_backend_pages")
	 */
	public function findPagesWithPwaAction()
	{
		/** @var PageModel[]|null $pages */
		$pages = PageModel::findByAddPwa(PageContainer::ADD_PWA_YES);
		if (!$pages)
		{
			return new JsonResponse([]);
		}
		$result = [];
		foreach ($pages as $page)
		{
			$result[] = [
				"name" => $page->title,
				"id" => $page->id
			];
		}
		return new JsonResponse($result);
	}

	/**
	 * @Route("/pwa/pages/update", name="huh_pwa_backend_page_update", methods={"POST"})
	 *
	 * @param Request $request
	 * @param int $pageId
	 * @return Response
	 */
	public function updatePageFilesAction(Request $request)
	{
		$pageId = $request->get('pageId');
		$page = PageModel::findByPk($pageId);
		if (!$page)
		{
			return new Response("Page with given id not exist", 404);
		}
		if (!$page->type === 'root')
		{
			return new Response("Page must be a root page", 404);
		}
		if(!$page->addPwa) {
			return new Response("Page don't support PWA", 404);
		}
		if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
		{
			return new Response("Page PWA config could not be found.", 404);
		}

		if (!$manifest = $this->get('huh.pwa.generator.manifest')->generatePageManifest($page))
		{
			return new Response("Error on generating manifest file for page ".$page->title." (".$page->id.")", 404);
		}
		if (!$this->get('huh.pwa.generator.serviceworker')->generatePageServiceworker($page))
		{
			return new JsonResponse([
				"message" => "Error on generating service worker file for page ".$page->title." (".$page->id.")"
			], 404);
		}
		return new JsonResponse([
			"message" => "Successfully updated manifest and serviceworker for page ".$page->title." (".$page->id.")"
		], 200);
	}

}