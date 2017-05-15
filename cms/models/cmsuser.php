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
use CMS;

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
     * Belongs To associations definition.
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
     * Additional validations.
     *
     * @return void
     */
    public function afterValidate()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->setError('email', 'invalid_format');
        }

        if (!$this->getError('password') && !Core\Utils::validatePassword($this->password)) {
            $this->setError('password', 'weak');
        }
    }

    /**
     * Store current password for later reference.
     *
     * @return void
     */
    public function beforePopulate()
    {
        $this->currentPassword = $this->password;
    }

    /**
     * Hashes the new password if the password is different form the stored one.
     *
     * @return void
     */
    public function beforeSave()
    {
        if ($this->password !== $this->currentPassword) {
            $this->password = Crypt::hash($this->password);
        }
    }

    public function beforeValidate()
    {
        $this->is_active      = 1;
        $this->login_attempts = 0;
    }

    /**
     * Checks whether the user has ownership over an entity model.
     *
     * @param string $entityModel Entity Model class name.
     *
     * @return boolean
     */
    public function owns($entityModel)
    {
        $ownership = $this->role()->ownership;

        return isset($ownership[$entityModel]) && $ownership[$entityModel];
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param integer $size   Size in pixels, defaults to 80px [ 1 - 2048 ].
     * @param string  $type   Default image-set to use [ 404 | mm | identicon | monsterid | wavatar ].
     * @param string  $rating Maximum rating (inclusive) [ g | pg | r | x ].
     *
     * @return string
     */
    public function getAvatar($size = 70, $type = 'mm', $rating = 'g')
    {
        return CMS\Helpers\CMSUsers::getGravatar($this->email, $size, $type, $rating);
    }

    public static function icrementLoginAttempts($userEmail)
    {
        Core\DB()->query('UPDATE '.self::$tableName.' SET `login_attempts` '
                . '= `login_attempts` + 1 WHERE `email` = ?', array($userEmail));
    }
}
