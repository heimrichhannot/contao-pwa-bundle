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

class Manifest implements \JsonSerializable
{

	const DISPLAY_VALUES = [
		'standalone',
		'fullscreen',
		'minimal-ui',
		'browser'
	];

	const ICONS_VALUE = [
		"src" => "",
		"type" => "",
		"sizes" => ""
	];

	const DIR_VALUES = ["ltr","rtl","auto"];

	const ORIENTATION_VALUES = [
		'any',
		'natural',
		'landscape',
		'landscape-primary',
		'landscape-secondary',
		'portrait',
		'portrait-primary',
		'portrait-secondary'
	];



	const RELATED_APPLICATIONS_VALUES = [
		'platform',
		'url',
		'id',
		'min_version',
		'sequence',
	];

	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $short_name;
	/**
	 * @var string
	 */
	public $description;
	/**
	 * @var string
	 */
	public $dir;
	/**
	 * @var string
	 */
	public $lang;
	/**
	 * @var string
	 */
	public $orientation;
	/**
	 * @var bool
	 */
	public $prefer_related_applications;
	/**
	 * @var array
	 */
	private $related_applications;
	/**
	 * @var string
	 */
	public $start_url;
	/**
	 * @var string
	 */
	public $scope;
	/**
	 * @var ManifestIcon
	 */
	public $icons;
	/**
	 * @var string Valid CSS color. RGB-Colors with # at the beginning
	 */
	public $background_color;
	/**
	 * @var string Valid CSS color. RGB-Colors with # at the beginning
	 */
	public $theme_color;
	/**
	 * @var string
	 */
	public $display;

	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 * @throws \ReflectionException
	 */
	public function jsonSerialize()
	{

		$reflectionClass = new \ReflectionClass($this);
		$classProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

		$manifestProperties = [];
		foreach ($classProperties as $property)
		{
			if ($this->{$property->getName()}) {
				$manifestProperties[$property->getName()] = $this->{$property->getName()};
			}
		}
		$classMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($classMethods as $method)
		{
			if (substr($method->getName(),0,3) !== 'get')
			{
				continue;
			}
			if ($method->getNumberOfParameters() > 0)
			{
				continue;
			}
			if (null !== ($value = $this->{$method->getName()}()))
			{
				$manifestProperties[lcfirst(substr($method->getName(),3))] = $value;
			}
		}

		if ($this->icons)
		{
			$manifestProperties['icons'] = $this->icons->toArray();
		}



		return json_encode($manifestProperties);
	}

	/**
	 * @return array|null
	 */
	public function getRelatedApplications()
	{
		return $this->related_applications;
	}

	/**
	 * @param string $plattform
	 * @param string|null $url
	 * @param null|string $id
	 * @param null|string $min_version
	 * @param array|null $fingerprints
	 * @return bool
	 */
	public function addRelatedApplication(string $plattform, string $url = null, ?string $id = null, ?string $min_version = null, ?array $fingerprints = null)
	{
		if (empty($plattform))
		{
			return false;
		}
		if (empty($url) && empty($id))
		{
			return false;
		}
		$application = [];
		$application['plattform'] = $plattform;
		if ($url) $application['url'] = $url;
		if ($id) $application['id'] = $id;
		if ($min_version) $application['min_version'] = $min_version;
		if ($fingerprints) $application['fingerprints'] = $fingerprints;
		$this->related_applications[] = $application;
		return true;
	}
}