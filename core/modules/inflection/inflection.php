<?php
/**
 * Multilingual inflection that transforms words from singular to plural, underscore to camel case, and more.
 *
 * Currently the implementation is wrapping ICanBoogie\Inflector;
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Inflection;

use ICanBoogie\Inflector;

/**
 * Router Class definition.
 */
final class Inflection
{
    /**
     * Reference to the current instance of the Registry object.
     *
     * @var object
     * @access private
     * @static
     */
    private static $instance = null;

    /**
     * Inflector instance.
     *
     * @var Inflector;
     */
    private $inflector;

    /**
     * Constructor, does nothing.
     *
     * @access private
     */
    private function __construct()
    {
        $this->inflector = Inflector::get(Inflector::DEFAULT_LOCALE);
    }

    /**
     * Pluralizes a word.
     *
     * @example 'child' -> 'children'
     *
     * @param string $word Word to pluralize.
     *
     * @return string
     */
    public function pluralize($word)
    {
        return $this->inflector->pluralize($word);
    }

    /**
     * Get a singular for a word.
     *
     * @example 'children' - 'child'
     *
     * @param string $word Word to singularize.
     *
     * @return string
     */
    public function singularize($word)
    {
        return $this->inflector->singularize($word);
    }

    /**
     * Camelizes a word.
     *
     * @example 'active_model' -> 'ActiveModel'
     *
     * @param string  $word              Word to camelizes.
     * @param boolean $upCaseFirstLetter Whether to capitalize or not the fist letter.
     *
     * @return string
     */
    public function camelize($word, $upCaseFirstLetter = true)
    {
        if ($upCaseFirstLetter) {
            return $this->inflector->camelize($word, Inflector::UPCASE_FIRST_LETTER);
        } else {
            return $this->inflector->camelize($word, Inflector::DOWNCASE_FIRST_LETTER);
        }

    }

    /**
     * Underscore a word.
     *
     * @example 'ActiveModel' -> 'active_model'
     *
     * @param string $word Word to underscore.
     *
     * @return string
     */
    public function underscore($word)
    {
        return $this->inflector->underscore($word);
    }

    /**
     * Humanizes a word.
     *
     * @example 'employee_salary' -> "Employee salary"
     *
     * @param string $word Word to underscore.
     *
     * @return string
     */
    public function humanize($word)
    {
        return $this->inflector->humanize($word);
    }

    /**
     * Titleize a word.
     *
     * @example 'man from the boondocks' -> "Man From The Boondocks"
     *
     * @param string $word Word to titleize.
     *
     * @return string
     */
    public function titleize($word)
    {
        return $this->inflector->titleize($word);
    }

    /**
     * Gets an Ordinal for a number.
     *
     * @example '1' -> 'st'
     *
     * @param integer $number Number to process.
     *
     * @return string
     */
    public function ordinal($number)
    {
        return $this->inflector->ordinal($number);
    }

    /**
     * Ordinalize a number.
     *
     * @example '1' -> '1st'
     *
     * @param integer $number Number to process.
     *
     * @return string
     */
    public function ordinalize($number)
    {
        return $this->inflector->ordinalize($number);
    }

    /**
     * Cloning of Registry is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is a singleton.', E_USER_ERROR);
    }

    /**
     * Returns an instance of the Inflection object.
     *
     * @access public
     * @static
     * @final
     *
     * @return Inflection
     */
    final public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Inflection();
        }

        return self::$instance;
    }

}
