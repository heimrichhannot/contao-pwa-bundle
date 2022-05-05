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


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{



	/**
	 * Generates the configuration tree builder.
	 *
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder(PwaExtension::ALIAS);
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root(PwaExtension::ALIAS);
        }

		$rootNode->children()
			->arrayNode('vapid')
				->children()
					->scalarNode('subject')->defaultNull()->end()
					->scalarNode('publicKey')->defaultNull()->end()
					->scalarNode('privateKey')->defaultNull()->end()
				->end()
			->end()
            ->scalarNode('manifest_path')->defaultValue('/pwa')->end()
            ->scalarNode('configfile_path')->defaultValue('/pwa')->end()
            ->arrayNode('push')
                ->children()
                    ->scalarNode('automatic_padding')->defaultNull()->end()
                ->end()
            ->end()
		->end();

		return $treeBuilder;
	}
}