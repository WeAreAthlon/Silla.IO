<?php
/**
 * Help Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

/**
 * Class Help Controller definition.
 */
class Help extends CMS
{
    /**
     * Init method.
     */
    public function __construct()
    {
        parent::__construct();

        $this->skipAclFor(array('create', 'edit', 'delete', 'export', 'show'));
    }
}
