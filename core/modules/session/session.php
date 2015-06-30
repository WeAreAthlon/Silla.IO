<?php
/**
 * Persistent Session Data Management.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Session
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Session;

use Core;

/**
 * Session Class definition.
 */
final class Session
{
    /**
     * Reference to the current adapter of the Session object.
     *
     * @var Core\Modules\Session\Interfaces\Adapter
     */
    private $adapter = null;

    /**
     * Session constructor.
     *
     * @param string $adapter  Session Adapter.
     * @param string $scope    Relative path to be used for the session cookie scope.
     * @param string $protocol Protocol name to used for the session cookie.
     * @param array  $settings Configuration settings.
     * @param array  $context  Execution context.
     *
     * @throws \DomainException          Not supported Session adapter.
     * @throws \InvalidArgumentException Not compatible Session adapter.
     */
    public function __construct($adapter, $scope, $protocol, array $settings, array $context)
    {
        if (!class_exists($adapter)) {
            throw new \DomainException('Not supported Session adapter type: ' . $adapter);
        }

        if (!is_subclass_of($adapter, 'Core\Modules\Session\Interfaces\Adapter')) {
            throw new \InvalidArgumentException('Not compatible Session adapter type: ' . $adapter);
        }

        $this->adapter = new $adapter($scope, $protocol, $settings, $context);
    }

    /**
     * Setter method. Set a variable.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     *
     * @throws \InvalidArgumentException Session varialbe name should be string.
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Session variable name should be string.');
        }

        $this->adapter->set($name, $value);
    }

    /**
     * Getter method. Get a variable.
     *
     * @param string $name Variable name.
     *
     * @throws \InvalidArgumentException Session varialbe name should be string.
     *
     * @return mixed
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Session variable name should be string.');
        }

        return $this->adapter->get($name);
    }

    /**
     * Delete variable.
     *
     * @param string $name Variable name.
     *
     * @throws \InvalidArgumentException Session varialbe name should be string.
     *
     * @return boolean
     */
    public function remove($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Session variable name should be string.');
        }

        return $this->adapter->remove($name);
    }

    /**
     * Generator of session key method.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->adapter->getKey();
    }

    /**
     * Regeneration of session keys method.
     *
     * @return void
     */
    public function regenerateKey()
    {
        $this->adapter->regenerateKey();
    }

    /**
     * Destroys the whole session.
     *
     * @return boolean
     */
    public function destroy()
    {
        return $this->adapter->destroy();
    }

    /**
     * Generic setter method.
     *
     * Provides an easier way of setting session variables.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Generic getter method.
     *
     * Provides an easier way of retrieving session variables.
     *
     * @param string $name Variable name.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Generic isset method.
     *
     * @param string $name Variable name.
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return !!$this->get($name);
    }

    /**
     * Generic unset method.
     *
     * @param string $name Variable name.
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->remove($name);
    }
}
