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

class AdminMain extends Magic {

    protected function forum() {
        $lastposts_sp = array();
        $sql_fids = $this->_db->query("SELECT
                p.id,
                p.title,
                p.topic_id,
                p.datum,
                p.message,
                f.id AS forum_id
        FROM
                " . PREFIX . "_f_post AS p,
                " . PREFIX . "_f_topic AS t,
                " . PREFIX . "_f_forum AS f
        WHERE
                t.id = p.topic_id
        AND
                t.forum_id = f.id
        ORDER BY datum DESC LIMIT 5");
        $limit = Tool::userSettings('Forum_Beitraege_Limit', 15);
        while ($row_fids = $sql_fids->fetch_object()) {
            $numPages = Tool::countPost($row_fids->id, $row_fids->topic_id, $limit);
            $row_fids->Datum = $row_fids->datum;
            $row_fids->message = Tool::cleanVideo(Tool::cleanTags($row_fids->message, array('codewidget')));
            $row_fids->title = Tool::cleanVideo(Tool::cleanTags($row_fids->title, array('codewidget')));
            $row_fids->LpLink = '../index.php?p=showtopic&amp;toid=' . $row_fids->topic_id . '&amp;pp=' . $limit . '&amp;page=' . $numPages . '#pid_' . $row_fids->id . '';
            $row_fids->LpTitle = (empty($row_fids->title)) ? strip_tags($row_fids->message) : $row_fids->title;
            $row_fids->LpTitle = preg_replace('/\[(.*?)\]/siu', ' ', sanitize($row_fids->LpTitle));
            $lastposts_sp[] = $row_fids;
        }
        $sql_fids->close();
        $this->_view->assign('last_post_array', $lastposts_sp);
        return $this->_view->fetch(THEME . '/forum/lastposts.tpl');
    }

    protected function faq() {
        $newfaq = $this->_db->fetch_object_all("SELECT *, Name_1 AS Name FROM " . PREFIX . "_faq WHERE Aktiv = '2' AND Sektion = '" . AREA . "' ORDER BY Datum DESC LIMIT 5");

        $this->_view->assign('newfaq', $newfaq);
        return $this->_view->fetch(THEME . '/faq/newsendfaq.tpl');
    }

    protected function links() {
        $downloads = $cheats = $links = array();
        $query = "SELECT Id, Name_1 as Name, DefektGemeldet, DEmail, DName, DDatum FROM " . PREFIX . "_downloads WHERE DefektGemeldet != '' ; ";
        $query .= "SELECT Id, Name_1 as Name, DefektGemeldet, DEmail, DName, DDatum FROM " . PREFIX . "_cheats WHERE DefektGemeldet != '' ; ";
        $query .= "SELECT Id, Name_1 as Name, DefektGemeldet, DEmail, DName, DDatum FROM " . PREFIX . "_links WHERE DefektGemeldet != ''";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row = $result->fetch_object()) {
                    $downloads[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $cheats[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $links[] = $row;
                }
                $result->close();
            }
        }

        if (perm('downloads_edit')) {
            $this->_view->assign('downloads', $downloads);
        }
        if (perm('cheats')) {
            $this->_view->assign('cheats', $cheats);
        }
        if (perm('links_edit')) {
            $this->_view->assign('links', $links);
        }
        return $this->_view->fetch(THEME . '/other/error_links.tpl');
    }

    protected function gdversion() {
        $info = gd_info();
        return $info['GD Version'];
    }

    protected function htaccess($dir) {
        if (!is_file($dir . '/.htaccess')) {
            File::set($dir . '/.htaccess', 'deny from all');
        }
    }

    protected function update() {
        if (is_file(SX_DIR . '/admin/action/update.php')) {
            $filemtime = filemtime(SX_DIR . '/admin/action/update.php');
            if (SX::get('system.Version') != VERSION || SX::get('system.Update') != $filemtime) {
                SX::save('system', array('Update' => $filemtime, 'Version' => VERSION));
                $this->_view->assign('sx_update', $this->_lang['Sys_update']);
            }
        }
    }

    protected function setup() {
        if (is_dir(SX_DIR . '/setup')) {
            $this->_view->assign('warning', $this->_lang['warning']);
        }
    }

    protected function dbopt() {
        $this->_view->assign('db_fields', $this->load(PREFIX));
        return $this->_view->fetch(THEME . '/other/dbopt.tpl');
    }

