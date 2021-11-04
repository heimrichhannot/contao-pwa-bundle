<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DataContainer;


use Contao\Message;
use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\ContaoPwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

class PageContainer
{
	const ADD_PWA_NO = 'no';
	const ADD_PWA_YES = 'yes';
	const ADD_PWA_INHERIT = 'inherit';

	/**
	 * @var ManifestGenerator
	 */
	private $manifestGenerator;
	/**
	 * @var FileLocator
	 */
	private $fileLocator;
	/**
	 * @var ContainerInterface
	 */
	private $container;
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	/**
	 * @var ServiceWorkerGenerator
	 */
	private $serviceWorkerGenerator;


	/**
	 * PageContainer constructor.
	 */
	public function __construct(ManifestGenerator $manifestGenerator, FileLocator $fileLocator, ContainerInterface $container, \Twig_Environment $twig, ServiceWorkerGenerator $serviceWorkerGenerator)
	{
		$this->manifestGenerator = $manifestGenerator;
		$this->fileLocator = $fileLocator;
		$this->container = $container;
		$this->twig = $twig;
		$this->serviceWorkerGenerator = $serviceWorkerGenerator;
	}

	public function onCreateVersionCallback($table, $pid, $version, $row)
	{
		if ($row['type'] !== 'root' || $row['addPwa'] !== self::ADD_PWA_YES )
		{
			return;
		}

		if (!$page = PageModel::findByPk($row['id']))
		{
			return;
		}

		if (!$pwaConfig = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
		{
			return;
		}

		try {
            $this->manifestGenerator->generatePageManifest($page);
        } catch (\Exception $e) {
            Message::addError(str_replace('%error%', $e->getMessage(), $GLOBALS['TL_LANG']['ERR']['huhPwaGenerateManifest']));
        }
		$this->serviceWorkerGenerator->generatePageServiceworker($page);
	}

	public function getPwaConfigurationsAsOptions()
	{
		$configs = PwaConfigurationsModel::findAll();
		if (!$configs)
		{
			return [];
		}
		$list = [];
		foreach ($configs as $config)
		{
			$list[$config->id] = $config->title;
		}
		return $list;
	}

	public function getInheritPwaPageConfigOptions()
	{
		$pages = PageModel::findBy('addPwa', PageContainer::ADD_PWA_YES);
		if (!$pages)
		{
			return [];
		}
		$options = [];
		/** @var PageModel $page */
		foreach ($pages as $page)
		{
			$pwaConfig = PwaConfigurationsModel::findByPk($pages->pwaConfiguration);
			if (!$pwaConfig)
			{
				continue;
			}
			$options[$page->id] = $page->title.' ('.$pwaConfig->title.')';
		}
		return $options;
	}
}