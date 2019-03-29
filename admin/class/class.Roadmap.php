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

class Roadmap extends Magic {

    public function show() {
        $sql = $this->_db->query("SELECT SQL_CACHE * FROM " . PREFIX . "_roadmap WHERE Aktiv = '1' AND Sektion = '" . AREA . "' ORDER BY Pos");
        while ($row = $sql->fetch_assoc()) {
            $query = "SELECT Datum FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $row['Id'] . "' AND Sektion = '" . AREA . "' ORDER BY Datum DESC LIMIT 1 ; ";
            $query .= "SELECT Id FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $row['Id'] . "' AND Sektion = '" . AREA . "' ; ";
            $query .= "SELECT Id FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $row['Id'] . "' AND Fertig = '1' AND Sektion = '" . AREA . "'";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $last_edit = $result->fetch_assoc();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $row['NumAll'] = $result->num_rows();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $row['NumFertig'] = $result->num_rows();
                    $result->close();
                }
            }
            $row['Edit'] = $last_edit['Datum'];
            $row['NumUFertig'] = $row['NumAll'] - $row['NumFertig'];
            if ($row['NumFertig'] != 0) {
                $row['Closed'] = round($row['NumFertig'] * 100 / $row['NumAll']);
            } else {
                $row['Closed'] = 0;
            }
            $row['Open'] = round(100 - $row['Closed']);
            $items[] = $row;
        }
        $sql->close();
        if (isset($items)) {
            $this->_view->assign('items', $items);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Roadmap'],
            'pagetitle' => $this->_lang['Roadmap'],
            'content'   => $this->_view->fetch(THEME . '/roadmap/roadmaps.tpl'));
        $this->_view->finish($seo_array);
    }

    public function get($id, $closed) {
        $id = intval($id);
        $closed = intval($closed);
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_roadmap_tickets WHERE Rid = '" . $id . "' AND Fertig = '" . $closed . "' AND Sektion = '" . AREA . "' ORDER BY pr");
        while ($row = $sql->fetch_assoc()) {
            $row['Benutzer'] = Tool::userName($row['Uid']);
            switch ($row['pr']) {
                case '1':
                    $row['prio'] = $this->_lang['highest'];
                    break;

                case '2':
                    $row['prio'] = $this->_lang['high'];
                    break;

                case '3':
                    $row['prio'] = $this->_lang['normal'];
                    break;

                case '4':
                    $row['prio'] = $this->_lang['low'];
                    break;

                case '5':
                    $row['prio'] = $this->_lang['lowest'];
                    break;
            }
            $items[] = $row;
        }
        $sql->close();
        $items = isset($items) ? $items : '';
        $name = $this->_db->cache_fetch_assoc("SELECT Name, Beschreibung FROM " . PREFIX . "_roadmap WHERE Id = '" . $id . "' AND Sektion = '" . AREA . "' LIMIT 1");

        $tpl_array = array(
            'name'  => $name['Name'],
            'items' => $items);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=roadmap&amp;area=' . AREA . '">' . $this->_lang['Roadmap'] . '</a>' . $this->_lang['PageSep'] . $name['Name'],
            'pagetitle' => sanitize($name['Name'] . $this->_lang['PageSep'] . $this->_lang['Roadmap']),
            'generate'  => $name['Name'] . ' ' . $name['Beschreibung'],
            'content'   => $this->_view->fetch(THEME . '/roadmap/show_roadmap.tpl'));
        $this->_view->finish($seo_array);
    }

}
