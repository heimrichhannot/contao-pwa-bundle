<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Manifest;

final class ManifestIcon
{
    const SIZES_DEFAULT = [
        '192x192',
        '512x512',
    ];

    /** @var array The configured sizes */
    private array $sizes = [];
    private string $sourceIconPath;
    /** @var string Icon name without file extension */
    private string $iconBaseName;
    /** @var string Icon file extension */
    private string $iconExtension;
    /** @var string Relative path from web root to the folder where icons should be stored */
    private string $iconBasePath = 'assets/images/heimrichhannotpwa';
    /** @var string path from the manifest file to the web root. Leave empty if manifest is in web root. */
    private string $manifestPath = '';
    /** @var string Absolute path to the web root */
    private string $webRootPath = __DIR__ . '/../../../../..';
    private string $applicationAlias;
    private bool $iconFilesMissing = false;

    public function __construct(string $sourceIconPath, string $applicationAlias, bool $addDefaultSizes = false)
    {
        $this->applicationAlias = $applicationAlias;
        $this->setSourceIconPath($sourceIconPath);

        if ($addDefaultSizes)
        {
            $this->sizes = ManifestIcon::SIZES_DEFAULT;
        }
    }

    public function getSourceIconPath(bool $absolutePath = false): string
    {
        if ($absolutePath)
        {
            return $this->webRootPath . '/' . $this->sourceIconPath;
        }

        return $this->sourceIconPath;
    }

    /**
     * @param string $sourceIconPath
     */
    public function setSourceIconPath(string $sourceIconPath): static
    {
        $this->sourceIconPath = $sourceIconPath;
        $extension = \pathinfo($sourceIconPath, \PATHINFO_EXTENSION);
        $this->iconBaseName = \basename($sourceIconPath, '.' . $extension);
        $this->iconExtension = $extension;

        return $this;
    }

    /**
     * @return array
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    /**
     * @param array $sizes
     * @return bool True if all sizes were valid and added. False, if at least on size was invalid.
     */
    public function setSizes(array $sizes): bool
    {
        $success = true;

        foreach ($sizes as $size)
        {
            if (!$this->addSize($size))
            {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param string $size Must be "any" or "[Width]x[Height]
     * @return bool True if size was valid and added, false if size was not valid and not added.
     */
    public function addSize(string $size): bool
    {
        if ($valid = $this->validateSize($size)) {
            $this->sizes[] = $size;
        }

        return $valid;
    }

    /**
     * Validate if a given string is a valid icon sizes
     */
    public function validateSize(string $size): bool
    {
        if ('any' === $size) {
            return true;
        }

        if (1 === \preg_match('/(\d+)x(\d+)/', $size, $matches))
        {
            if ($matches[0] === $size) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $width  Icon width in pixel
     * @param int $height Icon height in pixel
     */
    public function addSizePixel(int $width, int $height): static
    {
        $this->addSize($width . 'x' . $height);

        return $this;
    }

    /**
     * @return string
     */
    public function getIconBaseName(): string
    {
        return $this->iconBaseName;
    }

    /**
     * @return string
     */
    public function getIconExtension(): string
    {
        return $this->iconExtension;
    }

    /**
     * Relative path to the folder where current icons are stored.
     */
    public function getIconsPath(string $manifestPath = ''): string
    {
        $basePath = $this->iconBasePath;

        if (!empty($manifestPath)) {
            $basePath = $manifestPath . '/' . $basePath;
        }

        return $basePath . '/' . $this->applicationAlias . '/icons';
    }

    /**
     * Absolute path to the folder where current icons are stored.
     *
     * @return string
     */
    public function getIconAbsolutePath(): string
    {
        return $this->webRootPath . '/' . $this->getIconsPath();
    }

    public function generateIconName(string $size, bool $withAbsolutePath = false): string
    {
        if (!$this->validateSize($size))
        {
            throw new \InvalidArgumentException("Attribute size must be an valid icon size string!");
        }
        if ($size != 'any')
        {
            $size = (explode('x', $size))[0];
        }
        $iconName = $this->iconBaseName . '-' . $size . '.' . $this->iconExtension;

        if ($withAbsolutePath)
        {
            $iconName = $this->getIconAbsolutePath() . '/' . $iconName;
        }

        return $iconName;
    }

    public function toArray(): array
    {
        $manifestIcons = [];

        foreach ($this->sizes as $size)
        {
            $iconName = $this->generateIconName($size);
            $absoluteIconPath = $this->getIconAbsolutePath() . '/' . $iconName;
            $relativeIconPath = $this->getIconsPath($this->manifestPath) . '/' . $iconName;

            if (!file_exists($absoluteIconPath))
            {
                $this->iconFilesMissing = true;
                continue;
            }

            $manifestIcons[] = [
                "src" => $relativeIconPath,
                "type" => \mime_content_type($absoluteIconPath),
                "sizes" => $size,
            ];
        }

        return $manifestIcons;
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
    public function setIconBasePath(string $iconBasePath): static
    {
        $this->iconBasePath = $iconBasePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationAlias(): string
    {
        return $this->applicationAlias;
    }

    /**
     * @param string $applicationAlias
     */
    public function setApplicationAlias(string $applicationAlias): static
    {
        $this->applicationAlias = $applicationAlias;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIconFilesMissing(): bool
    {
        return $this->iconFilesMissing;
    }

    /**
     * @return string
     */
    public function getWebRootPath(): string
    {
        return $this->webRootPath;
    }

    /**
     * @param string $webRootPath
     */
    public function setWebRootPath(string $webRootPath): static
    {
        $this->webRootPath = $webRootPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getManifestPath(): string
    {
        return $this->manifestPath;
    }

    /**
     * Set the path from manifest to the web root (Example: '..'). Leave empty if manifest is placed in web root.
     *
     * @param string $manifestPath
     */
    public function setManifestPath(string $manifestPath): static
    {
        $this->manifestPath = $manifestPath;

        return $this;
    }

}