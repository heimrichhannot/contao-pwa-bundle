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
use HeimrichHannot\ContaoPwaBundle\Manifest\ManifestIcon;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ManifestIconGenerator
{
	/**
	 * @var ImageFactoryInterface
	 */
	protected $imageFactory;
	/**
	 * @var ResizerInterface
	 */
	protected $resizer;
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	/**
	 * @var string
	 */
	protected $iconBasePath;


	/**
	 * ManifestIconGenerator constructor.
	 */
	public function __construct(ImageFactoryInterface $imageFactory, ResizerInterface $resizer, ContainerInterface $container)
	{
		$this->imageFactory = $imageFactory;
		$this->resizer      = $resizer;
		$this->container    = $container;
	}

	/**
	 * @param string $sourceIconPath
	 * @param string $applicationAlias
	 * @param bool $addDefaultSizes
	 * @return ManifestIcon
	 */
	public function createIconInstance(string $sourceIconPath, string $applicationAlias, bool $addDefaultSizes = false)
	{
		$icon = new ManifestIcon($sourceIconPath, $applicationAlias, $addDefaultSizes);
		$icon->setWebRootPath($this->container->getParameter('contao.web_dir'));
		return $icon;
	}

	/**
	 * Create icons in the correct size
	 *
	 * @param ManifestIcon $icon
	 * @param string $appAlias
	 */
	public function generateIcons(ManifestIcon $icon)
	{
		$image   = $this->imageFactory->create($icon->getSourceIconPath());
		$config  = (new ResizeConfiguration())->setMode(ResizeConfiguration::MODE_PROPORTIONAL);
		$options = new ResizeOptions();

		foreach ($icon->getSizes() as $size)
		{
			$iconPath = $icon->generateIconName($size, true);

			if ($size !== 'all')
			{
				$sizes = explode('x', $size);
				$config->setWidth($sizes[0]);
				$config->setHeight($sizes[1]);
				$options->setTargetPath($iconPath);
				$this->resizer->resize($image, $config, $options);
			}
			else {
				copy($icon->getSourceIconPath(), $iconPath);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getIconBasePath(): string
	{
		return $this->iconBasePath;
	}

	/**
	 * @param string $iconBasePath
	 */
	public function setIconBasePath(string $iconBasePath): void
	{
		$this->iconBasePath = $iconBasePath;
	}
}