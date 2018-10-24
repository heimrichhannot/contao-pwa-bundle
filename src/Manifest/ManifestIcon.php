<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Manifest;


class ManifestIcon
{

	const SIZES_DEFAULT = [
		'192x192',
		'512x512'
	];

	protected $sizes = [];
	/**
	 * @var string
	 */
	protected $sourceIconPath;

	/**
	 * @var string Icon name without file extension
	 */
	protected $iconBaseName;

	/**
	 * @var string Icon file extension
	 */
	protected $iconExtension;

	/**
	 * @var string Relative path from web root to the folder where icons should be stored
	 */
	protected $iconBasePath = 'assets/heimrichhannotcontaopwa';

	/**
	 * @var string Absolute path to the web root
	 */
	protected $webRootPath = __DIR__;

	/**
	 * @var string
	 */
	private $applicationAlias;

	protected $iconFilesMissing = false;

	/**
	 * ManifestIcon constructor.
	 */
	public function __construct(string $sourceIconPath, string $applicationAlias, bool $addDefaultSizes = false)
	{
		$this->setSourceIconPath($sourceIconPath);
		if ($addDefaultSizes)
		{
			$this->sizes = static::SIZES_DEFAULT;
		}
		$this->applicationAlias = $applicationAlias;
	}

	/**
	 * @return string
	 */
	public function getSourceIconPath(): string
	{
		return $this->sourceIconPath;
	}

	/**
	 * @param string $sourceIconPath
	 */
	public function setSourceIconPath(string $sourceIconPath): void
	{
		$this->sourceIconPath = $sourceIconPath;
		$extension            = pathinfo($sourceIconPath, PATHINFO_EXTENSION);
		$this->iconBaseName   = basename($sourceIconPath, '.' . $extension);
		$this->iconExtension  = $extension;
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
		if (true === $this->validateSize($size))
		{
			$this->sizes[] = $size;
			return true;
		}
		return false;
	}

	/**
	 * Validate if an given string is an valid icon size
	 *
	 * @param string $size
	 * @return bool
	 */
	public function validateSize(string $size)
	{
		if ('any' === $size)
		{
			return true;
		}
		$matches = [];
		if (1 === preg_match('/(\d+)x(\d+)/', $size, $matches))
		{
			if ($matches[0] === $size)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @param int $width Icon width in pixel
	 * @param int $height Icon height in pixel
	 */
	public function addSizePixel(int $width, int $height)
	{
		$this->addSize($width . 'x' . $height);
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
	 *
	 * @return string
	 */
	public function getIconsPath(): string
	{
		return $this->iconBasePath . '/' . $this->applicationAlias . '/icons/';
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

	/**
	 * @param string $size
	 * @param bool $withAbsolutePath
	 * @return string
	 */
	public function generateIconName(string $size, bool $withAbsolutePath = false)
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

	public function toArray()
	{
		$manifestIcons = [];

		foreach ($this->sizes as $size)
		{
			$iconName         = $this->generateIconName($size);
			$absoluteIconPath = $this->getIconAbsolutePath() . '/' . $iconName;
			$relativeIconPath = $this->getIconsPath() . '/' . $iconName;

			if (!file_exists($absoluteIconPath))
			{
				$this->iconFilesMissing = true;
				continue;
			}

			$manifestIcons[] = [
				"src"   => $relativeIconPath,
				"type"  => mime_content_type($absoluteIconPath),
				"sizes" => $size
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
	public function setIconBasePath(string $iconBasePath): void
	{
		$this->iconBasePath = $iconBasePath;
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
	public function setApplicationAlias(string $applicationAlias): void
	{
		$this->applicationAlias = $applicationAlias;
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
	public function setWebRootPath(string $webRootPath): void
	{
		$this->webRootPath = $webRootPath;
	}


}