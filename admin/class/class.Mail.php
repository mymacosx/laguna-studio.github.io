<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

include_once (SX_DIR . '/lib/phpmailer/class.phpmailer.php');

class Mail extends Magic {

    protected $settings;

    public function __construct() {
        $this->settings = SX::get('system');
        $this->mailer = new PHPMailer;
    }

    /* Замена текста для подписи */
    protected function replace() {
        $link = SHEME_URL . $_SERVER['HTTP_HOST'];
        $text = $this->mailer->ContentType == 'text/html' ? '<br /><br />' . $this->settings['Mail_Fuss_HTML'] : PE . $this->settings['Mail_Fuss'];
        $array = array(
            '%%COMPANY%%'  => $this->settings['Firma'],
            '%%TOWN%%'     => $this->settings['Stadt'],
            '%%ZIP%%'      => $this->settings['Zip'],
            '%%STREET%%'   => $this->settings['Strasse'],
            '%%ADRESS%%'   => $this->settings['Strasse'],
            '%%MAIL%%'     => $this->settings['Mail_Absender'],
            '%%TELEFON%%'  => $this->settings['Telefon'],
            '%%FAX%%'      => $this->settings['Fax'],
            '%%HTTP%%'     => ($this->mailer->ContentType == 'text/html' ? '<a href="' . $link . '">' . $link . '</a>' : $link),
            '%%INN%%'      => $this->settings['Inn'],
            '%%KPP%%'      => $this->settings['Kpp'],
            '%%BIK%%'      => $this->settings['Bik'],
            '%%BANK%%'     => $this->settings['Bank'],
            '%%KSCHET%%'   => $this->settings['Kschet'],
            '%%RSCHET%%'   => $this->settings['Rschet'],
            '%%OWNER%%'    => $this->settings['Seitenbetreiber'],
            '%%DIREKTOR%%' => $this->settings['Seitenbetreiber'],
            '%%BUH%%'      => $this->settings['Buh']);
        return $this->_text->replace($text, $array);
    }

    /* Прицепляем подпись */
    protected function type($val, $body) {
        $text = ($val == '1') ? $this->replace() : '';
        return $body . $text;
    }

    /* Настройки для отправки через SMTP */
    protected function smtp() {
        $this->mailer->Host = $this->settings['Mail_Host'];
        $this->mailer->Port = $this->settings['Mail_Port'];
        $this->mailer->SMTPDebug = SX::get('configs.debug') == '1' ? 4 : false;
        $this->mailer->SMTPSecure = $this->settings['Mail_Type_Auth'] == 'not' ? '' : $this->settings['Mail_Type_Auth'];
        if ($this->settings['Mail_Auth'] == '1') {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->settings['Mail_Username'];
            $this->mailer->Password = $this->settings['Mail_Passwort'];
        }
    }

    /* Прицепляем вложения */
    protected function attachment($attach) {
        foreach ((array) $attach as $attachment) {
            if (is_file(UPLOADS_DIR . '/attachments/' . $attachment)) {
                $this->mailer->AddAttachment(UPLOADS_DIR . '/attachments/' . $attachment);
            }
        }
    }

    /* Отправляем письмо */
    public function send($globs, $to, $to_name, $text, $subject = '', $fromemail = '', $from = '', $type = '', $attach = '', $html = '', $prio = 3) {
        if (empty($to)) {
            return false;
        }
        $this->mailer->PluginDir = SX_DIR . '/lib/phpmailer/';
        $this->mailer->SetLanguage($this->settings['Land'], SX_DIR . '/lib/phpmailer/language/');
        $this->mailer->CharSet = CHARSET;
        $this->mailer->Mailer = $this->settings['Mail_Typ'];
        $this->mailer->ContentType = ($this->settings['Mail_Content'] == 'text/plain' || $type == 'text') ? 'text/plain' : 'text/html';
        $this->mailer->ContentType = ($html == 1) ? 'text/html' : $this->mailer->ContentType;
        $this->mailer->WordWrap = $this->settings['Mail_WordWrap'];
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $this->type($globs, $text);
        $this->mailer->From = Tool::isMail($fromemail) ? Tool::cleanMail($fromemail) : $this->settings['Mail_Absender'];
        $this->mailer->FromName = !empty($from) ? $from : $this->settings['Mail_Name'];
        $this->mailer->Sender = $this->mailer->From;
        $this->mailer->Priority = !empty($prio) ? $prio : 3;
        $this->mailer->AddReplyTo($this->mailer->From, $this->mailer->FromName);
        $this->mailer->AddAddress(trim($to), (!empty($to_name) ? trim($to_name) : ''));

        switch ($this->mailer->Mailer) {
            case 'sendmail':
                $this->mailer->Sendmail = $this->settings['Mail_Sendmailpfad'];
                break;
            case 'smtp':
                $this->smtp();
                break;
        }

        if (!empty($attach)) {
            $this->attachment($attach);
        }
        if (!$this->mailer->Send()) {
            SX::syslog('Ошибка отправки почты!' . PE . 'Текст: ' . $this->mailer->ErrorInfo, '3', $_SESSION['benutzer_id']);
            return false;
        }
        $this->mailer->ClearAddresses();
        $this->mailer->ClearAllRecipients();
        $this->mailer->ClearReplyTos();
        $this->mailer->ClearAttachments();
        $this->mailer->ClearCustomHeaders();
        return true;
    }

}