<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Generator;

use HeimrichHannot\PwaBundle\Manifest\ManifestIcon;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Component\Filesystem\Filesystem;

final class ManifestIconGenerator
{
    private string $iconBasePath;

    public function __construct(
        private readonly string $webDir,
    ) {}

    public function createIconInstance(
        string $sourceIconPath,
        string $applicationAlias,
        bool   $addDefaultSizes = false,
    ): ManifestIcon {
        return (new ManifestIcon($sourceIconPath, $applicationAlias, $addDefaultSizes))
            ->setWebRootPath($this->webDir)
            ->setManifestPath('..');
    }

    public function getIconBasePath(): string
    {
        return $this->iconBasePath;
    }

    public function setIconBasePath(string $iconBasePath): static
    {
        $this->iconBasePath = $iconBasePath;

        return $this;
    }

    /**
     * Create icons in the correct size
     *
     * @param ManifestIcon $icon
     */
    public function generateIcons(ManifestIcon $icon): void
    {
        $fs = new Filesystem();

        if (!$fs->exists($icon->getIconsPath())) {
            $fs->mkdir($icon->getIconsPath());
        }

        $imagine = new Imagine();
        foreach ($icon->getSizes() as $size)
        {
            $iconPath = $icon->generateIconName($size, true);

            if ($size === 'all')
            {
                \copy($icon->getSourceIconPath(), $iconPath);
                continue;
            }

            $sizes = explode('x', $size);

            $mask = $imagine->create(new Box($sizes[0], $sizes[1]));

            $image = $imagine->open($icon->getSourceIconPath(true));
            $thumb = $image->thumbnail(new Box($sizes[0], $sizes[1]), ImageInterface::THUMBNAIL_INSET);

            $posX = 0;
            $posY = 0;
            $iconWidth = $thumb->getSize()->getWidth();
            $iconHeight = $thumb->getSize()->getHeight();

            if ($iconWidth < $sizes[0]) {
                $posX = (($sizes[0] - $iconWidth) / 2);
            }

            if ($iconHeight < $sizes[1]) {
                $posY = (($sizes[1] - $iconHeight) / 2);
            }

            $mask->paste($thumb, new Point($posX, $posY))
                ->save($iconPath);
        }
    }
}