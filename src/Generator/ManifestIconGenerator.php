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


use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Contao\Image\ResizerInterface;
use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Manifest\ManifestIcon;

class ManifestIconGenerator
{
	/**
	 * @var ImageFactoryInterface
	 */
	private $imageFactory;
	/**
	 * @var ResizerInterface
	 */
	private $resizer;


	/**
	 * ManifestIconGenerator constructor.
	 */
	public function __construct(ImageFactoryInterface $imageFactory, ResizerInterface $resizer)
	{
		$this->imageFactory = $imageFactory;
		$this->resizer = $resizer;
	}

	/**
	 * Create icons in the correct size
	 *
	 * @param ManifestIcon $icon
	 * @param string $appAlias
	 * @return array
	 */
	public function generateIcons(ManifestIcon $icon, string $appAlias)
	{
		$image = $this->imageFactory->create($icon->getSourceIconPath());
		$config = (new ResizeConfiguration())->setMode(ResizeConfiguration::MODE_PROPORTIONAL);
		$options = new ResizeOptions();
		$basePath = $icon->getIconsPath();

		$manifestIcons = [];

		foreach ($icon->getSizes() as $size)
		{
			$icon_path = $basePath.$icon->getIconBaseName().'-'.$size['width'].'.'.$icon->getIconExtension();
			$config->setWidth($size['width']);
			$config->setHeight($size['height']);
			$options->setTargetPath(System::getContainer()->getParameter('contao.web_dir').'/'.$icon_path);
			$this->resizer->resize($image, $config, $options);
			$manifestIcons[] = [
				"src" => $icon_path,
				"type" => mime_content_type($icon_path),
				"sizes" => $size["width"].'x'.$size['height']
			];
		}

		return $manifestIcons;
	}
}