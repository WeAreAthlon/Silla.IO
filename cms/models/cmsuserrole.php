<?php
/**
 * CMS User Role Model.
 *
 * @package    Silla.IO
 * @subpackage CMS\Models
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Models;

use Core;
use Core\Base;
use Core\Modules\Crypt\Crypt;
use Core\Modules\DB\Decorators\Interfaces;

/**
 * Class CMSUserRole definition.
 */
class CMSUserRole extends Base\Model implements Interfaces\Serialization, Interfaces\TimezoneAwareness
{
    /**
     * Table storage name.
     *
     * @var string
     */
    public static $tableName = 'cms_userroles';

    /**
     * Belongs to relation definition.
     *
     * @var array
     */
    protected $hasMany = array(
        'users' => array(
            'table' => 'cms_users',
            'key' => 'id',
            'relative_key' => 'role_id',
            'class_name' => 'CMS\Models\CMSUser'
        ),
    );

    /**
     * Definition of the fields to be serialized.
     *
     * @static
     *
     * @return array
     */
    public static function serializableFields()
    {
        return array(
            'permissions' => 'json'
        );
    }

    /**
     * Before validation hook.
     *
     * @return void
     */
    public function afterValidate()
    {
        if (!$this->isNewRecord()) {
            if (!$this->current_password) {
                $this->errors['current_password'] = 'not_empty';
            } else {
                $currentUser = Core\Registry()->get('current_user');
                $passwordsMatch = Crypt::hashCompare($currentUser->password, $this->current_password);

                if (!$passwordsMatch) {
                    $this->errors['current_password'] = 'mismatch';
                }
            }
        }
    }
}
