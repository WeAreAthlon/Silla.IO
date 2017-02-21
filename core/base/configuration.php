<?php
/**
 * Settings and configuration variables of the framework.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;

/**
 * Class Configuration Definition.
 */
abstract class Configuration
{
    /**
     * @var boolean[] $ASSETS Assets Management options flags.
     *
     * @example cache    Whether to cache all assets groups on the file system.
     * @example combine  Whether to combine all assets groups in one file.
     * @example optimize Whether to minify assets.
     */
    public $ASSETS = array(
        'cache'    => false,
        'combine'  => false,
        'optimize' => false,
    );

    /**
     * @var string[] $RENDER Render engine configuration.
     *
     * @example adapter Render adapter name.
     */
    public $RENDER = array(
        'adapter' => 'Core\Modules\Render\Adapters\Smarty',
        'options' => array(
            'strip_white_space' => false,
        ),
    );

    /**
     * @var string[] $ROUTER Router related configuration options.
     *
     * @example rewrite          Whether to support url rewrite or not.
     * @example separator        URL elements separator.
     * @example variables_prefix Routes variables notation prefix. Must be different from the 'separator'.
     */
    public $ROUTER = array(
        'rewrite'          => true,
        'separator'        => '/',
        'variables_prefix' => ':'
    );

    /**
     * @var array $CACHE Cache related configuration options.
     *
     * @example adapter   Caching adapter name.
     * @example routes    Whether to cache Routing routes.
     * @example labels    Whether to cache Localisation labels.
     * @example db_schema Whether to cache Database Entity tables schemas.
     * @example database  Database cache adapter database schema.
     * @example redis     Redis cache adapter connection parameters.
     */
    public $CACHE = array(
        'adapter'       => 'FileSystem',
        'routes'        => false,
        'labels'        => false,
        'db_schema'     => false,
        'database' => array(
            'table_name' => 'cache',
            'fields'     => array(
                'cache_key',
                'value',
                'expire',
            ),
        ),
        'redis' => array(
            'scheme'    => 'tcp',
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'timeout'   => 5.0,
        ),
    );

    /**
     * @var (int|string)[] $MAILER Mailer configuration options.
     *
     * @example type        Type of the mailing infrastructure to use(Sendmail|SMTP).
     * @example identity    Mailer identity signature.
     * @example credentials Mailer service credentials.
     */
    public $MAILER = array(
        'type' => 'Sendmail',
        'identity' => array(
            'name'  => 'Athlon Production',
            'email' => 'hi@athlonproduction.com',
        ),
        'credentials' => array(
            'smtp' => array(
                'host' => 'localhost',
                'port' => '25',
                'user' => '',
                'password' => '',
            ),
        ),
    );

    /**
     * @var (int|string)[] $DB DSN (Data source name).
     *
     * @example adapter        Adapter type (pdo_mysql|mysql|sqllite).
     * @example host           Connection host name.
     * @example port           Connection host port.
     * @example user           User name.
     * @example password       Password phrase.
     * @example name           Database name.
     * @example tables_prefix  Storage tables prefix.
     * @example encryption_key Database encryption key.
     * @example crypt_vector   Initialization Vector value.
     */
    public $DB = array(
        'adapter'        => 'pdo_mysql',
        'host'           => 'localhost',
        'port'           => 3306,
        'user'           => '',
        'password'       => '',
        'name'           => '',
        'tables_prefix'  => '',
        'encryption_key' => '25c6c7ff35bd13b0ff9979b151f2136c',
        'crypt_vector'   => 'dasn312321nssa1k',
    );

    /**
     * @var array $I18N Localisation configuration options if supported.
     *
     * @example 'support' boolean Whether to support localisation or not.
     * @example 'default' string  Default localization code.
     * @example 'locales' array   Supported localisations options ['url-value' => 'database-value'].
     */
    public $I18N = array(
        'support' => false,
        'default' => 'en_US',
        'locales' => array(
            'en_US' => 'English (American)',
        ),
    );

    /**
     * @var (int|string)[] $SESSION Session related configuration options.
     *
     * @example adapter      Type of session. Possible values (Standard|DB).
     * @example name         Name of the session (used as cookie name).
     * @example ttl          Lifetime in seconds of cookie. If 0, until browser is restarted.
     * @example lifetime     After specified minutes, stored data will be cleaned up by the garbage collection.
     * @example key_length   Length of session unique id.
     * @example transparency Usage of transparent session id. Use with caution.
     * @example parameter    Name of the transparent session id parameter.
     */
    public $SESSION = array(
        'adapter'      => 'Standard',
        'name'         => 'ATHSESSID',
        'ttl'          => 0,
        'lifetime'     => 60,
        'key_length'   => 32,
        'transparency' => false,
        'parameter'    => 'ath',
    );

