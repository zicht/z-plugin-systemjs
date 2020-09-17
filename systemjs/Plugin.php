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
                        ->scalarNode('bundle_js')->defaultValue('/usr/local/bin/systemjs-bundle')->end()
                        ->scalarNode('system_config')->defaultValue('system.conf.js')->end()
                        ->scalarNode('root')->defaultValue('htmldev')->end()
                        ->scalarNode('src')->defaultValue('src')->end()
                        ->scalarNode('target')->defaultValue('javascript')->end()
                        ->arrayNode('modules')->performNoDeepMerging()->prototype('scalar')->end()
                    ->end()
                ->end()
                ->validate()
                    ->always(function($v) {
                        $bin = 'systemjs-bundle';
                        if (empty($v['bundle_js']) || !is_file($v['bundle_js'])) {
                            if (is_file('node_modules/.bin/' . $bin)) {
                                $v['bundle_js'] = 'node_modules/.bin/' . $bin;
                            } elseif (false !== $paths = getenv('PATH')) {
                                foreach (explode(PATH_SEPARATOR, $paths) as $path) {
                                    if (is_file($path . DIRECTORY_SEPARATOR . $bin)) {
                                        $v['bundle_js'] = realpath($path . DIRECTORY_SEPARATOR . $bin);
                                        break;
                                    }
                                }
                            }
                        }

                        return $v;
                    })
                ->end()
            ->end()
        ;
    }
}
