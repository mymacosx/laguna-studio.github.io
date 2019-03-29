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

class CodeWidget {

    /* Метод получения виджетов кода из базы */
    public function get($text) {
        return preg_replace_callback('!\[CODEWIDGET:([\d]*)\]!iu', array($this, 'code'), $text);
    }

    /* Метод получения виджетов кода из базы */
    public function code($codewidget) {
        $out = NULL;
        if (!empty($codewidget[1])) {
            $res = DB::get()->cache_fetch_object("SELECT Inhalt, Gruppen, Aktiv FROM " . PREFIX . "_codewidget WHERE Id = '" . intval($codewidget[1]) . "' LIMIT 1");
            if (is_object($res) && !empty($res->Inhalt)) {
                $perms_widget = explode(',', $res->Gruppen);
                if ((empty($res->Gruppen) || (!empty($res->Gruppen) && in_array(Arr::getSession('user_group'), $perms_widget))) && $res->Aktiv == 1) {
                    ob_start();
                    eval(' ?>' . $res->Inhalt . '<?php ');
                    $out = ob_get_clean();
                }
            }
        }
        return $out;
    }

}
