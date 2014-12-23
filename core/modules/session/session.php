<?php
/**
 * Persistent Session Data Management.
 *
 * @package    Silla
 * @subpackage Core\Modules\Session
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\Session;

use Core;

/**
 * Session Class definition.
 */
final class Session
{
    /**
     * Reference to the current instance of the Session object.
     *
     * @var Session
     * @access private
     * @static
     */
    private static $instance = null;

    /**
     * Reference to the current adapter of the Session object.
     *
     * @var Core\Modules\Session\Interfaces\Adapter
     * @access private
     */
    private $adapter = null;

    /**
     * Session constructor.
     *
     * @param string $adapter Session Adapter.
     *
     * @throws \DomainException          Not supported Session adapter.
     * @throws \InvalidArgumentException Not compatible Session adapter.
     * @access private
     */
    private function __construct($adapter)
    {
        if (!class_exists($adapter)) {
            throw new \DomainException('Not supported Session adapter type: ' . $adapter);
        }

        if (!is_subclass_of($adapter, 'Core\Modules\Session\Interfaces\Adapter')) {
            throw new \InvalidArgumentException('Not compatible Session adapter type: ' . $adapter);
        }

        $this->adapter = new $adapter;
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

    /**
     * Cloning of Session is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is singleton.', E_USER_ERROR);
    }

    /**
     * Returns an instance of the Session object.
     *
     * @param string $adapter Adapter name.
     *
     * @access public
     * @static
     * @final
     * @uses   Core\Registry()
     *
     * @return Session
     */
    final public static function getInstance($adapter)
    {
        if (null === self::$instance) {
            $adapter = 'Core\Modules\Session\Adapters\\' . $adapter;

            self::$instance = new Session($adapter);

            Core\Registry()->set('session', self::$instance);
        }

        return self::$instance;
    }
}
