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


use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Generator\ManifestGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

class PageContainer
{
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

	public function oncreateVersionCallback($table, $pid, $version, $row)
	{
		$this->manifestGenerator->generatePageManifest($row);

//		$swPath = $this->fileLocator->locate('@HeimrichHannotContaoPwaBundle/Resources/public/js/sw.js');
		file_put_contents(
			$this->container->getParameter('contao.web_dir') . '/sw.js',
			$this->twig->render('@HeimrichHannotContaoPwa/serviceworker.js.twig')
		);
	}
}