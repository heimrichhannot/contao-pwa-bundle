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

use Contao\FilesModel;
use Contao\PageModel;
use HeimrichHannot\ContaoPwaBundle\Manifest\Manifest;
use HeimrichHannot\ContaoPwaBundle\Manifest\ManifestIcon;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ManifestGenerator
{
	private $defaultManifestPath;
	/**
	 * @var ManifestIconGenerator
	 */
	private $iconGenerator;

	/**
	 * ManifestGenerator constructor.
	 * @param ContainerInterface $container
	 * @param ManifestIconGenerator $iconGenerator
	 */
	public function __construct(ContainerInterface $container, ManifestIconGenerator $iconGenerator)
	{
		$this->defaultManifestPath = $container->getParameter('contao.web_dir').'/manifest';
		$this->iconGenerator = $iconGenerator;
	}

	public function generateManifest(Manifest $manifest, string $filename, string $path)
	{
		$manifestJson = $manifest->jsonSerialize();
		if ($manifest->icons->isIconFilesMissing())
		{
			$this->iconGenerator->generateIcons($manifest->icons, $manifest->icons->getApplicationAlias());
			$manifestJson = $manifest->jsonSerialize();
		}

		$filesystem = new Filesystem();
		$filesystem->dumpFile($path.'/'.$filename, $manifestJson);
	}

	/**
	 * Generate an manifest out of an page
	 *
	 * @param PageModel|array $page
	 */
	public function generatePageManifest($page)
	{
		if ($page instanceof PageModel)
		{
			$page = $page->row();
		}
		if (!is_array($page))
		{
			throw new \InvalidArgumentException("Page Manifest could only be generated from PageModel or PageModel row array!");
		}

		$manifest = new Manifest();
		$manifest->name = $page['pageTitle'];
		$manifest->short_name = $page['pwaShortName'];
		$manifest->display = $page['pwaDisplay'];
		$manifest->lang = $page['language'];
		$manifest->background_color = '#'.$page['pwaBackgroundColor'];
		$manifest->theme_color = '#'.$page['pwaThemeColor'];

		$iconModel = FilesModel::findByUuid($page['pwaIcons']);
		if ($iconModel)
		{
			$manifest->icons = $this->iconGenerator->createIconInstance($iconModel->path, $page['alias'], true);
		}
		$filename = $page['alias'].'_manifest.json';
		$this->generateManifest($manifest, $filename, $this->defaultManifestPath);
	}
}