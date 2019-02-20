<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Generator;


use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\UtilsBundle\Template\TemplateUtil;
use Symfony\Bridge\Monolog\Logger;

class ServiceWorkerGenerator
{
    const DEFAULT_SERVICEWORKER_TEMPLATE = '@HeimrichHannotContaoPwa/serviceworker/pwa_serviceworker_default.js.twig';

	/**
	 * @var string
	 */
	protected $webDir;
	/**
	 * @var \Twig_Environment
	 */
	protected $twig;
	/**
	 * @var Logger
	 */
	private $logger;
    /**
     * @var TemplateUtil
     */
    private $templateUtil;


    /**
	 * ServiceWorkerGenerator constructor.
	 * @param string $webDir
	 * @param \Twig_Environment $twig
	 */
	public function __construct(string $webDir, \Twig_Environment $twig, Logger $logger, TemplateUtil $templateUtil)
	{
		$this->webDir = $webDir;
		$this->twig = $twig;
		$this->logger = $logger;
        $this->templateUtil = $templateUtil;
    }

	/**
	 * @param PageModel $page
	 * @return bool
	 */
	public function generatePageServiceworker(PageModel $page)
	{
		if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration)
		{
			return false;
		}
		if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
		{
			return false;
		}

		switch ($config->pwaName)
		{
			case PwaConfigurationsModel::PWA_NAME_CUSTOM:
				$title = $config->pwaCustomName;
				break;
			case PwaConfigurationsModel::PWA_NAME_META_PAGETITLE:
				$title = $page->pageTitle;
				break;
			default:
				$title = $page->title;
		}

		if ($config->serviceWorkerTemplate)
        {
            $template = $this->templateUtil->getTemplate($config->serviceWorkerTemplate, 'js.twig');
        }
		else {
            $template = static::DEFAULT_SERVICEWORKER_TEMPLATE;
        }

		$fileName = static::generateFileName($page);

		$offlinePage = '';
		if ($config->offlinePage > 0)
        {
            $offlinePageModel = PageModel::findById($config->offlinePage);
            if ($offlinePageModel)
            {
                $offlinePage = $offlinePageModel->getFrontendUrl();
            }

        }

		try
		{
			return (bool)file_put_contents(
				$this->webDir . '/'.$fileName,
				$this->twig->render($template, [
					'supportPush' => (bool)$config->supportPush,
					'pageTitle'   => $title,
					'version'     => date('YmdHis'),
					'alias'       => $page->alias,
					'debug'       => (bool)$config->addDebugLog,
					'startUrl'    => $config->pwaStartUrl,
                    'offlinePage' => $offlinePage,
				])
			);
		} catch (\Twig_Error_Loader $e)
		{
			$this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
			return false;
		} catch (\Twig_Error_Runtime $e)
		{
			$this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
			return false;
		} catch (\Twig_Error_Syntax $e)
		{
			$this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
			return false;
		}
	}

	public static function generateFileName (PageModel $page)
    {
        return 'sw_' . $page->alias . '.js';
    }
}