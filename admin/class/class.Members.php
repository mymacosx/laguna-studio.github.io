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

class Members extends Magic {

    protected function rank($posts) {
        $posts = ($posts < 1) ? 1 : $posts;
        $rank = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_rank WHERE count <= '" . intval($posts) . "' ORDER BY count DESC LIMIT 1");
        return is_object($rank) ? $rank->title : false;
    }

    public function get() {
        $num = 1;
        $seename = '';

        if (!permission('showuserpage')) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        } else {
            if (Arr::getRequest('ud') == 'ASC') {
                $ascdesc = 'ASC';
                $this->_view->assign('ud1', 'selected');
            } else {
                $ascdesc = $_REQUEST['ud'] = 'DESC';
                $this->_view->assign('ud2', 'selected="selected"');
            }

            $selbys = Arr::getRequest('selby');
            switch ($selbys) {
                case 'username':
                    $sortby = ' ORDER BY Benutzername';
                    $this->_view->assign('sel1', 'selected="selected"');
                    break;

                case 'posts':
                    $sortby = ' ORDER BY Beitraege';
                    $this->_view->assign('sel2', 'selected="selected"');
                    break;

                case 'joindate':
                    $sortby = ' ORDER BY Regdatum';
                    $this->_view->assign('sel3', 'selected="selected"');
                    break;

                default:
                    $sortby = ' ORDER BY Beitraege';
                    break;
            }

            if (!empty($_REQUEST['suname'])) {
                $seename = " AND (Benutzername like '" . $this->_db->escape(Arr::getRequest('suname')) . "%')";
                $sortby = $ascdesc = '';
            }

            $limit = Tool::getLim(15);
            $a = Tool::getLimit($limit);
            $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Gruppe!='2' $sortby $seename $ascdesc LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $table_data = array();
            $i = 2;
            $theme = SX::get('options.theme');
            while ($row = $sql->fetch_object()) {
                $beitraege_obj = $this->_db->cache_fetch_object("SELECT COUNT(id) AS Beitraege FROM " . PREFIX . "_f_post WHERE uid = '" . $row->Id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = '" . $beitraege_obj->Beitraege . "' WHERE Id = '" . $row->Id . "'");
                $useraim = '';
                $row->Icq_User = !empty($row->icq) ? '<a class="user_pop" href="index.php?p=misc&amp;do=icq&amp;uid=' . $row->Id . '"><img border="0" src="theme/' . $theme . '/images/forums/icq.png" alt="" /></a>' : '';
                $row->Pn_User = ($row->Pnempfang == 1 && $_SESSION['user_group'] != 2) ? '<a href="index.php?p=pn&amp;action=new&amp;to=' . base64_encode($row->Benutzername) . '"><img border="0" src="theme/' . $theme . '/images/forums/pn.png" alt="" /></a>' : '';
                $row->Email_User = ($row->Emailempfang == 1 && $_SESSION['user_group'] != 2) ? '<a class="user_pop" href="index.php?p=misc&amp;do=email&amp;uid=' . $row->Id . '"><img border="0" src="theme/' . $theme . '/images/forums/mail.png" alt="" /></a>' : '';
                $row->Skype_User = !empty($row->skype) ? '<a class="user_pop" href="index.php?p=misc&amp;do=skype&amp;uid=' . $row->Id . '"><img border="0" src="theme/' . $theme . '/images/forums/skype.png" alt="«вонок через —кайп" /></a>' : '';
                $row->Webseite = !empty($row->Webseite) ? Tool::checkSheme($row->Webseite) : '';
                $usergroup = Tool::userName($row->Gruppe);
                $userlink = 'index.php?p=user&amp;id=' . $row->Id . '&amp;area=' . AREA;
                $entry_array = array(
                    'avatar'        => $this->__object('Avatar')->load($row->Gravatar, $row->Email, $row->Gruppe, $row->Avatar, $row->Avatar_Default, 100),
                    'Skype_User'    => $row->Skype_User,
                    'name'          => $row->Benutzername,
                    'usergroup'     => $usergroup,
                    'userlink'      => $userlink,
                    'user_aim'      => $useraim,
                    'user_msn'      => $row->msn,
                    'Icq_User'      => $row->Icq_User,
                    'posts'         => $row->Beitraege,
                    'Pn_User'       => $row->Pn_User,
                    'Email_User'    => $row->Email_User,
                    'regtime'       => $row->Regdatum,
                    'gruppe'        => $row->Gruppe,
                    'team'          => $row->Team,
                    'teamName'      => $this->_lang['WebTeam'] . SX::get('system.Seitenname'),
                    'rank'          => $this->rank($row->Beitraege),
                    'Webseite'      => $row->Webseite,
                    'Profil_public' => $row->Profil_public
                );
                unset($useraim, $userlink, $usergroup);
                $i++;
                $table_data[] = $entry_array;
            }
            $sql->close();

            $pp_l = '';
            for ($i = 10; $i <= 50; $i += 10) {
                $isel = (Arr::getRequest('pp') == $i) ? 'selected' : '';
                $pp_l .= '<option value="' . $i . '" ' . $isel . '>' . $i . ' ' . $this->_lang['eachpage'] . '</option>';
            }

            $rud = !empty($_REQUEST['ud']) ? '&amp;ud=' . $_REQUEST['ud'] : '';
            $selby = !empty($_REQUEST['selby']) ? '&amp;selby=' . $_REQUEST['selby'] : '';
            $nav = $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=members{$rud}{$selby}&amp;pp=" . $limit . "&amp;page={s}\">{t}</a> ");

            $tpl_array = array(
                'pagenav'    => $nav,
                'pp_l'       => $pp_l,
                'table_data' => $table_data);
            $this->_view->assign($tpl_array);

            if ($num) {
                $this->_view->assign('found', 1);
            }

            $seo_array = array(
                'headernav' => $this->_lang['Forums_useroverview'],
                'pagetitle' => $this->_lang['Forums_useroverview'] . Tool::numPage(),
                'content'   => $this->_view->fetch(THEME . '/forums/userlist.tpl'));
            $this->_view->finish($seo_array);
        }
    }

}
