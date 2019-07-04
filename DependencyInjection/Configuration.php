<?php

namespace MottaPgBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('motta_pg');
        //$rootNode = $treeBuilder->root('motta_pg');
        $rootNode = $treeBuilder->getRootNode();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('paginator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('filters_theme')
                            ->defaultValue('panel')
                        ->end()
                        ->scalarNode('base_layout')
                            ->defaultValue('base')
                        ->end()
                        ->enumNode('panel_state')
                            ->values(array('open', 'closed'))
                            ->defaultValue('open')
                        ->end()
                        ->booleanNode('show_order')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('show_rows_per_page')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('show_rows_at_first')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('always_show_page_nav')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