    /**
     * @var array $PATHS File Paths definition.
     *
     * @example root      Path to Silla.IO files location on the file system.
     * @example mode      Path to Silla.IO current mode location.
     * @example labels    Path to Silla.IO localisation labels location.
     * @example cache     Path to Silla.IO file caches on the server.
     * @example tmp       Path to Silla.IO temorary file storage location.
     * @example public    Path to Silla.IO public accessible location.
     * @example uploads   Path to Silla.IO uploads storage location.
     * @example vendor    Path to Silla.IO vendor files location.
     * @example resources Path to Silla.IO resources files location.
     * @example assets    Path to Silla.IO assets storage location['source', 'distribution'].
     * @example views     Path to Silla.IO views['templates', 'compiled', 'config', 'cache'].
     */
    protected $PATHS = array(
        'root'      => null,
        'mode'      => null,
        'labels'    => null,
        'cache'     => null,
        'tmp'       => null,
        'public'    => null,
        'uploads'   => null,
        'vendor'    => null,
        'resources' => null,
        'views'     => array(),
        'assets'    => array(),
    );

    /**
     * @var string[] $URLS URLS paths definition.
     *
     * @example full     Full URL path for the current Silla.IO instance.
     * @example relative Relative URL path for the current Silla.IO instance.
     * @example protocol Current URL request protocol type.
     * @example public   URL path to the public accessible location.
     * @example assets   URL path to the assets storage location.
     * @example uploads  URL path to the uploads storage location.
     */
    protected $URLS = array(
        'full'     => null,
        'relative' => null,
        'protocol' => null,
        'assets'   => null,
        'public'   => null,
        'uploads'  => null,
    );

    /**
     * @var array $MODES Silla.IO Runtime Modes. Array representation of supported Silla.IO modes.
     *      Default mode(most common) is the last element the array.
     *
     * @example name     Semantic name of the mode.
     * @example location File path to the mode files WITH trailing slash.
     * @example url      URL prefix of the mode WITH trailing slash.
     */
    private $MODES = array(
        array(
            'name'     => 'cms',
            'location' => 'cms/',
            'url'      => 'cms/',
        ),
        array(
            'name'     => 'app',
            'location' => 'app/',
            'url'      => '/',
        ),
    );

    /**
     * @var array $MODE Current mode.
     */
    private $MODE = array();

    /**
     * @var Configuration $instance Reference to the current instance of the Configuration object.
     */
    protected static $instance = null;

    /**
     * Setup all variables values.
     */
    protected function __construct()
    {
        $current_dir = dirname(dirname(realpath(__DIR__)));

        $this->PATHS['root'] = $current_dir . DIRECTORY_SEPARATOR;

        if (!isset($_SERVER['CONTEXT_DOCUMENT_ROOT'])) {
            $this->URLS['relative'] = str_replace(
                '\\',
                '/',
                str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', $current_dir . '/')
            );
        } else {
            $this->URLS['relative'] = $_SERVER['CONTEXT_PREFIX'] . str_replace(
                realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']),
                '',
                $current_dir . '/'
            );

            $this->URLS['relative'] = str_replace(DIRECTORY_SEPARATOR, '/', $this->URLS['relative']);
        }

        /* Check if the request is sent over HTTPS */
        $is_SSL = Core\Utils::httpRequestIsSsl();
        $port = null;

        if (isset($_SERVER['SERVER_PORT'])) {
            $port = in_array($_SERVER['SERVER_PORT'], array('80', '443', true)) ? null : $_SERVER['SERVER_PORT'];
        }

        $this->URLS['protocol'] = 'http' . ($is_SSL ? 's' : '');
        $this->URLS['full']     = null;

        if (isset($_SERVER['SERVER_NAME'])) {
            $this->URLS['full'] = $this->URLS['protocol'] . '://' .
                $_SERVER['SERVER_NAME'] . $port . $this->URLS['relative'];
        }

        $this->PATHS['vendor'] = $this->PATHS['root'] . 'vendor' . DIRECTORY_SEPARATOR;
        $this->PATHS['resources'] = $this->PATHS['root'] . 'resources' . DIRECTORY_SEPARATOR;
        $this->PATHS['tmp']    = $this->PATHS['root'] . 'temp'   . DIRECTORY_SEPARATOR;
        $this->PATHS['cache']  = $this->PATHS['tmp']  . 'cache'  . DIRECTORY_SEPARATOR;
        $this->PATHS['public'] = $this->PATHS['root'] . 'public' . DIRECTORY_SEPARATOR;

        $this->PATHS['views']['compiled'] = $this->PATHS['cache'] . 'compiled' . DIRECTORY_SEPARATOR;
        $this->PATHS['views']['cache']    = $this->PATHS['cache'] . 'views'    . DIRECTORY_SEPARATOR;
        $this->PATHS['views']['plugins']  = $this->PATHS['resources'] . 'smarty_plugins' . DIRECTORY_SEPARATOR;
        $this->PATHS['views']['config']   = $this->PATHS['root']  .
            'configurations' . DIRECTORY_SEPARATOR . SILLA_ENVIRONMENT .
            DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

        /* Process modes */
        $this->MODES = $this->setupModes($this->MODES);

        /* Default mode */
        $this->setMode($this->MODES[0]);
    }

