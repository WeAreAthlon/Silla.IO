<?php
/**
 * Attachment Decorator Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB\Decorators\Interfaces;

/**
 * Attachment Decorator for management of file uploads
 */
interface Attachment
{
    /**
     * Attachments fields container.
     *
     * @static
     *
     * @return array
     */
    public static function attachmentsFields();

    /**
     * Full path to the storage location.
     *
     * @param string $name Name of the attachment.
     *
     * @return string
     */
    public function attachmentsStoragePath($name);
}
