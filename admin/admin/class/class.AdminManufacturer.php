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

class AdminManufacturer extends Magic {

    public function show() {
        $def_order = 'ORDER BY Name ASC';
        $def_order_n = $def_search = $def_search_n = $def_hits = $def_hits_n = $def_order_ns = '';

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['nid'] as $nid) {
                $this->_db->query("UPDATE " . PREFIX . "_hersteller SET Hits ='" . $this->_db->escape($_POST['Hits'][$nid]) . "' WHERE Id='" . intval($nid) . "'");
                if (isset($_POST['del'][$nid]) && $_POST['del'][$nid] == 1 && perm('manufacturer_del')) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_hersteller WHERE Id='" . intval($nid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getRequest('hits_from') < Arr::getRequest('hits_to')) {
            $def_hits = "AND (Hits BETWEEN '" . intval($_REQUEST['hits_from']) . "' AND '" . intval($_REQUEST['hits_to']) . "')";
            $def_hits_n = "&amp;hits_from=" . intval($_REQUEST['hits_from']) . "&amp;hits_to=" . intval($_REQUEST['hits_to']);
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.@ ');
            $def_search = " AND (Name LIKE '%{$this->_db->escape($pattern)}%')";
            $def_search_n = '&amp;q=' . $pattern;
        }

        if (isset($_REQUEST['sort'])) {
            $curr_page = '&amp;page=' . Arr::getRequest('page', 1);

            switch ($_REQUEST['sort']) {
                case 'name_asc':
                    $def_order = ' ORDER BY Name ASC';
                    $def_order_n = '&sort=name_asc' . $curr_page;
                    $def_order_ns = '&sort=name_desc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'name_desc':
                    $def_order = ' ORDER BY Name DESC';
                    $def_order_n = '&sort=name_desc' . $curr_page;
                    $def_order_ns = '&sort=name_asc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'date_asc':
                    $def_order = ' ORDER BY Datum ASC';
                    $def_order_n = '&sort=date_asc' . $curr_page;
                    $def_order_ns = '&sort=date_desc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'date_desc':
                    $def_order = ' ORDER BY Datum DESC';
                    $def_order_n = '&sort=date_desc' . $curr_page;
                    $def_order_ns = '&sort=date_asc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'hits_asc':
                    $def_order = ' ORDER BY Hits ASC';
                    $def_order_n = '&sort=hits_asc' . $curr_page;
                    $def_order_ns = '&sort=hits_desc' . $curr_page;
                    $this->_view->assign('hits_s', $def_order_ns);
                    break;

                case 'hits_desc':
                    $def_order = ' ORDER BY Hits DESC';
                    $def_order_n = '&sort=hits_desc' . $curr_page;
                    $def_order_ns = '&sort=hits_asc' . $curr_page;
                    $this->_view->assign('hits_s', $def_order_ns);
                    break;
            }
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_hersteller WHERE Sektion='" . AREA . "' {$def_search} {$def_hits} {$def_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $manufacturer = array();
        while ($row = $sql->fetch_object()) {
            $row->Comments = $this->__object('AdminCore')->countComments('products', $row->Id);
            $row->User = Tool::userName($row->Benutzer);
            $manufacturer[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=manufacturer&amp;sub=overview{$def_search_n}&amp;pp={$limit}{$def_hits_n}";
        $nastr = "{$def_order_n}{$def_search_n}&amp;pp={$limit}{$def_hits_n}";
        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('manufacturer', $manufacturer);
        $this->_view->assign('title', $this->_lang['Manufacturer']);
        $this->_view->assign('limit', $limit);
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=manufacturer&amp;sub=overview{$nastr}&page={s}\">{t}</a> "));
        }
        $this->_view->content('/manufacturer/manufacturer.tpl');
    }

