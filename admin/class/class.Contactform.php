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

class Contactform extends Magic {

    protected $template = 'contactform.tpl';

    /* Метод реплейза в контенте тегов контактных форм */
    public function get($text) {
        return preg_replace_callback('!\[CONTACT:([\d]*)\]!iu', array($this, 'load'), $text);
    }

    /* Метод получения из базы данных контактой формы */
    public function load($match) {
        if (!empty($match[1])) {
            $LC = Arr::getSession('Langcode', 1);
            $res = $this->_db->cache_fetch_object("SELECT Titel1 AS DefTitel, Titel" . $LC . " AS Titel, Intro" . $LC . " AS Intro, Email, Anlage, Gruppen, Button_Name, Id FROM " . PREFIX . "_kontakt_form WHERE Aktiv='1' AND Id='" . intval($match[1]) . "' LIMIT 1");
            if (is_object($res)) {
                $groups = explode(',', $res->Gruppen);
                if (!empty($res->Gruppen) && in_array(Arr::getSession('user_group'), $groups)) {
                    $felder = array();
                    $res_form = $this->_db->query("SELECT Name1 AS DefName, Werte, Id, Pflicht, Typ, Zahl, Email, Name" . $LC . " AS Name FROM " . PREFIX . "_kontakt_form_felder WHERE Form_Id='" . intval($res->Id) . "' ORDER BY Posi ASC");
                    while ($row_form = $res_form->fetch_object()) {
                        switch ($row_form->Typ) {
                            default:
                            case 'textfield':
                                $row_form->OutElemVal = $row_form->Werte;
                                break;

                            case 'radio':
                            case 'checkbox':
                            case 'dropdown':
                                $row_form->OutElemVal = explode(',', $row_form->Werte);
                                break;
                        }
                        $row_form->Name = (!$row_form->Name) ? $row_form->DefName : $row_form->Name;
                        $felder[] = $row_form;
                    }
                    $res_form->close();

                    $this->__object('Captcha')->start(); // Инициализация каптчи

                    $tpl_array = array(
                        'form_attachment' => $res->Anlage,
                        'form_intro'      => $res->Intro,
                        'contact_button'  => $res->Button_Name,
                        'form_id_raw'     => $res->Id,
                        'form_id'         => 'form_' . $res->Id,
                        'contact_fields'  => $felder,
                        'contact_title'   => (!$res->Titel ? $res->DefTitel : $res->Titel));
                    $this->_view->assign($tpl_array);
                    $template = !empty($match[2]) ? $match[2] : $this->template;
                    return $this->_view->fetch(THEME . '/contact/' . $template);
                }
            }
        }
        return '';
    }

    /* Метод отправки контактной формы */
    public function send() {
        $result = 'false';
        if (!empty($_POST['id']) && $this->__object('Captcha')->check() === true) {
            reset($_POST);
            $res = DB::get()->cache_fetch_object("SELECT * FROM " . PREFIX . "_kontakt_form WHERE Id='" . intval($_POST['id']) . "' LIMIT 1");
            if (is_object($res)) {
                $ignore = array(
                    '__hname',
                    '__hmail',
                    'submit',
                    'id',
                    'scode',
                    'files',
                    'mailcopy',
                    'secure_uniqid',
                    $this->__object('Captcha')->input(),
                );
                $newtext = '';
                foreach ($_POST as $key => $val) {
                    if (!empty($val) && !in_array($key, $ignore)) {
                        if (is_array($val)) {
                            $val = implode(', ', $val);
                        }
                        $newtext .= $key . ":\r\n" . $this->_text->substr($val, 0, 10000) . "\r\n-------------------------\r\n";
                    }
                }
                $ti = str_replace(array('__REF__', '__IP__'), array($this->__object('Redir')->referer(true), IP_USER), SX::$lang['Contact_textInf']);
                $text_info = "\r\n" . $ti . "\r\n";

                $attach = $this->upload(); // Загружаем вложения к письму

                if (!empty($newtext)) {
                    $result = 'true';
                    $hname = Arr::getPost('__hname');
                    $hmail = Arr::getPost('__hmail');
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $res->Email,
                        'to_name'   => '',
                        'text'      => $newtext . $text_info,
                        'subject'   => SX::$lang['Contact_subject'] . ' (' . $res->Titel1 . ')',
                        'fromemail' => $hmail,
                        'from'      => $hname,
                        'type'      => 'text',
                        'attach'    => $attach,
                        'html'      => '',
                        'prio'      => 3));

                    if (!empty($res->Email2)) {
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $res->Email2,
                            'to_name'   => '',
                            'text'      => $newtext . $text_info,
                            'subject'   => SX::$lang['Contact_subject'] . ' (' . $res->Titel1 . ')',
                            'fromemail' => $hmail,
                            'from'      => $hname,
                            'type'      => 'text',
                            'attach'    => $attach,
                            'html'      => '',
                            'prio'      => 3));
                    }

                    if ($_SESSION['loggedin'] == 1 && Arr::getPost('mailcopy') == 1) {
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $hmail,
                            'to_name'   => $hname,
                            'text'      => SX::$lang['MailCopyPre'] . "\r\n------------------------------\r\n" . $newtext . $text_info,
                            'subject'   => SX::$lang['Contact_subject'] . ' (' . $res->Titel1 . ') ' . SX::$lang['MailCopy'],
                            'fromemail' => SX::get('system.Mail_Absender'),
                            'from'      => SX::get('system.Mail_Name'),
                            'type'      => 'text',
                            'attach'    => $attach,
                            'html'      => '',
                            'prio'      => 3));
                    }
                }
            }
        }
        SX::output($result, true);
    }

    /* Метод загрузки вложений */
    protected function upload() {
        $options = array(
            'rand'   => true,
            'type'   => 'file',
            'size'   => 2048,
            'result' => 'list',
            'upload' => '/uploads/attachments/',
            'input'  => 'files',
        );
        return SX::object('Upload')->load($options);
    }

}