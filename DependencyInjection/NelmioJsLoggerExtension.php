<?php

namespace Nelmio\JsLoggerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NelmioJsLoggerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $levels = array_map(function ($level) {
            return strtolower($level);
        }, $config['allowed_levels']);
        $container->setParameter('nelmio_js_logger.allowed_levels', $levels);
        $container->setParameter('nelmio_js_logger.errors_to_ignore', $config['errors_to_ignore']);
        $container->setParameter('nelmio_js_logger.scripts_to_ignore', $config['scripts_to_ignore']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