    protected function sysinfo() {
        $mysqlversion = DB::get()->server_info();
        $array = array(
            'dbsize'       => File::filesize($this->dbsize()),
            'version'      => VERSION,
            'maxmemory'    => (ini_get('memory_limit') != '' ? ini_get('memory_limit') : $this->_lang['Sys_notcheckable']),
            'safemode'     => (ini_get('safe_mode') == 1 ? $this->_lang['Sys_on'] : $this->_lang['Sys_off']),
            'magicquotes'  => (ini_get('magic_quotes_gpc') == 1 ? $this->_lang['Sys_on'] : $this->_lang['Sys_off']),
            'runtime'      => (ini_get('magic_quotes_runtime') == 1 ? $this->_lang['Sys_on'] : $this->_lang['Sys_off']),
            'sybase'       => (ini_get('magic_quotes_sybase') == 1 ? $this->_lang['Sys_on'] : $this->_lang['Sys_off']),
            'maxtime'      => ini_get('max_execution_time'),
            'disabled'     => (strlen(ini_get('disable_functions')) > 1 ? ini_get('disable_functions') : $this->_lang['Sys_notcheckable']),
            'apache'       => (function_exists('apache_get_modules') ? apache_get_modules() : 'Функция, не доступна на этом хостинге!'),
            'maxupload'    => File::filesize(str_replace(array('M', 'm'), '', ini_get('upload_max_filesize')) * 1024),
            'phpversion'   => (PHP_VERSION != '' ? PHP_VERSION : $this->_lang['Sys_notcheckable']),
            'gdinfo'       => $this->gdversion(),
            'mysqlversion' => (!empty($mysqlversion) ? $mysqlversion : $this->_lang['Sys_notcheckable']),
        );
        $this->_view->assign($array);
        return $this->_view->fetch(THEME . '/other/sysinfo.tpl');
    }

    protected function sysactive() {
        $bereiche = $widgets = array();
        $admin_lang = $_SESSION['admin_lang'];
        $query = $this->_db->query("SELECT *, Aktiv_Section_" . intval($_SESSION['a_area']) . " AS Aktiv FROM " . PREFIX . "_bereiche ORDER BY Id ASC");
        while ($row = $query->fetch_assoc()) {
            if ($row['Type'] == 'modul') {
                $row['BName'] = $this->_lang['Sections_' . $row['Name']];
                $row['Typ'] = $this->_lang['IntModul'];
                $bereiche[] = $row;
            } elseif ($row['Type'] == 'extmodul') {
                if (admin_active($row['Name']) && is_file(MODUL_DIR . '/' . $row['Name'] . '/lang/' . $admin_lang . '/admin.txt')) {
                    SX::loadLang(MODUL_DIR . '/' . $row['Name'] . '/lang/' . $admin_lang . '/admin.txt');
                    $row['BName'] = SX::$lang['module_' . $row['Name']];
                    $row['Typ'] = SX::$lang['ExtModul'];
                }
                $bereiche[] = $row;
            } elseif ($row['Type'] == 'widget') {
                if (admin_active($row['Name']) && is_file(WIDGET_DIR . '/' . $row['Name'] . '/lang/' . $admin_lang . '/admin.txt')) {
                    SX::loadLang(WIDGET_DIR . '/' . $row['Name'] . '/lang/' . $admin_lang . '/admin.txt');
                    $row['BName'] = SX::$lang['widget_' . $row['Name']];
                }
                $widgets[] = $row;
            }
        }
        $query->close();

        $array = array(
            'bereiche' => $bereiche,
            'widgets' => $widgets
        );
        $this->_view->assign($array);
        return $this->_view->fetch(THEME . '/other/sysactive.tpl');
    }

    protected function sql() {
        if (Arr::getPost('sqlin') == 1) {
            $sql = Arr::getPost('sql');
            if (!empty($sql)) {
                SX::setDefine('SQLERROR_WIDTH', 100);
                $queries = str_replace(array(";\r\n", ";\n\n"), ";\n", $sql);
                $queries = explode(";\n", $queries);
                foreach ($queries as $qcontent) {
                    if (!empty($qcontent)) {
                        $qcontent = str_replace('PREFIX', PREFIX, $qcontent);
                        $this->_db->query($qcontent);
                    }
                }
            }
            SX::output('<strong>' . $this->_lang['MySQLOk'] . '</strong>', true);
        }
    }

