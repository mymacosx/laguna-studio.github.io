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

class Faq extends Magic {

    protected $Lc;
    protected $_categs = array();
    protected $_active = false;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    protected function categs($id = 0, $prefix = '') {
        $area = $_SESSION['area'];
        if ($this->_active === false) {
            $this->_active = true;
            $query = $this->_db->query("SELECT *, Name_1 AS Name FROM " . PREFIX . "_faq_kategorie WHERE Sektion = '" . $area . "' ORDER BY Posi ASC");
            while ($item = $query->fetch_object()) {
                $this->_categs[] = $item;
            }
            $query->close();
        }
        $categs = array();
        return $this->listCategs($id, $prefix, $categs, $area);
    }

    protected function listCategs($id, $prefix, &$categ, &$area) {
        $theme = SX::get('options.theme');
        foreach ($this->_categs as $item) {
            if ($item->Parent_Id == $id) {
                $item->visible_title = $prefix . $item->Name;
                $item->alt_title = sanitize($item->Name);
                $item->visible_image = $prefix . '<img class="absmiddle" src="theme/' . $theme . '/images/faq/folder.png" alt="' . $item->alt_title . '" border="0" hspace="2" /> ' . $item->alt_title;
                $item->visible_image_small = $prefix . '<img class="absmiddle" src="theme/' . $theme . '/images/faq/folder_small.png" alt="' . $item->alt_title . '" border="0" hspace="2" /> ' . $item->alt_title;
                $categ[] = $item;
                $this->listCategs($item->Id, '&nbsp;&nbsp;&nbsp;' . $prefix, $categ, $area);
            }
        }
        return $categ;
    }

    public function showcategs() {
        $this->_view->assign('categs', $this->categs());

        $seo_array = array(
            'headernav' => $this->_lang['Faq'],
            'pagetitle' => $this->_lang['Faq'],
            'content'   => $this->_view->fetch(THEME . '/faq/categfaq.tpl'));
        $this->_view->finish($seo_array);
    }

    public function get($id) {
        $id = intval($id);
        $faq = $this->_db->cache_fetch_object("SELECT *, Name_{$this->Lc} AS Name, Name_1 AS DefFaq, Antwort_{$this->Lc} AS text FROM " . PREFIX . "_faq WHERE Id = '$id' AND Aktiv = '1' AND Sektion = '" . AREA . "' LIMIT 1");
        $faq->Name = !empty($faq->Name) ? $faq->Name : $faq->DefFaq;
        $tb = "Textbilder_{$this->Lc}";
        $faq->Textbilder = $faq->$tb;
        $faq->text = $this->__object('Glossar')->get($faq->text);
        $faq->text = !empty($faq->Textbilder) ? Tool::screens($faq->Textbilder, $faq->text) : $faq->text;
        $faq->text = Tool::cleanTags($faq->text, array('screen', 'contact', 'audio', 'video'));
        $this->_view->assign('faq', $faq);
        $this->_view->assign('categs', $this->categs());
        $headernav = '<a href="index.php?p=faq&amp;area=' . AREA . '">' . $this->_lang['Faq'] . '</a>' . $this->_lang['PageSep'] . sanitize($faq->Name);

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($faq->Name . $this->_lang['PageSep'] . $this->_lang['Faq']),
            'generate'  => $faq->Name . ' ' . $faq->text,
            'content'   => $this->_view->fetch(THEME . '/faq/showonefaq.tpl'));
        $this->_view->finish($seo_array);
    }

    public function show() {
        $cid = (isset($_REQUEST['faq_id']) && is_numeric($_REQUEST['faq_id'])) ? intval($_REQUEST['faq_id']) : 0;
        $cat = $this->_db->cache_fetch_object("SELECT *, Name_{$this->Lc} AS Name, Name_1 AS DefFaq FROM " . PREFIX . "_faq_kategorie WHERE Id = '$cid' AND Sektion = '" . AREA . "' LIMIT 1");
        $cat->Name = !empty($cat->Name) ? $cat->Name : $cat->DefFaq;

        $tpl_array = array(
            'cat'    => $cat,
            'faq'    => $this->load(),
            'categs' => $this->categs());
        $this->_view->assign($tpl_array);

        $headernav = '<a href="index.php?p=faq&amp;area=' . AREA . '">' . $this->_lang['Faq'] . '</a>' . $this->_lang['PageSep'] . sanitize($cat->Name);

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($cat->Name . $this->_lang['PageSep'] . $this->_lang['Faq']),
            'content'   => $this->_view->fetch(THEME . '/faq/showfaq.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function load() {
        $array = array();
        $sql = $this->_db->query("SELECT *,Name_1 AS DefFaq, Name_{$this->Lc} AS Faq, Antwort_{$this->Lc} AS Antwort FROM " . PREFIX . "_faq WHERE Sektion = '" . AREA . "' AND Aktiv = '1' ORDER BY Position ASC");
        while ($item = $sql->fetch_object()) {
            $item->Faq = (empty($item->Faq)) ? $item->DefFaq : $item->Faq;
            $item->text = $item->Antwort;
            $tb = "Textbilder_{$this->Lc}";
            $item->Textbilder = $item->$tb;
            $item->text = $this->__object('Glossar')->get($item->text);
            $item->text = (!empty($item->Textbilder)) ? Tool::screens($item->Textbilder, $item->text) : $item->text;
            $item->text = Tool::cleanTags($item->text, array('screen', 'contact', 'audio', 'video'));
            $array[] = $item;
        }
        $sql->close();
        return $array;
    }

    public function mail() {
        if (!permission('faq_sent')) {
            SX::object('Core')->noAccess();
        }
        SX::setDefine('OUT_TPL', 'popup.tpl');
        if (Arr::getRequest('faqsend') == 1) {
            $error = array();
            if (!Tool::isMail($_POST['email'])) {
                $error[] = $this->_lang['RegE_wrongmail'];
            }
            if (empty($_REQUEST['body'])) {
                $error[] = $this->_lang['No_Message'];
            }
            if (SX::object('Captcha')->check($error, true)) {
                $body = $this->_db->escape(Tool::cleanTags(strip_tags($_REQUEST['body']), array('codewidget')));
                $newcateg = Tool::cleanAllow($_REQUEST['newcateg'], ' !?.,');
                $faq_id = intval($_REQUEST['faq_id']);
                $email = Tool::cleanMail($_POST['email']);
                $insert_array = array(
                    'Kategorie' => $faq_id,
                    'Name_1'    => $body,
                    'Datum'     => time(),
                    'Aktiv'     => 2,
                    'Sektion'   => AREA,
                    'Sender'    => $email,
                    'NewCat'    => $newcateg);
                $this->_db->insert_query('faq', $insert_array);

                $mail_array = array('__TEXT__' => $body, '__MAIL__' => $email);
                $message = $this->_text->replace($this->_lang['NewFaqSend'], $mail_array);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => SX::get('system.Mail_Absender'),
                    'to_name'   => SX::get('system.Mail_Name'),
                    'text'      => $message,
                    'subject'   => $this->_lang['NewFaqSendSubj'],
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
            }
        }

        SX::object('Captcha')->start(); // Инициализация каптчи

        $tpl_array = array(
            'sname'  => $this->_lang['New_Guest'],
            'categs' => $this->categs());
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['New_Guest'],
            'pagetitle' => $this->_lang['New_Guest'] . $this->_lang['PageSep'] . $this->_lang['Faq'],
            'content'   => $this->_view->fetch(THEME . '/faq/faq_popup.tpl'));
        $this->_view->finish($seo_array);
    }

}
