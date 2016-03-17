<?php
/**
 * CMS User Model.
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
 * Class CMSUser definition.
 */
class CMSUser extends Base\Model implements Interfaces\TimezoneAwareness
{
    /**
     * Table storage name.
     *
     * @var string
     */
    public static $tableName = 'cms_users';

    /**
     * Has many association definition.
     *
     * @var array
     */
    public $belongsTo = array(
        'role' => array(
            'table' => 'cms_userroles',
            'key' => 'role_id',
            'relative_key' => 'id',
            'class_name' => 'CMS\Models\CMSUserRole',
        ),
    );

    /**
     * Current password.
     *
     * @var string
     */
    private $currentPassword;

    /**
     * Definition of the timezone aware fields.
     *
     * @static
     *
     * @return array
     */
    public static function timezoneAwareFields()
    {
        return array('created_on', 'updated_on', 'login_on');
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->setError('email', 'invalid_format');
        }

        if (!$this->isNewRecord() && $this->getError('password')) {
            $this->removeError('password');
        }

        if ($this->password && !Core\Utils::validatePassword($this->password)) {
            $this->setError('password', 'weak');
        }
    }

    /**
     * @inheritdoc
     */
    public function beforePopulate()
    {
        $this->currentPassword = $this->password;
    }

    /**
     * Hashes the user password.
     *
     * @inheritdoc
     */
    public function afterPopulate()
    {
        if ($this->password !== $this->currentPassword) {
            $this->password = Crypt::hash($this->password);
        }
    }
}
