<?php
/**
 * Assets Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\CoffeeScriptFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\JSMinFilter;
use Assetic\AssetWriter;

/**
 * Include and manage Assets into templates.
 *
 * Supported styles:
 *  - CSS
 *  - LESS via /vendor/leafo/LessphpFilter
 *  - SCSS via /vendor/leafo/ScssphpFilter
 *
 * Supported scripts:
 *  - JavaScript
 *  - Coffee Script via Assetic\Filter\CoffeeScriptFilter
 *
 * @param array $options Assets source options.
 * @param Smarty_Internal_Template $template Smarty Template object.
 *
 * @uses   Core\Config
 * @uses   Core\Utils
 * @see    Assetic
 *
 * @return string
 */
function smarty_function_assets(array $options, Smarty_Internal_Template $template)
{
    $result = array();

    if (isset($options['source'])) {
        $assetsPath           = Core\Config()->paths('assets');
        $optimization_enabled = Core\Config()->ASSETS['optimize'];
        $combination_enabled  = Core\Config()->ASSETS['combine'];
        $caching_enabled      = Core\Config()->ASSETS['cache'];
        $dist_path            = $assetsPath['distribution'];
        $source_path          = $assetsPath['source'];
        $dist_url             = Core\Config()->urls('assets');

        $media    = isset($options['media']) ? $options['media'] : 'all';
        $rel      = isset($options['rel'])   ? $options['rel']   : 'stylesheet';
        $mimetype = isset($options['type'])  ? $options['type']  : 'text/css';

        $assets    = is_array($options['source']) ? $options['source'] : array($options['source']);
        $assets_id = md5(implode(Core\Utils::arrayFlatten($assets)));
        $assets_to_process = array();

        /* Format assets if needed */
        if (!Core\Utils::arrayIsAssoc($options['source'])) {
            $formatted_assets = array();
            foreach ($options['source'] as $file) {
                $file_extension = pathinfo($file, PATHINFO_EXTENSION);

                $formatted_assets[$file_extension][] = $file;
                $formatted_assets[$file_extension] = array_unique($formatted_assets[$file_extension]);
            }

            $assets = $formatted_assets;
        }

        if ($caching_enabled) {
            if ($combination_enabled) {
                if (array_intersect(array('css', 'less', 'scass'), array_keys($assets))) {
                    $cached_asset = 'css' . DIRECTORY_SEPARATOR . $assets_id . '.css';

                    if (file_exists($dist_path . $cached_asset)) {
                        $result[] = sprintf('<link href="%s" rel="%s" type="%s" media="%s" />', $dist_url . $cached_asset, $rel, $mimetype, $media);
                    } else {
                        $assets_to_process = $assets;
                    }
                } elseif (array_intersect(array('js'), array_keys($assets))) {
                    $cached_asset = 'js' . DIRECTORY_SEPARATOR . $assets_id . '.js';

                    if (file_exists($dist_path . $cached_asset)) {
                        $result[] = sprintf('<script src="%s"></script>', $dist_url . $cached_asset);
                    } else {
                        $assets_to_process = $assets;
                    }
                }
            } else {
                foreach ($assets as $type => $files) {
                    switch ($type) {
                        case 'css':
                        case 'less':
                        case 'scass':
                            foreach ($files as $file) {
                                $filename = basename($file, '.css');
                                $filename = basename($filename, '.less');
                                $filename = basename($filename, '.scss');

                                $cached_asset = 'css' . DIRECTORY_SEPARATOR . $filename . '.css';

                                if (file_exists($dist_path . $cached_asset)) {
                                    $result[] = sprintf('<link href="%s" rel="%s" type="%s" media="%s" />', $dist_url . $cached_asset, $rel, $mimetype, $media);
                                } else {
                                    $assets_to_process[$type][] = $file;
                                }
                            }
                            break;
                        case 'js':
                        case 'coffee':
                            foreach ($files as $file) {
                                $filename = basename($file, '.js');
                                $filename = basename($filename, '.coffee');

                                $cached_asset = 'js' . DIRECTORY_SEPARATOR . $filename . '.js';

                                if (file_exists($dist_path . $cached_asset)) {
                                    $result[] = sprintf('<script src="%s"></script>', $dist_url . $cached_asset);
                                } else {
                                    $assets_to_process[$type][] = $file;
                                }
                            }
                            break;
                    }
                }
            }
        }

        if (!$caching_enabled || $assets_to_process) {
            $assets = $assets_to_process ? $assets_to_process : $assets;

            $writer = new AssetWriter($dist_path);
            $styles = new AssetCollection(array(), $optimization_enabled ? array(new CssMinFilter()) : array());
            $scripts = new AssetCollection(array(), $optimization_enabled ? array(new JsMinFilter()) : array());

            foreach ($assets as $type => $files) {
                switch ($type) {
                    case 'js':
                        foreach ($files as $file) {
                            $scripts->add(new FileAsset($source_path . $file));
                        }
                        break;
                    case 'css':
                        foreach ($files as $file) {
                            $styles->add(new FileAsset($source_path . $file));
                        }
                        break;
                    case 'less':
                        foreach ($files as $file) {
                            $styles->add(new FileAsset($source_path . $file, array(new LessphpFilter())));
                        }
                        break;
                    case 'scss':
                        foreach ($files as $file) {
                            $styles->add(new FileAsset($source_path . $file, array(new ScssphpFilter())));
                        }
                        break;
                    case 'coffee':
                        foreach ($files as $file) {
                            $scripts->add(new FileAsset($source_path . $file), array(new CoffeeScriptFilter()));
                        }
                        break;
                }
            }

            if ($combination_enabled) {
                if ($styles->all()) {
                    $am = new AssetManager($dist_path);

                    $styles->setTargetPath('css' . DIRECTORY_SEPARATOR . $assets_id . '.css');
                    $am->set('styles', $styles);

                    $writer->writeManagerAssets($am);

                    $result[] = sprintf('<link href="%s" rel="%s" type="%s" media="%s" />', $dist_url . $styles->getTargetPath(), $rel, $mimetype, $media);
                }

                if ($scripts->all()) {
                    $am = new AssetManager($dist_path);

                    $scripts->setTargetPath('js' . DIRECTORY_SEPARATOR . $assets_id . '.js');
                    $am->set('scripts', $scripts);

                    $writer->writeManagerAssets($am);
                    $result[] = sprintf('<script src="%s"></script>', $dist_url . $scripts->getTargetPath());
                }
            } else {
                foreach ($styles->all() as $style) {
                    $filename = basename($style->getSourcePath(), '.css');
                    $filename = basename($filename, '.less');
                    $filename = basename($filename, '.scss');

                    $style->setTargetPath('css' . DIRECTORY_SEPARATOR . $filename . '.css');

                    $writer->writeAsset($style);

                    $result[] = sprintf('<link href="%s" rel="%s" type="%s" media="%s" />', $dist_url . $style->getTargetPath(), $rel, $mimetype, $media);
                }

                foreach ($scripts->all() as $script) {
                    $filename = basename($script->getSourcePath(), '.js');
                    $filename = basename($filename, '.coffee');

                    $script->setTargetPath('js' . DIRECTORY_SEPARATOR . $filename . '.js');

                    $writer->writeAsset($script);

                    $result[] = sprintf('<script src="%s"></script>', $dist_url . $script->getTargetPath());
                }
            }
        }
    }

    return $result ? implode("\n\t", $result) : '';
}