    /**
     * Setup mode configuration variables.
     *
     * @param array $mode Silla.IO mode data.
     *
     * @return void
     */
    final public function setMode(array $mode)
    {
        $this->MODE = $mode;

        $this->PATHS['mode']               = $mode['location'];
        $this->PATHS['views']['templates'] = $this->PATHS['mode'] . 'views' . DIRECTORY_SEPARATOR;
        $this->PATHS['views']['layouts']   = $this->PATHS['views']['templates'] . '_layouts' . DIRECTORY_SEPARATOR;

        $this->PATHS['labels']  = $this->PATHS['mode']   . 'labels' . DIRECTORY_SEPARATOR;
        $this->PATHS['uploads'] =
            $this->PATHS['public'] . $mode['relative'] . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        $this->PATHS['assets']['source']       = $this->PATHS['root'];
        $this->PATHS['assets']['distribution'] =
            $this->PATHS['public'] . $mode['relative'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        $this->URLS['mode']    = $this->URLS['relative'] . $mode['url'];
        $this->URLS['public']  = $this->URLS['relative'] . 'public/' . $mode['relative'] . '/';
        $this->URLS['assets']  = $this->URLS['public'] . 'assets/';
        $this->URLS['uploads'] = $this->URLS['public'] . 'uploads/';
    }

    /**
     * Retrieves a configuration path value.
     *
     * @param string $name Path configuration variable name.
     *
     * @throws \OutOfBoundsException When requesting a none existing path.
     *
     * @return mixed
     */
    final public function paths($name = null)
    {
        if ($name) {
            if (isset($this->PATHS[$name])) {
                return $this->PATHS[$name];
            } else {
                throw new \OutOfBoundsException("No configuration path variable with name {$name}");
            }
        } else {
            return $this->PATHS;
        }
    }

    /**
     * Retrieves a configuration URL value.
     *
     * @param string $name URL configuration variable name.
     *
     * @throws \OutOfBoundsException When requesting a none existing URL.
     *
     * @return mixed
     */
    final public function urls($name = null)
    {
        if ($name) {
            if (isset($this->URLS[$name])) {
                return $this->URLS[$name];
            } else {
                throw new \OutOfBoundsException("No configuration URL variable with name {$name}");
            }
        } else {
            return $this->URLS;
        }
    }

    /**
     * Retrieves Silla.IO modes.
     *
     * @param string $name Name of the mode.
     *
     * @return array
     */
    final public function modes($name = '')
    {
        if ($name) {
            foreach ($this->MODES as $mode) {
                if ($mode['name'] === $name) {
                    return $mode;
                }
            }
        }

        return $this->MODES;
    }

    /**
     * Retrieves current Silla.IO mode.
     *
     * @param string $segment Segment name of the mode.
     *
     * @return mixed
     */
    final public function mode($segment = null)
    {
        if ($segment && isset($this->MODE[$segment])) {
            return $this->MODE[$segment];
        }

        return $this->MODE;
    }

    /**
     * Returns an instance of the Configuration object.
     *
     * @return Configuration
     */
    final public static function getInstance()
    {
        if (null === self::$instance) {
            $configuration = get_called_class();
            self::$instance = new $configuration;
        }

        return self::$instance;
    }

    /**
     * Cloning of Configuration is disallowed.
     *
     * @return void
     */
    final public function __clone()
    {
        trigger_error(get_called_class() . ' cannot be cloned! It is a singleton.', E_USER_ERROR);
    }

    /**
     * Format and setup Silla.IO modes.
     *
     * @param array $modes Array of Silla.IO modes to setup.
     *
     * @return array
     */
    final protected function setupModes(array $modes)
    {
        if ($this->ROUTER['separator'] !== '/') {
            foreach ($modes as &$mode) {
                $mode['url'] = str_replace('/', $this->ROUTER['separator'], $mode['url']);
            }
        }

        foreach ($modes as &$mode) {
            $mode['relative'] = trim($mode['location'], '/');
            $mode['location'] = $this->PATHS['root'] . $mode['location'];
            $mode['url'] = trim(
                str_replace($this->URLS['relative'], '', $mode['url']),
                $this->ROUTER['separator']
            );
            $mode['namespace'] = trim(
                str_replace('/', '\\', str_replace($this->PATHS['root'], '', $mode['location'])),
                '\\'
            );
        }

        return $modes;
    }
}
