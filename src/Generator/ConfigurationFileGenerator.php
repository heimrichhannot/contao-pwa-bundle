<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Generator;


use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\EventDispatcher\Tests\Service;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigurationFileGenerator
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var array
     */
    private $bundleConfiguration;
    /**
     * @var string
     */
    private $webDir;
    /**
     * @var TranslatorInterface
     */
    private $translator;


    /**
     * ConfigurationFileGenerator constructor.
     * @param RouterInterface $router
     * @param string $webDir
     * @param array $bundleConfiguration
     */
    public function __construct(RouterInterface $router, string $webDir, array $bundleConfiguration, TranslatorInterface $translator)
    {
        $this->router              = $router;
        $this->bundleConfiguration = $bundleConfiguration;
        $this->webDir = $webDir;
        $this->translator = $translator;
    }

    /**
     * Generate a json file containing config parameters for js part of the web app into the config folder.
     *
     * @param PageModel $page
     * @return bool
     */
    public function generateConfigurationFile(PageModel $page)
    {
        if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration)
        {
            return false;
        }
        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
        {
            return false;
        }

        $configurationJson = json_encode($this->generateConfiguration($page, $config));
        $relativePath = $this->bundleConfiguration['configfile_path'];
        if (substr($relativePath, 0,1) != '/')
        {
            $relativePath = '/'.$relativePath;
        }
        $path = $this->webDir.$relativePath;
        $filename = $this->generateFileName($page);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path.'/'.$filename, $configurationJson);
        return true;
    }

    /**
     * Returns the configuration
     *
     * @param PageModel $page
     * @param PwaConfigurationsModel $config
     * @return array
     */
    public function generateConfiguration(PageModel $page, PwaConfigurationsModel $config)
    {
        $configuration = [];
        $configuration['debug'] = (bool) $config->addDebugLog;
        $configuration['serviceWorker'] = [
            'path' => ServiceWorkerGenerator::generateFileName($page),
            'scope' => ltrim($config->pwaScope, "/"),
        ];
        $configuration['pushNotifications'] = [
            'support' => (bool) $config->supportPush,
            'subscribePath' => $this->router->generate('push_notification_subscription', ['config' => $config->id]),
            'unsubscribePath' => $this->router->generate('push_notification_unsubscription', ['config' => $config->id]),
        ];
        $configuration['translations'] = [
            'pushnotifications' => [
                'subscribe'     => $this->translator->trans('huh.pwa.pushnotifications.subscribe'),
                'unsubscribe'   => $this->translator->trans('huh.pwa.pushnotifications.unsubscribe'),
                'blocked'       => $this->translator->trans('huh.pwa.pushnotifications.blocked'),
                'not_supported' => $this->translator->trans('huh.pwa.pushnotifications.not_supported'),
            ]
        ];
        $configuration['hideInstallPrompt'] = (bool) $config->hideInstallPrompt;
        return $configuration;
    }

    public function generateFileName (PageModel $page)
    {
        return $page->alias.'_config.json';
    }
}