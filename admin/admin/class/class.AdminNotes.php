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

class AdminNotes extends Magic {

    public function add() {
        if (Arr::getRequest('save') == '1' && !empty($_REQUEST['text_notes'])) {
            $insert_array = array(
                'UserId' => $_SESSION['benutzer_id'],
                'Datum'  => time(),
                'Text'   => sanitize(Arr::getRequest('text_notes')),
                'Type'   => (Arr::getRequest('type') == 'pub' ? 'pub' : 'main'));
            $this->_db->insert_query('admin_notes', $insert_array);
            $this->show();
        } else {
            $this->_view->assign('types', 'add');
            SX::output($this->_view->fetch(THEME . '/notes/addnotes.tpl'));
        }
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getRequest('edit') == '1' && !empty($_REQUEST['text_notes'])) {
            $type = Arr::getRequest('type') == 'pub' ? 'pub' : 'main';
            $text_notes = sanitize(Arr::getRequest('text_notes'));
            $this->_db->query("UPDATE " . PREFIX . "_admin_notes SET Text='" . $this->_db->escape($text_notes) . "', Type='" . $type . "' WHERE Id = '" . $id . "'");
            $this->show();
        } else {
            $enotes = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_admin_notes WHERE Id='" . $id . "' LIMIT 1");
            $this->_view->assign('enotes', $enotes);
            $this->_view->assign('types', 'edit');
            SX::output($this->_view->fetch(THEME . '/notes/addnotes.tpl'));
        }
    }

    public function delete($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_admin_notes WHERE Id = '" . intval($id) . "'");
        }
        $this->show();
    }

    public function show() {
        $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 'all';
        switch ($type) {
            case 'main':
                $def = "UserId = '" . $_SESSION['benutzer_id'] . "' AND Type = 'main'";
                break;
            case 'pub':
                $def = "Type = 'pub'";
                break;
            default:
            case 'all':
                $def = "(UserId = '" . $_SESSION['benutzer_id'] . "' AND Type = 'main') OR Type = 'pub'";
                break;
        }
        $items = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_admin_notes WHERE " . $def . " ORDER BY Id DESC");
        while ($row = $sql->fetch_object()) {
            $row->Autor = Tool::userName($row->UserId);
            $items[] = $row;
        }
        $sql->close();
        $this->_view->assign('notes', $items);
        SX::output($this->_view->fetch(THEME . '/notes/shownotes.tpl'), true);
    }

}
