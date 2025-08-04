<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Generator;

use Contao\PageModel;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ConfigurationFileGenerator
{
    public function __construct(
        private array               $bundleConfig,
        private string              $webDir,
        private RouterInterface     $router,
        private TranslatorInterface $translator
    ) {}

    /**
     * Generate a json file containing config parameters for js part of the web app into the config folder.
     */
    public function generateConfigurationFile(PageModel $page): bool
    {
        if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration)
        {
            return false;
        }

        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
        {
            return false;
        }

        if (!$configurationJson = \json_encode($this->generateConfiguration($page, $config)))
        {
            return false;
        }

        $relativePath = $this->bundleConfig['configfile_path'] ?? '/pwa';

        if (!\str_starts_with($relativePath, '/')) {
            $relativePath = '/' . $relativePath;
        }

        $filepath = $this->webDir . $relativePath . '/' . $page->alias . '_config.json';

        $fs = new Filesystem();
        $fs->dumpFile($filepath, $configurationJson);

        return $fs->exists($filepath);
    }

    /**
     * Returns the configuration
     */
    public function generateConfiguration(PageModel $page, PwaConfigurationsModel $config): array
    {
        return [
            'debug' => (bool) $config->addDebugLog,
            'serviceWorker' => [
                'path' => ServiceWorkerGenerator::generateFileName($page),
                'scope' => \ltrim($config->pwaScope, "/"),
            ],
            'pushNotifications' => [
                'support' => (bool) $config->supportPush,
                'subscribePath' => $this->router->generate('huh_pwa.notification.subscribe', ['config' => $config->id]),
                'unsubscribePath' => $this->router->generate('huh_pwa.notification.unsubscribe', ['config' => $config->id]),
            ],
            'translations' => [
                'pushnotifications' => [
                    'subscribe'     => $this->translator->trans('huh.pwa.pushnotifications.subscribe'),
                    'unsubscribe'   => $this->translator->trans('huh.pwa.pushnotifications.unsubscribe'),
                    'blocked'       => $this->translator->trans('huh.pwa.pushnotifications.blocked'),
                    'not_supported' => $this->translator->trans('huh.pwa.pushnotifications.not_supported'),
                ]
            ],
            'hideInstallPrompt' => (bool) $config->hideInstallPrompt,
        ];
    }
}