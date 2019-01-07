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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

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
     * ConfigurationFileGenerator constructor.
     */
    public function __construct(RouterInterface $router, string $webDir, array $bundleConfiguration)
    {
        $this->router              = $router;
        $this->bundleConfiguration = $bundleConfiguration;
        $this->webDir = $webDir;
    }

    public function generateConfigurationFile(PageModel $page)
    {
        $configuration = [];
        if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration)
        {
            return false;
        }
        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
        {
            return false;
        }

        $configuration['debug'] = (bool) $config->addDebugLog;
        $configuration['serviceworker']['path'] = 'sw_'.$page->alias.'.js';
        $configuration['pushNotifications'] = [
            'support' => (bool) $config->supportPush,
            'subscribePath' => $this->router->generate('push_notification_subscription', ['config' => $config->id]),
            'unsubscribePath' => $this->router->generate('push_notification_unsubscription', ['config' => $config->id]),
        ];

        $configurationJson = json_encode($configuration);
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

    public function generateFileName (PageModel $page)
    {
        return $page->alias.'_config.json';
    }
}