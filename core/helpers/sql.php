<?php
/**
 * SQL Helper.
 *
 * @package    Silla
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Helpers;

use Core;

/**
 * SQL Class Helper definition.
 */
class SQL
{
    /**
     * Custom query wrapper.
     *
     * @param string  $table      Table name.
     * @param mixed   $attributes Column names to query.
     * @param string  $where      Condition.
     * @param string  $order      Order clause.
     * @param integer $offset     Result set offset.
     * @param integer $limit      Result set limit.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function customQuery(
        $table,
        $attributes = '*',
        $where = '1',
        $order = null,
        $offset = null,
        $limit = null
    ) {
        if (is_array($attributes)) {
            $attributes = implode(',', $attributes);
        }

        $where = $where ? $where : 1;

        $sql = "SELECT {$attributes} FROM {$table} WHERE {$where}";
        $sql .= $order ? ' ORDER BY ' . $order : '';
        $sql .= $limit ? ' LIMIT ' . (int)$limit : '';
        $sql .= $offset ? ' OFFSET ' . (int)$offset : '';

        return $sql;
    }

    /**
     * Builds a UNION SQL query with shared ORDER BY and LIMIT.
     *
     * @param array   $queries Array of queries to union.
     * @param string  $order   Order clouse.
     * @param integer $offset  Offset count.
     * @param integer $limit   Limit count.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function union(array $queries, $order = null, $offset = null, $limit = null)
    {
        $sql = '(' . implode(') UNION (', $queries) . ')';
        $sql .= $order ? ' ORDER BY ' . $order : '';
        $sql .= $limit ? ' LIMIT ' . (int)$limit : '';
        $sql .= $offset ? ' OFFSET ' . (int)$offset : '';

        return $sql;
    }

    /**
     * Builds a WHERE part of the query suitable for filters.
     *
     * @param array   $attributes              Filtering attributes.
     * @param boolean $use_prepared_statements Whether to use prepared statements or not.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function filter(array $attributes, $use_prepared_statements = true)
    {
        $sql = array();

        $attributes = array_filter($attributes, array(__CLASS__, 'removeFilterAttributes'));

        if ($use_prepared_statements) {
            foreach ($attributes as $key => $value) {
                $sql[] = "{$key} = ?";
            }

            return array(implode(' AND ', $sql), array_values($attributes));
        } else {
            foreach ($attributes as $key => $value) {
                $sql[] = "{$key} = " . DB()->escapeString($value);
            }

            return implode(' AND ', $sql);
        }
    }


    /**
     * Builds a WHERE part of the query suitable for filters with defined data types of the attribute.
     *
     * @param array $attributes Filtering attributes.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function filterByType(array $attributes)
    {
        $sql = array();

        foreach ($attributes as $key => $attribute) {
            if ($attribute['value']) {
                if (is_array($attribute['value'])) {
                    if (
                        isset($attribute['value']['start'], $attribute['value']['end'])
                        && !empty($attribute['value']['start'])
                        && !empty($attribute['value']['end'])
                    ) {
                        if ('datetime' === $attribute['type']) {
                            $sql[] = "({$key} BETWEEN "
                                . DB()->escapeString($attribute['value']['start'] . ' 00:00:00')
                                . " AND "
                                . DB()->escapeString($attribute['value']['end'] . ' 23:59:59')
                                . ')';
                        } else {
                            $sql[] = "({$key} BETWEEN "
                                . DB()->escapeString($attribute['value']['start'])
                                . " AND "
                                . DB()->escapeString($attribute['value']['end'])
                                . ')';
                        }
                    }
                } else {
                    if ($attribute['type'] === 'string') {
                        $value_to_match = trim(DB()->escapeString($attribute['value']), "'");
                        $sql[] = "({$key} LIKE \"%{$value_to_match}%\")";
                    } else {
                        $sql[] = "({$key} = " . DB()->escapeString($attribute['value']) . ')';
                    }
                }
            }
        }

        return implode(' AND ', $sql);
    }

    /**
     * Builds a set - offset part of a query.
     *
     * @param integer $offset Offset count.
     * @param integer $limit  Limit count.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function setOffsetLimit($offset, $limit)
    {
        $offset = intval($offset);
        $limit = intval($limit);

        return "{$offset}, {$limit}";
    }

    /**
     * Removes filtered attributes.
     *
     * @param string $attr Attribute value.
     *
     * @access private
     * @static
     *
     * @return boolean
     */
    private static function removeFilterAttributes($attr)
    {
        return $attr != '';
    }
}
