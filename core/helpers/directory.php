<?php
/**
 * Directory Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;

/**
 * Contains helper methods concerned with directory manipulation.
 */
class Directory
{
    /**
     * Creates a directory and subdirectories, if not present.
     *
     * @param string $path Full or relative path to directory.
     *
     * @throws \DomainException If directory already exists.
     * @uses   File::getFullPath To format path to file.
     *
     * @return boolean If the directory was successfully created.
     */
    public static function create($path)
    {
        $fullPath = File::getFullPath($path);

        if (is_dir($fullPath)) {
            throw new \DomainException('Directory exists.');
        }

        /* 0777 default mode with recursive creation */
        $created = mkdir($fullPath, 0777, true);

        return $created;
    }

    /**
     * Deletes a directory and all its subdirectories and files.
     *
     * @param string $path Full or relative path to directory.
     *
     * @throws \InvalidArgumentException If path is empty or is not a directory.
     * @uses   File::getFullPath To format path to file.
     * @uses   self::delete To recursively delete subdirectories.
     * @uses   File::delete To delete files in directories.
     *
     * @return boolean Result of the operation.
     */
    public static function delete($path)
    {
        $fullPath = File::getFullPath($path);

        if (empty($path) || !is_dir($fullPath)) {
            throw new \InvalidArgumentException('Path is empty or is not a directory.');
        }

        /* Open a directory handle. */
        $handle = opendir($fullPath);

        /* Read entries from the directory handle. */
        while (($entry = readdir($handle)) !== false) {
            /* Skip directory handles for current and previous directories. */
            if ($entry == '.'  || $entry == '..') {
                continue;
            }

            /* Check whether the current entry is a directory and is not a symbolic link */
            if (is_dir($fullPath . DIRECTORY_SEPARATOR . $entry) && !is_link($fullPath)) {
                $deleted = self::delete($fullPath . DIRECTORY_SEPARATOR . $entry);
            } else {
                $deleted = File::delete($fullPath . DIRECTORY_SEPARATOR . $entry);
            }
        }

        /* Close directory handle */
        closedir($handle);

        $deleted = rmdir($fullPath);

        return $deleted;
    }

    /**
     * Copies a directory to another destination, recursively.
     *
     * @param string $from Full or relative path to directory.
     * @param string $to   Full or relative path to destination directory.
     *
     * @uses   File::getFullPath To format path to file.
     * @uses   self::create To create a directory.
     * @uses   self::copy To recursively copy subdirectories.
     * @uses   File::copy To copy files to the destination.
     *
     * @return boolean If the directory and its contents were copied successfully.
     */
    public static function copy($from, $to)
    {
        $from = File::getFullPath($from);

        /* Create destination directory. */
        $copied = self::create($to);

        /* Open a directory handle. */
        $handle = opendir($from);

        /* Read entries from the directory handle. */
        while (($entry = readdir($handle)) !== false) {
            /* Skip directory handles for current and previous directories. */
            if ($entry == '.'  || $entry == '..') {
                continue;
            }

            /* Check whether the current entry is a directory and is not a symbolic link */
            if (is_dir($from . DIRECTORY_SEPARATOR . $entry) && !is_link($from)) {
                $copied = self::copy(
                    $from . DIRECTORY_SEPARATOR . $entry,
                    $to . DIRECTORY_SEPARATOR . $entry
                );
            } else {
                $copied = File::copy(
                    $from . DIRECTORY_SEPARATOR . $entry,
                    $to . DIRECTORY_SEPARATOR . $entry
                );
            }
        }

        /* Close directory handle */
        closedir($handle);

        return $copied;
    }
}
