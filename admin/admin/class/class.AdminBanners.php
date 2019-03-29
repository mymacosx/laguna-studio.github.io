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

class AdminBanners extends Magic {

    public function show() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Kategorie'] as $pid => $em) {
                $array = array(
                    'Kategorie'    => $_POST['Kategorie'][$pid],
                    'Aktiv'        => $_POST['Aktiv'][$pid],
                    'Gewicht'      => $_POST['Gewicht'][$pid],
                    'Anzeigen'     => $_POST['Anzeigen'][$pid],
                    'Anzeigen_Max' => $_POST['Anzeigen_Max'][$pid],
                    'Click'        => $_POST['Click'][$pid],
                );
                $this->_db->update_query('banner', $array, "Id='" . intval($pid) . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил баннеры', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $banners = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_banner WHERE Sektion='" . AREA . "' ORDER BY Name ASC");

        $this->_view->assign('banners', $banners);
        $this->_view->assign('banner_categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Banners']);
        $this->_view->content('/banners/banner.tpl');
    }

    public function edit($id) {
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Name'         => Arr::getPost('Name'),
                'Kategorie'    => Arr::getPost('Kategorie'),
                'Aktiv'        => Arr::getPost('Aktiv'),
                'HTML_Code'    => Arr::getPost('HTML_Code'),
                'Gewicht'      => Arr::getPost('Gewicht'),
                'Anzeigen'     => Arr::getPost('Anzeigen'),
                'Anzeigen_Max' => Arr::getPost('Anzeigen_Max'),
            );
            $this->_db->update_query('banner', $array, "Id='" . intval($id) . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал баннер (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_banner WHERE Id='" . intval($id) . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('banner_categs', $this->categs());
        $this->_view->assign('title', $this->_lang['BannersEdit']);
        $this->_view->content('/banners/banner_edit.tpl');
    }

    public function delete($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_banner WHERE Id='" . intval($id) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил баннер (' . Arr::getRequest('name') . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect('index.php?do=banners');
    }

    public function add() {
        if (Arr::getPost('new') == 1) {
            $insert_array = array(
                'Sektion'      => $_SESSION['a_area'],
                'Kategorie'    => Arr::getPost('Kategorie'),
                'Name'         => Arr::getPost('Name'),
                'HTML_Code'    => Arr::getPost('HTML_Code'),
                'Aktiv'        => Arr::getPost('Aktiv'),
                'Gewicht'      => Arr::getPost('Gewicht'),
                'Anzeigen'     => Arr::getPost('Anzeigen'),
                'Anzeigen_Max' => Arr::getPost('Anzeigen_Max'));
            $this->_db->insert_query('banner', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый баннер (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('banner_categs', $this->categs());
        $this->_view->assign('title', $this->_lang['BannersNew']);
        $this->_view->content('/banners/banner_new.tpl');
    }

    public function delCateg($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_banner_kategorie WHERE Id='" . intval($id) . "'");
        $this->_db->query("ALTER TABLE " . PREFIX . "_banner_kategorie  AUTO_INCREMENT =1");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию баннеров (' . Arr::getRequest('name') . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect('index.php?do=banners&sub=categs');
    }

    public function showCateg() {
        if (Arr::getPost('new') == 1) {
            if (!empty($_POST['Name'])) {
                $insert_array = array(
                    'Name'         => Arr::getPost('Name'),
                    'Beschreibung' => Arr::getPost('Beschreibung'),
                    'Sektion'      => $_SESSION['a_area']);
                $this->_db->insert_query('banner_kategorie', $insert_array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую категорию баннеров (' . $_POST['Name'] . ')', '0', $_SESSION['benutzer_id']);
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $pid => $em) {
                if (!empty($_POST['Name'][$pid])) {
                    $array = array(
                        'Name'         => $_POST['Name'][$pid],
                        'Beschreibung' => $_POST['Beschreibung'][$pid],
                    );
                    $this->_db->update_query('banner_kategorie', $array, "Id='" . intval($pid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('banner_categs', $this->categs());
        $this->_view->assign('title', $this->_lang['BannersCategs']);
        $this->_view->content('/banners/banner_categs.tpl');
    }

    protected function categs() {
        $categs = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_banner_kategorie WHERE Sektion='" . $_SESSION['a_area'] . "' ORDER BY Name ASC");
        return $categs;
    }

}
