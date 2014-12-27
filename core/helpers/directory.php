<?php
/**
 * Directory Helper.
 *
 * @package    Silla
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
     * @throws \InvalidArgumentException If directory already exists.
     * @uses   File::getFullPath To format path to file.
     *
     * @return boolean Result of the operation.
     */
    public static function create($path)
    {
        $path = File::getFullPath($path);

        if (file_exists($path)) {
            throw new \InvalidArgumentException('Directory exists.');
        }

        /* path, 0777 is the default mode, true for recursive creation */
        return mkdir($path, 0777, true);
    }

    /**
     * Deletes a directory and all its subdirectories and files.
     *
     * @param string $path Full or relative path to directory.
     *
     * @throws \InvalidArgumentException If no directory path is found.
     * @uses   Core\Config() To get the root path of the framework.
     * @uses   File::getRestrictedPath To format path to file.
     * @uses   File::delete To delete files in directories.
     *
     * @return boolean Result of the operation.
     */
    public static function delete($path)
    {
        $success = true;
        $rootPath = Core\Config()->paths('root');
        $path = File::getRestrictedPath($path);

        if (empty($path) || !is_dir($rootPath . $path)) {
            throw new \InvalidArgumentException('No directory with the given path found.');
        }

        /* Get all items in the specified diectory */
        $items = glob($rootPath . $path . DIRECTORY_SEPARATOR . '*');
        $hiddenItems = glob($rootPath . $path . DIRECTORY_SEPARATOR . '.*');
        $fsItems = $items || $hiddenItems ? array_merge((array)$items, (array)$hiddenItems) : false;

        if ($fsItems) {
            /* Separate directories from files */
            $itemsToDelete = array('directories' => array(), 'files' => array());
            foreach ($fsItems as $fsItems) {
                /* Ignore service files - '.', '..' */
                if ($fsItems[strlen($fsItems) - 1] != '.') {
                    $itemsToDelete[(is_dir($fsItems) ? 'directories' : 'files')][] = $fsItems;
                }
            }
            foreach ($itemsToDelete['files'] as $file) {
                File::delete($file);
            }
            foreach ($itemsToDelete['directories'] as $directory) {
                $success = $success ? self::delete($directory) : $success;
            }
        }

        return $success && is_dir($rootPath . $path) ? rmdir($rootPath . $path) : $success;
    }

    /**
     * Copies a directory to another destination, recursively.
     *
     * @param string $from Full path to directory.
     * @param string $to   Full path to destination directory.
     *
     * @uses   Core\Config() To get the root path of the framework.
     * @uses   File::getRestrictedPath To format path to file.
     * @uses   File::copy To copy files to the destination.
     *
     * @return integer Number of bytes written to the file, or FALSE on failure.
     */
    public static function copy($from, $to)
    {
        $success = true;
        /* Get all items in the specified diectory */
        if ($fsItems = glob(Core\Config()->paths('root') . $from . DIRECTORY_SEPARATOR . '*')) {
            /* Separate directories from files */
            $itemsToCopy = array('directories' => array(), 'files' => array());

            foreach ($fsItems as $fsItems) {
                $itemsToCopy[(is_dir($fsItems) ? 'directories' : 'files')][] = $fsItems;
            }

            foreach ($itemsToCopy['files'] as $file) {
                $destination = str_replace($from, $to, $file);
                $success = $success ? File::copy($destination, $file) : $success;
            }

            foreach ($itemsToCopy['directories'] as $directory) {
                $destination = str_replace($from, $to, $directory);
                $success = $success ? self::copy($directory, $destination) : $success;
            }
        }

        return $success;
    }
}
