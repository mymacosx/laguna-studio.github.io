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

class Rating extends Magic {

    public function get($id) {
        $value = $this->_lang['ErrorReferer'];
        if ($this->__object('Redir')->referer()) {
            $id = intval($id);
            $val = intval(Arr::getRequest('starrate'));
            $where = Tool::cleanAllow(Arr::getRequest('where'));
            if ($val >= 1 && $val <= 5 && $id >= 1) {
                $res = $this->_db->cache_fetch_object("SELECT IPAdresse FROM " . PREFIX . "_wertung WHERE Bereich = '" . $this->_db->escape($where) . "' AND Objekt_Id = '" . $id . "' AND IPAdresse='" . IP_USER . "' LIMIT 1");
                if (is_object($res) && $res->IPAdresse == IP_USER) {
                    $value = $this->_lang['Rating_Allready'];
                } else {
                    $insert_array = array(
                        'Bereich'   => $where,
                        'Objekt_Id' => $id,
                        'IPAdresse' => IP_USER,
                        'Datum'     => time(),
                        'Wertung'   => $val,
                        'Gesamt'    => 1);
                    $this->_db->insert_query('wertung', $insert_array);
                    $value = $this->_lang['Rating_ThankYou'];
                }
            }
        }
        SX::output($value, true);
    }

}
