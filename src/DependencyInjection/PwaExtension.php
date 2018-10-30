<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PwaExtension extends Extension
{
	const ALIAS = 'huh_pwa';

	/**
	 * Loads a specific configuration.
	 *
	 * @throws \InvalidArgumentException When provided tag is not defined in this extension
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		$container->setParameter('huh.pwa', $config);
	}

	public function getAlias()
	{
		return static::ALIAS;
	}
}