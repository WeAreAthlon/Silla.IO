<?php
/**
 * Formatting Decorator.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Modules\DB\Interfaces;

/**
 * Class Formatting Decorator Implementation definition.
 */
abstract class Formatting implements Interfaces\Decorator
{
    /**
     * Decorator entry point.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decorate(Base\Model $resource)
    {
        $resource->on('afterCreate', array(__CLASS__, 'fetch'));

        $resource->on('beforeValidate', array(__CLASS__, 'format'));
        $resource->on('afterValidate', array(__CLASS__, 'fetch'));

        $resource->on('beforeSave', array(__CLASS__, 'format'));
        $resource->on('afterSave', array(__CLASS__, 'fetch'));
    }

    /**
     * Retrieve both formats of the content fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function fetch(Base\Model $resource)
    {
        foreach ($resource::formattingFields() as $field => $formatter) {
            if ($resource->{$field}) {
                $resource->{$field} = json_decode($resource->{$field}, true);
            } else {
                $resource->{$field} = array('formatted' => '', 'raw' => '');
            }
        }
    }

    /**
     * Formats the content fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @throws \RuntimeException Missing method parse for the specified parser.
     * @static
     * @access public
     *
     * @return void
     */
    public static function format(Base\Model $resource)
    {
        foreach ($resource::formattingFields() as $field => $formatter) {
            $parser = new $formatter;

            if (method_exists($parser, 'parse')) {
                if (is_array($resource->{$field})) {
                    $resource->{$field} = json_encode(array(
                        'formatted' => $parser->parse($resource->{$field}['raw']),
                        'raw' => $resource->{$field}['raw'],
                    ));
                } else {
                    $resource->{$field} = json_encode(array(
                        'formatted' => $parser->parse($resource->{$field}),
                        'raw' => $resource->{$field},
                    ));
                }
            } else {
                throw new \RuntimeException('Missing method parse for the specified parser: ' . get_class($parser));
            }
        }
    }
}
