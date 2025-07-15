<?php /** @noinspection PhpUndefinedClassInspection, PhpUndefinedNamespaceInspection */

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Message;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PageModel;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\PwaBundle\Notification\DefaultNotification;
use HeimrichHannot\PwaBundle\Sender\PushNotificationSender;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\VAPID;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment as TwigEnvironment;

#[Route('/%contao.backend.route_prefix%/huh_pwa', name: 'huh_pwa.backend.', defaults: [
    '_scope' => 'backend',
    '_token_check' => true,
    '_custom_backend_view' => true,
    '_backend_module' => 'huh_pwa'
])]
class BackendController extends AbstractController
{
    public function __construct(
        private readonly ContaoFramework           $contaoFramework,
        private readonly TwigEnvironment           $twig,
        private readonly ManifestGenerator         $manifestGenerator,
        private readonly ServiceWorkerGenerator    $serviceWorkerGenerator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly PushNotificationSender    $pushNotificationSender
    ) {}

    #[Route('/control', name: 'control')]
    public function huhBackendControlAction(Request $request): Response
    {
        if (!$webPush = \class_exists(WebPush::class))
        {
            Message::addInfo(
                'Install WebPush to use web push notifications. See the installation instructions in the
                <a href="https://github.com/heimrichhannot/contao-pwa-bundle/blob/main/README.md" target="_blank">README</a>.'
            );
        }

        $this->contaoFramework->initialize();

        $config = $this->getParameter('huh_pwa') ?: [];

        $keys = $config['vapid'] ?? null;
        $generatedKeys = null;

        if (!$keys && \class_exists(VAPID::class)) {
            $generatedKeys = VAPID::createVapidKeys();
        }

        $params = [
            'rt' => $this->csrfTokenManager->getToken($this->getParameter('contao.csrf_token_name'))->getValue(),
            'ref' => $request->get('_contao_referer_id'),
        ];

        $backendBackRoute        = $this->generateUrl('contao_backend', [...$params, 'do' => 'huh_pwa_configurations']);
        $unsentNotificationRoute = $this->generateUrl('huh_pwa.backend.push_notifications.unsent', $params);
        $sendNotificationRoute   = $this->generateUrl('huh_pwa.backend.push_notifications.send', $params);
        $findPagesRoute          = $this->generateUrl('huh_pwa.backend.pages', $params);
        $updatePageRoute         = $this->generateUrl('huh_pwa.backend.pages.update', $params);

        $content = $this->twig->render("@HeimrichHannotPwa/backend/backend.html.twig", [
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

    #[Route('/push_notifications/unsent', name: 'push_notifications.unsent', methods: ['GET'])]
    public function findUnsentNotificationAction(): Response
    {
        if (!$notifications = PwaPushNotificationsModel::findUnsentPublishedNotifications()) {
            return new JsonResponse(['count' => 0]);
        }

        $response          = [];
        $response['count'] = $notifications->count();

        foreach ($notifications as $notification) {
            $response['notifications'][] = $notification->id;
        }

        return new JsonResponse($response);
    }

    #[Route('/push_notifications/send', name: 'push_notifications.send', methods: ['POST'])]
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
            $result = $this->pushNotificationSender->send($pushNotification, $config);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse($result);
    }

    #[Route('/pages', name: 'pages', methods: ['GET'])]
    public function findPagesWithPwaAction(): Response
    {
        if (!$pages = PageModel::findAllWithActivePwaConfiguration()) {
            return new JsonResponse([]);
        }

        $result = [];

        foreach ($pages as $page)
        {
            $result[] = [
                "name" => $page->title,
                "id"   => $page->id
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/pages/update', name: 'pages.update', methods: ['POST'])]
    public function updatePageFilesAction(Request $request)
    {
        if (!$pageId = $request->get('pageId')) {
            return new Response('Page id is missing', Response::HTTP_BAD_REQUEST);
        }

        if (!$page = PageModel::findByPk($pageId)) {
            return new Response('Page not found', Response::HTTP_NOT_FOUND);
        }

        if ($page->type !== 'root') {
            return new Response('Page is not a root page', Response::HTTP_BAD_REQUEST);
        }

        if (!$page->addPwa) {
            return new Response('Page does not have PWA enabled', Response::HTTP_FORBIDDEN);
        }

        if (!PwaConfigurationsModel::findByPk($page->pwaConfiguration)) {
            return new Response('Page does not have a valid PWA configuration', Response::HTTP_I_AM_A_TEAPOT);
        }

        if (!$this->manifestGenerator->generatePageManifest($page)) {
            return new Response(\sprintf('Manifest generate failed for page with ID %s', $page->id), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$this->serviceWorkerGenerator->generatePageServiceworker($page)) {
            return new JsonResponse(['message' => 'Service worker generation failed for page with ID ' . $page->id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Service worker and manifest successfully generated for page with ID ' . $page->id], Response::HTTP_OK);
    }
}