    public function edit($id) {
        if (!perm('manufacturer_newedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $_POST['Datum'] = (!empty($_POST['Datum'])) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $Bild = '';
            $Beschreibung_1 = $_POST['Beschreibung_1'];
            $Beschreibung_2 = (!empty($_POST['Beschreibung_2']) && $this->_text->strlen($_POST['Beschreibung_2']) > 6) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = (!empty($_POST['Beschreibung_3']) && $this->_text->strlen($_POST['Beschreibung_3']) > 6) ? $_POST['Beschreibung_3'] : $Beschreibung_1;

            $array = array(
                'Datum'          => Arr::getPost('Datum'),
                'Name'           => Arr::getPost('Name'),
                'NameLang'       => Arr::getPost('NameLang'),
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
                'Gruendung'      => Arr::getPost('Gruendung'),
                'GruendungLand'  => Arr::getPost('GruendungLand'),
                'Personen'       => Arr::getPost('Personen'),
                'Homepage'       => Arr::getPost('Homepage'),
                'Adresse'        => Arr::getPost('Adresse'),
                'Telefonkontakt' => Arr::getPost('Telefonkontakt'),
            );
            if (Arr::getPost('NoImg') == 1) {
                $array['Bild'] = '';
            }
            if (!empty($_POST['newImg_1'])) {
                $array['Bild'] = Arr::getPost('newImg_1');
            }
            $this->_db->update_query('hersteller', $array, "Id='" . $id . "'");

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил производителя (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_hersteller WHERE Sektion='" . AREA . "' AND Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('Beschreibung_1', $this->__object('Editor')->load('admin', $res->Beschreibung_1, 'Beschreibung_1', 250, 'Settings'));
        $this->_view->assign('Beschreibung_2', $this->__object('Editor')->load('admin', $res->Beschreibung_2, 'Beschreibung_2', 250, 'Settings'));
        $this->_view->assign('Beschreibung_3', $this->__object('Editor')->load('admin', $res->Beschreibung_3, 'Beschreibung_3', 250, 'Settings'));
        $this->_view->assign('title', $this->_lang['Manufacturer_edit']);
        $this->_view->content('/manufacturer/edit_new.tpl');
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $_POST['Datum'] = (!empty($_POST['Datum'])) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $Beschreibung_1 = $_POST['Beschreibung_1'];
            $Beschreibung_2 = (!empty($_POST['Beschreibung_2']) && $this->_text->strlen($_POST['Beschreibung_2']) > 6) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = (!empty($_POST['Beschreibung_3']) && $this->_text->strlen($_POST['Beschreibung_3']) > 6) ? $_POST['Beschreibung_3'] : $Beschreibung_1;
            $name = Arr::getPost('Name');

            $insert_array = array(
                'Datum'          => Arr::getPost('Datum'),
                'Benutzer'       => $_SESSION['benutzer_id'],
                'Name'           => $name,
                'NameLang'       => Arr::getPost('NameLang'),
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
                'Bild'           => Arr::getPost('newImg_1'),
                'Gruendung'      => Arr::getPost('Gruendung'),
                'GruendungLand'  => Arr::getPost('GruendungLand'),
                'Personen'       => Arr::getPost('Personen'),
                'Homepage'       => Arr::getPost('Homepage'),
                'Adresse'        => Arr::getPost('Adresse'),
                'Telefonkontakt' => Arr::getPost('Telefonkontakt'),
                'Sektion'        => $_SESSION['a_area']);
            $this->_db->insert_query('hersteller', $insert_array);
            $new_id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $options = array(
                'name' => $name,
                'url'  => BASE_URL . '/index.php?p=manufacturer&area=' . AREA . '&action=showdetails&id=' . $new_id . '&name=' . translit($name),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $name,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал нового производителя (' . $name . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('Beschreibung_1', $this->__object('Editor')->load('admin', ' ', 'Beschreibung_1', 250, 'Settings'));
        $this->_view->assign('Beschreibung_2', $this->__object('Editor')->load('admin', ' ', 'Beschreibung_2', 250, 'Settings'));
        $this->_view->assign('Beschreibung_3', $this->__object('Editor')->load('admin', ' ', 'Beschreibung_3', 250, 'Settings'));
        $this->_view->assign('title', $this->_lang['Manufacturer_new']);
        $this->_view->content('/manufacturer/edit_new.tpl');
    }

}
