<?php
/**
 * Render Assets pipeline processor queue.
 *
 * @package    Silla
 * @subpackage Core\Modules\Render
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\Render;

use Core;

/**
 * Class Assets.
 */
class Assets
{
    /**
     * Assets queue contents.
     *
     * @var array
     * @access protected
     */
    private $assets = array('styles' => array(), 'scripts' => array());

    /**
     * Adds an asset to the queue.
     *
     * @param string|array $assets Assets string paths.
     *
     * @uses addAsset()
     *
     * @return void
     */
    public function add($assets)
    {
        if (is_array($assets)) {
            foreach ($assets as $asset) {
                $this->addAsset($asset);
            }
        } else {
            $this->addAsset($assets);
        }
    }

    /**
     * Removes an asset from the queue.
     *
     * @param string|array $assets Assets string paths.
     *
     * @uses removeAsset()
     *
     * @return void
     */
    public function remove($assets)
    {
        if (is_array($assets)) {
            foreach ($assets as $asset) {
                $this->removeAsset($asset);
            }
        } else {
            $this->removeAsset($assets);
        }
    }

    /**
     * Verifies whether there is at least one asset in the queue.
     *
     * @return boolean
     */
    public function any()
    {
        return !empty($this->assets['styles']) || !empty($this->assets['scripts']);
    }

    /**
     * Get All assets.
     *
     * @return array
     */
    public function all()
    {
        return $this->assets;
    }

    /**
     * Adds an asset to the assets processor queue.
     *
     * Guarantees that the assets queue will be consistent of unique values.
     *
     * @param string $file Asset file to be added.
     *
     * @throws \InvalidArgumentException Asset should be a string representation of the path to the file.
     * @access public
     * @see    pathinfo()
     *
     * @return void
     */
    private function addAsset($file)
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Asset should be a string representation of the path to the file');
        }

        $file_extension = pathinfo($file, PATHINFO_EXTENSION);

        switch ($file_extension) {
            case 'css':
            case 'less':
            case 'scss':
                $type = 'styles';
                break;
            case 'js':
            case 'coffee':
                $type = 'scripts';
                break;
            default:
                $type = 'misc';
                break;
        }

        $this->assets[$type][$file_extension][] = $file;
        $this->assets[$type][$file_extension] = array_unique($this->assets[$type][$file_extension]);
    }

    /**
     * Removes an asset from the assets processor queue.
     *
     * @param string $file File to be removed.
     *
     * @throws \InvalidArgumentException Asset should be a string representation of the path to the file.
     * @access public
     *
     * @return void
     */
    private function removeAsset($file)
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Asset should be a string representation of the path to the file');
        }

        foreach ($this->assets as $type => $assets) {
            foreach ($assets as $ext => $asset) {
                $asset_to_remove = array_search($file, $this->assets[$type][$ext], true);

                if ($asset_to_remove && isset($this->assets[$type][$ext][$asset_to_remove])) {
                    unset($this->assets[$type][$ext][$asset_to_remove]);
                }
            }
        }
    }
}
