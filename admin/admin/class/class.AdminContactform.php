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

class AdminContactform extends Magic {

    public function show() {
        $cforms = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_kontakt_form ORDER BY Titel1 ASC");

        $this->_view->assign('cforms', $cforms);
        $this->_view->assign('title', $this->_lang['ContactForms']);
        $this->_view->content('/contactforms/overview.tpl');
    }

    public function save() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Aktiv'] as $cid => $fieldid) {
                $this->_db->query("UPDATE " . PREFIX . "_kontakt_form SET Aktiv = '" . $this->_db->escape($_POST['Aktiv'][$cid]) . "' WHERE Id = '" . intval($cid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('new') == 1) {
            if (!empty($_POST['Name1'])) {
                $Name1 = $_POST['Name1'];
                $Name2 = empty($_POST['Name2']) ? $Name1 : $_POST['Name2'];
                $Name3 = empty($_POST['Name3']) ? $Name1 : $_POST['Name3'];

                switch ($_POST['Typ']) {
                    case 'dropdown':
                    case 'checkbox':
                    case 'radio':
                        if (empty($_POST['Werte'])) {
                            $_POST['Werte'] = 'Option 1,Option 2,Option 3';
                        }
                        $_POST['Pflicht'] = '0';
                        $_POST['Email'] = '0';
                        $_POST['Zahl'] = '0';
                        break;

                    case 'textarea':
                        $_POST['Zahl'] = '0';
                        $_POST['Email'] = '0';
                        break;
                }

                $insert_array = array(
                    'Form_Id' => $id,
                    'Typ'     => Arr::getPost('Typ'),
                    'Pflicht' => Arr::getPost('Pflicht'),
                    'Posi'    => Arr::getPost('Posi'),
                    'Zahl'    => Arr::getPost('Zahl'),
                    'Email'   => trim(Arr::getPost('Email')),
                    'Werte'   => Arr::getPost('Werte'),
                    'Name1'   => $Name1,
                    'Name2'   => $Name2,
                    'Name3'   => $Name3);
                $this->_db->insert_query('kontakt_form_felder', $insert_array);
            }

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую контактную форму (' . $id . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('Redir')->redirect('index.php?do=contactforms&sub=edit&id=' . $id . '&noframes=1');
        }

        if (Arr::getPost('save') == 1) {
            $E_Name1 = $_POST['E_Titel1'];
            $E_Name2 = empty($_POST['E_Titel2']) ? $E_Name1 : $_POST['E_Titel2'];
            $E_Name3 = empty($_POST['E_Titel3']) ? $E_Name1 : $_POST['E_Titel3'];

            $E_Intro1 = $_POST['E_Intro1'];
            $E_Intro2 = empty($_POST['E_Intro2']) ? $E_Intro1 : $_POST['E_Intro2'];
            $E_Intro3 = empty($_POST['E_Intro3']) ? $E_Intro1 : $_POST['E_Intro3'];

            $array = array(
                'Titel1'      => $E_Name1,
                'Titel2'      => $E_Name2,
                'Titel3'      => $E_Name3,
                'Intro1'      => $E_Intro1,
                'Intro2'      => $E_Intro2,
                'Intro3'      => $E_Intro3,
                'Email'       => Arr::getPost('E_Email'),
                'Email2'      => Arr::getPost('E_Email2'),
                'Anlage'      => Arr::getPost('E_Anlage'),
                'Aktiv'       => Arr::getPost('E_Aktiv'),
                'Button_Name' => Arr::getPost('Button_Name'),
                'Gruppen'     => implode(',', $_POST['E_Gruppen']),
            );
            $this->_db->update_query('kontakt_form', $array, "Id = '" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил контактную форму (' . $id . ')', '0', $_SESSION['benutzer_id']);

            foreach ($_POST['Name1'] as $cid => $fieldid) {
                if (!empty($_POST['Name1'][$cid])) {
                    $Name1 = $_POST['Name1'][$cid];
                    $Name2 = (empty($_POST['Name2'][$cid])) ? $Name1 : $_POST['Name2'][$cid];
                    $Name3 = (empty($_POST['Name3'][$cid])) ? $Name1 : $_POST['Name3'][$cid];

                    switch ($_POST['Typ'][$cid]) {
                        case 'dropdown':
                        case 'checkbox':
                        case 'radio':
                            if (empty($_POST['Werte'][$cid])) {
                                $_POST['Werte'][$cid] = 'Option 1,Option 2,Option 3';
                            }
                            $_POST['Pflicht'][$cid] = '0';
                            $_POST['Email'][$cid] = '0';
                            $_POST['Zahl'][$cid] = '0';
                            break;

                        case 'textarea':
                            $_POST['Zahl'][$cid] = '0';
                            $_POST['Email'][$cid] = '0';
                            break;
                    }

                    $array = array(
                        'Typ'     => $_POST['Typ'][$cid],
                        'Pflicht' => $_POST['Pflicht'][$cid],
                        'Posi'    => $_POST['Posi'][$cid],
                        'Zahl'    => $_POST['Zahl'][$cid],
                        'Email'   => $_POST['Email'][$cid],
                        'Werte'   => $_POST['Werte'][$cid],
                        'Name1'   => $Name1,
                        'Name2'   => $Name2,
                        'Name3'   => $Name3
                    );
                    $this->_db->update_query('kontakt_form_felder', $array, "Id = '" . intval($cid) . "'");
                    if (isset($_POST['del'][$cid]) && $_POST['del'][$cid] == 1) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_kontakt_form_felder WHERE Id = '" . intval($cid) . "'");
                    }
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $fields = array();
        $query = "SELECT * FROM " . PREFIX . "_kontakt_form WHERE Id = '" . $id . "' ; ";
        $query .= "SELECT * FROM " . PREFIX . "_kontakt_form_felder WHERE Form_Id = '" . $id . "' ORDER BY Posi ASC";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $res = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $fields[] = $row;
                }
                $result->close();
            }
        }

        $res->Groups = explode(',', $res->Gruppen);

        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('res', $res);
        $this->_view->assign('fields', $fields);
        $this->_view->assign('title', $this->_lang['ContactFormEdit']);
        $this->_view->content('/contactforms/edit_new.tpl');
    }

    public function copy($id) {
        $id = intval($id);
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_kontakt_form WHERE Id = '" . $id . "' LIMIT 1");
        $insert_array = array(
            'Datum'       => time(),
            'Autor'       => $res->Autor,
            'Titel1'      => $res->Titel1 . ' - ' . $this->_lang['ContactForms_copy_C'],
            'Titel2'      => $res->Titel2,
            'Titel3'      => $res->Titel3,
            'Intro1'      => $res->Intro1,
            'Intro2'      => $res->Intro2,
            'Intro3'      => $res->Intro3,
            'Email'       => $res->Email,
            'Email2'      => $res->Email2,
            'Anlage'      => $res->Anlage,
            'Aktiv'       => $res->Aktiv,
            'Gruppen'     => $res->Gruppen,
            'Button_Name' => $res->Button_Name);
        $this->_db->insert_query('kontakt_form', $insert_array);
        $newid = $this->_db->insert_id();
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал контактную форму (' . $res->Titel1 . ')', '0', $_SESSION['benutzer_id']);

        $query = $this->_db->query("SELECT * FROM " . PREFIX . "_kontakt_form_felder WHERE Form_Id = '" . $id . "'");
        while ($row = $query->fetch_object()) {
            $insert_array = array(
                'Form_Id' => $newid,
                'Typ'     => $row->Typ,
                'Pflicht' => $row->Pflicht,
                'Posi'    => $row->Posi,
                'Zahl'    => $row->Zahl,
                'Email'   => $row->Email,
                'Werte'   => $row->Werte,
                'Name1'   => $row->Name1,
                'Name2'   => $row->Name2,
                'Name3'   => $row->Name3
            );
            $this->_db->insert_query('kontakt_form_felder', $insert_array);
        }
        $query->close();
        $this->__object('AdminCore')->backurl();
    }

    public function delete($id) {
        $id = intval($id);
        $this->_db->query("DELETE FROM " . PREFIX . "_kontakt_form WHERE Id = '" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_kontakt_form_felder WHERE Form_Id = '" . $id . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил контактную форму (' . $id . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function add() {
        if (Arr::getPost('new') == 1) {
            $E_Name1 = Arr::getPost('E_Titel1');
            $E_Intro1 = Arr::getPost('E_Intro1');
            $insert_array = array(
                'Datum'       => time(),
                'Autor'       => $_SESSION['benutzer_id'],
                'Titel1'      => $E_Name1,
                'Titel2'      => (empty($_POST['E_Titel2']) ? $E_Name1 : $_POST['E_Titel2']),
                'Titel3'      => (empty($_POST['E_Titel3']) ? $E_Name1 : $_POST['E_Titel3']),
                'Intro1'      => $E_Intro1,
                'Intro2'      => (empty($_POST['E_Intro2']) ? $E_Intro1 : $_POST['E_Intro2']),
                'Intro3'      => (empty($_POST['E_Intro3']) ? $E_Intro1 : $_POST['E_Intro3']),
                'Email'       => trim(Arr::getPost('E_Email')),
                'Email2'      => trim(Arr::getPost('E_Email2')),
                'Anlage'      => Arr::getPost('E_Anlage'),
                'Aktiv'       => Arr::getPost('E_Aktiv'),
                'Gruppen'     => implode(',', Arr::getPost('E_Gruppen')),
                'Button_Name' => Arr::getPost('Button_Name'));
            $this->_db->insert_query('kontakt_form', $insert_array);
            $newid = $this->_db->insert_id();
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новую контактную форму: ' . $E_Name1, '0', $_SESSION['benutzer_id']);

            switch ($_POST['Typ']) {
                case 'dropdown':
                case 'checkbox':
                case 'radio':
                    $_POST['Pflicht'] = $_POST['Email'] = $_POST['Zahl'] = '0';
                    break;

                case 'textarea':
                    $_POST['Zahl'] = $_POST['Email'] = '0';
                    break;
            }

            $Name1 = Arr::getPost('Name1');
            $insert_array = array(
                'Form_Id' => $newid,
                'Typ'     => Arr::getPost('Typ'),
                'Pflicht' => Arr::getPost('Pflicht'),
                'Posi'    => Arr::getPost('Posi'),
                'Zahl'    => Arr::getPost('Zahl'),
                'Email'   => trim(Arr::getPost('Email')),
                'Werte'   => Arr::getPost('Werte'),
                'Name1'   => $Name1,
                'Name2'   => (empty($_POST['Name2']) ? $Name1 : $_POST['Name2']),
                'Name3'   => (empty($_POST['Name3']) ? $Name1 : $_POST['Name3']));
            $this->_db->insert_query('kontakt_form_felder', $insert_array);
            $this->__object('Redir')->redirect('index.php?do=contactforms&sub=edit&id=' . $newid . '&noframes=1');
        }
        $this->_view->assign('new', 1);
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('title', $this->_lang['ContactForms_new']);
        $this->_view->content('/contactforms/edit_new.tpl');
    }

}