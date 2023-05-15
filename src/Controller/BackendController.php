<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedNamespaceInspection */

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Message;
use HeimrichHannot\ContaoPwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\ContaoPwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\ContaoPwaBundle\Model\PageModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Minishlink\WebPush\VAPID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

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
 *
 * @property ContainerInterface $container
 */
class BackendController extends AbstractController
{
    private ContaoFramework           $contaoFramework;
    private Utils                     $utils;
    private Environment               $twig;
    private ManifestGenerator         $manifestGenerator;
    private ServiceWorkerGenerator    $serviceWorkerGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        ContaoFramework $contaoFramework,
        Utils $utils,
        Environment $twig,
        ManifestGenerator $manifestGenerator,
        ServiceWorkerGenerator $serviceWorkerGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->contaoFramework = $contaoFramework;
        $this->utils = $utils;
        $this->twig = $twig;
        $this->manifestGenerator = $manifestGenerator;
        $this->serviceWorkerGenerator = $serviceWorkerGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/pwa/control", name="huh_pwa_backend_control")
     */
    public function huhBackendControlAction(Request $request): Response
    {
        $webPush = true;
        if (!class_exists('Minishlink\WebPush\WebPush')) {
            Message::addInfo("You need to install WebPush library to use web push function! (\"composer require minishlink/web-push ^5.0\")");
            $webPush = false;
        }

        $this->contaoFramework->initialize();

        $config = $this->getParameter('huh_pwa');

        $keys          = isset($config["vapid"]) ? $config['vapid'] : null;
        $generatedKeys = null;

        if (!$keys && class_exists(VAPID::class)) {
            $generatedKeys = VAPID::createVapidKeys();
        }

        $params        = [];
        $params['rt']  = $this->csrfTokenManager->getToken($this->getParameter('contao.csrf_token_name'))->getValue();
        $params['ref'] = $request->get('_contao_referer_id');

        $backendBackRoute        = $this->utils->routing()->generateBackendRoute(['do' => 'huh_pwa_configurations']);
        $unsentNotificationRoute = $this->generateUrl('huh_pwa_backend_pushnotification_find_unsent', $params);
        $sendNotificationRoute   = $this->generateUrl('huh_pwa_backend_pushnotification_send', $params);
        $findPagesRoute          = $this->generateUrl('huh_pwa_backend_pages', $params);
        $updatePageRoute         = $this->generateUrl('huh_pwa_backend_page_update', $params);


        $content = $this->twig->render("@HeimrichHannotContaoPwa/backend/backend.html.twig", [
            'messages'              => Message::generate(),
            "vapidkeys"               => $keys,
            "generatedKeys"           => $generatedKeys,
            "content"                 => "Content",
            "backendBackRoute"        => $backendBackRoute,
            "unsentNotificationRoute" => $unsentNotificationRoute,
            "sendNotificationRoute"   => $sendNotificationRoute,
            "findPagesRoute"          => $findPagesRoute,
            "updatePageRoute"         => $updatePageRoute,
            "webPush"                 => $webPush,
        ]);

        return new Response($content);
    }

    /**
     * @Route("/pwa/pushnotification/unsent", name="huh_pwa_backend_pushnotification_find_unsent")
     */
    public function findUnsentNotificationAction(): Response
    {
        $notifications = PwaPushNotificationsModel::findUnsentPublishedNotifications();
        if (!$notifications) {
            return new JsonResponse(['count' => 0]);
        }
        $response          = [];
        $response['count'] = $notifications->count();

        foreach ($notifications as $notification) {
            $response['notifications'][] = $notification->id;
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/pwa/pushnotification/send", name="huh_pwa_backend_pushnotification_send", methods={"POST"})
     */
    public function sendNotificationAction(Request $request): Response
    {
        $id           = $request->get('notificationId');
        $notification = PwaPushNotificationsModel::findUnsentNotificationById($id);
        if (!$notification) {
            return new JsonResponse([
                "success" => false,
                "message" => "No unsent notification for given Id found!",
                "id"      => $id,
            ], 404);
        }

        if (!$config = PwaConfigurationsModel::findByPk($notification->pid)) {
            return new JsonResponse([
                "success"  => false,
                "message"  => "No parent configuration for notification found!",
                "id"       => $id,
                "parentId" => $notification->pid,
            ], 404);
        }

        $pushNotification = new DefaultNotification($notification);

        try {
            $result = $this->container->get('huh.pwa.sender.pushnotification')->send($pushNotification, $config);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/pwa/pages", name="huh_pwa_backend_pages")
     */
    public function findPagesWithPwaAction(): Response
    {
        $pages = PageModel::findAllWithActivePwaConfiguration();
        if (!$pages) {
            return new JsonResponse([]);
        }
        $result = [];
        foreach ($pages as $page) {
            $result[] = [
                "name" => $page->title,
                "id"   => $page->id
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/pwa/pages/update", name="huh_pwa_backend_page_update", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function updatePageFilesAction(Request $request)
    {
        $pageId = $request->get('pageId');
        $page   = PageModel::findByPk($pageId);
        if (!$page) {
            return new Response("Page with given id not exist", 404);
        }
        if (!($page->type === 'root')) {
            return new Response("Page must be a root page", 404);
        }
        if (!$page->addPwa) {
            return new Response("Page don't support PWA", 404);
        }
        if (!PwaConfigurationsModel::findByPk($page->pwaConfiguration)) {
            return new Response("Page PWA config could not be found.", 404);
        }

        if (!$this->manifestGenerator->generatePageManifest($page)) {
            return new Response("Error on generating manifest file for page " . $page->title . " (" . $page->id . ")", 404);
        }
        if (!$this->serviceWorkerGenerator->generatePageServiceworker($page)) {
            return new JsonResponse([
                "message" => "Error on generating service worker file for page " . $page->title . " (" . $page->id . ")"
            ], 404);
        }
        return new JsonResponse([
            "message" => "Successfully updated manifest and serviceworker for page " . $page->title . " (" . $page->id . ")"
        ], 200);
    }

}