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

class AdminGroups extends Magic {

    /* Вывод списка групп пользователей и создание новой группы */
    public function show() {
        if (Arr::getPost('new') == 1 && !empty($_POST['Name'])) {
            $n = Tool::cleanAllow($_POST['Name'], ' ');
            $this->_db->insert_query('benutzer_gruppen', array('Name_Intern' => $n, 'Name' => $n));
            $NewId = $this->_db->insert_id();
            $row_nr = $this->_db->fetch_object("SELECT Rechte_Admin, Rechte FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '2' AND Sektion = '1' LIMIT 1");
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_sektionen");
            while ($row = $sql->fetch_object()) {
                $insert_array = array(
                    'Gruppe'       => $NewId,
                    'Sektion'      => $row->Id,
                    'Rechte'       => $row_nr->Rechte,
                    'Rechte_Admin' => $row_nr->Rechte_Admin);
                $this->_db->insert_query('berechtigungen', $insert_array);
            }
            $this->__object('AdminCore')->script('save');
        }

        $groups = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_benutzer_gruppen ORDER BY Id ASC");
        while ($row = $sql->fetch_object()) {
            $uc = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS UCount FROM " . PREFIX . "_benutzer WHERE Gruppe='$row->Id'");
            $row->uc = $uc;
            $groups[] = $row;
        }
        $sql->close();
        $this->_view->assign('groups', $groups);
        $this->_view->assign('title', $this->_lang['Groups_Name']);
        $this->_view->content('/groups/overview.tpl');
    }

    /* Обзор групп */
    public function showGroup() {
        $sections = array();
        $sql = $this->_db->query("SELECT Name, Id FROM " . PREFIX . "_sektionen ORDER BY Id ASC");
        while ($row = $sql->fetch_object()) {
            $row->Groups = $this->__object('AdminCore')->groups();
            $sections[] = $row;
        }
        $sql->close();
        $this->_view->assign('sections', $sections);
        $this->_view->assign('title', $this->_lang['Groups_PermissionsEdit']);
        $this->_view->content('/groups/permission_overview.tpl');
    }

