<?php

namespace Nelmio\JsLoggerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NelmioJsLoggerExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $levels = array_map('strtolower', $config['allowed_levels']);
        $container->setParameter('nelmio_js_logger.allowed_levels', $levels);
        $container->setParameter('nelmio_js_logger.ignore_messages', $config['ignore_messages']);
        $container->setParameter('nelmio_js_logger.ignore_url_prefixes', $config['ignore_url_prefixes']);
        if ($config['use_stacktrace_js']['enabled']) {
            $container->setParameter('nelmio_js_logger.stacktrace_js_path', $config['use_stacktrace_js']['path']);
        } else {
            $container->setParameter('nelmio_js_logger.stacktrace_js_path', null);
        }

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');
    }
}
