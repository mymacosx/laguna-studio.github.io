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

switch (Arr::getRequest('action')) {
    default:
        $seo_array = array(
            'headernav' => SX::$lang['PassLost'],
            'pagetitle' => SX::$lang['PassLost'],
            'content'   => View::get()->fetch(THEME . '/user/lostpassword.tpl'));
        View::get()->finish($seo_array);
        break;

    case 'getnew':
        if (SX::object('Redir')->referer()) {
            SX::setDefine('AJAX_OUTPUT', 1);
            $error = '';
            $email = Arr::getPost('mail');
            if (!Tool::isMail($email)) {
                $error = '<strong>' . SX::$lang['RegE_wrongmail'] . '</strong><br />';
            }

            $email = Tool::cleanMail($email);
            if (!empty($error)) {
                SX::output($error, true);
            } else {
                $temp_pass_raw = Tool::getPass(Tool::random(8), false);
                $temp_pass = Tool::getPass($temp_pass_raw);
                $check = DB::get()->fetch_object("SELECT Id, Benutzername, Geloescht FROM " . PREFIX . "_benutzer WHERE Email='" . DB::get()->escape($email) . "' AND Aktiv='1' LIMIT 1");
                if (is_object($check)) {
                    if ($check->Geloescht != '1') {
                        DB::get()->query("UPDATE " . PREFIX . "_benutzer SET KennwortTemp='" . DB::get()->escape($temp_pass) . "' WHERE Email='" . DB::get()->escape($email) . "'");
                        $mail_array = array(
                            '__WEBSITE__' => BASE_URL . '/',
                            '__LINK__'    => BASE_URL . '/index.php?p=pwlost&email=' . $email . '&pass=' . $temp_pass_raw,
                            '__PASS__'    => $temp_pass_raw);
                        $text = Text::get()->replace(SX::$lang['PassLostTextMail'], $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $email,
                            'to_name'   => $check->Benutzername,
                            'text'      => $text,
                            'subject'   => SX::$lang['PassLost'],
                            'fromemail' => SX::get('system.Mail_Absender'),
                            'from'      => SX::get('system.Mail_Name'),
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 1));
                        SX::output('<br /><strong>' . SX::$lang['PassLostInfMail'] . '</strong><br />', true);
                    } else {
                        SX::output('<br /><strong>' . SX::$lang['NoSendPassMail'] . '</strong><br />', true);
                    }
                } else {
                    SX::output('<br /><strong>' . SX::$lang['RegE_wrongmail'] . '</strong><br />', true);
                }
            }
        } else {
            SX::output('<br /><strong>' . SX::$lang['ErrorReferer'] . '</strong>', true);
        }
        break;

    case 'activate':
        if (SX::object('Redir')->referer()) {
            SX::setDefine('AJAX_OUTPUT', 1);
            $error = '';
            $email = Arr::getPost('mail');
            $pass = Tool::getPass(Arr::getRequest('pass'), false);
            if (!Tool::isMail($email)) {
                $error .= SX::$lang['RegE_wrongmail'] . '<br />';
            }
            if (empty($pass)) {
                $error .= SX::$lang['PassLostNoPass'] . '<br />';
            }

            $email = Tool::cleanMail($email);
            if (empty($error)) {
                $check = DB::get()->cache_fetch_object("SELECT Id, Benutzername FROM " . PREFIX . "_benutzer WHERE Email='" . DB::get()->escape($email) . "' AND KennwortTemp='" . Tool::getPass($pass) . "' AND Aktiv='1' AND Geloescht!='1' LIMIT 1");
                if (!is_object($check)) {
                    $error = SX::$lang['PassLostNoMatch'] . '<br />';
                }
            }

            if (!empty($error)) {
                SX::output('<br /><strong>' . $error . '</strong>', true);
            } else {
                $new_pass = Tool::getPass(Tool::random(8), false);
                $md_pass = Tool::getPass($new_pass);
                DB::get()->query("UPDATE " . PREFIX . "_benutzer SET Kennwort='" . DB::get()->escape($md_pass) . "', KennwortTemp='' WHERE Email='" . DB::get()->escape($email) . "' AND KennwortTemp='" . Tool::getPass($pass) . "'");
                $mail_array = array('__WEBSITE__' => BASE_URL . '/', '__PASS__' => $new_pass);
                $text = Text::get()->replace(SX::$lang['NewPassMailSend'], $mail_array);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $email,
                    'to_name'   => $check->Benutzername,
                    'text'      => $text,
                    'subject'   => SX::$lang['PassLost'],
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
                SX::output('<br /><strong>' . SX::$lang['PassLostOk'] . '</strong>', true);
            }
        } else {
            SX::output('<br /><strong>' . SX::$lang['ErrorReferer'] . '</strong>', true);
        }
        break;
}
