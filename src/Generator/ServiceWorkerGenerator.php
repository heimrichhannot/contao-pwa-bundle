<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Generator;

use Contao\PageModel;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Error\RuntimeError as TwigRuntimeError;
use Twig\Error\SyntaxError as TwigSyntaxError;

readonly class ServiceWorkerGenerator
{
    public const DEFAULT_SERVICEWORKER_TEMPLATE = '@HeimrichHannotPwa/pwa/serviceworker.js.twig';

    public function __construct(
        private string                $webDir,
        private LoggerInterface       $logger,
        private TwigEnvironment       $twig,
        private UrlGeneratorInterface $router,
    ) {}

    public function generatePageServiceworker(PageModel $page): bool
    {
        if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration) {
            return false;
        }

        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration)) {
            return false;
        }

        $title = match ($config->pwaName)
        {
            PwaConfigurationsModel::PWA_NAME_CUSTOM => $config->pwaCustomName,
            PwaConfigurationsModel::PWA_NAME_META_PAGETITLE => $page->pageTitle,
            default => $page->title,
        };

        if ($config->serviceWorkerTemplate)
        {
            $template = '@Contao/' . $config->serviceWorkerTemplate . '.js.twig';
        }
        else
        {
            $template = static::DEFAULT_SERVICEWORKER_TEMPLATE;
        }

        $fileName = static::generateFileName($page);

        $offlinePage = '';
        if ($config->offlinePage > 0 && $offlinePageModel = PageModel::findById($config->offlinePage)) {
            $offlinePage = $offlinePageModel->getFrontendUrl();
        }

        $serviceworkerClass = '/bundles/heimrichhannotpwa/frontend/huh-pwa-serviceworker.js';

        try
        {
            $workerPath = $this->webDir . '/' . $fileName;
            $workerJs = $this->twig->render($template, [
                'supportPush' => (bool) $config->supportPush,
                'pageTitle' => $title,
                'version' => date('YmdHis'),
                'alias' => $page->alias,
                'debug' => (bool) $config->addDebugLog,
                'startUrl' => $config->pwaStartUrl,
                'offlinePage' => $offlinePage,
                'serviceworkerClass' => $serviceworkerClass,
                'updateSubscriptionPath' => $this->router->generate(
                    'huh_pwa.notification.update',
                    ['config' => $config->id],
                ),
            ]);

            return (bool) \file_put_contents($workerPath, $workerJs);
        }
        catch (TwigLoaderError|TwigRuntimeError|TwigSyntaxError $e)
        {
            $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return false;
    }

    public static function generateFileName(PageModel $page): string
    {
        return 'sw_' . $page->alias . '.js';
    }
}