    protected function dbsize() {
        $size = 0;
        if (($query = $this->_db->query("SHOW TABLE STATUS WHERE Name LIKE '" . PREFIX . "_%'"))) {
            while ($row = $query->fetch_assoc()) {
                $size += $row['Data_length'] + $row['Index_length'];
            }
        }
        return $size / 1024;
    }

    protected function fields($table) {
        $res = array();
        $result = $this->_db->query("SHOW FIELDS FROM $table");
        while ($row = $result->fetch_object()) {
            $res[count($res) + 1] = $row->Field;
        }
        return $res;
    }

    protected function create($table) {
        $def = "DROP TABLE IF EXISTS `$table`;\n";
        $def .= "CREATE TABLE `$table` (\n";
        $result = $this->_db->query("SHOW FIELDS FROM $table");
        while ($row = $result->fetch_assoc()) {
            $def .= " `" . $row['Field'] . "` " . $row['Type'];
            $def .= ( $row['Null'] != 'YES') ? ' NOT NULL' : ' NULL';
            if ($row['Default'] != '' && $row['Type'] != 'timestamp') {
                $def .= " DEFAULT '" . $row['Default'] . "'";
            }
            if ($row['Type'] == 'timestamp') {
                $def .= " DEFAULT " . $row['Default'];
            }
            if ($row['Extra'] != '') {
                $def .= ' ' . $row['Extra'];
            }
            $def .= ",\n";
        }

        $def = preg_replace("#,\n$#iu", '', $def);
        $qkey = $this->_db->query("SHOW INDEX FROM $table");
        $keys = $knames = array();
        if (($rkey = $qkey->fetch_assoc())) {
            do {
                $keys[$rkey['Key_name']]['nonunique'] = $rkey['Non_unique'];
                $keys[$rkey['Key_name']]['order'][$rkey['Seq_in_index'] - 1] = (!$rkey['Sub_part']) ? "`" . $rkey['Column_name'] . "`" : "`" . $rkey['Column_name'] . "`(" . $rkey['Sub_part'] . ")";
                if (!in_array($rkey['Key_name'], $knames)) {
                    $knames[] = $rkey['Key_name'];
                }
            } while ($rkey = $qkey->fetch_assoc());
            for ($kl = 0, $count = count($knames); $kl < $count; $kl++) {
                if ($knames[$kl] == 'PRIMARY') {
                    $def .= ",\n PRIMARY KEY";
                } else {
                    $def .= ($keys[$knames[$kl]]['nonunique'] == '0') ? ",\n UNIQUE `" . $knames[$kl] . "`" : ",\n KEY `" . $knames[$kl] . "`";
                }
                $temp = implode(',', $keys[$knames[$kl]]['order']);
                $def .= ' (' . $temp . ')';
            }
        }
        $def .= "\n) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n\n";
        return $def;
    }

    public function db($what, $dbprefix) {
        set_time_limit(600);
        $tables = $dump = '';
        $arr = Arr::getRequest('ta');
        reset($arr);

        switch ($what) {
            case 'dump':
                foreach ($arr as $key => $val) {
                    $doit = true;
                    if (!preg_match('#^' . preg_quote($dbprefix) . '#iu', $val)) {
                        $doit = false;
                    }
                    if ($doit) {
                        $dump .= $this->create($val);
                        $felder = $this->fields($val);
                        $zeilen = $this->_db->query("SELECT * FROM " . $val);
                        while ($zrow = $zeilen->fetch_array()) {
                            $def = $cnt = '';
                            for ($i = 1, $count = count($felder); $i <= $count; $i++) {
                                $def .= ", `" . $felder[$i] . "`";
                                $cnt .= ", '" . str_replace("\r\n", '\r\n', addslashes($zrow[$felder[$i]])) . "'";
                            }
                            $def = substr($def, 2);
                            $cnt = substr($cnt, 2);
                            $dump .= "INSERT INTO `" . $val . "` (" . $def . ") VALUES (" . str_replace('\n', '\\n', $cnt) . ");\n";
                        }
                        $dump .= "\n";
                        $zeilen->close();
                    }
                }
                File::download($dump, 'Дамп_базы_' . $dbprefix . '_' . date('d-m-Y') . '.sql');
                break;

            case 'optimize':
                $query = "OPTIMIZE TABLE ";
                $query_msg = $this->_lang['Sys_db_optimized'];
                break;

            case 'repair':
                $query = "REPAIR TABLE ";
                $query_msg = $this->_lang['Sys_db_repaired'];
                break;
        }

        foreach ($arr as $key => $val) {
            $tables .= ", `$val`";
        }
        $query .= substr($tables, 1);
        if ($this->_db->query($query)) {
            SX::output($query_msg . $this->_lang['Sys_errors_no'], true);
        } else {
            SX::output($query_msg . $this->_lang['Sys_errors'], true);
        }
    }

