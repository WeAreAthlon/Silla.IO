<?php
/**
 * Implementation of the Registry pattern for global access to commonly used objects throughout the code.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Registry;

/**
 * Router Class definition.
 */
final class Registry
{
    /**
     * Keeps all object references.
     *
     * @var array
     *
     * @access private
     */
    private $store = array();

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
