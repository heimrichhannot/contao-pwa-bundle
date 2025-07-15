<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('huh_pwa');

        $treeBuilder->getRootNode()
            ->children()
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