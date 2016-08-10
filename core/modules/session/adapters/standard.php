<?php
/**
 * Session handler wrapping the $_SESSION.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Session\Adapters
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Session\Adapters;

use Core;
use Core\Modules\Session\Interfaces;

/**
 * Standard session handler class definition.
 */
final class Standard implements Interfaces\Adapter
{
    /**
     * IP Address of the client.
     *
     * @var string
     * @access private
     * @static
     */
    private static $ip = null;

    /**
     * User Agent (OS, Browser, etc) of the client.
     *
     * @var string
     * @access private
     * @static
     */
    private static $userAgent = null;

    /**
     * Session started flag.
     *
     * @var boolean
     * @access private
     */
    private static $started = false;

    /**
     * All stored session variables.
     *
     * @var array
     * @access private
     */
    private $vars = array();

    /**
     * Unique session ID.
     *
     * @var string
     * @access private
     */
    private $sessionKey;

    /**
     * The path on the server in which the cookie will be available on.
     *
     * @var string
     * @access private
     */
    private $cookie_path;

    /**
     * Initialize values.
     */
    public function __construct()
    {
        $this->cookie_path = Core\Config()->urls('relative');
        $this->start();
    }

    /**
     * Starts session.
     *
     * @return void
     */
    public function start()
    {
        if (!self::$started) {
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Pragma: no-cache');

            ini_set('session.name', Core\Config()->SESSION['name']);
            ini_set('session.cookie_lifetime', Core\Config()->SESSION['ttl']);
            ini_set('session.gc_probability', 1);
            ini_set('session.gc_maxlifetime', Core\Config()->SESSION['lifetime'] * 60);

            header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');

            self::$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            self::$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            if (Core\Config()->SESSION['transparency']) {
                $_COOKIE[Core\Config()->SESSION['name']] =
                    isset($_REQUEST[Core\Config()->SESSION['parameter']]) ?
                    $_REQUEST[Core\Config()->SESSION['parameter']] : null;
            }

            if ($this->isValidHost()
                && array_key_exists(Core\Config()->SESSION['name'], $_COOKIE)
                && $_COOKIE[Core\Config()->SESSION['name']]
            ) {
                session_id($_COOKIE[Core\Config()->SESSION['name']]);
            } else {
                session_id($this->generateKey());
            }

            session_set_cookie_params(
                null,
                $this->cookie_path,
                $_SERVER['SERVER_NAME'],
                Core\Config()->urls('protocol') === 'https',
                true
            );

            session_cache_limiter('nocache');
            session_start();

            $this->sessionKey = session_id();

            $this->loadVars();

            if (isset($this->vars['last_updated']) &&
                $this->vars['last_updated'] < time() - Core\Config()->SESSION['lifetime'] * 60
            ) {
                $this->destroy();
                $this->start();
                return;
            }

            self::$started = true;

            $_SESSION[$this->getHash()]['last_updated'] = time();
        }
    }

    /**
     * Test whether the user host is as expected for this session.
     *
     * @access private
     *
     * @return boolean
     */
    private function isValidHost()
    {
        return ($_SERVER['REMOTE_ADDR'] == self::$ip) && ($_SERVER['HTTP_USER_AGENT'] == self::$userAgent);
    }

    /**
     * Get key that uniquely identifies this session.
     *
     * @access public
     *
     * @return string
     */
    public function getHash()
    {
        return md5(session_id() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }

    /**
     * Check if session with a particular key exists in the database.
     *
     * @access public
     *
     * @return boolean
     */
    public function isSession()
    {
        return isset($_SESSION['last_active']) && $_SESSION['last_active'] > time() - Core\Config()->SESSION['ttl'];
    }

    /**
     * Generate unique session key.
     *
     * @access private
     *
     * @return string
     */
    private function generateKey()
    {
        $key = 0;
        mt_srand((double)microtime() * 1000000);
        $puddle = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i < Core\Config()->SESSION['key_length'] - 1; $i++) {
            $key .= substr($puddle, (mt_rand() % (strlen($puddle))), 1);
        }

        return $key;
    }

    /**
     * Load variables, associated with a particular session key.
     *
     * @param mixed $key Variable key.
     *
     * @access public
     *
     * @return boolean
     */
    public function loadVars($key = false)
    {
        $key = $key ? $key : $this->getHash();

        if ($this->isValidHost() && isset($_SESSION[$key])) {
            $this->vars = $_SESSION[$this->getHash()];
        }

        return true;
    }

    /**
     * Destroys the session.
     *
     * @access public
     *
     * @return boolean
     */
    public function destroy()
    {
        session_destroy();

        $this->vars = array();
        $this->sessionKey = null;

        unset($_COOKIE[Core\Config()->SESSION['name']]);
        self::$started = false;
        
        return true;
    }

    /**
     * Free all session variables currently registered.
     *
     * @return void
     */
    public function unsetSession()
    {
        unset($_SESSION[$this->getHash()]);
    }

    /**
     * Set a session variable.
     *
     * @param string $name  Variable name.
     * @param mixed  $value Variable value.
     *
     * @access public
     *
     * @return boolean
     */
    public function set($name, $value)
    {
        if ($this->isValidHost()) {
            if (isset($this->vars[$name])) {
                $this->remove($name);
            }

            $_SESSION[$this->getHash()][$name] = $value;
            $this->loadVars();
        }

        return true;
    }

    /**
     * Retrieves the value of a session variable.
     *
     * @param string $name Variable name.
     *
     * @access public
     *
     * @return mixed
     */
    public function get($name)
    {
        if ($this->isValidHost() && isset($this->vars[$name])) {
            return $this->vars[$name];
        }

        return null;
    }

    /**
     * Unset a session variable.
     *
     * @param string $name Variable name.
     *
     * @access public
     *
     * @return boolean
     */
    public function remove($name)
    {
        if ($this->isValidHost()) {
            unset($_SESSION[$this->getHash()][$name]);

            $this->loadVars();
        }

        return true;
    }

    /**
     * Retrieves session key.
     *
     * @access public
     *
     * @return string
     */
    public function getKey()
    {
        return $this->sessionKey;
    }

    /**
     * Regenerates Session ID.
     *
     * You should roll session ID whenever elevation occurs.
     * E.G when a user logs in, the session ID of the session should be changed, since it's importance is changed.
     *
     * @access public
     *
     * @return void
     */
    public function regenerateKey()
    {
        $this->destroy();
        $this->start();
    }
}