    /* Редактирование прав доступа групп */
    public function editGroup($id, $area) {
        $id = intval($id);
        $area = intval($area);
        if (Arr::getPost('save') == 1 && $id != 1) {
            $res_page = Arr::getPost('Rechte') >= 1 ? $this->_db->escape(implode(',', $_POST['Rechte'])) : '';
            $res_admin = Arr::getPost('Rechte_Admin') >= 1 ? $this->_db->escape(implode(',', $_POST['Rechte_Admin'])) : '';
            if ($id == 2) {
                $res_admin = '';
            }
            $ws = (Arr::getPost('setall') == 1) ? 'Sektion!=0' : "Sektion='$area'";
            $this->_db->query("UPDATE " . PREFIX . "_berechtigungen SET Rechte='$res_page', Rechte_Admin='$res_admin' WHERE Gruppe='" . $id . "' AND {$ws}");
            SX::output("<script type=\"text/javascript\">
			parent.frames.document.getElementById('group_" . $area . "_" . $id . "').innerHTML='" . $this->_lang['Groups_PermsSaved'] . "';
			parent.$.fn.colorbox.close();
			</script>", true);
        }
        $admin_lang = $_SESSION['admin_lang'];
        $modul = $this->modules();
        $perms_vars = $this->files($modul, $admin_lang, 'Page');
        $admin_vars = $this->files($modul, $admin_lang, 'Admin');

        foreach ($modul as $file) {
            if (is_file(MODUL_DIR . '/' . $file . '/lang/' . $admin_lang . '/admin.txt')) {
                $this->_view->configLoad(MODUL_DIR . '/' . $file . '/lang/' . $admin_lang . '/admin.txt', 'Admin');
            }
        }
        $this->_view->configLoad(LANG_DIR . '/' . $admin_lang . '/admin.txt');
        $this->_lang = $lang = $this->_view->getConfigVars();
        $this->_view->assign('lang', $lang);
        $this->_view->assign('perms_page', $perms_vars);
        $this->_view->assign('perms_admin', $admin_vars);

        $res = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_berechtigungen WHERE Gruppe='" . $id . "' AND Sektion='" . $area . "' LIMIT 1");
        $res->Page = explode(',', $res->Rechte);
        $res->Admin = explode(',', $res->Rechte_Admin);
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Groups_PermissionsEdit']);
        $this->_view->content('/groups/permission_edit.tpl');
    }

    /* Грузим файлы с правами */
    protected function files($modul, $admin_lang, $type) {
        $this->_view->clearConfig();
        $this->_view->configLoad(LANG_DIR . '/' . $admin_lang . '/perms.txt', $type);
        foreach ($modul as $file) {
            if (is_file(MODUL_DIR . '/' . $file . '/lang/' . $admin_lang . '/perms.txt')) {
                $this->_view->configLoad(MODUL_DIR . '/' . $file . '/lang/' . $admin_lang . '/perms.txt', $type);
            }
        }
        return $this->_view->getConfigVars();
    }

    /* Получаем массив с именами инсталированных модулей */
    protected function modules() {
        $active = SX::get('admin_active');
        $modul = array();
        $files = glob(MODUL_DIR . '/*', GLOB_ONLYDIR);
        foreach ($files as $file) {
            $file = basename($file);
            if (!empty($active[$file])) {
                $modul[] = $file;
            }
        }
        return $modul;
    }

    /* Удаление группы пользователей */
    public function delete($id) {
        $id = intval($id);
        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Gruppe = '3' WHERE Gruppe = '" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_gruppen WHERE Id = '" . $id . "'");
        $this->_db->query("ALTER TABLE " . PREFIX . "_benutzer_gruppen AUTO_INCREMENT =1");
        $this->__object('Redir')->redirect('index.php?do=groups&sub=useroverview');
    }

    /* Редактирование группы пользователей */
    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            if ($id == 2) {
                $array = array(
                    'Name'           => Arr::getPost('Name'),
                    'Name_Intern'    => Arr::getPost('Name_Intern'),
                    'VatByCountry'   => Arr::getPost('VatByCountry'),
                    'Rabatt'         => Arr::getPost('Rabatt'),
                    'ShopAnzeige'    => Arr::getPost('ShopAnzeige'),
                    'Avatar'         => '',
                    'MaxAnlagen'     => Arr::getPost('MaxAnlagen'),
                    'MaxZeichenPost' => Arr::getPost('MaxZeichenPost'),
                );
            } else {
                $array = array(
                    'Name'             => Arr::getPost('Name'),
                    'Name_Intern'      => Arr::getPost('Name_Intern'),
                    'VatByCountry'     => Arr::getPost('VatByCountry'),
                    'Rabatt'           => Arr::getPost('Rabatt'),
                    'ShopAnzeige'      => Arr::getPost('ShopAnzeige'),
                    'Avatar_Default'   => Arr::getPost('Avatar_Default'),
                    'Avatar_B'         => Arr::getPost('Avatar_B'),
                    'Avatar_H'         => Arr::getPost('Avatar_H'),
                    'MaxPn'            => Arr::getPost('MaxPn'),
                    'MaxPn_Zeichen'    => Arr::getPost('MaxPn_Zeichen'),
                    'MaxAnlagen'       => Arr::getPost('MaxAnlagen'),
                    'MaxZeichenPost'   => Arr::getPost('MaxZeichenPost'),
                    'SysCode_Signatur' => Arr::getPost('SysCode_Signatur'),
                    'Signatur_Laenge'  => Arr::getPost('Signatur_Laenge'),
                    'Signatur_Erlaubt' => Arr::getPost('Signatur_Erlaubt'),
                );
                if (Arr::getPost('Avdel') == 1) {
                    $array['Avatar'] = '';
                }
                if (!empty($_POST['newImg_1'])) {
                    $array['Avatar'] = Arr::getPost('newImg_1');
                }
            }
            $this->_db->update_query('benutzer_gruppen', $array, "Id = '" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer_gruppen WHERE Id = '" . $id . "' LIMIT 1");

        if (!empty($res->Avatar) && is_file(UPLOADS_DIR . '/avatars/' . $res->Avatar)) {
            $this->_view->assign('avatar', '<img src="../uploads/avatars/' . $res->Avatar . '" alt="" border="0" />');
        }

        if (!is_writable(UPLOADS_DIR . '/avatars/')) {
            chmod(UPLOADS_DIR . '/avatars/', 0777);
        }
        if (!is_writable(UPLOADS_DIR . '/avatars/')) {
            $this->_view->assign('not_writable', 1);
        }
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Groups_Edit']);
        $this->_view->content('/groups/groupform.tpl');
    }

}
