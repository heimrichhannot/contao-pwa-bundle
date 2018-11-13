<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Notification;


use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;

abstract class AbstractNotification implements \JsonSerializable
{

	abstract function getSource(): ?PwaPushNotificationsModel;

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

		$classMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		$properties = [];
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
				$properties[lcfirst(substr($method->getName(),3))] = $value;
			}
		}

		return json_encode($properties);
	}
}