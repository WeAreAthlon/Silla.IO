<?php
/**
 * Labels Helper.
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

    /**
     * Fetch labels from the filesystem.
     *
     * @param string $labels Name of the labels file.
     * @param string $locale Locale code.
     *
     * @static
     * @see    \Spyc
     *
     * @return array
     */
    public static function getExtendWithGlobals($labels, $locale = '')
    {
        if (!$locale) {
            $locale = Core\Registry()->get('locale');
        }

        $globals = \Spyc::YAMLLoad(Core\Config()->paths('labels') . $locale . DIRECTORY_SEPARATOR . 'globals.yaml');
        $labelsLocalFile = Core\Config()->paths('labels') . $locale . DIRECTORY_SEPARATOR . $labels . '.yaml';

        $labels = $globals;

        if (is_file($labelsLocalFile)) {
            $labels = Core\Utils::arrayExtend($globals, \Spyc::YAMLLoad($labelsLocalFile));
        }

        return $labels;
    }
}
