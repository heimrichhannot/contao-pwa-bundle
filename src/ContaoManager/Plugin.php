<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\ContaoManager;


use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use HeimrichHannot\ContaoPwaBundle\HeimrichHannotContaoPwaBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, ConfigPluginInterface, RoutingPluginInterface
{

	/**
	 * Gets a list of autoload configurations for this bundle.
	 *
	 * @return ConfigInterface[]
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(HeimrichHannotContaoPwaBundle::class)
			->setLoadAfter([
				ContaoCoreBundle::class,
			])
		];
	}

	/**
	 * Allows a plugin to load container configuration.
	 */
	public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
	{
		$loader->load('@HeimrichHannotContaoPwaBundle/Resources/config/services.yml');
	}

	/**
	 * Returns a collection of routes for this bundle.
	 *
	 * @param LoaderResolverInterface $resolver
	 * @param KernelInterface $kernel
	 * @throws \Exception
	 */
	public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
	{
		$file = "@HeimrichHannotContaoPwaBundle/Resources/config/routing.yml";
		return $resolver->resolve($file)->load($file);
	}
}