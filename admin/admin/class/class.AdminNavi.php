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

class AdminNavi extends Magic {

    public function speedbar() {
        if (Arr::getPost('new') == 1 && !empty($_POST['Name_1']) && !empty($_POST['Dokument'])) {
            $n1 = $_POST['Name_1'];
            $n2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $n1;
            $n3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $n1;
            $insert_array = array(
                'Sektion'  => AREA,
                'Name_1'   => $n1,
                'Name_2'   => $n2,
                'Name_3'   => $n3,
                'Dokument' => Arr::getPost('Dokument'),
                'Position' => Arr::getPost('Position'),
                'Ziel'     => '_self',
                'Aktiv'    => 1);
            $this->_db->insert_query('quicknavi', $insert_array);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1 && isset($_POST['Name_1'])) {
            foreach (array_keys($_POST['Name_1']) as $nid) {
                if (!empty($_POST['Name_1'][$nid])) {
                    $n1 = $_POST['Name_1'][$nid];
                    $n2 = (!empty($_POST['Name_2'][$nid])) ? $_POST['Name_2'][$nid] : $n1;
                    $n3 = (!empty($_POST['Name_3'][$nid])) ? $_POST['Name_3'][$nid] : $n1;
                    $array = array(
                        'Name_1'   => $n1,
                        'Name_2'   => $n2,
                        'Name_3'   => $n3,
                        'Dokument' => $_POST['Dokument'][$nid],
                        'Position' => $_POST['Position'][$nid],
                        'Ziel'     => $_POST['Ziel'][$nid],
                        'Aktiv'    => $_POST['Aktiv'][$nid],
                        'Gruppe'   => $_POST['Gruppe'][$nid],
                    );
                    $this->_db->update_query('quicknavi', $array, "Id='" . intval($nid) . "'");

                    if (isset($_POST['del'][$nid]) && $_POST['del'][$nid] == 1) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_quicknavi WHERE Id='" . intval($nid) . "'");
                    }
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $navis = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_quicknavi WHERE Sektion = '" . $_SESSION['a_area'] . "' ORDER BY Position ASC");

        $this->_view->assign('aGroups', $this->avail());
        $this->_view->assign('navis', $navis);
        $this->_view->assign('title', $this->_lang['Quicknavi']);
        $this->_view->content('/navigation/speedbar.tpl');
    }

    protected function avail() {
        $groups = array(
            'articles'     => $this->_lang['Sections_articles'],
            'downloads'    => $this->_lang['Downloads'],
            'faq'          => $this->_lang['Sections_faq'],
            'gallery'      => $this->_lang['Gallery'],
            'links'        => $this->_lang['Sections_links'],
            'manufacturer' => $this->_lang['Manufacturer'],
            'newsarchive'  => $this->_lang['News'],
            'poll'         => $this->_lang['Polls'],
            'products'     => $this->_lang['Products'],
            'index'        => $this->_lang['StartPage'],
            'shop'         => $this->_lang['Global_Shop'],
            'calendar'     => $this->_lang['Sections_calendar'],
            'cheats'       => $this->_lang['Gaming_cheats']);
        return $groups;
    }

    public function showMenu() {
        if (!perm('navigation_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1) {
            $t1 = $_POST['Name_1'];
            $t2 = !empty($_REQUEST['Name_2']) ? $_REQUEST['Name_2'] : $t1;
            $t3 = !empty($_REQUEST['Name_3']) ? $_REQUEST['Name_3'] : $t1;
            $insert_array = array(
                'Name_1'   => $t1,
                'Name_2'   => $t2,
                'Name_3'   => $t3,
                'Sektion'  => $_SESSION['a_area'],
                'Position' => intval(Arr::getPost('Position')),
                'Aktiv'    => Arr::getPost('Aktiv'));
            $this->_db->insert_query('navi_cat', $insert_array);
            $this->__object('Redir')->redirect('index.php?do=navigation&sub=edit&id=' . $this->_db->insert_id());
        }

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['nid']) as $nid) {
                if (!empty($_POST['Name_1'][$nid])) {
                    $t1 = $_POST['Name_1'][$nid];
                    $t2 = (!empty($_POST['Name_2'][$nid])) ? $_POST['Name_2'][$nid] : $t1;
                    $t3 = (!empty($_POST['Name_3'][$nid])) ? $_POST['Name_3'][$nid] : $t1;
                    $array = array(
                        'Name_1'   => $t1,
                        'Name_2'   => $t2,
                        'Name_3'   => $t3,
                        'Position' => $_POST['Position'][$nid],
                        'Aktiv'    => $_POST['Aktiv'][$nid],
                    );
                    $this->_db->update_query('navi_cat', $array, "Id = '" . intval($nid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $navis = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_navi_cat WHERE Sektion = '" . $_SESSION['a_area'] . "' ORDER BY Id ASC");

        $this->_view->assign('navis', $navis);
        $this->_view->assign('title', $this->_lang['Navigation_list']);
        $this->_view->content('/navigation/navi_categs.tpl');
    }

    public function deleteMenu($id) {
        if (perm('navigation_edit')) {
            $id = intval($id);
            $this->_db->query("DELETE FROM " . PREFIX . "_navi WHERE NaviCat = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_navi_cat WHERE Id = '" . $id . "'");
            $this->__object('Redir')->redirect('index.php?do=navigation');
        }
    }

    public function editMenu($id) {
        if (!perm('navigation_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Titel_1']) as $nid) {
                if (!empty($_POST['Titel_1'][$nid]) && !empty($_POST['Dokument'][$nid])) {
                    $array = array(
                        'Link_Titel_1' => $_POST['Link_Titel_1'][$nid],
                        'Titel_1'      => $_POST['Titel_1'][$nid],
                        'Dokument'     => $_POST['Dokument'][$nid],
                        'Position'     => $_POST['Position'][$nid],
                        'Ziel'         => $_POST['Ziel'][$nid],
                        'Aktiv'        => $_POST['Aktiv'][$nid],
                    );
                    $this->_db->update_query('navi', $array, "Id='" . intval($nid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('title', $this->_lang['Navigation_docsedit']);
        $this->_view->assign('items', $this->navigation($id));
        $this->_view->content('/navigation/navi_show.tpl');
    }

    public function addResource($navi) {
        if (!perm('navigation_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $t1 = $_POST['Titel_1'];
            $t2 = !empty($_REQUEST['Titel_2']) ? $_REQUEST['Titel_2'] : $t1;
            $t3 = !empty($_REQUEST['Titel_3']) ? $_REQUEST['Titel_3'] : $t1;
            $group_id = isset($_POST['group_id']) ? implode(',', Arr::getPost('group_id')) : '';
            $insert_array = array(
                'NaviCat'      => intval($navi),
                'ParentId'     => intval(Arr::getPost('ParentId')),
                'Titel_1'      => $t1,
                'Titel_2'      => $t2,
                'Titel_3'      => $t3,
                'Dokument'     => Arr::getPost('Dokument'),
                'DokumentRub'  => '',
                'Sektion'      => $_SESSION['a_area'],
                'openonclick'  => Arr::getPost('openonclick'),
                'group_id'     => $group_id,
                'Position'     => intval(Arr::getPost('Position')),
                'Ziel'         => Arr::getPost('Ziel'),
                'Link_Titel_1' => Arr::getPost('Link_Titel_1'),
                'Link_Titel_2' => Arr::getPost('Link_Titel_2'),
                'Link_Titel_3' => Arr::getPost('Link_Titel_3'));
            $this->_db->insert_query('navi', $insert_array);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('title', $this->_lang['Navigation_newdoc']);
        $this->_view->assign('items', $this->navigation($navi));
        $this->_view->assign('groups', $this->groups());
        $this->_view->content('/navigation/edit_navidoc.tpl');
    }

    public function deleteResource($id, $navi) {
        if (perm('navigation_edit')) {
            $id = intval($id);
            $r_sub_navi = $this->_db->query("SELECT id FROM " . PREFIX . "_navi WHERE ParentId = '" . $id . "' ");
            while ($sub_navi = $r_sub_navi->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_navi WHERE ParentId = '" . $sub_navi->id . "'");
            }
            $r_sub_navi->close();
            $this->_db->query("DELETE FROM " . PREFIX . "_navi WHERE ParentId = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_navi WHERE Id = '" . $id . "'");
        }
        $this->__object('Redir')->redirect('index.php?do=navigation&sub=edit&id=' . $navi);
    }

    public function editResource($id) {
        if (!perm('navigation_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Link_Titel_1' => Arr::getPost('Link_Titel_1'),
                'Link_Titel_2' => Arr::getPost('Link_Titel_2'),
                'Link_Titel_3' => Arr::getPost('Link_Titel_3'),
                'Titel_1'      => Arr::getPost('Titel_1'),
                'Titel_2'      => Arr::getPost('Titel_2'),
                'Titel_3'      => Arr::getPost('Titel_3'),
                'Dokument'     => Arr::getPost('Dokument'),
                'openonclick'  => Arr::getPost('openonclick'),
                'group_id'     => implode(',', $_POST['group_id']),
                'Position'     => Arr::getPost('Position'),
                'Ziel'         => Arr::getPost('Ziel'),
            );
            $this->_db->update_query('navi', $array, "Id='" . $id . "'");

            if (Arr::getPost('perms_to_section') == 1) {
                $this->_db->query("UPDATE " . PREFIX . "_navi SET group_id = '" . $this->_db->escape(implode(',', $_POST['group_id'])) . "' WHERE Id!=0 AND NaviCat='" . intval(Arr::getPost('categ')) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_navi WHERE Id='" . $id . "' LIMIT 1");
        $res->Gruppen = explode(',', $res->group_id);
        $this->_view->assign('title', $this->_lang['Navigation_docedit']);
        $this->_view->assign('res', $res);
        $this->_view->assign('groups', $this->groups());
        $this->_view->content('/navigation/edit_navidoc.tpl');
    }

    protected function navigation($id) {
        $id = intval($id);
        $items = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_navi WHERE NaviCat='" . $id . "' AND ParentId='0' ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $items_2 = array();
            $sql_2 = $this->_db->query("SELECT * FROM " . PREFIX . "_navi WHERE NaviCat='" . $id . "' AND ParentId='$row->Id' ORDER BY Position ASC");
            while ($row_2 = $sql_2->fetch_object()) {
                $items_3 = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_navi WHERE NaviCat='" . $id . "' AND ParentId='$row_2->Id' ORDER BY Position ASC");
                $row_2->sub2 = $items_3;
                $items_2[] = $row_2;
            }
            $row->sub1 = $items_2;
            $items[] = $row;
        }
        $sql->close();
        return $items;
    }

    protected function groups() {
        $groups = $this->_db->fetch_object_all("SELECT Id AS ugroup, Name AS groupname FROM  " . PREFIX . "_benutzer_gruppen");
        return $groups;
    }

    public function editFlashtag($id, $title, $size, $url) {
        if (!perm('navigation_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($title) && !empty($url)) {
            $array = array(
                'Title'    => Tool::cleanAllow($title, ' '),
                'Size'     => $size,
                'Dokument' => Tool::cleanUrl($url),
            );
            $this->_db->update_query('navi_flashtag', $array, "id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('vkl', 1);
            $this->_view->assign('error', 1);
            SX::output("<script type=\"text/javascript\">alert('" . $this->_lang['Validate_required'] . "')</script>");
        }
        $this->showFlashtag();
    }

    public function addFlashtag($title, $size, $url) {
        if (!perm('navigation_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($title)) {
            $insert_array = array(
                'Title'    => Tool::cleanAllow($title, ' '),
                'Size'     => $size,
                'Dokument' => Tool::cleanUrl($url),
                'Aktiv'    => 1);
            $this->_db->insert_query('navi_flashtag', $insert_array);
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('error', 1);
            SX::output("<script type=\"text/javascript\">alert('" . $this->_lang['Validate_required'] . "')</script>");
        }
        $this->showFlashtag();
    }

    public function deleteFlashtag($id) {
        if (!perm('navigation_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_navi_flashtag WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->script('save');
        $this->showFlashtag();
    }

    public function cleanFlashtag() {
        if (!perm('navigation_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (perm('seo_del')) {
            Tool::cleanTable('navi_flashtag');
        }
        $this->__object('AdminCore')->script('save');
        $this->showFlashtag();
    }

    public function activeFlashtag($type, $id) {
        if (isset($type) && !empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_navi_flashtag SET Aktiv='" . intval($type) . "' WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showFlashtag();
    }

    public function getFlashtag($id) {
        if (!empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_navi_flashtag WHERE Id = '" . intval($id) . "' LIMIT 1");
            $this->_view->assign('vkl', 1);
            $this->_view->assign('row', $row);
        }
        $this->showFlashtag();
    }

    public function showFlashtag() {
        $db_sort = " ORDER BY Title ASC";
        $nav_sort = "&amp;sort=name_asc";
        $namesort = $sizesort = $docsort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Title ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Title DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'size_asc':
                $db_sort = 'ORDER BY Size ASC';
                $nav_sort = '&amp;sort=size_asc';
                $sizesort = 'size_desc';
                break;
            case 'size_desc':
                $db_sort = 'ORDER BY Size DESC';
                $nav_sort = '&amp;sort=size_desc';
                $sizesort = 'size_asc';
                break;
            case 'doc_asc':
                $db_sort = 'ORDER BY Dokument ASC';
                $nav_sort = '&amp;sort=doc_asc';
                $docsort = 'doc_desc';
                break;
            case 'doc_desc':
                $db_sort = 'ORDER BY Dokument DESC';
                $nav_sort = '&amp;sort=doc_desc';
                $docsort = 'doc_asc';
                break;
        }
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('sizesort', $sizesort);
        $this->_view->assign('docsort', $docsort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $def_search = " WHERE (Title LIKE '%{$pattern}%' OR Dokument LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $limit = $this->__object('AdminCore')->limit(20);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_navi_flashtag {$def_search} {$db_sort} LIMIT $a, $limit");
        $items = array();
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=navigation&amp;sub=flashtag{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->content('/navigation/flashtag.tpl');
    }

}
