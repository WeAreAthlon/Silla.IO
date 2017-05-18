<?php
/**
 * Pagination Feature.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Features\Paginator
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Features\Paginator;

use Core;

/**
 * Paginator class definition.
 */
class Paginator
{
    /**
     * Total pages count.
     *
     * @var integer
     */
    public $pagesCount;

    /**
     * Current page.
     *
     * @var Page
     */
    protected $currentPage;

    /**
     * Items count per page.
     *
     * @var integer
     */
    protected $itemsPerPage;

    /**
     * Total items count.
     *
     * @var integer
     */
    protected $itemsTotalCount;

    /**
     * Init method.
     *
     * @param integer $itemsCount   Items count number.
     * @param integer $itemsPerPage Items count number per page.
     * @param integer $currentPage  Current page number.
     */
    public function __construct($itemsCount, $itemsPerPage, $currentPage)
    {
        $this->pagesCount      = ($itemsCount == 0) ? 0 : ceil($itemsCount / $itemsPerPage);
        $this->itemsTotalCount = $itemsCount;
        $this->itemsPerPage    = $itemsPerPage;

        $this->setCurrentPage($currentPage);
    }

    /**
     * Total pages count getter.
     *
     * @access public
     *
     * @return integer
     */
    public function totalPages()
    {
        return $this->pagesCount;
    }

    /**
     * Total retrieved items count getter.
     *
     * @access public
     *
     * @return integer
     */
    public function totalItems()
    {
        return $this->itemsTotalCount;
    }

    /**
     * Items per page counter getter.
     *
     * @access public
     *
     * @return integer
     */
    public function itemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Checks if a pagination page exists.
     *
     * @param mixed $page Pager number.
     *
     * @return boolean
     */
    public function hasPage($page)
    {
        return $page >= 1 && $page <= $this->pagesCount;
    }

    /**
     * Paginates to the first pagination set.
     *
     * @return Page
     */
    public function first()
    {
        return new Page($this, 1);
    }

    /**
     * Paginates to the last pagination set.
     *
     * @return Page
     */
    public function last()
    {
        return new Page($this, $this->pagesCount);
    }

    /**
     * Paginates to the current pagination set.
     *
     * @return Page
     */
    public function current()
    {
        return new Page($this, $this->currentPage);
    }

    /**
     * Paginates to the next pagination set.
     *
     * @return Page
     */
    public function next()
    {
        $nextPage = $this->hasPage($this->currentPage + 1) ? $this->currentPage + 1 : $this->currentPage;

        return new Page($this, $nextPage);
    }

    /**
     * Paginates to the previous pagination set.
     *
     * @return Page
     */
    public function prev()
    {
        $prevPage = $this->hasPage($this->currentPage - 1) ? $this->currentPage - 1 : $this->currentPage;

        return new Page($this, $prevPage);
    }

    /**
     * Paginates to the specified pagination number set.
     *
     * @param integer $page Page number.
     *
     * @return boolean|Page
     */
    public function page($page)
    {
        if (!$this->hasPage($page)) {
            return false;
        }

        return new Page($this, $page);
    }

    /**
     * Pagination range.
     *
     * @param integer $padding Padding count.
     *
     * @return array
     */
    public function range($padding = 1)
    {
        $range = array('first' => 0, 'last' => 0);

        if ($this->pagesCount) {
            $first_in_range =
                ($this->currentPage - $padding) > 1 ? $this->currentPage - $padding : 1;

            $last_in_range =
                ($this->currentPage + $padding) < $this->pagesCount ? $this->currentPage + $padding : $this->pagesCount;

            if (!$this->hasPage($first_in_range)) {
                $last_in_range += $padding - ($this->currentPage + 1);

                while (!$this->hasPage($last_in_range)) {
                    $last_in_range--;
                }
            }

            if (!$this->hasPage($last_in_range)) {
                $first_in_range -= $padding - ($this->pagesCount - $this->currentPage + 1);

                while (!$this->hasPage($first_in_range)) {
                    $first_in_range++;
                }
            }

            $range = array(
                'first' => $this->page($first_in_range),
                'last'  => $this->page($last_in_range),
            );
        }

        return $range;
    }

    /**
     * Pages in range.
     *
     * @param integer $padding Padding count.
     *
     * @return array
     */
    public function pagesInRange($padding = 2)
    {
        $range = $this->range($padding);

        $pages = array();
        for ($i = $range['first']->pageNumber; $i <= $range['last']->pageNumber; $i++) {
            array_push($pages, $this->page($i));
        }

        return $pages;
    }

    /**
     * Sets current page.
     *
     * @param integer $page Page number.
     *
     * @return void
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = $this->hasPage($page) ? $page : 1;
    }
}
