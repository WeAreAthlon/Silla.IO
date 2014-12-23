<?php
/**
 * Attachment Decorator Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
