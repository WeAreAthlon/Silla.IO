<?php
/**
 * DataTable Helper.
 *
 * @package    Silla.IO
 * @subpackage CMS\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Helpers;

use Core;
use Core\Base;
use Core\Modules\DB;

/**
 * DataTables Server-side processing wrapper.
 */
class DataTables
{
    /**
     * Queries and formats the data from the database to be used via DataTables.
     *
     * @param Base\Model $model  Instance of BaseModel or its children.
     * @param array      $params Query params.
     *
     * @static
     *
     * @return DB\Query
     */
    public static function queryModel(Base\Model $model, array $params)
    {
        $params['pagination']['page'] = isset($params['pagination']['page']) ? (int)$params['pagination']['page'] : 0;

        if (isset($params['pagination']['limit'])) {
            $params['pagination']['limit'] = intval($params['pagination']['limit']);
        } else {
            /* Get first pagination limit from the configuration. */
            $pagination = Core\Helpers\YAML::get('pagination', 'cms');
            $params['pagination']['limit'] = intval(current($pagination['limits']));
        }

        $result = $model::find();
        $result = self::assignFilter($result, $params);
        $result = self::assignOrder($result, $params)
            ->page($params['pagination']['page'], $params['pagination']['limit']);

        return $result;
    }

    /**
     * Formats the query object from the DataTable query params.
     *
     * @param Base\Model $model  Instance of BaseModel or its children.
     * @param array      $fields Fields to process.
     * @param array      $params Params values to substitute.
     *
     * @uses   Core\Helpers\SQL
     * @static
     *
     * @return Core\Modules\DB\Query
     */
    public static function toQuery(Base\Model $model, array $fields, array $params)
    {
        $result = $model::find($fields);
        $result = self::assignFilter($result, $params);
        $result = self::assignOrder($result, $params);

        return $result;
    }

    /**
     * Assigns order of the result set.
     *
     * @param DB\Query $query  Current query object instance of Base\Model or its children.
     * @param array    $params Query params to format order.
     *
     * @access private
     * @static
     *
     * @return Core\Modules\DB\Query
     */
    private static function assignOrder(DB\Query $query, array $params)
    {
        if (isset($params['sorting']['field'], $params['sorting']['order'])
            && array_key_exists($params['sorting']['field'], $query->getObject()->fields())
            && in_array(strtolower($params['sorting']['order']), array('asc', 'desc'), true)) {
            return $query->order($params['sorting']['field'], $params['sorting']['order']);
        } else {
            $resource = $query->getObject();

            return $query->order($resource::primaryKeyField(), 'desc');
        }
    }

    /**
     * Assigns filtering of the results.
     *
     * @param DB\Query $query  Current query object instance of BaseModel or its children.
     * @param array    $params Query params to format filtering criteria.
     *
     * @access private
     * @static
     * @uses   Core\DB()
     * @uses   Core\DbCache()
     * @uses   Core\Helpers\SQL
     *
     * @return Core\Modules\DB\Query
     */
    private static function assignFilter(DB\Query $query, array $params)
    {
        if (isset($params['filtering']) && !empty($params['filtering']) && is_array($params['filtering'])) {
            $model_fields = $query->getObject()->getSchema();

            if ($query->getObject()->hasAndBelongsToMany) {
                $model_fields = array_merge($model_fields, $query->getObject()->hasAndBelongsToMany);
            }

            foreach ($params['filtering'] as $field => $value) {
                if ($value && isset($model_fields[$field])) {
                    if (is_array($value)) {
                        if (isset($value['start'], $value['end']) && !empty($value['start']) && !empty($value['end'])) {
                            if (in_array($model_fields[$field]['type'], array('date', 'datetime'), true)) {
                                $decorator = 'Core\Modules\DB\Decorators\Interfaces\TimezoneAwareness';
                                if (is_subclass_of($query->getObject(), $decorator)) {
                                    $value['start'] = Core\Helpers\DateTime::formatGmt(
                                        $value['start'] . date(' H:i:s'),
                                        'Y-m-d'
                                    );
                                    $value['end'] = Core\Helpers\DateTime::formatGmt(
                                        $value['end'] . date(' H:i:s'),
                                        'Y-m-d'
                                    );
                                }

                                $query = $query->where(
                                    "(DATE({$field}) BETWEEN " . Core\DB()->escapeString($value['start']) .
                                    ' AND ' .
                                    Core\DB()->escapeString($value['end']) . ')'
                                );
                            } else {
                                $query = $query->where(
                                    "{$field} BETWEEN " . Core\DB()->escapeString($value['start']) .
                                    ' AND ' .
                                    Core\DB()->escapeString($value['end'])
                                );
                            }
                        } else {
                            if (isset($query->getObject()->hasAndBelongsToMany[$field]) &&
                                $query->getObject()->hasAndBelongsToMany[$field]
                            ) {
                                $related = $query->getObject()->hasAndBelongsToMany[$field];
                                $obj = $query->getObject();
                                $primaryKey = $obj->primaryKeyField();
                                $prefix = Core\Config()->DB['tables_prefix'];

                                foreach ($value as $v) {
                                    $query = $query->join(
                                        "{$related['table']} as {$related['table']}{$v}",
                                        "{$prefix}{$obj::$tableName}.{$primaryKey} = " .
                                        "{$related['table']}{$v}.{$related['key']}" .
                                        ' AND ' .
                                        "{$related['table']}{$v}.{$related['relative_key']} = {$v}"
                                    );
                                }
                            }
                        }
                    } else {
                        if ($model_fields[$field]['type'] === 'string') {
                            $value_to_match = trim(Core\DB()->escapeString($value), "'");
                            $query = $query->where("{$field} LIKE \"%{$value_to_match}%\"");
                        } else {
                            $query = $query->where($field . ' = ' . Core\DB()->escapeString($value));
                        }
                    }
                }
            }
        }

        return $query;
    }
}
