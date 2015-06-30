<?php
/**
 * Silla.IO Boot loader and entry point.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core;

use Aura;

/**
 * Silla class definition.
 */
final class Silla
{
    /**
     * Execution environment.
     *
     * @var string
     */
    protected $environment;

    /**
     * Execution configuration.
     *
     * @var Base\Configuration
     */
    protected $configuration;

    /**
     * Execution context.
     *
     * @var array
     */
    protected $context;

    /**
     * Current working directory.
     *
     * var string
     */
    protected $cwd;

    /**
     * @var Aura\Router\Router
     */
    protected $router;

    /**
     * @var Modules\Registry\Registry
     */
    protected $registry;

    /**
     * @var Modules\Session\Session
     */
    protected $session;

    /**
     * @var Modules\Cache\Cache
     */
    protected $cache;

    /**
     * @var Modules\DB\DB
     */
    protected $db;

    /**
     * Constructor. Creates a Silla.IO instance.
     *
     * @param string $environment Environment name.
     * @param array $context Execution context.
     */
    public function __construct($environment, array $context)
    {
        /**
         * Registers vendors auto-loaders.
         */
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .  'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        include __DIR__ . DIRECTORY_SEPARATOR . 'core.php';

        $this->verifyServerSoftware();

        $this->cwd = dirname(__DIR__);

        chdir($this->cwd);

        /* Hook the default auto-load class function. */
        spl_autoload_extensions('.php');
        spl_autoload_register('spl_autoload');

        $this->context     = $context;
        $this->environment = $environment;

        $this->loadConfiguration();

        $router_factory = new Aura\Router\RouterFactory;
        $this->router = $router_factory->newInstance();

        $this->registry = new Modules\Registry\Registry;
        $this->registry->set('locale', $this->configuration()->I18N['default']);

        $this->session = new Modules\Session\Session(
            $this->configuration->SESSION['adapter'],
            $this->configuration->urls('relative'),
            $this->configuration->urls('protocol'),
            $this->configuration->SESSION,
            $context
        );

        $this->registry->set('session', $this->session);

        $this->cache = new Modules\Cache\Cache(
            $this->configuration->CACHE['adapter'],
            $this->configuration->CACHE
        );

        $this->db = new Modules\DB\DB($this->configuration->DB, $this->cache, $this->configuration->CACHE['db_schema']);

        if ($adapter = $this->cache->getAdapter() instanceof Modules\Cache\Adapters\Database) {
            $this->cache->getAdapter()->setDatabaseStorage($this->db->getAdapter());
        }
    }

    /**
     * Dispatch method.
     *
     * @param string $path Request Path to be dispatched.
     *
     * @return Modules\Http\Response
     */
    public function dispatch($path)
    {
        $path = $this->normalizeQueryString($path);
        $mode = $this->getMode($path);

        $routesConfiguration = '\\' . $mode['namespace'] . '\Configuration\Routes';
        $routesConfiguration = new $routesConfiguration();

        $this->router->setRoutes($routesConfiguration->routes());
        $route = $this->router->match($path, $this->context['_SERVER']);
        dd($route);
        return new Modules\Http\Response($route);
    }

    /**
     * Gets a router instance.
     *
     * @return Aura\Router\Router
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * Gets a configuration instance.
     *
     * @return Base\Configuration
     */
    public function configuration()
    {
        return $this->configuration;
    }

    /**
     * Gets a registry instance.
     *
     * @return Modules\Registry\Registry
     */
    public function registry()
    {
        return $this->registry;
    }

    /**
     * Gets a session instance.
     *
     * @return Modules\Session\Session
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * Gets a cache instance.
     *
     * @return Modules\Cache\Cache
     */
    public function cache()
    {
        return $this->cache();
    }

    /**
     * Loads configuration file.
     *
     * @throws \Exception Cannot load environment configuration file.
     *
     * @return void
     */
    private function loadConfiguration()
    {
        $configurationEnvironmentFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'configurations' .
            DIRECTORY_SEPARATOR . $this->environment . DIRECTORY_SEPARATOR . 'environment.php';

        if (file_exists($configurationEnvironmentFile)) {
            require $configurationEnvironmentFile;
        } else {
            throw new \Exception('Cannot load environment configuration file: ' . $configurationEnvironmentFile);
        }

        if (class_exists('\Configurations\\' . $this->environment . '\\Configuration')) {
            $configurationClass = 'Configurations\\' . $this->environment . '\\Configuration';
            $this->configuration = new $configurationClass($this->environment, $this->context['_SERVER']);
        } else {
            throw new \Exception('Cannot load environment configuration class: ' . $configurationEnvironmentFile);
        }
    }

    /**
     * Verifies server software requirements.
     *
     * @throws \Exception
     *
     * @return void
     */
    private function verifyServerSoftware()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            throw new \Exception('Sorry, Silla.IO framework will only run on PHP version 5.3.7 or greater!');
        }
    }

    /**
     * Extract current mode.
     *
     * @param string $path Query request string.
     *
     * @return array
     */
    private function getMode($path)
    {
        $modes = $this->configuration->modes();

        foreach ($modes as $mode) {
            if ($mode['url'] && 0 === strpos($path, $mode['url'])) {
                return $mode;
            }
        }

        /* Default mode */
        return end($modes);
    }

    /**
     * Retrieve the relative request query string.
     *
     * @param string $path Request query string.
     *
     * @return string
     */
    private function normalizeQueryString($path)
    {
        $applicationPath = $this->configuration->urls('relative');

        if ($applicationPath !== '/') {
            $path = '/' . str_replace($applicationPath, '', $path);
        }

        return $path;
    }
}
