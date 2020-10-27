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
                        ->scalarNode('bundle_js')->end()
                        ->scalarNode('system_config')->defaultValue('system.conf.js')->end()
                        ->scalarNode('root')->defaultValue('htmldev')->end()
                        ->scalarNode('src')->defaultValue('src')->end()
                        ->scalarNode('target')->defaultValue('javascript')->end()
                        ->arrayNode('modules')->performNoDeepMerging()->prototype('scalar')->end()
                    ->end()
                ->end()
                ->validate()
                    ->always(function($v) {
                        $candidates = [
                            // is it... manually specified
                            isset($v['bundle_js']) ? $v['bundle_js'] : null,

                            // is it... installed in the project (should be the case most of the times)
                            'node_modules/.bin/systemjs-bundle',

                            // is it... installed globally (should be for very old projects)
                            '/usr/local/bin/systemjs-bundle',

                            // is it... installed somewhere else entirely
                            $this->getBundleJsFromPath(),
                        ];

                        $bundleJs = null;
                        foreach ($candidates as $candidate) {
                            if (!empty($candidate) && is_file($candidate)) {
                                $bundleJs = $candidate;
                                break;
                            }
                        }

                        if ($bundleJs) {
                            $v['bundle_js'] = $bundleJs;
                        } else {
                            throw new \Exception(sprintf('Unable to locate systemjs-bundle.  Looked in %s', implode(', ', array_filter($candidates))));
                        }

                        return $v;
                    })
                ->end()
            ->end()
        ;
    }

    private function getBundleJsFromPath()
    {
        $paths = getenv('PATH');
        if (false !== $paths) {
            foreach (explode(PATH_SEPARATOR, $paths) as $path) {
                if (is_file($path . DIRECTORY_SEPARATOR . 'systemjs-bundle')) {
                    return realpath($path . DIRECTORY_SEPARATOR . 'systemjs-bundle');
                }
            }
        }
        return null;
    }
}
