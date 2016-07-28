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

        $this->title = $title;
        $this->logo = $logo;
        $this->font = $font;

        $this->SetTitle($title);
        $this->setFontSubsetting(true);
        $this->setCellPaddings(3, 3, 3, 3);
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
        if ($this->logo && file_exists($this->logo)) {
            $this->Image($this->logo, 10, 7, 15, 0, '', '', 'T', false, 300, 'R');
        }

        $this->SetFont($this->font, 'B', 16);
        $this->SetY(15);
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
        $csv = fopen($file, 'r');
        $data  = array();

        while (($line = fgetcsv($csv)) !== false) {
            $data[] = $line;
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

                $this->MultiCell($this->tableCellWidth, 6, $col, 'LR', 'L', $fill);
                $cell_heights[] = $this->GetY();
                $this->SetXY($current_x + $this->tableCellWidth, $current_y);
            }

            $this->SetY(max($cell_heights));

            $fill = !$fill;

            if ($this->GetY() >= 260) {
                $this->AddPage();
                $this->generateTableCaption($header);
                $this->SetY($this->GetY() + 9);
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
