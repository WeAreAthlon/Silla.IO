<?php
/**
 * Render output module.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Render
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Render;

use Core;

/**
 * Render Class definition.
 */
final class Render
{
    /**
     * Assets pipeline processor queue object.
     *
     * @var Assets
     * @access public
     */
    public $assets = null;

    /**
     * Render instance.
     *
     * @var Core\Modules\Render\Interfaces\Adapter
     */
    private $render = null;

    /**
     * Render configuration.
     *
     * @var array
     */
    private $configuration;

    /**
     * Layout name.
     *
     * @var string
     */
    private $layout;

    /**
     * View name.
     *
     * @var string
     */
    private $view;

    /**
     * Template files extension.
     *
     * @var string
     */
    private $filesExtension;

    /**
     * Rendered content type(MIME type).
     *
     * @var string
     */
    private $contentType;

    /**
     * Output render container.
     *
     * @var string
     */
    private $output = '';

    /**
     * Render engine content variables.
     *
     * @var array
     */
    private $variables = array();

    /**
     * Creates a render instance.
     *
     * @param string $adapter Adapter class name.
     * @param array  $options Adapter options.
     */
    public function __construct($adapter, array $options = array())
    {
        $this->setAdapter($adapter, Core\Config()->paths('views'), $options);
        $this->filesExtension = $this->render->getTemplatesFileExtension();
        $this->contentType    = $this->render->getRenderedContentType();

        $this->assets = new Assets;
    }

    /**
     * Sets render adapter.
     *
     * @param string $adapter       Adapter class name.
     * @param array  $configuration Adapter configuration.
     * @param array  $options       Adapter additional options.
     *
     * @throws \DomainException          Not supported Renderer adapter.
     * @throws \InvalidArgumentException Not compatible Renderer adapter.
     *
     * @return void
     */
    public function setAdapter($adapter, array $configuration, array $options = array())
    {
        if (!class_exists($adapter)) {
            throw new \DomainException('Not supported Render adapter type: ' . $adapter);
        }

        if (!is_subclass_of($adapter, 'Core\Modules\Render\Interfaces\Adapter')) {
            throw new \InvalidArgumentException('Not compatible Render adapter type: ' . $adapter);
        }

        $this->configuration = $configuration;

        $this->render = new $adapter($configuration, $options);
    }

    /**
     * Retrieves renderer adapter name.
     *
     * @return string
     */
    public function getAdapter()
    {
        return get_class($this->render);
    }

    /**
     * Assigns a template variable.
     *
     * @param string $variable Template variable name.
     * @param mixed  $value    Template variable value.
     *
     * @return void
     */
    public function set($variable, $value)
    {
        $this->variables[$variable] = $value;
    }

    /**
     * Retrieves an assigned template variable.
     *
     * @param string $variable Template variable name.
     *
     * @return mixed
     */
    public function get($variable)
    {
        return isset($this->variables[$variable]) ? $this->variables[$variable] : null;
    }

    /**
     * Removes an assigned template variable.
     *
     * @param string $variable Name of the variable.
     *
     * @return void
     */
    public function remove($variable)
    {
        unset($this->variables[$variable]);
    }

    /**
     * Removes all assigned template variables.
     *
     * @return void
     */
    public function clear()
    {
        $this->variables = array();
    }

    /**
     * Sets layout for the template.
     *
     * @param string $layout Layout name.
     *
     * @throws \InvalidArgumentException Layout file cannot be located.
     *
     * @return void
     */
    public function setLayout($layout)
    {
        $file = $this->configuration['layouts'] . $layout . '.' . $this->filesExtension;

        if (!empty($layout) && !file_exists($file)) {
            throw new \InvalidArgumentException('Layout not located: ' . $file);
        }

        $this->layout = $layout ? $file : null;
    }

    /**
     * Retrieves current layout name.
     *
     * @return string
     */
    public function getLayout()
    {
        $layout = str_replace($this->configuration['layouts'], '', $this->layout);
        $layout = str_replace('.' . $this->filesExtension, '', $layout);

        return $layout;
    }

