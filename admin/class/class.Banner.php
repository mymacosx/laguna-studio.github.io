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

class Banner extends Magic {

    protected $_separator = '%%%%';

    /* Метод вывода баннеров */

    public function get($param) {
        if ($_REQUEST['p'] != 'notfound' && SX::object('Agent')->is_robot === false) {
            $content = array();
            $kategorie = !empty($param['categ']) ? " AND Kategorie='" . intval($param['categ']) . "'" : '';
            $sql = $this->_db->query("SELECT
                    Id,
                    Name,
                    Gewicht,
                    HTML_Code
            FROM
                    " . PREFIX . "_banner
            WHERE
                    Sektion = '" . AREA . "'
            AND
                    Aktiv = '1'
            AND
                    (Anzeigen < Anzeigen_Max OR Anzeigen_Max = 0) {$kategorie}");
            while ($row = $sql->fetch_object()) {
                $value = $row->Id . $this->_separator . $row->HTML_Code;
                switch ($row->Gewicht) {
                    case 1:
                        $content[] = $value;
                        break;
                    case 2:
                        array_push($content, $value, $value);
                        break;
                    case 3:
                        array_push($content, $value, $value, $value);
                        break;
                }
            }
            $sql->close();
            if (!empty($content)) {
                shuffle($content);
                $out = explode($this->_separator, $content[array_rand($content)]);
                if (!empty($out[0])) {
                    $this->_db->query("UPDATE " . PREFIX . "_banner SET Anzeigen = Anzeigen + 1 WHERE id = '" . intval($out[0]) . "'");
                    return $this->out($out[0], $out[1]);
                }
            }
        }
        return NULL;
    }

    /* Метод формирования кода учета кликов по баннеру */

    protected function out($id, $code) {
        return '<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $("#click_' . $id . '").on("click", function() {
        var options = { url:"index.php?action=click&p=banner&click=' . $id . '", timeout:3000 };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>
<div id="click_' . $id . '">
' . $code . '
</div>';
    }

    /* Метод добавления клика по баннеру в базу */

    public function click($id) {
        if ($this->__object('Redir')->referer()) {
            $this->_db->query("UPDATE " . PREFIX . "_banner SET Click = Click + 1 WHERE Id='" . intval($id) . "'");
        }
    }

}
