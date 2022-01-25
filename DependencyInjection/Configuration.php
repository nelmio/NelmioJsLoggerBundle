<?php

namespace Nelmio\JsLoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @final
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('nelmio_js_logger');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('nelmio_js_logger');
        }

        $levels = array('DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY');
        $levelsCI = array_merge($levels, array_map('strtolower', $levels));

        $rootNode
            ->fixXmlConfig('allowed_level')
            ->fixXmlConfig('ignore_message')
            ->fixXmlConfig('ignore_url_prefix', 'ignore_url_prefixes')
            ->children()
                ->arrayNode('allowed_levels')
                    ->defaultValue($levels)
                    ->prototype('scalar')
                        ->validate()
                            ->ifNotInArray($levelsCI)
                            ->thenInvalid('The level %s is not supported. Please choose one of '.json_encode($levels))
                        ->end()
                    ->end()
                    ->requiresAtLeastOneElement()
                ->end()
                ->arrayNode('ignore_messages')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('ignore_url_prefixes')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('use_stacktrace_js')
                    ->info('add StackTrace.js as logging provider')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('path')
                            ->defaultValue("https://cdnjs.cloudflare.com/ajax/libs/stacktrace.js/1.3.1/stacktrace.min.js")
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
