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

class Phrases extends Magic {

    /* Вывод случайного контента */
    public function get() {
        $sql = $this->_db->query("SELECT SQL_CACHE id, phrase FROM " . PREFIX . "_phrases WHERE active = '1'");
        $i = 0;
        while ($row = $sql->fetch_object()) {
            $content[$i] = '%%' . $row->id . '%%' . $row->phrase;
            $i++;
        }
        $sql->close();
        if (empty($content)) {
            return '';
        }
        $random = mt_rand(0, count($content) - 1);
        $content_out = explode('%%', $content[$random]);
        return $content_out[2];
    }

}
