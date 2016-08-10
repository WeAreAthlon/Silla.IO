<?php
/**
 * CMS Help Pages Model.
 *
 * @package    Silla.IO
 * @subpackage CMS\Models
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Models;

use Core\Base;
use Core\Modules\DB\Decorators\Interfaces;

/**
 * Class CMS Help Pages definition.
 */
class CMSHelp extends Base\Model implements Interfaces\TimezoneAwareness, Interfaces\Formatting
{
    /**
     * Table storage name.
     *
     * @var string
     */
    public static $tableName = 'cms_help';

    /**
     * Fields that support formatting.
     *
     * @static
     *
     * @return array
     */
    public static function formattingFields()
    {
        return array(
            'content' => '\Parsedown',
        );
    }
}
