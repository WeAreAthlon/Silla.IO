<?php
/**
 * Medium Model.
 *
 * @package    Silla.IO
 * @subpackage cms\Models
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
*/

namespace CMS\Models;

use Core;
use Core\Base;
use CMS;

/**
 * Medium class definition.
 */
class Medium extends Base\Model
{
    /**
     * Table storage name.
     *
     * @var string
     */
    public static $tableName = 'media';

    /**
     * Whether the table we're looking at has a corresponding table, holding internationalized values.
     *
     * @var boolean
     * @access public
     * @static
     */
    public static $isI18n = true;

    /**
     * Before save Hook.
     */
    public function beforeSave()
    {

    }

    /**
     * After save Hook.
     */
    public function afterSave()
    {

    }

    /**
     * After delete Hook.
     */
    public function afterDelete()
    {
        /* Delete the asset from the storage */
        try {
            $storagePath = Core\Config()->getMediaStorageLocation() . CMS\Helpers\Media::getSavePath($this);
            Core\Helpers\Directory::delete($storagePath);
        } catch (\Exception $e) {}
    }
}
