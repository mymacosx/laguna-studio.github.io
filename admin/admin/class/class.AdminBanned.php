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

class AdminBanned extends Magic {

    public function add($User_idd, $Name, $Email, $Ipp, $Reson, $TimeStart, $TimeEnde, $edit) {
        $User_id = ($User_idd != 1 && $User_idd != 0) ? Tool::cleanDigit($User_idd) : '';
        $Ip = ($Ipp != '127.0.0.1') ? preg_replace('/[^\d.*]/u', '', $Ipp) : '';
        $Name = Tool::cleanSpace(Tool::cleanAllow($Name));
        $Email = Tool::cleanSpace(Tool::cleanAllow($Email, '@.*'));
        $Reson = Tool::cleanSpace(Tool::cleanAllow($Reson, '.,!? '));
        $TimeStart = $this->__object('AdminCore')->mktime(Tool::cleanSpace($TimeStart));
        $TimeEnd = $this->__object('AdminCore')->mktime(Tool::cleanSpace($TimeEnde));

        if ((!empty($Reson) && !empty($TimeEnd) && !empty($TimeEnd)) && (!empty($User_id) || !empty($Name) || !empty($Email) || !empty($Ip))) {
            $IdSel = $NameSel = $EmailSel = $IpSel = array();
            $sql = $this->_db->query("SELECT User_id, Name, Email, Ip FROM " . PREFIX . "_banned");
            while ($row = $sql->fetch_object()) {
                if (!empty($row->User_id)) {
                    $IdSel[] = $row->User_id;
                }
                if (!empty($row->Ip)) {
                    $IpSel[] = $row->Ip;
                }
                if (!empty($row->Name)) {
                    $NameSel[] = $row->Name;
                }
                if (!empty($row->Email)) {
                    $EmailSel[] = $row->Email;
                }
            }
            $sql->close();

            if (in_array($User_id, $IdSel)) {
                $set = "User_id = '" . $User_id . "'";
            } elseif (in_array($Name, $NameSel)) {
                $set = "Name = '" . $Name . "'";
            } elseif (in_array($Email, $EmailSel)) {
                $set = "Email = '" . $Email . "'";
            } elseif (in_array($Ip, $IpSel)) {
                $set = "Ip = '" . $Ip . "'";
            } else {
                $set = '';
            }

            $array = array(
                'User_id'   => $User_id,
                'Reson'     => $Reson,
                'Type'      => 'bann',
                'TimeStart' => $TimeStart,
                'TimeEnd'   => $TimeEnd,
                'Name'      => $Name,
                'Email'     => $this->_text->lower($Email),
                'Ip'        => $Ip,
                'Aktiv'     => 1
            );
            if (!empty($set)) {
                $this->_db->update_query('banned', $array, $set);
                SX::syslog($_SESSION['user_name'] . ' продлил нахождение пользователя в бан-листе до ' . $TimeEnde, '0', $User_id);
            } else {
                $this->_db->insert_query('banned', $array);
                SX::syslog($_SESSION['user_name'] . ' добавил пользователя в бан-лист до ' . $TimeEnde, '0', $User_id);
            }
            $this->__object('AdminCore')->script('save');
        } else {
            if ($edit == 1) {
                $this->_view->assign('vkl', 1);
            }
            $this->_view->assign('error', 1);
            SX::output("<script type=\"text/javascript\">alert('" . $this->_lang['Validate_required'] . "')</script>");
        }
        $this->show();
    }

