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


use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\Generator\ManifestGenerator;
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
	 * PageContainer constructor.
	 */
	public function __construct(ManifestGenerator $manifestGenerator, FileLocator $fileLocator, ContainerInterface $container, \Twig_Environment $twig)
	{
		$this->manifestGenerator = $manifestGenerator;
		$this->fileLocator = $fileLocator;
		$this->container = $container;
		$this->twig = $twig;
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

		$manifest = $this->manifestGenerator->generatePageManifest($page);

		file_put_contents(
			$this->container->getParameter('contao.web_dir') . '/sw_'.$page->alias.'.js',
			$this->twig->render('@HeimrichHannotContaoPwa/serviceworker/serviceworker_default.js.twig', [
				'supportPush' => true,
				'pageTitle' => $manifest->name,
				'version' => date('YmdHis')
			])
		);
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