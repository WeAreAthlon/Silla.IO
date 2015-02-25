<?php
/**
 * Render Adapter Interface.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Render\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Render\Interfaces;

/**
 * Cache Adapter Interface.
 */
interface Adapter
{
    /**
     * Assigns a template variable.
     *
     * @param string $name  Name of the variable.
     * @param mixed  $value Value of the variable.
     *
     * @return void
     */
    public function assignVariable($name, $value);

    /**
     * Retrieves an assigned template variable.
     *
     * @param string $name Name of the variable.
     *
     * @return mixed
     */
    public function getVariable($name);

    /**
     * Removes an assigned template variable.
     *
     * @param string $name Name of the variable.
     *
     * @return void
     */
    public function removeVariable($name);

    /**
     * Parse a template file via the rending engine.
     *
     * @param string $template Path to template to parse.
     *
     * @return mixed
     */
    public function parse($template);

    /**
     * Retrieves default templates file extension.
     *
     * @return string
     */
    public function getDefaultTemplatesFileExtension();
}
