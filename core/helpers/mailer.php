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
 * Contains helper methods concerned with sending email.
 */
class Mailer
{
    /**
     * Sends an email via standard Unix /usr/bin/sendmail.
     *
     * @param array   $params Array containing the sender, receiver, and mail content details.
     * @param boolean $auth   Enable authentication (only applicable for SMTP mailer type).
     *
     * @throws \Exception When mail cannot be processed.
     *
     * @return boolean Result TRUE if the email was successfully sent, FALSE otherwise.
     */
    public static function send(array $params, $auth = true)
    {
        switch (Core\Config()->MAILER['type']) {
            case 'SMTP':
                $sent = self::processSmtp($params, $auth);
                break;

            case 'Sendmail':
                $sent = self::processSendmail($params);
                break;
            default:
                $sent = self::processDefault($params);
                break;
        }

        return $sent;
    }

    /**
     * Sends email via standard PHP mailer.
     *
     * @param array $params Sending parameters.
     * @throws \Exception When mail cannot be processed.
     *
     * @return boolean Result TRUE if the email was successfully sent, FALSE otherwise.
     */
    private static function processDefault(array $params)
    {
        $mail = new \PHPMailer(Core\Config()->MAILER['debug']);

        return self::processEmail($mail, $params);
    }


    /**
     * Sends email via standard PHP mailer.
     *
     * @param array $params Sending parameters.
     *
     * @throws \Exception When mail cannot be processed.
     *
     * @return boolean Result TRUE if the email was successfully sent, FALSE otherwise.
     */
    private static function processSendmail(array $params)
    {
        $mail = new \PHPMailer(Core\Config()->MAILER['debug']);
        $mail->IsSendmail();

        return self::processEmail($mail, $params);
    }


    /**
     * Sends email via SMTP Mail Server.
     *
     * @param array   $params Sending parameters.
     * @param boolean $auth   Flag for usage of SMTP authentication or not.
     *
     * @uses Core\Config()
     * @throws \Exception When mail cannot be processed.
     *
     * @return boolean Result TRUE if the email was successfully sent, FALSE otherwise.
     */
    private static function processSmtp(array $params, $auth = true)
    {
        /* Disable authentication if there is no valid SMTP user specified. */
        if ($auth && !Core\Config()->MAILER['credentials']['smtp']['user']) {
            $auth = false;
        }

        $mail = new \PHPMailer(Core\Config()->MAILER['debug']);
        $mail->IsSMTP();

        $mail->Host = Core\Config()->MAILER['credentials']['smtp']['host'];
        $mail->Port = Core\Config()->MAILER['credentials']['smtp']['port'];

        $mail->SMTPAuth = $auth;
        $mail->Username = Core\Config()->MAILER['credentials']['smtp']['user'];
        $mail->Password = Core\Config()->MAILER['credentials']['smtp']['password'];

        /* $mail->SMTPSecure = 'tls'; */

        return self::processEmail($mail, $params);
    }

    /**
     * Process email.
     *
     * @param \PHPMailer $mail   PHPMailer object.
     * @param array      $params Sending parameters.
     *
     * @uses   Core\Config()
     * @throws \Exception When mail cannot be processed.
     *
     * @return boolean Result TRUE if the email was successfully sent, FALSE otherwise.
     */
    private static function processEmail(\PHPMailer $mail, array $params)
    {
        $mail->CharSet = 'UTF-8';

        $mail->SetFrom(key($params['from']), current($params['from']));
        $mail->Subject = $params['subject'];
        $mail->AltBody = strip_tags($params['content']);
        $mail->MsgHTML($params['content']);

        if (isset($params['to']) && is_array($params['to'])) {
            foreach ($params['to'] as $toAddr => $toName) {
                $mail->AddAddress($toAddr, $toName);
            }
        }

        if (isset($params['reply_to']) && is_array($params['reply_to'])) {
            foreach ($params['reply_to'] as $replyAddr => $replyName) {
                $mail->AddReplyTo($replyAddr, $replyName);
            }
        }

        if (isset($params['inline_attachments']) && is_array($params['inline_attachments'])) {
            foreach ($params['inline_attachments'] as $attachmentPath => $contentId) {
                $mail->AddEmbeddedImage($attachmentPath, $contentId);
            }
        }

        if (isset($params['custom_headers']) && is_array($params['custom_headers'])) {
            foreach ($params['custom_headers'] as $headerName => $headerValue) {
                $mail->addCustomHeader($headerName, $headerValue);
            }
        }

        return $mail->Send();
    }
}