    /**
     * Sets view for the template.
     *
     * @param string $view Layout name.
     *
     * @throws \InvalidArgumentException View file cannot be located.
     *
     * @return void
     */
    public function setView($view)
    {
        $file = $this->configuration['templates'] . $view . '.' . $this->filesExtension;

        if (!empty($view) && !file_exists($file)) {
            throw new \InvalidArgumentException('View cannot be located under: ' . $file);
        }

        $this->view = $view ? $file : null;
    }

    /**
     * Retrieves current view name.
     *
     * @return string
     */
    public function getView()
    {
        $view = str_replace($this->configuration['templates'], '', $this->view);
        $view = str_replace('.' . $this->filesExtension, '', $view);

        return $view;
    }

    /**
     * Sets template files extension.
     *
     * @param string $extension Files extension.
     *
     * @return void
     */
    public function setTemplateExtension($extension)
    {
        $this->filesExtension = ltrim($extension, '.');
    }

    /**
     * Retrieves template files extension.
     *
     * @return string;
     */
    public function getTemplateExtension()
    {
        return $this->filesExtension;
    }

    /**
     * Retrieves template content type(MIME type).
     *
     * @return string
     */
    public function getOutputContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets template content type(MIME type).
     *
     * @param string $mimeType Content type.
     *
     * @return void
     */
    public function setOutputContentType($mimeType)
    {
        $this->contentType = $mimeType;
    }

    /**
     * Verifies whether a view template exists.
     *
     * @param string $view Template name.
     *
     * @return boolean
     */
    public function isView($view)
    {
        return file_exists($this->configuration['templates'] . $view . '.' . $this->getTemplateExtension());
    }

    /**
     * Verifies whether a layout template exists.
     *
     * @param string $layout Template name.
     *
     * @return boolean
     */
    public function isLayout($layout)
    {
        return file_exists($this->configuration['layouts'] . $layout . '.' . $this->getTemplateExtension());
    }

    /**
     * Explicitly set a render output.
     *
     * @param string $output Output value.
     *
     * @throws \InvalidArgumentException Output must be a valid string.
     *
     * @return void
     */
    public function setOutput($output)
    {
        if (!empty($output) && !is_string($output)) {
            throw new \InvalidArgumentException('Output must be a valid string.');
        }

        $this->output = $output;
    }

    /**
     * Explicitly set a render output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Flag whether there is something to render.
     *
     * @return boolean
     */
    public function hasOutput()
    {
        return !empty($this->output) && !empty($this->layout) && !empty($this->view);
    }

    /**
     * Renders a template and displays the results.
     *
     * @return string
     */
    public function render()
    {
        $this->assignRenderVariables();

        if (!$this->output) {
            if ($this->layout) {
                $outputForView = null;

                if ($this->view) {
                    $outputForView = $this->render->parse($this->view);
                }

                $this->render->assignVariable('_content_view_yield', $outputForView);
                $this->output = $this->render->parse($this->layout);
            } elseif ($this->view) {
                $this->output = $this->render->parse($this->view);
            }
        }

        return $this->output;
    }

    /**
     * Renders a template and returns the results.
     *
     * @param string $template Template file.
     *
     * @throws \InvalidArgumentException Template file missing.
     *
     * @return mixed
     */
    public function parse($template)
    {
        $template = $this->configuration['templates'] . $template . '.' . $this->getTemplateExtension();

        if (!file_exists($template)) {
            throw new \InvalidArgumentException('Template file missing: ' . $template);
        }

        $this->assignRenderVariables();

        return $this->render->parse($template);
    }

    /**
     * For easier access. Retrieve all render output assets.
     *
     * @return array
     */
    public function assets()
    {
        return $this->assets->all();
    }

    /**
     * Assigns render engine content variables.
     *
     * @return void
     */
    private function assignRenderVariables()
    {
        foreach ($this->variables as $name => $value) {
            $this->render->assignVariable($name, $value);
        }
    }
}
