<?php
/**
 * Smarty Output Render Adapter.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Render\Adapters
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Render\Adapters;

use Core;

/**
 * Class FileSystem Adapter for caching data.
 */
class Smarty implements Core\Modules\Render\Interfaces\Adapter
{
    /**
     * Smarty Template Engine instance.
     *
     * @var \Smarty
     */
    private $tpl;

    /**
     * Initialize the template engine.
     *
     * @param array $config  Template configuration.
     * @param array $options Additional options.
     *
     * @throws \SmartyException if filter could not be loaded
     */
    public function __construct(array $config, array $options = array())
    {
        $this->tpl = new \Smarty;

        $this->tpl->setTemplateDir($config['templates'])
                  ->setCompileDir($config['compiled'])
                  ->setCacheDir($config['cache'])
                  ->setConfigDir($config['config'])
                  ->addPluginsDir($config['plugins']);

        $this->tpl->configLoad('globals.conf');

        if (isset($options['strip_white_space']) && $options['strip_white_space']) {
            $this->tpl->loadFilter('output', 'trimwhitespace');
        }
    }

    /**
     * Assigns a template variable.
     *
     * @param string $name  Name of the variable.
     * @param mixed  $value Value of the variable.
     *
     * @return void
     */
    public function assignVariable($name, $value)
    {
        $this->tpl->assign($name, $value);
    }

    /**
     * Retrieves an assigned template variable.
     *
     * @param string $name Name of the variable.
     *
     * @return mixed
     */
    public function getVariable($name)
    {
        return $this->tpl->getTemplateVars($name);
    }

    /**
     * Removes an assigned template variable.
     *
     * @param string $name Name of the variable to remove.
     *
     * @return void
     */
    public function removeVariable($name)
    {
        $this->tpl->clearAssign($name);
    }

    /**
     * Applies a render filter.
     *
     * @param string $type       Filter type.
     * @param string $filterName Filter name.
     *
     * @return boolean
     */
    public function applyFilter($type, $filterName)
    {
        return $this->tpl->loadFilter($type, $filterName);
    }

    /**
     * Removes a render filter.
     *
     * @param string $type       Filter type.
     * @param string $filterName Filter name.
     *
     * @return void
     */
    public function removeFilter($type, $filterName)
    {
        $this->tpl->unloadFilter($type, $filterName);
    }

    /**
     * Parse and return render output.
     *
     * @param string $template Path to template file to parse.
     *
     * @return string
     */
    public function parse($template)
    {
        return $this->tpl->fetch($template);
    }

    /**
     * Retrieves rendered templates file extension.
     *
     * @return string
     */
    public function getTemplatesFileExtension()
    {
        return 'html.tpl';
    }

    /**
     * Retrieves rendered content type(MIME type).
     *
     * @return string
     */
    public function getRenderedContentType()
    {
        return 'text/html';
    }
}
