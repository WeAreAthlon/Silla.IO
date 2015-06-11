<?php

class Silla
{
    private static $version = '1.0.0';

    public static $environment;

    public static $packages = [];

    public static $request;

    public static $routes;

    public static $routesSource = [];

    public static function boot($environment = 'development') 
    {
        self::$environment = $environment;

        self::$packages['app'] = [
            'name'      => 'app',
            'extendLocation'  => Core\Config()->paths('root') . 'app',
            'location'  => Core\Config()->paths('root') . 'app',
            'url'       => ''
        ];

        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            exit("Sorry, Silla.IO framework will only run on PHP version 5.4.0 or greater!\n");
        }

        /**
         * @TODO check if we need this anymore
         */
        /*chdir(dirname(__DIR__));*/

        Core\Registry()->set('locale', Core\Config()->I18N['default']);
    }

    public static function definePackage($options)
    {
        if($options['name'] != 'app') {
            $options['extendLocation'] = 'packages/' . $options['name'];

            self::$packages[$options['name']] = $options;
        }
    }

    public static function getPackage($name = 'app')
    {
        return self::$packages[$name];
    }
}

