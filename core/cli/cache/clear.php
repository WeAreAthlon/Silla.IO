<?php
/**
 * Clear Cache functionality.
 *
 * @package    Silla.IO
 * @subpackage Core\CLI
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2016, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\CLI\Cache;

use Core;

/**
 * Class Clear definition.
 */
final class Clear
{
    /**
     * Cache types.
     *
     * @var array
     */
    private static $CACHES = array(
        'system' => 'temp/cache',
        'assets' => array(
            'public/cms/assets/css',
            'public/cms/assets/js',
            'public/app/assets/css',
            'public/app/assets/js',
        ),
    );

    /**
     * Clears all cache types.
     *
     * @param string $cache Cache type.
     *
     * @return void
     */
    public static function run($cache = null)
    {
        $cwd = getcwd() . DIRECTORY_SEPARATOR;

        if ($cache) {
            if (isset(self::$CACHES[$cache])) {
                if (is_array(self::$CACHES[$cache])) {
                    foreach (self::$CACHES[$cache] as $path) {
                        self::deleteFolder($cwd . $path);
                    } 
                } else {
                    self::deleteFolder($cwd . self::$CACHES[$cache]);
                }
            }
        } else {
            foreach (self::$CACHES as $type => $path) {
                if (is_array($path)) {
                    foreach ($path as $p) {
                        self::deleteFolder($cwd . $p);
                    } 
                } else {
                    self::deleteFolder($cwd . $path);
                }
            }
        }
    }

    /**
     * Remove all files and directories.
     *
     * Open the source directory to read in files.
     *
     * @param string $path File path.
     *
     * @return void
     */
    private static function deleteFolder($path)
    {
        $i = new \DirectoryIterator($path);

        foreach ($i as $f) {
            if ($f->isFile()) {
                unlink($f->getRealPath());
            } elseif (!$f->isDot() && $f->isDir()) {
                self::deleteFolder($f->getRealPath());
                rmdir($f->getRealPath());
            }
        }
    }
}