    public function aktive($id, $type) {
        if (!empty($id) && isset($type)) {
            $this->_db->query("UPDATE " . PREFIX . "_banned SET Aktiv = '" . intval($type) . "' WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    public function delete($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_banned WHERE id='" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    public function get($id) {
        if (!empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_banned WHERE id = '" . intval($id) . "' LIMIT 1");
            $this->_view->assign('vkl', 1);
            $this->_view->assign('row', $row);
        }
        $this->show();
    }

    public function show() {
        $db_sort = " ORDER BY User_id ASC";
        $nav_sort = "&amp;sort=id_asc";
        $User_idsort = $Resonsort = $Typesort = $TimeStartsort = $TimeEndsort = $Namesort = $Emailsort = $Ipsort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'id_asc':
            default:
                $db_sort = 'ORDER BY User_id ASC';
                $nav_sort = '&amp;sort=id_asc';
                $User_idsort = 'id_desc';
                break;
            case 'id_desc':
                $db_sort = 'ORDER BY User_id DESC';
                $nav_sort = '&amp;sort=id_desc';
                $User_idsort = 'id_asc';
                break;
            case 'res_asc':
                $db_sort = 'ORDER BY Reson ASC';
                $nav_sort = '&amp;sort=res_asc';
                $Resonsort = 'res_desc';
                break;
            case 'res_desc':
                $db_sort = 'ORDER BY Reson DESC';
                $nav_sort = '&amp;sort=res_desc';
                $Resonsort = 'res_asc';
                break;
            case 'typ_asc':
                $db_sort = 'ORDER BY Type ASC';
                $nav_sort = '&amp;sort=typ_asc';
                $Typesort = 'typ_desc';
                break;
            case 'typ_desc':
                $db_sort = 'ORDER BY Type DESC';
                $nav_sort = '&amp;sort=typ_desc';
                $Typesort = 'typ_asc';
                break;
            case 'tst_asc':
                $db_sort = 'ORDER BY TimeStart ASC';
                $nav_sort = '&amp;sort=tst_asc';
                $TimeStartsort = 'tst_desc';
                break;
            case 'tst_desc':
                $db_sort = 'ORDER BY TimeStart DESC';
                $nav_sort = '&amp;sort=tst_desc';
                $TimeStartsort = 'tst_asc';
                break;
            case 'tend_asc':
                $db_sort = 'ORDER BY TimeEnd ASC';
                $nav_sort = '&amp;sort=tend_asc';
                $TimeEndsort = 'tend_desc';
                break;
            case 'tend_desc':
                $db_sort = 'ORDER BY TimeEnd DESC';
                $nav_sort = '&amp;sort=tend_desc';
                $TimeEndsort = 'tend_asc';
                break;
            case 'name_asc':
                $db_sort = 'ORDER BY Name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $Namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name DESC';
                $nav_sort = '&amp;sort=name_desc';
                $Namesort = 'name_asc';
                break;
            case 'mail_asc':
                $db_sort = 'ORDER BY Email ASC';
                $nav_sort = '&amp;sort=mail_asc';
                $Emailsort = 'mail_desc';
                break;
            case 'mail_desc':
                $db_sort = 'ORDER BY Email DESC';
                $nav_sort = '&amp;sort=mail_desc';
                $Emailsort = 'mail_asc';
                break;
            case 'ip_asc':
                $db_sort = 'ORDER BY Ip ASC';
                $nav_sort = '&amp;sort=ip_asc';
                $Ipsort = 'ip_desc';
                break;
            case 'ip_desc':
                $db_sort = 'ORDER BY Ip DESC';
                $nav_sort = '&amp;sort=ip_desc';
                $Ipsort = 'ip_asc';
                break;
        }
        $this->_view->assign('User_idsort', $User_idsort);
        $this->_view->assign('Resonsort', $Resonsort);
        $this->_view->assign('Typesort', $Typesort);
        $this->_view->assign('TimeStartsort', $TimeStartsort);
        $this->_view->assign('TimeEndsort', $TimeEndsort);
        $this->_view->assign('Namesort', $Namesort);
        $this->_view->assign('Emailsort', $Emailsort);
        $this->_view->assign('Ipsort', $Ipsort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.* ');
            $def_search_n = "&amp;q=" . urlencode($pattern);

            $_REQUEST['seltab'] = !empty($_REQUEST['seltab']) ? $_REQUEST['seltab'] : '';
            switch ($_REQUEST['seltab']) {
                case '1':
                    $def_search = "WHERE (User_id LIKE '%{$pattern}%') ";
                    break;

                case '2':
                    $def_search = "WHERE (Name LIKE '%{$pattern}%') ";
                    break;

                case '3':
                    $def_search = "WHERE (Email LIKE '%{$pattern}%') ";
                    break;

                case '4':
                    $def_search = "WHERE (Ip LIKE '%{$pattern}%') ";
                    break;

                case '5':
                    $def_search = "WHERE (Reson LIKE '%{$pattern}%') ";
                    break;

                default:
                case 'all':
                    $def_search = "WHERE (User_id LIKE '%{$pattern}%' OR Name LIKE '%{$pattern}%' OR Email LIKE '%{$pattern}%' OR Ip LIKE '%{$pattern}%' OR Reson LIKE '%{$pattern}%') ";
                    break;
            }
        }

        $limit = $this->__object('AdminCore')->limit(10);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_banned {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $banned = array();
        while ($row = $sql->fetch_object()) {
            $banned[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=banned{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('banned', $banned);
        $this->_view->content('/banned/banned.tpl');
    }

}
