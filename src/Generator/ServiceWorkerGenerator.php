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
use HeimrichHannot\ContaoPwaBundle\Manifest\Manifest;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use Symfony\Bridge\Monolog\Logger;

class ServiceWorkerGenerator
{
	/**
	 * @var string
	 */
	protected $webDir;
	/**
	 * @var \Twig_Environment
	 */
	protected $twig;
	/**
	 * @var string
	 */
	protected $serviceWorkerTemplate = '@HeimrichHannotContaoPwa/serviceworker/serviceworker_default.js.twig';
	/**
	 * @var Logger
	 */
	private $logger;


	/**
	 * ServiceWorkerGenerator constructor.
	 * @param string $webDir
	 * @param \Twig_Environment $twig
	 */
	public function __construct(string $webDir, \Twig_Environment $twig, Logger $logger)
	{
		$this->webDir = $webDir;
		$this->twig = $twig;
		$this->logger = $logger;
	}

	/**
	 * @param PageModel $page
	 * @return bool
	 */
	public function generatePageServiceworker(PageModel $page)
	{
		if (!$page->addPwa || !$page->pwaConfiguration)
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

		try
		{
			return (bool)file_put_contents(
				$this->webDir . '/sw_' . $page->alias . '.js',
				$this->twig->render($this->serviceWorkerTemplate, [
					'supportPush' => true,
					'pageTitle'   => $title,
					'version'     => date('YmdHis'),
					'alias'       => $page->alias,
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
}