<?php
/**
 * Export Helper.
 *
 * @package    Silla.IO
 * @subpackage CMS\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Helpers;

/**
 * Export Helper Class definition.
 */
class Export
{
    /**
     * Formats data for MS Excel document.
     *
     * @param array   $data          Array of objects.
     * @param array   $fields        Fields to export.
     * @param boolean $table_caption Whether to show table caption.
     * @param string  $filename      Name of the file.
     * @param string  $date_format   Date format.
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function exportToExcel(
        array $data,
        array $fields,
        $table_caption = true,
        $filename = 'export',
        $date_format = 'Ymd'
    ) {
        header('Content-Type: application/vnd.ms-excel');
        header('Cache-control: private');
        header('Pragma: public');

        $filename = str_replace(' ', '', $filename);

        $date = date($date_format);

        header("Content-Disposition: attachment; filename=\"{$filename}_{$date}.xls\"");
        header("Content-Title: {$filename} Data Output - Run on {$date}");

        $result = '<table>';

        /* Print Table Headers */
        if ($table_caption) {
            $result .= '<tr>';

            foreach (array_values($fields) as $field) {
                $result .= "<td><b>{$field}</b></td>";
            }

            $result .= '</tr>';
        }

        foreach ($data as $entry) {
            $result .= '<tr>';

            foreach (array_keys($fields) as $field) {
                $result .= "<td>{$entry->{$field}}</td>";
            }

            $result .= '</tr>';
        }

        $result .= '</table>';

        echo $result;
        exit;
    }

    /**
     * Exports data in CSV format and stores it in existing file.
     *
     * @param array   $query       Example ['fields' => '', 'query' => '', 'params' => ''].
     * @param string  $csv_file    Representation of absolute path to a file with writable permission over it.
     * @param integer $walker_step Number of paginated query rows per iteration.
     * @param boolean $caption     Whether to print or not leading row as caption.
     *
     * @access public
     * @static
     * @uses   Core\Base\Model
     * @uses   Core\DB()
     *
     * @return boolean
     */
    public static function populateCsvfileCustomQuery(array $query, $csv_file, $walker_step = 1200, $caption = true)
    {
        $query = array_merge(array('fields' => array(), 'query' => '', 'params' => array()), $query);

        if ($csv_file = fopen($csv_file, 'w')) {
            if ($caption) {
                $fields = array_filter(array_values($query['fields']));
                if (!empty($fields)) {
                    fputcsv($csv_file, $fields);
                }
            }

            $fetched_records_offset = 0;

            while (true) {
                $items = $query['query']->limit($walker_step, $fetched_records_offset)->all();

                foreach ($items as $item) {
                    $row = array();

                    foreach (array_keys($query['fields']) as $field) {
                        $row[] = $item->$field;
                    }

                    fputcsv($csv_file, $row);
                }

                $fetched_records_count  = count($items);
                $fetched_records_offset += $fetched_records_count;

                if ($fetched_records_count < $walker_step) {
                    break;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Buffered output of a csv file for download.
     *
     * @param string  $file       Path to the file.
     * @param integer $chunk_size Size in MB (1M = 1024 * 1024) default is set to 2MB.
     * @param string  $filename   Optional exported file name.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function getCsvBuffered($file, $chunk_size = 2, $filename = null)
    {
        if ($fp = fopen($file, 'r')) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Cache-control: private');
            header('Pragma: public');

            $filename = str_replace(' ', '', $filename ? $filename : basename($file, '.csv'));

            $date = date('Ymd');
            header("Content-Disposition: attachment; filename=\"{$filename}_{$date}.csv\"");
            header("Content-Title: {$filename} Data Output - Run on {$date}");

            while (!feof($fp)) {
                print(fread($fp, $chunk_size << 20));
                ob_flush();
                flush();
            }

            exit(0);
        }

        return false;
    }
}
