<?php
/**
 * Labels Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;

/**
 * Labels Helper Class definition.
 */
class YAML
{
    /**
     * Gets a defined label.
     *
     * @param string $key    Key of the array.
     * @param string $type   Name of array group.
     * @param string $locale Locale code.
     *
     * @access public
     * @static
     * @uses   Core\Config()
     * @uses   \Spyc
     *
     * @return array|null
     */
    public static function get($key, $type = 'globals', $locale = '')
    {
        if (!$locale) {
            $locale = Core\Registry()->get('locale');
        }

        $labels = \Spyc::YAMLLoad(Core\Config()->paths('labels') . $locale . DIRECTORY_SEPARATOR . $type . '.yaml');

        return isset($labels[$key]) ? $labels[$key] : null;
    }

    /**
     * Gets all labels for a defined type.
     *
     * @param string $type   Name of the array group.
     * @param string $locale Locale code.
     *
     * @access public
     * @static
     * @uses   Config()
     * @uses   \Spyc
     *
     * @return array
     */
    public static function getAll($type, $locale = '')
    {
        if (!$locale) {
            $locale = Core\Registry()->get('locale');
        }

        return \Spyc::YAMLLoad(Core\Config()->paths('labels') . $locale . DIRECTORY_SEPARATOR . $type . '.yaml');
    }
}
