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

class Partner extends Magic {

    public function show() {
        $partners = array();
        $sql = $this->_db->query("SELECT SQL_CACHE Id, PartnerUrl, PartnerName, Bild, Nofollow FROM " . PREFIX . "_partner WHERE Aktiv='1' AND Sektion='" . AREA . "' ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $row->Bild = (is_file(UPLOADS_DIR . '/partner/' . $row->Bild)) ? 'uploads/partner/' . $row->Bild : '';
            $partners[] = $row;
        }
        $sql->close();

        $this->_view->assign('small_partners', $partners);
        $this->_view->assign('PartnerDisplay', $this->_view->fetch(THEME . '/partners/partner_small.tpl'));
    }

    public function update($id) {
        if ($this->__object('Redir')->referer()) {
            $this->_db->query("UPDATE " . PREFIX . "_partner SET Hits=Hits+1 WHERE Aktiv='1' AND Id='" . intval($id) . "'");
        }
    }

}
