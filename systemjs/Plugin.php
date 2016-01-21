<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Tool\Plugin\Systemjs;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Zicht\Tool\Plugin as BasePlugin;

class Plugin extends BasePlugin
{
    public function appendConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('systemjs')
                    ->children()
                        ->scalarNode('bundle_js')
                            ->defaultValue('/usr/local/bin/systemjs-bundle')
                            ->validate()
                                ->ifTrue(function($f) {
                                    return !is_file($f);
                                })
                                ->thenInvalid('File does not exist. Do you need to install zicht-systemjs-bundle?')
                            ->end()
                        ->end()
                        ->scalarNode('system_config')->defaultValue('system.conf.js')->end()
                        ->scalarNode('root')->defaultValue('htmldev')->end()
                        ->scalarNode('src')->defaultValue('src')->end()
                        ->scalarNode('target')->defaultValue('javascript')->end()
                        ->arrayNode('modules')->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}