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

class AdminPoll extends Magic {

    public function show() {
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['poll']) as $pollid) {
                $start = $this->__object('AdminCore')->mktime($_POST['Start'][$pollid], 0, 0, 1);
                $end = $this->__object('AdminCore')->mktime($_POST['Ende'][$pollid], 23, 59, 59);

                if ($start > $end) {
                    $start = $end - 100;
                }
                $this->_db->query("UPDATE " . PREFIX . "_umfrage SET Start = '$start', Ende = '$end' WHERE Id = '" . intval($pollid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $polls = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_umfrage WHERE Sektion='" . $_SESSION['a_area'] . "' ORDER BY Id DESC");
        while ($row = $sql->fetch_object()) {
            $row->Users = count(explode(',', $row->IpLog)) - 1;
            $row->Comments = $this->__object('AdminCore')->countComments('poll', $row->Id);
            $polls[] = $row;
        }
        $sql->close();
        $this->_view->assign('polls', $polls);
        $this->_view->assign('title', $this->_lang['Polls']);
        $this->_view->content('/poll/overview.tpl');
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $Titel_1 = $_POST['Titel_1'];
            $Titel_2 = empty($_POST['Titel_2']) ? $Titel_1 : $_POST['Titel_2'];
            $Titel_3 = empty($_POST['Titel_3']) ? $Titel_1 : $_POST['Titel_3'];
            $start = $this->__object('AdminCore')->mktime($_POST['Start'], 0, 0, 1);
            $end = $this->__object('AdminCore')->mktime($_POST['Ende'], 23, 59, 59);

            if ($start > $end) {
                $start = $end - 100;
            }
            $this->_db->query("UPDATE " . PREFIX . "_umfrage SET Aktiv = 0");

            $insert_array = array(
                'Sektion'    => $_SESSION['a_area'],
                'Titel_1'    => $Titel_1,
                'Titel_2'    => $Titel_2,
                'Titel_3'    => $Titel_3,
                'Gruppen'    => implode(',', Arr::getPost('Gruppen')),
                'Start'      => $start,
                'Ende'       => $end,
                'Kommentare' => Arr::getPost('Kommentare'),
                'Multi'      => Arr::getPost('Multi'));
            $this->_db->insert_query('umfrage', $insert_array);
            $new_id = $this->_db->insert_id();
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новое голосование (' . $new_id . ')', '0', $_SESSION['benutzer_id']);

            // Добавляем задание на пинг
            $options = array(
                'name' => $Titel_1,
                'url'  => BASE_URL . '/index.php?p=poll&id=' . $new_id . '&name=' . translit($Titel_1) . '&area=' . AREA,
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $Titel_1,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            $this->__object('Redir')->redirect('index.php?do=poll&sub=edit&id=' . $new_id . '&noframes=1');
        }

        $res->Start = time();
        $res->Ende = mktime(0, 0, 1, date('m') + 3, date('d'), date('Y'));
        $res->Multi = 0;
        $res->Kommentare = 1;
        $this->_view->assign('new', 1);
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('title', $this->_lang['Polls_new']);
        $this->_view->content('/poll/edit_new.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('update_settings') == 1) {
            $Titel_1 = $_POST['Titel_1'];
            $Titel_2 = (empty($_POST['Titel_2'])) ? $Titel_1 : $_POST['Titel_2'];
            $Titel_3 = (empty($_POST['Titel_3'])) ? $Titel_1 : $_POST['Titel_3'];
            $start = $this->__object('AdminCore')->mktime($_POST['Start'], 0, 0, 1);
            $end = $this->__object('AdminCore')->mktime($_POST['Ende'], 23, 59, 59);

            if ($start > $end) {
                $start = $end - 100;
            }

            $array = array(
                'Titel_1'    => $Titel_1,
                'Titel_2'    => $Titel_2,
                'Titel_3'    => $Titel_3,
                'Gruppen'    => implode(',', $_POST['Gruppen']),
                'Start'      => $start,
                'Ende'       => $end,
                'Kommentare' => Arr::getPost('Kommentare'),
                'Multi'      => Arr::getPost('Multi'),
            );
            $this->_db->update_query('umfrage', $array, "Id = '" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил голосование (' . $id . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1) {
            if (!empty($_POST['Frage_1'])) {
                $Frage_1 = $_POST['Frage_1'];
                $Frage_2 = empty($_POST['Frage_2']) ? $Frage_1 : $_POST['Frage_2'];
                $Frage_3 = empty($_POST['Frage_3']) ? $Frage_1 : $_POST['Frage_3'];

                $insert_array = array(
                    'UmfrageId' => $id,
                    'Frage_1'   => $Frage_1,
                    'Frage_2'   => $Frage_2,
                    'Frage_3'   => $Frage_3,
                    'Farbe'     => Arr::getPost('Farbe'),
                    'Position'  => Arr::getPost('Position'));
                $this->_db->insert_query('umfrage_fragen', $insert_array);
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил голосование (' . $id . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('update') == 1) {
            foreach ($_POST['poll'] as $cid => $pid) {
                if (!empty($_POST['Frage_1'][$cid])) {
                    $Frage_1 = $_POST['Frage_1'][$cid];
                    $Frage_2 = (empty($_POST['Frage_2'][$cid])) ? $Frage_1 : $_POST['Frage_2'][$cid];
                    $Frage_3 = (empty($_POST['Frage_3'][$cid])) ? $Frage_1 : $_POST['Frage_3'][$cid];

                    $array = array(
                        'Frage_1'  => $Frage_1,
                        'Frage_2'  => $Frage_2,
                        'Frage_3'  => $Frage_3,
                        'Farbe'    => $_POST['Farbe'][$cid],
                        'Position' => $_POST['Position'][$cid],
                    );
                    $this->_db->update_query('umfrage_fragen', $array, "Id = '" . intval($cid) . "'");
                }

                if ($_POST['del'][$cid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_umfrage_fragen WHERE Id='" . intval($cid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_umfrage WHERE Sektion='" . $_SESSION['a_area'] . "' AND Id='" . $id . "' LIMIT 1");
        $res->Groups = explode(',', $res->Gruppen);

        $items = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='" . $res->Id . "' ORDER BY Position ASC");

        $this->_view->assign('items', $items);
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Polls_edit']);
        $this->_view->content('/poll/edit_new.tpl');
    }

    public function delete($id) {
        $id = intval($id);
        $this->_db->query("DELETE FROM " . PREFIX . "_umfrage WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='poll' AND Objekt_Id='" . $id . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил голосование (' . $id . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function active($id, $op = '') {
        switch ($op) {
            default:
            case 'open':
                $dba = "SET Aktiv='1'";
                break;
            case 'close':
                $dba = "SET Aktiv='0'";
                break;
        }
        $this->_db->query("UPDATE " . PREFIX . "_umfrage SET Aktiv='0' WHERE Sektion='" . $_SESSION['a_area'] . "'");
        $this->_db->query("UPDATE " . PREFIX . "_umfrage {$dba} WHERE Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function clean($id) {
        $id = intval($id);
        $this->_db->query("UPDATE " . PREFIX . "_umfrage SET IpLog='', UserLog='' WHERE Id='" . $id . "'");
        $this->_db->query("UPDATE " . PREFIX . "_umfrage_fragen SET Hits='' WHERE UmfrageId='" . $id . "'");
        $this->__object('AdminCore')->backurl();
    }

}
