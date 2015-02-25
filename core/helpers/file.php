<?php
/**
 * File Helper.
 *
 * @package    Silla
 * @subpackage Core\Helpers;
 * @author     Krum Motsov <krum@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Helpers;

use Core;

/**
 * Contains helper methods concerned with file manipulation.
 */
class File
{
    /**
     * @var array Errors container.
     */
    protected static $errors = array();

    /**
     * @var array Mime types list.
     */
    public static $mimetypeList = array(
        '323' => 'text/h323',
        'acx' => 'application/internet-property-stream',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asf' => 'video/x-ms-asf',
        'asr' => 'video/x-ms-asf',
        'asx' => 'video/x-ms-asf',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'axs' => 'application/olescript',
        'bas' => 'text/plain',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'c' => 'text/plain',
        'cat' => 'application/vnd.ms-pkiseccat',
        'cdf' => 'application/x-cdf',
        'cer' => 'application/x-x509-ca-cert',
        'class' => 'application/octet-stream',
        'clp' => 'application/x-msclip',
        'cmx' => 'image/x-cmx',
        'cod' => 'image/cis-cod',
        'cpio' => 'application/x-cpio',
        'crd' => 'application/x-mscardfile',
        'crl' => 'application/pkix-crl',
        'crt' => 'application/x-x509-ca-cert',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'csv' => 'text/plain',
        'dcr' => 'application/x-director',
        'der' => 'application/x-x509-ca-cert',
        'dir' => 'application/x-director',
        'dll' => 'application/x-msdownload',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'dvi' => 'application/x-dvi',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'etx' => 'text/x-setext',
        'evy' => 'application/envoy',
        'exe' => 'application/octet-stream',
        'fif' => 'application/fractals',
        'flr' => 'x-world/x-vrml',
        'gif' => 'image/gif',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'h' => 'text/plain',
        'hdf' => 'application/x-hdf',
        'hlp' => 'application/winhlp',
        'hqx' => 'application/mac-binhex40',
        'hta' => 'application/hta',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htt' => 'text/webviewhtml',
        'ico' => 'image/x-icon',
        'ief' => 'image/ief',
        'iii' => 'application/x-iphone',
        'ins' => 'application/x-internet-signup',
        'isp' => 'application/x-internet-signup',
        'jfif' => 'image/pipeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsf' => 'video/x-la-asf',
        'lsx' => 'video/x-la-asf',
        'lzh' => 'application/octet-stream',
        'm13' => 'application/x-msmediaview',
        'm14' => 'application/x-msmediaview',
        'm3u' => 'audio/x-mpegurl',
        'man' => 'application/x-troff-man',
        'mdb' => 'application/x-msaccess',
        'me' => 'application/x-troff-me',
        'mht' => 'message/rfc822',
        'mhtml' => 'message/rfc822',
        'mid' => 'audio/mid',
        'mny' => 'application/x-msmoney',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'video/mpeg',
        'mp3' => 'audio/mpeg',
        'mpa' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpp' => 'application/vnd.ms-project',
        'mpv2' => 'video/mpeg',
        'ms' => 'application/x-troff-ms',
        'mvb' => 'application/x-msmediaview',
        'nws' => 'message/rfc822',
        'oda' => 'application/oda',
        'p10' => 'application/pkcs10',
        'p12' => 'application/x-pkcs12',
        'p7b' => 'application/x-pkcs7-certificates',
        'p7c' => 'application/x-pkcs7-mime',
        'p7m' => 'application/x-pkcs7-mime',
        'p7r' => 'application/x-pkcs7-certreqresp',
        'p7s' => 'application/x-pkcs7-signature',
        'pbm' => 'image/x-portable-bitmap',
        'pdf' => 'application/pdf',
        'pfx' => 'application/x-pkcs12',
        'pgm' => 'image/x-portable-graymap',
        'pko' => 'application/ynd.ms-pkipko',
        'pma' => 'application/x-perfmon',
        'pmc' => 'application/x-perfmon',
        'pml' => 'application/x-perfmon',
        'pmr' => 'application/x-perfmon',
        'pmw' => 'application/x-perfmon',
        'pnm' => 'image/x-portable-anymap',
        'png' => 'image/png',
        'pot' => 'application/vnd.ms-powerpoint',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'prf' => 'application/pics-rules',
        'ps' => 'application/postscript',
        'pub' => 'application/x-mspublisher',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'image/x-cmu-raster',
        'rgb' => 'image/x-rgb',
        'rmi' => 'audio/mid',
        'roff' => 'application/x-troff',
        'rtf' => 'application/rtf',
        'rtx' => 'text/richtext',
        'scd' => 'application/x-msschedule',
        'sct' => 'text/scriptlet',
        'setpay' => 'application/set-payment-initiation',
        'setreg' => 'application/set-registration-initiation',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'snd' => 'audio/basic',
        'spc' => 'application/x-pkcs7-certificates',
        'spl' => 'application/futuresplash',
        'src' => 'application/x-wais-source',
        'sst' => 'application/vnd.ms-pkicertstore',
        'stl' => 'application/vnd.ms-pkistl',
        'stm' => 'text/html',
        'svg' => 'image/svg+xml',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tgz' => 'application/x-compressed',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'trm' => 'application/x-msterminal',
        'tsv' => 'text/tab-separated-values',
        'txt' => 'text/plain',
        'uls' => 'text/iuls',
        'ustar' => 'application/x-ustar',
        'vcf' => 'text/x-vcard',
        'vrml' => 'x-world/x-vrml',
        'wav' => 'audio/x-wav',
        'wcm' => 'application/vnd.ms-works',
        'wdb' => 'application/vnd.ms-works',
        'wks' => 'application/vnd.ms-works',
        'wmf' => 'application/x-msmetafile',
        'wps' => 'application/vnd.ms-works',
        'wri' => 'application/x-mswrite',
        'wrl' => 'x-world/x-vrml',
        'wrz' => 'x-world/x-vrml',
        'xaf' => 'x-world/x-vrml',
        'xbm' => 'image/x-xbitmap',
        'xla' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xof' => 'x-world/x-vrml',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'z' => 'application/x-compress',
        'zip' => 'application/zip'
    );