    public function load($dbprefix) {
        $tabellen = '';
        $sql = $this->_db->query("SHOW TABLES");
        while ($row = $sql->fetch_array()) {
            $titel = $row[0];
            if (substr($titel, 0, strlen($dbprefix)) == $dbprefix) {
                $tabellen .= "<option value=\"$titel\" selected=\"selected\">" . $titel . "</option>\n";
            }
        }
        return $tabellen;
    }

    public function delCaches() {
        Folder::clean(TEMP_DIR . '/cache/');
        Folder::clean(TEMP_DIR . '/private/');
        SX::output($this->_lang['Sys_clearcache_ok'], true);
    }

    public function delCompiled() {
        $compiled = TEMP_DIR . '/compiled/' . $_SESSION['a_area'] . '/';
        $dirs = array($compiled . 'main', $compiled . 'admin');
        foreach ($dirs as $dir) {
            Folder::clean($dir . '/');
            $this->htaccess($dir);
        }
        SX::output($this->_lang['Sys_clearTplcache_ok'], true);
    }

    public function online($all = false) {
        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_online WHERE Expire <= '" . time() . "'");
        $this->_db->query("INSERT IGNORE INTO " . PREFIX . "_benutzer_online (
                Ip,
                Uid,
                Expire,
                Benutzername,
                Type
        ) VALUES (
                INET_ATON('" . IP_USER . "'),
                '" . $_SESSION['benutzer_id'] . "',
                '" . (time() + 600) . "',
                '" . $_SESSION['user_name'] . "',
                'admin')
        ON DUPLICATE KEY UPDATE
                Expire = '" . (time() + 600) . "',
                Benutzername = '" . $_SESSION['user_name'] . "',
                Uid = '" . $_SESSION['benutzer_id'] . "',
                Type = 'admin'");

        $online_user = $online_admin = array();
        $limit = $all ? '' : ' LIMIT 10';
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS INET_NTOA(Ip) AS Ip, Uid, Benutzername, Bots, Type FROM " . PREFIX . "_benutzer_online" . $limit);
        $count = $this->_db->found_rows();
        while ($row = $sql->fetch_object()) {
            if ($row->Type == 'admin') {
                $online_admin[] = $row;
            } else {
                $online_user[] = $row;
            }
        }
        $sql->close();

        $tpl_array = array(
            'count'       => $count,
            'onlineUser'  => $online_user,
            'onlineAdmin' => $online_admin);
        $this->_view->assign($tpl_array);

        if ($all) {
            $this->_view->content('/other/onlineuser.tpl');
        } else {
            return $this->_view->fetch(THEME . '/other/onlineuser.tpl');
        }
    }

    public function start() {
        if (perm('settings')) {
            $this->sql();
            $this->update();
            $this->setup();
        }

        $array = array(
            'StartInfos'    => $this->_view->fetch(THEME . '/other/startinfos.tpl'),
            'CacheDel'      => $this->_view->fetch(THEME . '/other/cachedel.tpl'),
            'Sql'           => $this->_view->fetch(THEME . '/other/sql.tpl'),
            'dbopt'         => $this->dbopt(),
            'sysactive'     => $this->sysactive(),
            'sysinfo'       => $this->sysinfo(),
            'version'       => VERSION,
            'NewFaq'        => $this->faq(),
            'ErrorLinks'    => $this->links(),
            'startOrders'   => $this->__object('AdminShop')->startOrders(),
            'startVotes'    => $this->__object('AdminShop')->startVotes(),
            'NewComments'   => $this->__object('AdminComment')->last(),
            'NewForumPosts' => $this->forum(),
            'OnlineUser'    => $this->online(),
            'NewUsers'      => $this->__object('AdminUsers')->show('new'),
        );
        $this->_view->assign($array);
        $this->_view->content('/start.tpl');
    }

}
