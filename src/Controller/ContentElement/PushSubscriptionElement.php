<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment as TwigEnvironment;

#[AsContentElement(type: self::TYPE, category: 'pwa', template: 'content_element/pwa_push_subscription')]
class PushSubscriptionElement extends AbstractContentElementController implements ServiceSubscriberInterface
{
    public const TYPE = 'pwa_pushSubscription';

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        return $this->container->get('huh.utils')?->container()->isBackend()
            ? $this->getBackendResponse($template, $model, $request)
            : $this->getFrontendResponse($template, $model, $request);
    }

    protected function getBackendResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $beTemplate = new BackendTemplate('be_wildcard');
        $beTemplate->title = 'Web Push Notification Subscribe Button';

        $buffer = $beTemplate->parse();

        return new Response($buffer);
    }

    protected function getFrontendResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $buttonTemplate = \sprintf(
            '@Contao/%s.html.twig',
            $model->pwaSubscribeButtonTemplate ?: 'pwa/subscribe_button',
        );

        $template->setData([
            'button' => $this->container->get('twig')?->render($buttonTemplate),
        ]);

        return $template->getResponse();
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['twig'] = TwigEnvironment::class;
        $services['huh.utils'] = Utils::class;

        return $services;
    }
}