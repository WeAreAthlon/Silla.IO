<?php
/**
 * Session Adapter Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\Session\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\Session\Interfaces;

/**
 * Session Adapter definition.
 */
interface Adapter
{
    /**
     * Session destroy method.
     *
     * @return boolean
     */
    public function destroy();

    /**
     * Generator of session key method.
     *
     * @return string
     */
    public function getKey();

    /**
     * Regeneration of session keys method.
     *
     * @return void
     */
    public function regenerateKey();

    /**
     * Setter method.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     *
     * @return void
     */
    public function set($name, $value);

    /**
     * Getter method.
     *
     * @param string $name Variable name.
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Unset method.
     *
     * @param string $name Variable name.
     *
     * @return boolean
     */
    public function remove($name);
}
