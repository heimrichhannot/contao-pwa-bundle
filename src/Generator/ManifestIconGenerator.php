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
use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\Basic\Fill;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
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
		$icon->setManifestPath('..');
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
//		$image   = $this->imageFactory->create($icon->getSourceIconPath());
//		$config  = (new ResizeConfiguration())->setMode(ResizeConfiguration::MODE_BOX);
//		$options = new ResizeOptions();

		$filesystem = new Filesystem();
		if (!$filesystem->exists($icon->getIconsPath()))
		{
			$filesystem->mkdir($icon->getIconsPath());
		}

		$imagine = new Imagine();
//		$sourceImage = $imagine->open($icon->getSourceIconPath());





//		$image = $imagine->open($icon->getSourceIconPath());

		foreach ($icon->getSizes() as $size)
		{

			$iconPath = $icon->generateIconName($size, true);

			if ($size !== 'all')
			{
				$sizes = explode('x', $size);

				$mask = $imagine->create(new Box($sizes[0], $sizes[1]));

				$image = $imagine->open($icon->getSourceIconPath());
				$thumb = $image->thumbnail(new Box($sizes[0], $sizes[1]), ImageInterface::THUMBNAIL_INSET);

				$posX = 0;
				$posY = 0;
				$iconWidth = $thumb->getSize()->getWidth();
				$iconHeigth = $thumb->getSize()->getHeight();

				if ($iconWidth < $sizes[0])
				{
					$posX = (($sizes[0]- $iconWidth) / 2);
				}
				if ($iconHeigth < $sizes[1])
				{
					$posY = (($sizes[1]- $iconHeigth) / 2);
				}
				$mask->paste($thumb, new Point($posX, $posY))->save($iconPath);


//				$thumb = $image->thumbnail(new Box($sizes[0], $sizes[1]), ImageInterface::THUMBNAIL_INSET)->save($iconPath);
//				$thumb->thumbnail(new Box($sizes[0], $sizes[1]), ImageInterface::THUMBNAIL_OUTBOUND)->save($iconPath);




//				$mask->applyMask($thumb)->save($iconPath);


//				$image->resize(new Box($sizes[0], $sizes[1])->cr, ImageInterface::)->save($iconPath);
//				$size = $image->getSize();





//				$config->setWidth($sizes[0]);
//				$config->setHeight($sizes[1]);
//				$options->setTargetPath($iconPath);
//				$this->resizer->resize($image, $config, $options);
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