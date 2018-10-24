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
		['width' => 192, "height" => 192],
		['width' => 512, "height" => 512],
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

	protected $defaultIconPath = 'assets/heimrichhannotcontaopwa';
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
		$extension = pathinfo($sourceIconPath, PATHINFO_EXTENSION);
		$this->iconBaseName = basename($sourceIconPath, '.'.$extension);
		$this->iconExtension = $extension;
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
	 */
	public function setSizes(array $sizes): void
	{
		$this->sizes = $sizes;
	}

	/**
	 * @param int $width Icon width in pixel
	 * @param int $height Icon height in pixel
	 */
	public function addSize(int $width, int $height)
	{
		$this->sizes[] = ['width' => $width, "height" => $height];
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

	public function getIconsPath(): string
	{
		return $this->defaultIconPath.'/'.$this->applicationAlias.'/icons/';
	}

	public function toArray()
	{
		$manifestIcons = [];

		foreach ($this->sizes as $size)
		{
			$icon_path = $this->getIconsPath().$this->iconBaseName.'-'.$size['width'].$this->iconExtension;

			if (!file_exists($icon_path))
			{
				$this->iconFilesMissing = true;
				continue;
			}

			$manifestIcons[] = [
				"src" => $icon_path,
				"type" => mime_content_type($icon_path),
				"sizes" => $size["width"].'x'.$size['height']
			];
		}

		return $manifestIcons;
	}

	/**
	 * @return string
	 */
	public function getDefaultIconPath(): string
	{
		return $this->defaultIconPath;
	}

	/**
	 * @param string $defaultIconPath
	 */
	public function setDefaultIconPath(string $defaultIconPath): void
	{
		$this->defaultIconPath = $defaultIconPath;
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


}