<?php
/**
 * Help Controller.
 *
 * @package    Silla
 * @subpackage CMS\Controllers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
