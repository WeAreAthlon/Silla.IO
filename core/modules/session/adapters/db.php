<?php
/**
 * Session handler wrapping the $_SESSION using the database storage engine.
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
 * DB session handler class defintion.
 */
final class DB implements Interfaces\Adapter
{
    /**
     * All stored session variables.
     *
     * @var array
     * @access private
     */
    private $vars = array();

    /**
     * Instance of Database connection.
     *
     * @var object DB
     * @access private
     */
    private $db;

    /**
     * Store already set unique session ID.
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
     * @link setcookie()
     */
    private $cookie_path;

    /**
     * Table name which store all unique session IDs.
     *
     * @var string
     * @access private
     */
    private $storageTable;

    /**
     * Table name which store all session variables.
     *
     * @var string
     * @access private
     */
    private $storageVariablesTable;

    /**
     * Flag if session has been started.
     *
     * @var boolean
     * @access private
     * @static
     */
    private static $started = false;

    /**
     * IP Address of client.
     *
     * @var string
     * @access private
     * @static
     */
    private static $ip = null;

    /**
     * User Agent (OS, Browser, etc) of client.
     *
     * @var string
     * @access private
     * @static
     */
    private static $userAgent = null;

    /**
     * Initialize variables.
     */
    public function __construct()
    {
        if (!self::$started) {
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Pragma: no-cache');

            $this->db = Core\DB();

            $this->cookie_path = Core\Config()->urls('relative');

            self::$ip = $_SERVER['REMOTE_ADDR'];
            self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
            self::$started = true;

            $this->storageTable = Core\Config()->DB['tables_prefix'] . 'sessions';
            $this->storageVariablesTable = Core\Config()->DB['tables_prefix'] . 'session_vars';

            if (Core\Config()->SESSION['transparency']) {
                $_COOKIE[Core\Config()->SESSION['name']] =
                    isset($_REQUEST[Core\Config()->SESSION['parameter']]) ?
                    $_REQUEST[Core\Config()->SESSION['parameter']] : null;
            }

            if (isset($_COOKIE[Core\Config()->SESSION['name']])
                && strlen($_COOKIE[Core\Config()->SESSION['name']]) == Core\Config()->SESSION['key_length']
                && $this->isSession($_COOKIE[Core\Config()->SESSION['name']])
            ) {
                $this->sessionKey = $_COOKIE[Core\Config()->SESSION['name']];

                if ($this->isValidHost()) {
                    $this->loadVars($this->sessionKey);
                }

                $this->updateTimestamp($this->sessionKey);
                $this->killOld();

                return true;
            }

            $this->sessionKey = null;
            $this->start();

            return true;
        }
    }

    /**
     * Test whether the user host is as expected for this session.
     *
     * Compare the REMOTE_ADDR and HTTP_USER_AGENT elements.
     *
     * @uses   $ip
     * @uses   $userAgent
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
     * Calculate a unique session key based on the session ID and user agent, plus the user's IP address if not on AOL.
     *
     * @uses   md5()
     * @uses   session_id()
     *
     * @return string
     */
    public function getHash()
    {
        return md5($this->sessionKey . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }

    /**
     * Begin a new session.
     *
     * @return boolean
     */
    public function start()
    {
        $key = $this->generateKey();

        $this->db->query(
            "INSERT INTO {$this->storageTable}(sessionKey, last_active) VALUES(?, ?)",
            array($key, time())
        );

        Core\Router()->setCookie(Core\Config()->SESSION['name'], $key, false, true);

        $this->sessionKey = $key;

        return true;
    }

    /**
     * Check if session with a particular key exists in the database.
     *
     * @param string $key Session key to check for.
     *
     * @return boolean
     */
    public function isSession($key)
    {
        $rs = $this->db->query(
            "SELECT session_key FROM {$this->storageTable} WHERE session_key = ? AND last_active > ?",
            array($key, (time() - Core\Config()->SESSION['lifetime'] * 60))
        );

        return isset($rs[0]) && (1 === count($rs[0]));
    }

    /**
     * Generate unique session key.
     *
     * @return string
     */
    private function generateKey()
    {
        $this->killOld();

        $key = 0;
        mt_srand((double)microtime() * 1000000);
        $puddle = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i < Core\Config()->SESSION['key_length'] - 1; $i++) {
            $key .= substr($puddle, (mt_rand() % (strlen($puddle))), 1);
        }

        while ($this->isSession($key) === true) {
            $key = $this->generateKey();
        }

        return $key;
    }

