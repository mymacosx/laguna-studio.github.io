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

class AdminDataExport {

    public $id;
    public $db;
    public $groups;
    public $separator;
    public $enclosed;
    public $cutter;

    public function __construct($filename, $format, $groups = '0') {
        $DB = DB::get();
        $whichgroups_pre = " OR Gruppe = " . $DB->escape(implode(" OR Gruppe = ", $groups));
        $whichgroups = " (Gruppe = " . $DB->escape($groups[0]) . " $whichgroups_pre)";
        $admin = ($_SESSION['benutzer_id'] != 1) ? " Id != '1' AND " : '';
        $sql = $DB->query("SELECT *  FROM " . PREFIX . "_benutzer WHERE $admin $whichgroups");

        $separator = !empty($_REQUEST['separator']) ? Arr::getRequest('separator') : ";";
        $enclosed = !empty($_REQUEST['enclosed']) ? Arr::getRequest('enclosed') : "\"";
        $cutter = !empty($_REQUEST['cutter']) ? Arr::getRequest('cutter') : "\r\n";
        $cutter = str_replace('\\r', "\015", $cutter);
        $cutter = str_replace('\\n', "\012", $cutter);
        $cutter = str_replace('\\t', "\011", $cutter);
        $xoutput = '';

        if (Arr::getRequest('showcsvnames') == 'yes') {
            $fieldcount = $sql->field_count();
            for ($i = 0; $i < $fieldcount; $i++) {
                $xoutput .= $enclosed . $sql->field_name($i) . $enclosed . $separator;
            }
            $xoutput .= $cutter;
        }

        while ($row = $sql->fetch_object()) {
            foreach ($row as $val) {
                $val = str_replace("\r\n", "\n", $val);
                $xoutput .= ( $val == '') ? $separator : $enclosed . $val . $enclosed . $separator;
            }
            $xoutput .= $cutter;
        }
        $xoutput = str_replace(array("\";\r\n", "\";\n"), "\"\r\n", $xoutput);
        $header = ($format == 'txt') ? 'text/plain' : 'text/csv';
        File::download($xoutput, $filename . '.' . $format, $header);
    }

}