    /**
     * Write a string to a file.
     *
     * @param string $path    Full or relative path to file.
     * @param mixed  $content Content to be persisted.
     *
     * @uses self::getFullPath To format path to file.
     * @uses Directory::create To create a directory for the file, if necessary.
     *
     * @return integer Number of bytes written to the file, or FALSE on failure.
     */
    public static function putContents($path, $content)
    {
        $path = self::getFullPath($path);

        if (!is_dir(dirname($path))) {
            Directory::create(dirname($path));
        }

        $result = file_put_contents($path, $content);

        return $result;
    }

    /**
     * Reads a file into a string.
     *
     * @param string $path Full or relative path to file.
     *
     * @throws \InvalidArgumentException If path does not lead to a file.
     * @uses self::getFullPath To format path to file.
     *
     * @return string The read data or FALSE on failure.
     */
    public static function getContents($path)
    {
        $path = self::getFullPath($path);

        if (!is_file($path)) {
            throw new \InvalidArgumentException('Given path does not lead to a file.');
        }

        return file_get_contents($path);
    }

    /**
     * Fetches an external file.
     *
     * @param string $url         URI of the file resource.
     * @param array  $credentials Optional parameter to deal
     *      with Basic Authentication ['user' => '', 'password' => ''].
     *
     * @throws \UnexpectedValueException If PHP extension cURL is not enabled.
     *
     * @return string Binary representation of the file.
     */
    public static function getContentsCurl($url, array $credentials = array())
    {
        if (!function_exists('curl_version')) {
            throw new \UnexpectedValueException('Extension cURL is not enabled.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, implode(':', $credentials));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        $raw = curl_exec($ch);
        curl_close($ch);

        return $raw;
    }

    /**
     * Deletes a file.
     *
     * @param string $path Full or relative path to file.
     *
     * @throws \InvalidArgumentException If path does not lead to a file.
     * @uses self::getFullPath To format path to file.
     *
     * @return boolean Result of the operation.
     */
    public static function delete($path)
    {
        $path = self::getFullPath($path);

        if (!is_file($path)) {
            throw new \InvalidArgumentException('Given path does not lead to a file.');
        } else {
            return unlink($path);
        }
    }

    /**
     * Copies a file to another destination.
     *
     * @param string $from Full or relative path to file.
     * @param string $to   Full or relative path to destination.
     *
     * @uses self::getRestrictedPath To format path to file.
     * @uses self::getContents To get the file for copying.
     * @uses self::putContents To do the copying.
     *
     * @return integer Number of bytes written to the file, or FALSE on failure.
     */
    public static function copy($from, $to)
    {
        $from = self::getRestrictedPath($from);
        $to   = self::getRestrictedPath($to);

        return self::putContents($to, self::getContents($from));
    }

    /**
     * Uploads a file.
     *
     * @param array   $file           Value from $_FILES.
     * @param string  $directory      Full path to storage.
     * @param string  $saveName       Filename in storage.
     * @param boolean $skipValidation Whether to skip file validations.
     * @param array   $allowedTypes   Allowed MIME types of the file.
     * @param integer $maxAllowedSize Maximum allowed file size in kilobytes.
     *
     * @uses self::validate To validate the file by MIME type and size.
     * @uses Directory::create To create a directory for the file, if necessary.
     * @uses self::processUpload To upload the file.
     *
     * @return boolean Result of the operation.
     */
    public static function upload(
        array $file,
        $directory,
        $saveName = null,
        $skipValidation = false,
        array $allowedTypes = array(),
        $maxAllowedSize = 5120
    ) {
        /* validate */
        $result = true;
        if (!$skipValidation) {
            $result = self::validate($file, $allowedTypes, $maxAllowedSize);
        }

        if ($result) {
            /* create the directory if it does not exists */
            if (!is_dir($directory)) {
                $result = (Directory::create($directory)) ? self::processUpload($file, $directory, $saveName) : false;
            } else {
                $result = self::processUpload($file, $directory, $saveName);
            }
        }

        return $result;
    }

    /**
     * Validates a file by allowed size and MIME types.
     *
     * @param array   $file           Value from $_FILES.
     * @param array   $allowedTypes   Allowed MIME types of the file.
     * @param integer $maxAllowedSize Maximum allowed file size in kilobytes.
     *
     * @uses self::isValidMimeType To check if file is one of the allowed MIME types.
     *
     * @todo Distinct the errors from size and type.
     *
     * @return boolean TRUE if file is within allowed max size
     *      and MIME types, FALSE otherwise.
     */
    public static function validate(array $file, array $allowedTypes, $maxAllowedSize)
    {
        $isValidMimeType = false;
        $result = false;

        /* Check the size of the file */
        if ($maxAllowedSize < ($file['size'] / 1024)) {
            return false;
        }

        /* Check if the mime-type is correct */
        foreach ($allowedTypes as $type) {
            $isValidMimeType = self::isValidMimeType($file, $type);

            if ($isValidMimeType) {
                $result = true;
                break;
            }
        }

        if (!$isValidMimeType) {
            $result = false;
        }

        return $result;
    }

    /**
     * Checks if a file is of certain MIME type.
     *
     * @param array  $file Value from $_FILES.
     * @param string $type MIME type to check against.
     *
     * @uses self::getMimeType To retrieve the MIME type of the file.
     *
     * @return boolean Result of the operation.
     */
    private static function isValidMimeType(array $file, $type)
    {
        $valid_mime_types = array(
            'flash_video' => array('video/x-flv'),
            'flash'       => array('application/x-shockwave-flash'),
            'photo'       => array('image/jpeg', 'image/gif', 'image/png'),
            'zip'         => array(
                'application/x-compressed',
                'application/x-zip-compressed',
                'application/zip',
                'multipart/x-zip'
            ),
            'gzip'        => array('application/x-gzip', 'multipart/x-gzip'),
            'pdf'         => array('application/pdf'),
            'ms_word'     => array('application/msword'),
            'ms_excel'    => array(
                'application/excel',
                'application/vnd.ms-excel',
                'application/x-excel',
                'application/x-msexcel'
            ),
            'rtf'         => array(
                'application/rtf',
                'application/x-rtf',
                'text/richtext',
                'text/plain',
                'text/rtf'
            ),
            /* composite types */
            'documents'   => array('pdf', 'ms_word', 'ms_excel', 'rtf'),
            'archive'     => array('zip', 'gzip'),
            'banner'      => array('photo', 'flash'),
            'newsletter_attachment' => array(
                'pdf',
                'ms_word',
                'ms_excel',
                'rtf',
                'photo',
                'flash',
                'zip',
                'gzip'
            )
        );

        if (!is_file($file['tmp_name'])) {
            return false;
        }

        /* Get the mime type of the file */
        $mimeType = self::getMimeType($file);

        $validateAgainst = array();

        /* Manage composite mime-types */
        foreach ($validMimeTypes[$type] as $key) {
            if (array_key_exists($key, $validMimeTypes)) {
                $validateAgainst = array_merge($validateAgainst, $validMimeTypes[$key]);
            }
        }

        if (!$validateAgainst) {
            $validateAgainst = $validMimeTypes[$type];
        }

        return in_array($mimeType, $validateAgainst, true);
    }

    /**
     * Gets the MIME type of a file.
     *
     * @param array $file Value from $_FILES.
     *
     * @throws \InvalidArgumentException If path does not lead to a file.
     * @see    http://php.net/manual/en/fileinfo.installation.php
     *      If using PHP versions prior to 5.3+.
     *
     * @return string MIME Type.
     */
    private static function getMimeType(array $file)
    {
        $mimeType = null;

        if (!is_file($file['tmp_name'])) {
            throw new \InvalidArgumentException('Given path does not lead to a file.');
        }

        /* for >= PHP 5.3.0 and activated PHP extension fileinfo OOP variation */
        if (class_exists('\finfo')) {
            $fileInfo = new \finfo(FILEINFO_MIME);
            $mimeType = $fileInfo->buffer(file_get_contents($file['tmp_name']));
        } elseif (function_exists('finfo_open')) {
            /* for >= PHP 5.3.0 and activated PHP extension fileinfo procedural variation */
            $fhandle = finfo_open(FILEINFO_MIME);
            $mimeType = finfo_file($fhandle, $file['tmp_name']);
        } else {
            /* for < PHP 5.3.0 or not activated fileinfo */
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $mimeType = self::$mimetypeList[$extension];
        }

        if ($mimeType && ($pos = strpos($mimeType, ' '))) {
            $mimeType = substr($mimeType, 0, $pos);
        }

        $mimeType = trim($mimeType, ';');

        return $mimeType;
    }

    /**
     * Uploads a file.
     *
     * @param array  $file      Value from $_FILES.
     * @param string $directory Full path to storage.
     * @param string $saveName  Filename in storage.
     *
     * @throws \InvalidArgumentException If a non-existing directory was supplied.
     * @throws \UnexpectedValueException If errors occured while uploading file.
     * @uses self::filterFilename To filter the filename used in storage.
     *
     * @return boolean Result of the operation.
     */
    private static function processUpload(array $file, $directory, $saveName)
    {
        $saveName = $saveName ? $saveName : self::filterFilename($file['name']);

        /* Check in case Directory::create didn't work */
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('A non-existing directory was supplied: ' . $directory);
        }

        $destination = rtrim($directory, '\/') . DIRECTORY_SEPARATOR . $saveName;

        if (!$result = move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \UnexpectedValueException('Errors occured while uploading file. Result was: ' . $result);
        }

        return $result;
    }

    /**
     * Filters a filename.
     *
     * @param string $filename Name of the file to filter.
     *
     * @return string Filtered filename.
     */
    public static function filterFilename($filename)
    {
        $pathInfo = pathinfo($filename);
        $pathInfo['filename'] = strtolower($pathInfo['filename']);

        return preg_replace(
            '/[^ \w]+/',
            '',
            str_replace(' ', '_', $pathInfo['filename'])
        ) . ($pathInfo['filename'] ? '.' : '') . strtolower($pathInfo['extension']);
    }

    /**
     * Adds an extension to a filename, if not present.
     *      The file extension is taken from uploadedFile.
     *
     * @param string $filename     Name of the file to format.
     * @param string $uploadedFile Name of the file that is currently uploading.
     *
     * @return string Formated filename.
     */
    public static function formatFilename($filename, $uploadedFile)
    {
        $pathInfo = pathinfo($filename);
        $pathInfo['filename'] = strtolower($pathInfo['filename']);

        if (!array_key_exists('extension', $pathInfo)) {
            $meta = pathinfo($uploadedFile);
            $pathInfo['extension'] = $meta['extension'];
        }

        return preg_replace(
            '/[^ \w]+/',
            '',
            str_replace(' ', '_', $pathInfo['filename'])
        ) . ($pathInfo['filename'] ? '.' : '') . strtolower($pathInfo['extension']);
    }

    /**
     * Check uploaded file exists.
     *
     * @param string $filename Name of the file.
     *
     * @todo Perhaps use is_uploaded_file().
     *  But accepts tmp_name only.
     *
     * @return boolean Result of the operation.
     */
    public static function uploadedFileExists($filename)
    {
        return isset($_FILES[$filename]) &&
            is_array($_FILES[$filename]) &&
            !empty($_FILES[$filename]['name']) &&
            !empty($_FILES[$filename]['tmp_name']) &&
            !empty($_FILES[$filename]['size']);
    }

    /**
     * Fetches a restricted path.
     *
     * @param string $path Path to file.
     *
     * @uses self::getFullPath To format path to file.
     *
     * @return string Restricted path.
     */
    public static function getRestrictedPath($path)
    {
        $path = str_replace('..', '', rtrim($path, '\/. '));
        $path = self::getFullPath($path);

        return $path;
    }

    /**
     * Formats a (relative) path to full path.
     *
     * @param string $path File path.
     *
     * @uses Core\Base\Configuration::paths To get root path of framework.
     *
     * @return string Full path.
     */
    public static function getFullPath($path)
    {
        $path = trim(str_replace(Core\Config()->paths('root'), '', $path), '\/');
        $path = Core\Config()->paths('root') . $path;

        return $path;
    }
}