    /**
     * Clean up old sessions.
     *
     * @return boolean
     */
    private function killOld()
    {
        return !!$this->db->query(
            "DELETE FROM {$this->storageTable} WHERE last_active < ?",
            array(time() - Core\Config()->SESSION['lifetime'] * 60)
        );
    }

    /**
     * Load variables, associated with a particular session key.
     *
     * @param string $key Session key name.
     *
     * @return boolean
     */
    public function loadVars($key)
    {
        $this->vars = array();
        $query = "SELECT name, value FROM {$this->storageVariablesTable} WHERE session_key = ? AND private_key = ?";

        if ($results = $this->db->query($query, array($key, $this->getHash()), 'assoc_array')) {
            foreach ($results as $result) {
                $this->vars[$result['name']] = unserialize($result['value']);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Update session expiry time, does nothing for permanent sessions.
     *
     * @param string $key Session key name.
     *
     * @return boolean
     */
    private function updateTimestamp($key)
    {
        $query = "UPDATE {$this->storageTable} SET last_active = ? WHERE session_key = ?";

        return !!$this->db->query($query, array(time(), $key));
    }

    /**
     * Destroys session.
     *
     * @param mixed $key Session key, defaults to {$this->sessionKey}.
     *
     * @return boolean
     */
    public function destroy($key = false)
    {
        $key = $key ? $key : $this->sessionKey;

        $query = "DELETE FROM {$this->storageTable} WHERE session_key = ?";

        if ($this->db->query($query, array($key))) {
            Core\Router()->deleteCookie(Core\Config()->SESSION['name']);

            $this->vars = array();
            $this->sessionKey = null;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Set a session variable.
     *
     * @param string $name  Variable name, max 20 chars.
     * @param string $value Variable value.
     * @param mixed  $key   Session key (defaults to $this->sessionKey).
     *
     * @return boolean
     */
    public function set($name, $value, $key = false)
    {
        if ($this->isValidHost()) {
            $key = $key ? $key : $this->sessionKey;

            if (isset($this->vars[$name])) {
                $this->remove($name, $key);
            }

            $isStored = $this->db->query(
                "INSERT INTO {$this->storageVariablesTable}(session_key, private_key, name, value) VALUES(?, ?, ?, ?)",
                array($key, $this->getHash(), $name, serialize($value))
            );

            if ($isStored) {
                $this->loadVars($key);

                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves the value of a session variable.
     *
     * @param string $name Variable name.
     *
     * @return mixed
     */
    public function get($name)
    {
        if ($this->isValidHost() && isset($this->vars[$name])) {
            return $this->vars[$name];
        }
    }

    /**
     * Unset a session variable.
     *
     * @param string $name Variable name.
     * @param mixed  $key  Session key name (defaults to $this->sessionKey).
     *
     * @return boolean
     */
    public function remove($name, $key = false)
    {
        if ($this->isValidHost()) {
            $key = $key ? $key : $this->sessionKey;

            return $this->db->query(
                "DELETE FROM {$this->storageVariablesTable} WHERE session_key = ? AND private_key = ? AND name = ?",
                array($key, $this->getHash(), $name)
            );
        }

        return false;
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
        /* Delete associated data */
        $this->destroy();
        $this->killOld();

        Core\Router()->deleteCookie($this->sessionKey);

        /* Starts new session */
        $this->start();
    }
}
