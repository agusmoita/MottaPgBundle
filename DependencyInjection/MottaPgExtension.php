<?php

namespace MottaPgBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MottaPgExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $paginadorServiceDefinition = $container->getDefinition('motta.pg');
        $paginadorServiceDefinition->addMethodCall('setConfig', array(
            $config['paginator']['filters_theme'],
            $config['paginator']['base_layout'],
            $config['paginator']['panel_state'],
            $config['paginator']['show_order'],
            $config['paginator']['show_rows_per_page'],
            $config['paginator']['show_rows_at_first'],
            $config['paginator']['always_show_page_nav']
        ));
    }
}
