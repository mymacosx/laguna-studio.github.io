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

class AdminFaq extends Magic {

    public function showSend() {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        $newfaq = $this->_db->fetch_object_all("SELECT *, Name_1 AS Name FROM " . PREFIX . "_faq WHERE Aktiv = '2' AND Sektion = '" . AREA . "' ORDER BY Datum DESC");

        $this->_view->assign('newfaq', $newfaq);
        $this->_view->assign('title', $this->_lang['NewSendFaq']);
        $this->_view->content('/faq/newallfaq.tpl');
    }

    public function editSend($id) {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1 && !empty($_POST['Beschreibung'])) {
            $n1 = Tool::cleanAllow($_POST['Name'], ' !?.,');
            $n2 = (!empty($_POST['Name_2'])) ? Tool::cleanAllow($_POST['Name_2'], ' !?.,') : $n1;
            $n3 = (!empty($_POST['Name_3'])) ? Tool::cleanAllow($_POST['Name_3'], ' !?.,') : $n1;
            $b1 = $_POST['Beschreibung'];
            $array = array(
                'Kategorie'    => $_POST['Kategorie'],
                'Name_1'       => $n1,
                'Name_2'       => $n2,
                'Name_3'       => $n3,
                'Antwort_1'    => $b1,
                'Antwort_2'    => $b1,
                'Antwort_3'    => $b1,
                'Textbilder_1' => base64_decode($_POST['screenshots']),
                'Benutzer'     => $_SESSION['benutzer_id'],
                'Aktiv'        => '1',
                'NewCat'       => '',
            );
            $this->_db->update_query('faq', $array, "Id = '" . $id . "'");
            if (Arr::getPost('sendmail') == '1' && !empty($_POST['autormail'])) {
                $mail_array = array(
                    '__DATUM__' => $_POST['datum'],
                    '__QUEST__' => $n1,
                    '__TEXT__'  => strip_tags(preg_replace("/\[SCREEN:(.*)\]/iu", '', $_POST['Beschreibung'])),
                    '__LINK__'  => BASE_URL . '/index.php?p=faq&action=faq&fid=' . $id . '&area=' . AREA . '&name=' . translit($n1));
                $message = $this->_text->replace($this->_lang['MailFaqSend'], $mail_array);
                $subject = $this->_text->replace($this->_lang['MailFaqSendSubj'], '__LINK__', BASE_URL);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $_POST['autormail'],
                    'to_name'   => '',
                    'text'      => $message,
                    'subject'   => $subject,
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 3));
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' ответил на присланный вопрос F.A.Q. (' . $n1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT *, Name_1 AS Name FROM " . PREFIX . "_faq WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 350, 'Settings'));
        $this->_view->assign('title', $this->_lang['Faq']);
        $this->_view->content('/faq/editsendfaq.tpl');
    }

    public function delCateg($id) {
        if (perm('faq_category')) {
            $id = intval($id);
            $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_faq_kategorie WHERE Id='" . $id . "' LIMIT 1");
            $this->categDel($id);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию F.A.Q. (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    public function editCateg($id) {
        if (!perm('faq_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $Name_1 = $_POST['Name_1'];
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;
            $array = array(
                'Name_1' => $Name_1,
                'Name_2' => $Name_2,
                'Name_3' => $Name_3,
            );
            $this->_db->update_query('faq_kategorie', $array, "Id='" . $id . "'");
            SX::syslog('Пользователь' . $_SESSION['user_name'] . ' отредактировал категорию F.A.Q. (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_faq_kategorie WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/faq/faq_categ.tpl');
    }

    public function addCateg() {
        if (!perm('faq_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $Name_1 = Arr::getPost('Name_1');

            $insert_array = array(
                'Parent_Id' => intval(Arr::getPost('categ')),
                'Name_1'    => $Name_1,
                'Name_2'    => (!empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1),
                'Name_3'    => (!empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1),
                'Sektion'   => $_SESSION['a_area']);
            $this->_db->insert_query('faq_kategorie', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил категорию F.A.Q. (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('new', 1);
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Global_NewCateg']);
        $this->_view->content('/faq/faq_categ.tpl');
    }

    public function showCategs() {
        if (!perm('faq_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Global_Categories'] . ' ' . $this->_lang['Faq']);
        $this->_view->content('/faq/categs.tpl');
    }

    protected function categs($prefix = '') {
        $area = $_SESSION['a_area'];
        $categs = array();
        return $this->loadCategs(0, $prefix, $categs, $area);
    }

    protected function loadCategs($id, $prefix, &$categ, &$area) {
        $query = $this->_db->query("SELECT *, Name_1 AS Name FROM " . PREFIX . "_faq_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY POSI ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $categ[] = $item;
            $this->loadCategs($item->Id, $prefix . ' - ', $categ, $area);
        }
        $query->close();
        return $categ;
    }

    protected function categDel($id) {
        if (!perm('faq_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_faq_kategorie WHERE Parent_Id='" . $id . "'");
        while ($item = $query->fetch_object()) {
            $this->_db->query("DELETE FROM " . PREFIX . "_faq WHERE Kategorie='" . $id . "'");
            $this->categDel($item->Id);
        }
        $query->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_faq_kategorie WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_faq WHERE Kategorie='" . $id . "'");
    }

    public function show() {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $fid) {
                $array = array(
                    'Aktiv'     => $_POST['Aktiv'][$fid],
                    'Position'  => $_POST['Position'][$fid],
                    'Kategorie' => $_POST['Kategorie'][$fid],
                );
                $this->_db->update_query('faq', $array, "Id = '" . intval($fid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('faq', $this->load());
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Faq']);
        $this->_view->content('/faq/faq.tpl');
    }

    public function edit($id) {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $LC = $this->__object('AdminCore')->getLangcode();
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Kategorie'         => $_POST['Kategorie'],
                'Textbilder_' . $LC => base64_decode($_POST['screenshots']),
                'Name_' . $LC       => Arr::getPost('Name'),
                'Antwort_' . $LC    => $_POST['Beschreibung'],
            );
            $this->_db->update_query('faq', $array, "Id = '" . $id . "'");

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал F.A.Q. (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT *,Textbilder_{$LC} as Textbilder,Name_{$LC} AS Name, Antwort_{$LC} AS Beschreibung FROM " . PREFIX . "_faq WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('InlineShots', unserialize($res->Textbilder));
        $this->_view->assign('field_inline', "Textbilder_{$LC}");
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', $res->Beschreibung, 'Beschreibung', 350, 'Settings'));
        $this->_view->assign('title', $this->_lang['Faq']);
        $this->_view->content('/faq/faq_edit.tpl');
    }

    public function add() {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $name = Arr::getPost('Name');
            $insert_array = array(
                'Kategorie'    => Arr::getPost('Kategorie'),
                'Name_1'       => $name,
                'Name_2'       => $name,
                'Name_3'       => $name,
                'Antwort_1'    => $_POST['Beschreibung'],
                'Antwort_2'    => $_POST['Beschreibung'],
                'Antwort_3'    => $_POST['Beschreibung'],
                'Textbilder_1' => base64_decode(Arr::getPost('screenshots')),
                'Datum'        => time(),
                'Benutzer'     => $_SESSION['benutzer_id'],
                'Aktiv'        => 1,
                'Sektion'      => AREA);
            $this->_db->insert_query('faq', $insert_array);
            $id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $options = array(
                'name' => $name,
                'url'  => BASE_URL . '/index.php?p=faq&action=faq&fid=' . $id . '&area=' . AREA . '&name=' . translit($name),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $name,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый F.A.Q. (' . $name . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 350, 'Settings'));
        $this->_view->assign('title', $this->_lang['Faq_new']);
        $this->_view->content('/faq/faq_new.tpl');
    }

    public function delete($id) {
        if (!perm('faq')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $this->_db->query("DELETE FROM " . PREFIX . "_faq WHERE Id='" . $id . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил F.A.Q. (' . $id . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    protected function load() {
        $array = array();
        $sql = $this->_db->query("SELECT *, Name_1 AS visible_title FROM " . PREFIX . "_faq WHERE Sektion = '" . AREA . "' AND Aktiv != '2' ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $row->User = Tool::userName($row->Benutzer);
            $array[] = $row;
        }
        $sql->close();
        return $array;
    }

}
