<?php
/**
 * Implementation of the Registry pattern for global access to commonly used objects throughout the code.
 *
 * @package    Silla
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\Registry;

/**
 * Router Class definition.
 */
final class Registry
{
    /**
     * Reference to the current instance of the Registry object.
     *
     * @var object
     * @access private
     * @static
     */
    private static $instance = null;

    /**
     * Keeps all object references.
     *
     * @var array
     *
     * @access private
     */
    private $store = array();

    /**
     * Constructor, does nothing.
     *
     * @access private
     */
    private function __construct()
    {
    }

    /**
     * Cloning of Registry is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is a singleton.', E_USER_ERROR);
    }

    /**
     * Returns an instance of the registry object.
     *
     * @access public
     * @static
     * @final
     *
     * @return Registry
     */
    final public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Registry();
        }

        return self::$instance;
    }

    /**
     * Magic method. Alias of set().
     *
     * @param string $label  Variable name.
     * @param mixed  $object Variable value.
     *
     * @access public
     *
     * @return void
     */
    public function __set($label, $object)
    {
        $this->set($label, $object);
    }

    /**
     * Registers an object with the Registry.
     *
     * @param string $label  Variable name.
     * @param mixed  $object Variable value.
     *
     * @throws \InvalidArgumentException Registry varialbe name should be string.
     * @access public
     *
     * @return void
     */
    public function set($label, &$object)
    {
        if (!is_string($label)) {
            throw new \InvalidArgumentException('Registry variable name should be string.');
        }

        $this->store[$label] = &$object;
    }

    /**
     * Magic method. Returns a reference to an object in the Registry.
     *
     * @param string $label Variable name.
     *
     * @access public
     *
     * @return mixed
     */
    public function __get($label)
    {
        return $this->get($label);
    }

    /**
     * Returns a reference to an object in the Registry.
     *
     * @param string $label Variable name.
     *
     * @throws \InvalidArgumentException Registry variable name should be string.
     * @access public
     *
     * @return mixed
     */
    public function get($label)
    {
        if (!is_string($label)) {
            throw new \InvalidArgumentException('Registry variable name should be string.');
        }

        return isset($this->store[$label]) ? $this->store[$label] : false;
    }

    /**
     * Un-registers an object from the registry.
     *
     * @param string $label Variable name.
     *
     * @throws \InvalidArgumentException Registry variable name should be string.
     * @access public
     *
     * @return void
     */
    public function remove($label)
    {
        if (!is_string($label)) {
            throw new \InvalidArgumentException('Registry variable name should be string.');
        }

        if (isset($this->store[$label])) {
            unset($this->store[$label]);
        }
    }

    /**
     * Checks if there's an object registered under a specific label.
     *
     * @param string $label Variable name.
     *
     * @access public
     *
     * @return boolean
     */
    public function __isset($label)
    {
        return isset($this->store[$label]);
    }

    /**
     * Magic method. Un-registers an object from the registry.
     *
     * @param string $label Variable name.
     *
     * @access public
     *
     * @return void
     */
    public function __unset($label)
    {
        $this->remove($label);
    }
}
