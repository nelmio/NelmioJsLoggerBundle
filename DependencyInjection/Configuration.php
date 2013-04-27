<?php

namespace Nelmio\JsLoggerBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nelmio_js_logger');

        $levels = array('DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY');
        $levelsCI = array_merge($levels, array_map('strtolower', $levels));

        $rootNode
            ->children()
                ->arrayNode('allowed_levels')
                    ->defaultValue($levels)
                    ->prototype('scalar')
                        ->validate()
                            ->ifNotInArray($levelsCI)
                            ->thenInvalid('The level %s is not supported. Please choose one of '.json_encode($levels))
                        ->end()
                    ->end()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('errors_to_ignore')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('scripts_to_ignore')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
