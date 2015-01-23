<?php
/**
 * Generator functionality.
 *
 * @package    Silla.IO
 * @subpackage Core\CLI
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\CLI;

use Core;
use Core\Helpers;

/**
 * Class Generate definition.
 */
final class Generate
{
    /**
     * Generic call method.
     *
     * @param string $name      Method name.
     * @param array  $arguments Method parameters.
     *
     * @return void
     */
    public static function __callStatic($name, array $arguments)
    {
        list($mode, $command) = explode(':', $name);
        array_unshift($arguments, $mode);
        call_user_func_array(array('self', $command), $arguments);
    }

    /**
     * Controller generator.
     *
     * @param string $mode Framework mode name.
     * @param string $name Controller name.
     *
     * @return void
     */
    public static function controller($mode, $name)
    {
        $action_names = array_slice(func_get_args(), 2);
        $result = self::parseTemplate('controller', array(
            'mode' => $mode,
            'controller' => $name,
            'actions' => $action_names
        ));

        Helpers\File::putContents(
            $mode . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . strtolower($name) . '.php',
            $result
        );

        /* Generate views */
        try {
            Helpers\Directory::create($mode . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . strtolower($name));
        } catch (\Exception $e) {
        }

        foreach ($action_names as $action) {
            $path = $mode . DIRECTORY_SEPARATOR . 'views'
                . DIRECTORY_SEPARATOR . strtolower($name) . DIRECTORY_SEPARATOR . $action . '.html';
            $view = self::parseTemplate('view', array(
                'controller' => $name,
                'action' => $action,
                'path' => $path
            ));

            Helpers\File::putContents($path, $view);
        }
    }

    /**
     * Model generator.
     *
     * @param string $mode Framework mode name.
     * @param string $name Model name.
     *
     * @return void
     */
    public static function model($mode, $name)
    {
        $result = self::parseTemplate('model', array(
            'mode' => $mode,
            'model' => $name
        ));
        $path = $mode . DIRECTORY_SEPARATOR . 'models'
            . DIRECTORY_SEPARATOR . strtolower($name) . '.php';
        Helpers\File::putContents($path, $result);

        /* Create migration */
        self::migration('create_table_' . $name, array_slice(func_get_args(), 2));
    }

    /**
     * Migration generator.
     *
     * @param string $name   Name string.
     * @param mixed  $params Migration parameters.
     *
     * @return void
     */
    public static function migration($name, $params)
    {
        if (is_array($params)) {
            $params = array_merge($params, array_slice(func_get_args(), 2));
        } else {
            $params = array_slice(func_get_args(), 1);
        }

        $migration_name = strtolower($name) . '_' . time();
        $tpl_vars = array(
            'migration_name' => self::getMigrationName($migration_name),
            'name' => explode('_', $name),
            'fields' => array_map(
                function ($item) {
                    return explode(':', $item);
                },
                $params
            )
        );

            $path = Core\Config()->paths('root') . 'db' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
            $result = self::parseTemplate('migration', $tpl_vars);
            Helpers\File::putContents($path . $migration_name . '.php', $result);
    }

    /**
     * Parses a template.
     *
     * @param string $template Path to template.
     * @param array  $params   Template parameters.
     *
     * @return string
     *
     * @throws \Exception General Exception.
     * @throws \SmartyException Smarty Exception.
     */
    private static function parseTemplate($template, array $params)
    {
        $tpl = new \Smarty();
        $tpl->addPluginsDir(Core\Config()->paths('vendor') . 'athlon' . DIRECTORY_SEPARATOR . 'smarty_plugins');

        foreach ($params as $key => $value) {
            $tpl->assign($key, $value);
        }

        $path = Core\Config()->paths('root') . 'core' . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR;

        return $tpl->fetch($path . '_templates' . DIRECTORY_SEPARATOR . $template . '.php.tpl');
    }

    /**
     * Retrieves migration name.
     *
     * @param string $migration Migration name.
     *
     * @return string
     */
    private static function getMigrationName($migration)
    {
        return preg_replace('/_[0-9]{10}$/', '', $migration);
    }
}
