<?php

namespace Core\Cli;

class Cli
{
    public static function run(array $args)
    {
        $options = self::parseArguments($args);

        call_user_func_array(array('\\Core\\Cli\\' . $options['class'], $options['method']), [$options['arguments'], $options['options']]);
    }

    public static function parseArguments(array $args)
    {
        $options = [];

        foreach($args as $key=>$arg) {
            if(preg_match('/(-[a-zA-Z]|--[a-zA-Z]+)/', $arg)) {
                $options = array_slice($args, $key);
                array_splice($args, $key);
                break;
            }
        }

        $parts = explode(':', $args[0]);

        $method = array_pop($parts);
        $class = implode('\\', $parts);

        $methodArguments = array_slice($args, 1);

        preg_match_all('/-([a-zA-Z])(?:$|\s([^\s-]*)?)/', implode(' ', $options), $shortOptions);

        $options = [];
        foreach($shortOptions[1] as $index=>$match) {
            $options[$match] = $shortOptions[2][$index];

            if(!$options[$match]) {
                $options[$match] = true;
            }
        }

        return [
            'class'     => $class,
            'method'    => $method,
            'arguments' => $methodArguments,
            'options'   => $options
        ];
    }
}

