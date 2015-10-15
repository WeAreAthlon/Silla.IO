<?php
/**
 * PDF Format Helper.
 *
 * @package    Silla.IO
 * @subpackage CMS\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Helpers;

use Core;

/**
 * Generates PDF document from a set of data.
 */
class PDF extends \TCPDF
{
    /**
     * Title of the document.
     *
     * @var string
     */
    protected $title;

    /**
     * Name of the font used in the document.
     *
     * @var string
     */
    private $font;

    /**
     * Path to the image logo that will be embedded in the document.
     *
     * @var boolean
     */
    private $logo;

    /**
     * Main table cell width in pixels.
     *
     * @var integer
     */
    private $tableCellWidth;

    /**
     * Init method.
     *
     * @param string  $title Title.
     * @param string  $font  Font name.
     * @param boolean $logo  Logo.
     */
    public function __construct($title, $font = 'helvetica', $logo = false)
    {
        parent::__construct();

        $_path = Core\Config()->paths('resources') . 'fonts' . DIRECTORY_SEPARATOR;

        $this->AddFont('freeserif', '', $_path . 'freeserif.php');
        $this->AddFont('freeserif', 'B', $_path . 'freeserifb.php');
        $this->AddFont('freeserif', 'I', $_path . 'freeserifi.php');
        $this->AddFont('freeserif', 'BI', $_path . 'freeserifbi.php');

        $this->title = $title;
        $this->logo = $logo;
        $this->font = $font;

        $this->SetTitle($title);
        $this->setFontSubsetting(true);
    }

    /**
     * Manages the header part of the document.
     *
     * @access public
     *
     * @return void
     */
    public function header()
    {
        if ($this->logo) {
            $this->Image($this->logo, 10, 10, 15, '', '', '', 'T');
        }

        $this->SetFont($this->font, 'B', 16);
        $this->setY(17);
        $this->setX(28);
        $this->SetTextColor(100);
        $this->Cell(0, 20, $this->title, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    /**
     * Manages the footer part of document.
     *
     * @access public
     *
     * @return void
     */
    public function footer()
    {
        $paging = 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
        $this->SetY(-15);
        $this->SetFont($this->font, 'I', 8);
        $this->Cell(0, 10, $paging, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    /**
     * Loads data.
     *
     * @param string $file Absolute file path.
     *
     * @access public
     *
     * @return array
     */
    public function loadData($file)
    {
        $lines = file($file);
        $data  = array();

        foreach ($lines as $line) {
            $fields = array();
            $tmp = explode(',', trim($line));
            foreach ($tmp as $field) {
                $fields[] = trim($field, "'\"");
            }

            $data[] = $fields;
        }

        unset($data[0]);

        return $data;
    }

    /**
     * Sets main content for the document represented in a table view.
     *
     * @param array $header Table header.
     * @param array $data   Table contents.
     *
     * @access public
     *
     * @return void
     */
    public function embedContentTable(array $header, array $data)
    {
        $this->generateTableCaption($header);

        $fill = false;

        foreach ($data as $row) {
            $this->SetFillColor(240);
            $this->SetTextColor(0);
            $this->SetFont($this->font, '', 8);
            $cell_heights = array();

            foreach ($row as $col) {
                $current_y = $this->GetY();
                $current_x = $this->GetX();

                $this->MultiCell(
                    $this->tableCellWidth,
                    6,
                    str_pad($col, $this->tableCellWidth - mb_strlen($col) + 10, ' '),
                    'LR',
                    'L',
                    $fill
                );

                $cell_heights[] = $this->getY();

                $this->SetXY($current_x + $this->tableCellWidth, $current_y);
            }

            $this->SetY(max($cell_heights));

            $fill = !$fill;

            if ($this->getY() >= 260) {
                $this->AddPage();
                $this->generateTableCaption($header);
                $this->setY($this->getY() + 9);
            }
        }
    }

    /**
     * Formats the table caption part.
     *
     * @param array $header Header data.
     *
     * @access private
     *
     * @return void
     */
    private function generateTableCaption(array $header)
    {
        $this->SetY(30);
        $this->SetFillColor(50);
        $this->SetTextColor(255);
        $this->SetDrawColor(255);
        $this->SetLineWidth(0.2);
        $this->SetFont($this->font, 'B', 8);

        $headerCount = count($header);
        $this->tableCellWidth = 190 / $headerCount;

        for ($i = 0; $i < $headerCount; $i++) {
            $this->Cell($this->tableCellWidth, 8, $header[$i], 1, 0, 'L', true);
        }

        $this->Ln();
    }
}
