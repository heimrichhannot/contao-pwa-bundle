<?php

namespace HeimrichHannot\PwaBundle\Controller\BackendRoute;

use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\Message;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/%contao.backend.route_prefix%/huh_pwa/control',
    name: 'huh_pwa.backend.control',
    defaults: [
        '_scope' => 'backend',
        '_token_check' => true,
        '_custom_backend_view' => true,
        '_backend_module' => 'huh_pwa'
    ]
)]
class AdministrationController extends AbstractBackendController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly TranslatorInterface       $translator,
    ) {}

    public function __invoke(Request $request): Response
    {
        if (!$webPush = \class_exists(WebPush::class)) {
            Message::addInfo(
                'Install WebPush to use web push notifications. See the installation instructions in the
                <a href="https://github.com/heimrichhannot/contao-pwa-bundle/blob/main/README.md" target="_blank">README</a>.'
            );
        }

        $this->initializeContaoFramework();

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

        $backendBackRoute = $this->generateUrl('contao_backend', [...$params, 'do' => 'huh_pwa_configurations']);
        $unsentNotificationsRoute = $this->generateUrl('huh_pwa.backend.push_notifications.unsent', $params);
        $sendNotificationsRoute = $this->generateUrl('huh_pwa.backend.push_notifications.send', $params);
        $findPagesRoute = $this->generateUrl('huh_pwa.backend.pages', $params);
        $updatePageRoute = $this->generateUrl('huh_pwa.backend.pages.update', $params);


        return $this->render(
            '@Contao/backend/administration.html.twig',
            [
                'title' => $this->translator->trans('huh.pwa.backend.control.headline'),
                'headline' => $this->translator->trans('huh.pwa.backend.control.headline'),
                'messages' => Message::generate(),
                'vapid_keys' => $keys,
                'generatedKeys' => $generatedKeys,
                'content' => 'Content',
                'backend_back_route' => $backendBackRoute,
                'unsent_notifications_route' => $unsentNotificationsRoute,
                'send_notifications_route' => $sendNotificationsRoute,
                'find_pages_route' => $findPagesRoute,
                'update_page_route' => $updatePageRoute,
                'web_push' => $webPush,
            ]
        );
    }

}