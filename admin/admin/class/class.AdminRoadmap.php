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

class AdminRoadmap extends Magic {

    public function start() {
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_roadmap WHERE Sektion = '" . AREA . "' ORDER BY Pos");
        while ($row = $sql->fetch_assoc()) {
            $query = "SELECT Id FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $row['Id'] . "' AND Fertig = '0' AND Sektion = '" . AREA . "' ; ";
            $query .= "SELECT Id FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $row['Id'] . "' AND Fertig = '1' AND Sektion = '" . AREA . "'";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $row['num_ufertig'] = $result->num_rows();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $row['num_fertig'] = $result->num_rows();
                    $result->close();
                }
            }
            $items[] = $row;
        }
        $sql->close();

        if (isset($items)) {
            $this->_view->assign('items', $items);
        }
        $this->_view->assign('title', $this->_lang['Roadmaps'] . ' - ' . $this->_lang['Global_Overview']);
        $this->_view->content('/roadmap/roadmaps.tpl');
    }

    public function editroadmap($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $name = Arr::getRequest('Name');
            if (!empty($id) && !empty($name)) {
                $array = array(
                    'Beschreibung' => Arr::getRequest('Beschreibung'),
                    'Name'         => $name,
                    'Pos'          => Arr::getRequest('Pos'),
                    'Aktiv'        => Arr::getRequest('Aktiv'),
                );
                $this->_db->update_query('roadmap', $array, "Id = '" . $id . "'");
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('formaction', 'index.php?do=roadmap&amp;sub=editroadmap&amp;id=' . $id . '&amp;noframes=1');
        $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_roadmap WHERE Id='" . $id . "' AND Sektion = '" . AREA . "' LIMIT 1");
        $this->_view->assign('item', $row);
        $this->_view->assign('title', $this->_lang['Roadmaps'] . ' - ' . $this->_lang['Global_Overview']);
        $this->_view->content('/roadmap/roadmapform.tpl');
    }

    public function newroadmap() {
        if (Arr::getRequest('action') == 'save') {
            $name = Arr::getRequest('Name');
            if (!empty($name)) {
                $insert_array = array(
                    'Name'         => Tool::cleanAllow($name, ' '),
                    'Beschreibung' => Tool::cleanAllow(Arr::getRequest('Beschreibung'), ' '),
                    'Aktiv'        => 1,
                    'Pos'          => intval(Arr::getRequest('Pos')),
                    'Sektion'      => AREA);
                $this->_db->insert_query('roadmap', $insert_array);

                // Добавляем задание на пинг
                $options = array(
                    'name' => $name,
                    'url'  => BASE_URL . '/index.php?p=roadmap&area=' . AREA,
                    'lang' => $_SESSION['admin_lang']);

                $cron_array = array(
                    'datum'   => time(),
                    'type'    => 'sys',
                    'modul'   => 'ping',
                    'title'   => $name,
                    'options' => serialize($options),
                    'aktiv'   => 1);
                $this->__object('Cron')->add($cron_array);
            }
            $this->__object('Redir')->redirect('index.php?do=roadmap');
        }
        $this->_view->assign('formaction', 'index.php?do=roadmap&amp;sub=newroadmap&amp;action=save');
        $this->_view->assign('title', $this->_lang['Roadmaps'] . ' - ' . $this->_lang['Global_Overview']);
        $this->_view->content('/roadmap/roadmapform.tpl');
    }

    public function delroadmap($id) {
        if (!empty($id)) {
            $id = intval($id);
            $this->_db->query("DELETE FROM " . PREFIX . "_roadmap WHERE Id = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $id . "'");
        }
        $this->__object('Redir')->redirect('index.php?do=roadmap');
    }

    public function showtickets($id, $closed) {
        $id = intval($id);
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $id . "' AND Fertig = '" . intval($closed) . "' AND Sektion = '" . AREA . "' ORDER BY pr");
        while ($row = $sql->fetch_assoc()) {
            $row['Benutzer'] = Tool::userName($row['Uid']);
            switch ($row['pr']) {
                case '1':
                    $row['Prio'] = $this->_lang['highest'];
                    break;
                case '2':
                    $row['Prio'] = $this->_lang['high'];
                    break;
                case '3':
                    $row['Prio'] = $this->_lang['normal'];
                    break;
                case '4':
                    $row['Prio'] = $this->_lang['low'];
                    break;
                case '5':
                    $row['Prio'] = $this->_lang['lowest'];
                    break;
            }
            $items[] = $row;
        }
        $sql->close();

        $this->_view->assign('title', $this->_lang['Roadmaps'] . ' - ' . $this->_lang['Tickets']);
        if (isset($items)) {
            $this->_view->assign('items', $items);
        }
        $this->_view->content('/roadmap/tickets.tpl');
    }

    public function newticket($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $name = Arr::getRequest('Beschreibung');
            if (!empty($name)) {
                $insert_array = array(
                    'Rid'          => $id,
                    'Beschreibung' => Tool::cleanAllow($name, ' '),
                    'Datum'        => time(),
                    'Fertig'       => intval(Arr::getRequest('Fertig')),
                    'Uid'          => $_SESSION['benutzer_id'],
                    'pr'           => intval(Arr::getRequest('pr')),
                    'Sektion'      => AREA);
                $this->_db->insert_query('roadmap_tickets', $insert_array);
            }
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('formaction', 'index.php?do=roadmap&amp;sub=newticket&amp;id=' . $id . '&amp;noframes=1');
        $this->_view->content('/roadmap/ticketform.tpl');
    }

    public function editticket($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $name = Arr::getRequest('Beschreibung');
            if (!empty($name)) {
                $array = array(
                    'Beschreibung' => $name,
                    'Fertig'       => Arr::getRequest('Fertig'),
                    'Uid'          => Arr::getRequest('Uid'),
                    'Datum'        => time(),
                    'pr'           => Arr::getRequest('pr'),
                );
                $this->_db->update_query('roadmap_tickets', $array, "Id = '" . $id . "'");
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('formaction', 'index.php?do=roadmap&amp;sub=editticket&amp;id=' . $id . '&amp;noframes=1');
        $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_roadmap_tickets WHERE Id = '" . $id . "' AND Sektion = '" . AREA . "' LIMIT 1");
        $row['Benutzer'] = Tool::userName($row['Uid']);
        $this->_view->assign('item', $row);
        $this->_view->content('/roadmap/ticketform.tpl');
    }

    public function delticket($id, $rid, $closed) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_roadmap_tickets WHERE Id = '" . intval($id) . "'");
        }
        $this->__object('Redir')->redirect('index.php?do=roadmap&sub=showtickets&id=' . $rid . '&closed=' . $closed);
    }

}
