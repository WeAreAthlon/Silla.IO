<?php
/**
 * Mailing Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;

/**
 * Mailing Activity Helper definition.
 */
class Mailer
{
    /**
     * Sends an email via standard Unix /usr/bin/sendmail.
     *
     * @param array   $params Array containing the sender, receiver, and mail content details.
     * @param boolean $auth   Enable authentication (only applicable for SMTP mailer type).
     *
     * @static
     *
     * @return boolean
     */
    public static function send(array $params, $auth = true)
    {
        switch (Core\Config()->MAILER['type']) {
            case 'SMTP':
                $is_sent = self::processSmtp($params, $auth);
                break;

            case 'Sendmail':
            default:
                $is_sent = self::processSendmail($params);
                break;
        }

        return $is_sent;
    }

    /**
     * Sends email via standard PHP mailer.
     *
     * @param array $params Sending options.
     *
     * @access private
     * @static
     * @uses   \PHPMailer
     *
     * @return boolean
     */
    private static function processSendmail(array $params)
    {
        $mail = new \PHPMailer();
        $mail->IsSendmail();
        $mail->CharSet = 'UTF-8';

        try {
            if (isset($params['reply_mail'], $params['reply_name'])) {
                $mail->AddReplyTo($params['reply_mail'], $params['reply_name']);
            }

            if (is_array($params['to_mail'])) {
                foreach ($params['to_mail'] as $recipient_address) {
                    $mail->AddAddress($recipient_address, $params['to_name']);
                }
            } else {
                $mail->AddAddress($params['to_mail'], $params['to_name']);
            }

            if (isset($params['images']) && is_array($params['images'])) {
                foreach ($params['images'] as $image) {
                    $mail->AddEmbeddedImage($image['url'], $image['name']);
                }
            }

            /* Custom Headers */
            if (isset($params['additional_headers']) && is_array($params['additional_headers'])) {
                foreach ($params['additional_headers'] as $additional_header) {
                    $mail->addCustomHeader($additional_header);
                }
            }

            $mail->SetFrom($params['from_mail'], $params['from_name']);
            $mail->Subject = $params['subject'];
            $mail->MsgHTML($params['content']);

            $mail->Send();
        } catch (\phpmailerException $e) {
            trigger_error($e->errorMessage());
        } catch (\Exception $e) {
            trigger_error($e->getMessage());
        }

        return true;
    }


    /**
     * Sends email via SMTP Mail Server.
     *
     * @param array   $params Sending options.
     * @param boolean $auth   Flag for usage of SMTP authentication or not.
     *
     * @access private
     * @static
     * @uses   Config
     * @uses   \PHPMailer
     *
     * @return boolean
     */
    private static function processSmtp(array $params, $auth = true)
    {
        $mail = new \PHPMailer();
        $mail->IsSMTP();

        /*
         * Enables SMTP debug information (for testing)
         * 1 = errors and messages
         * 2 = messages only
         */

        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = $auth;
        $mail->CharSet = 'UTF-8';
        $mail->Host = Core\Config()->MAILER['credentials']['SMTP']['host'];
        $mail->Port = Core\Config()->MAILER['credentials']['SMTP']['port'];
        $mail->Username = Core\Config()->MAILER['credentials']['SMTP']['user'];
        $mail->Password = Core\Config()->MAILER['credentials']['SMTP']['password'];
        //$mail->SMTPSecure = 'tls';

        $mail->SetFrom($params['from_mail'], $params['from_name']);

        if (isset($params['reply_mail'], $params['reply_name'])) {
            $mail->AddReplyTo($params['reply_mail'], $params['reply_name']);
        }

        $mail->Subject = $params['subject'];
        $mail->AltBody = strip_tags($params['content']);

        $mail->MsgHTML($params['content']);

        if (is_array($params['to_mail'])) {
            foreach ($params['to_mail'] as $recipient_address) {
                $mail->AddAddress($recipient_address, $params['to_name']);
            }
        } else {
            $mail->AddAddress($params['to_mail'], $params['to_name']);
        }

        if (isset($params['images']) && is_array($params['images'])) {
            foreach ($params['images'] as $image) {
                $mail->AddEmbeddedImage($image['url'], $image['name']);
            }
        }

        /* Custom Headers */
        if (isset($params['additional_headers']) && is_array($params['additional_headers'])) {
            foreach ($params['additional_headers'] as $additional_header) {
                $mail->addCustomHeader($additional_header);
            }
        }

        try {
            $mail->Send();
        } catch (\phpmailerException $e) {
            trigger_error($e->errorMessage());
        } catch (\Exception $e) {
            trigger_error($e->getMessage());
        }

        return true;
    }
}
