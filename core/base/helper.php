<?php
/**
 * Base Helper class.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;

/**
 * Class Configuration Definition.
 */
abstract class Helper
{
    /**
     * Environment application instance.
     *
     * @var Core\Silla
     */
    protected $environment;

    /**
     * Constructor. Setup application environment instance.
     *
     * @param Core\Silla $environment Application environment instance.
     */
    public function __construct(Core\Silla $environment)
    {
        $this->environment = $environment;
    }
}
