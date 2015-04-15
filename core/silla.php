<?php

namespace Core;

class Silla
{
    private static $version = '1.0.0';

    public static $environment;

    public static function boot($environment = 'development') 
    {
        self::$environment = $environment;

        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            exit("Sorry, Silla.IO framework will only run on PHP version 5.3.7 or greater!\n");
        }

        chdir(dirname(__DIR__));

        require implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'configurations', $environment, 'environment.php']);
        require __DIR__ . DIRECTORY_SEPARATOR . 'core.php';

        Registry()->set('locale', Config()->I18N['default']);
    }
}
