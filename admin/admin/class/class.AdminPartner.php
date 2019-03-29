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

class AdminPartner extends Magic {

    public function show() {
        if (Arr::getPost('save') == 1 && isset($_POST['PartnerUrl'])) {
            foreach ($_POST['PartnerUrl'] as $pid => $em) {
                if (!empty($_POST['PartnerUrl'][$pid]) && !empty($_POST['PartnerName'][$pid])) {
                    $_POST['PartnerUrl'][$pid] = Tool::checkSheme($_POST['PartnerUrl'][$pid]);

                    $array = array(
                        'PartnerUrl'  => $_POST['PartnerUrl'][$pid],
                        'PartnerName' => $_POST['PartnerName'][$pid],
                        'Hits'        => $_POST['Hits'][$pid],
                        'Position'    => $_POST['Position'][$pid],
                        'Nofollow'    => $_POST['Nofollow'][$pid],
                        'Aktiv'       => $_POST['Aktiv'][$pid],
                    );
                    $this->_db->update_query('partner', $array, "Id='" . intval($pid) . "'");
                }
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил партнера (' . $_POST['PartnerName'][$pid] . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1) {
            $insert_array = array(
                'PartnerUrl'  => Arr::getPost('PartnerUrl'),
                'Position'    => intval(Arr::getPost('Position')),
                'Nofollow'    => intval(Arr::getPost('Nofollow')),
                'Hits'        => 0,
                'PartnerName' => Arr::getPost('PartnerName'),
                'Bild'        => Arr::getPost('newImg_1'),
                'Sektion'     => AREA,
                'Aktiv'       => 1);
            $this->_db->insert_query('partner', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал нового партнера (' . $_POST['PartnerName'] . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $partners = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_partner WHERE Sektion='" . AREA . "' ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $row->Bild = (is_file(UPLOADS_DIR . '/partner/' . $row->Bild)) ? 'uploads/partner/' . $row->Bild : '';
            $partners[] = $row;
        }
        $sql->close();
        $this->_view->assign('partners', $partners);
        $this->_view->assign('title', $this->_lang['Partners']);
        $this->_view->content('/partner/partner.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $this->_db->query("UPDATE " . PREFIX . "_partner SET Bild='" . $this->_db->escape(Arr::getPost('newImg_1')) . "' WHERE Id='" . $id . "'");
            $row = $this->_db->cache_fetch_object("SELECT PartnerName FROM " . PREFIX . "_partner WHERE Id='" . $id . "' LIMIT 1");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил логотип партнера (' . $row->PartnerName . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_partner WHERE Id='" . $id . "' LIMIT 1");
        $row->Bild = (is_file(UPLOADS_DIR . '/partner/' . $row->Bild)) ? 'uploads/partner/' . $row->Bild : '';
        $this->_view->assign('res', $row);
        $this->_view->assign('title', $this->_lang['Partners_imgEdit']);
        $this->_view->content('/partner/img_edit.tpl');
    }

    public function delete($id) {
        $id = intval($id);
        $res = $this->_db->cache_fetch_object("SELECT Bild, PartnerName FROM " . PREFIX . "_partner WHERE Id='" . $id . "' LIMIT 1");
        File::delete(UPLOADS_DIR . '/partner/' . $res->Bild);
        $this->_db->query("DELETE FROM " . PREFIX . "_partner WHERE Id='" . $id . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил партнера (' . $res->PartnerName . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

}
