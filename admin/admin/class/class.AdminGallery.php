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

class AdminGallery extends Magic {

    public $_settings = array();

    public function __construct() {
        $this->_settings = SX::get('galerie');
        $this->_view->assign('gs', $this->_settings);
    }

    public function settings() {
        if (!perm('gallery_overview')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $image = !empty($_POST['newImg_1']) ? Arr::getPost('newImg_1') : Arr::getPost('watermark_old');
            $array = array(
                'GTyp'                   => Arr::getPost('GTyp'),
                'Bilder_Klein'           => Arr::getPost('Bilder_Klein'),
                'Bilder_Mittel'          => Arr::getPost('Bilder_Mittel'),
                'Bilder_Gross'           => Arr::getPost('Bilder_Gross'),
                'Bilder_Seite'           => Arr::getPost('Bilder_Seite'),
                'Bilder_Zeile'           => Arr::getPost('Bilder_Zeile'),
                'Limit_Start'            => Arr::getPost('Limit_Start'),
                'Sortierung_Start'       => Arr::getPost('Sortierung_Start'),
                'Zufall_Start'           => (Arr::getPost('Sortierung_Start') == 'RAND' ? 'TRUE' : 'FALSE'),
                'Wasserzeichen'          => Arr::getPost('Wasserzeichen'),
                'Wasserzeichen_Vorschau' => Arr::getPost('Wasserzeichen_Vorschau'),
                'Watermark_Position'     => Arr::getPost('Watermark_Position'),
                'Watermark_File'         => $image,
                'Transparenz'            => Arr::getPost('Transparenz'),
                'Quali_Gross'            => Arr::getPost('Quali_Gross'),
                'Diashow_Zeit'           => Arr::getPost('Diashow_Zeit'),
                'Banner_Code'            => Arr::getPost('Banner_Code'),
                'Kommentare'             => Arr::getPost('Kommentare'),
                'Favoriten'              => Arr::getPost('Favoriten'),
                'Download'               => Arr::getPost('Download'),
                'Meist_Gesehen'          => Arr::getPost('Meist_Gesehen'),
                'Kategorien_zeile'       => 1,
                'Kategorie_Icon_Breite'  => Arr::getPost('Bilder_Mittel'),
                'Info_Klein'             => Arr::getPost('Info_Klein'));
            SX::save('galerie', $array);

            if (Arr::getPost('renew') == 1) {
                Folder::clean(TEMP_DIR . '/cache/');
            }
            $this->__object('Redir')->redirect('index.php?do=gallery&sub=gallerysettings');
        }

        if (empty($this->_settings['Watermark_File']) || !is_file(UPLOADS_DIR . '/watermarks/' . $this->_settings['Watermark_File'])) {
            $this->_settings['Watermark_File'] = 'watermark.png';
        }

        $this->_view->assign('res', $this->_settings);
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' ' . $this->_lang['Gallery']);
        $this->_view->content('/gallery/settings.tpl');
    }

    public function showCategs() {
        if (!perm('gallery_overview')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $lid) {
                $this->_db->query("UPDATE " . PREFIX . "_galerie_kategorien SET Aktiv = '" . $this->_db->escape($_POST['Aktiv'][$lid]) . "' WHERE Id = '" . intval($lid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $LC = 1;
        $db_sort = " ORDER BY Name_1 ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $activesort = $imgsort = $usersort = $def_search_n = $def_search = $namesort = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Name_1 ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name_1 DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'date_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'img_asc':
                $db_sort = 'ORDER BY Bilder ASC';
                $nav_sort = '&amp;sort=img_asc';
                $imgsort = 'img_desc';
                break;
            case 'img_desc':
                $db_sort = 'ORDER BY Bilder DESC';
                $nav_sort = '&amp;sort=img_desc';
                $imgsort = 'img_asc';
                break;
            case 'active_asc':
                $db_sort = 'ORDER BY Aktiv ASC';
                $nav_sort = '&amp;sort=active_asc';
                $activesort = 'active_desc';
                break;
            case 'active_desc':
                $db_sort = 'ORDER BY Aktiv DESC';
                $nav_sort = '&amp;sort=active_desc';
                $activesort = 'active_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY Autor ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY Autor DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $def_search = " AND (Name_1 LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('imgsort', $imgsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('usersort', $usersort);
        $a_area = $_SESSION['a_area'];

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS Id, Name_{$LC} AS Name, Text_{$LC} AS Text, Bild, Tags, Autor, Datum, Aktiv FROM " . PREFIX . "_galerie_kategorien WHERE Sektion = '" . $a_area . "' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $galleries = array();
        while ($row = $sql->fetch_object()) {
            $row->User = Tool::userName($row->Autor);
            if ($row->Tags) {
                $row->Tags = array_unique(explode(',', $row->Tags));
                sort($row->Tags);
            }
            $galleries[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('GalNavi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=gallery{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }

        $this->_view->assign('title', $this->_lang['Gallery_categoverview']);
        $this->_view->assign('galleries', $galleries);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('def_sort_n', $nav_sort);
        $this->_view->content('/gallery/galleryshow.tpl');
    }

    public function deleteCateg($id) {
        if (perm('gallery_delete')) {
            $id = intval($id);
            $sql = $this->_db->query("SELECT Id,Parent_Id FROM " . PREFIX . "_galerie WHERE Kategorie='" . $id . "'");
            while ($row_g = $sql->fetch_object()) {
                $this->delete($row_g->Id, '0');
            }
            $sql->close();
            $this->_db->query("DELETE FROM " . PREFIX . "_galerie_kategorien WHERE Id='" . $id . "'");
            $this->__object('AdminCore')->backurl();
        }
    }

    public function addCateg() {
        if (!perm('gallery_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $tags = '';
            if (!empty($_POST['Tags'])) {
                $tags = str_replace(array("\n", "\r\n"), '', $_POST['Tags']);
                $tags = str_replace(array(' ,', ', ', ',,'), ',', Tool::cleanSpace($tags));
            }

            $img = !empty($_POST['newImg_1']) ? Tool::cleanAllow($_POST['newImg_1'], '.') : '';
            $Name_1 = Arr::getPost('Name_1');
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $_POST['Name_1'];
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $_POST['Name_1'];
            $Text_1 = Arr::getPost('Text_1');
            $Text_2 = !empty($_POST['Text_2']) ? $_POST['Text_2'] : $Text_1;
            $Text_3 = !empty($_POST['Text_3']) ? $_POST['Text_3'] : $Text_1;

            $insert_array = array(
                'Name_1'  => $Name_1,
                'Name_2'  => $Name_2,
                'Name_3'  => $Name_3,
                'Text_1'  => $Text_1,
                'Text_2'  => $Text_2,
                'Text_3'  => $Text_3,
                'Aktiv'   => Arr::getPost('Aktiv'),
                'Tags'    => $tags,
                'Bild'    => $img,
                'Datum'   => time(),
                'Autor'   => $_SESSION['benutzer_id'],
                'Sektion' => $_SESSION['a_area']);
            $this->_db->insert_query('galerie_kategorien', $insert_array);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('title', $this->_lang['GlobalAddCateg']);
        $this->_view->assign('Beschreibung_1', $this->__object('Editor')->load('admin', ' ', 'Text_1', 150, 'Basic'));
        $this->_view->assign('Beschreibung_2', $this->__object('Editor')->load('admin', ' ', 'Text_2', 150, 'Basic'));
        $this->_view->assign('Beschreibung_3', $this->__object('Editor')->load('admin', ' ', 'Text_3', 150, 'Basic'));
        $this->_view->content('/gallery/newcateg.tpl');
    }

    public function editCateg($id) {
        if (!perm('gallery_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $tags = str_replace(array("\n", "\r\n"), '', $_POST['Tags']);
            $tags = str_replace(array(' ,', ', ', ',,'), ',', Tool::cleanSpace($tags));
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $_POST['Name_1'];
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $_POST['Name_1'];
            $Text_1 = $_POST['Text_1'];
            $Text_2 = !empty($_POST['Text_2']) ? $_POST['Text_2'] : $Text_1;
            $Text_3 = !empty($_POST['Text_3']) ? $_POST['Text_3'] : $Text_1;

            if ((Arr::getPost('delold') == 1) || !empty($_POST['newImg_1'])) {
                File::delete(UPLOADS_DIR . '/galerie_icons/' . $_POST['oldimg']);
                $newImg = Tool::cleanAllow($_POST['newImg_1'], '.');
            } else {
                $newImg = Tool::cleanAllow(Arr::getPost('oldimg'), '.');
            }

            $array = array(
                'Name_1' => Arr::getPost('Name_1'),
                'Name_2' => $Name_2,
                'Name_3' => $Name_3,
                'Text_1' => $Text_1,
                'Text_2' => $Text_2,
                'Text_3' => $Text_3,
                'Aktiv'  => Arr::getPost('Aktiv'),
                'Tags'   => $tags,
                'Bild'   => $newImg,
            );
            $this->_db->update_query('galerie_kategorien', $array, "Id='" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_galerie_kategorien WHERE Id='" . $id . "' LIMIT 1");
        $res->Beschreibung_1 = $this->__object('Editor')->load('admin', $res->Text_1 . ' ', 'Text_1', 150, 'Basic');
        $res->Beschreibung_2 = $this->__object('Editor')->load('admin', $res->Text_2 . ' ', 'Text_2', 150, 'Basic');
        $res->Beschreibung_3 = $this->__object('Editor')->load('admin', $res->Text_3 . ' ', 'Text_3', 150, 'Basic');
        $res->Img = (!empty($res->Bild) && is_file(UPLOADS_DIR . '/galerie_icons/' . $res->Bild)) ? '<img src="../uploads/galerie_icons/' . $res->Bild . '" />' : '';
        $res->ImgPath = $res->Bild;
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Gallery_editCateg']);
        $this->_view->content('/gallery/editcateg.tpl');
    }

    public function addGallery($id) {
        if (!perm('gallery_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1 && !empty($_POST['Name_1'])) {
            $tags = '';
            if (!empty($_POST['Tags'])) {
                $tags = str_replace(array("\n", "\r\n"), '', $_POST['Tags']);
                $tags = str_replace(array(' ,', ', ', ',,'), ',', Tool::cleanSpace($tags));
            }

            $insert_array = array(
                'Kategorie'      => $id,
                'Parent_Id'      => Arr::getRequest('thegal'),
                'Sektion'        => $_SESSION['a_area'],
                'Name_1'         => Arr::getPost('Name_1'),
                'Name_2'         => Arr::getPost('Name_1'),
                'Name_3'         => Arr::getPost('Name_1'),
                'Beschreibung_1' => Arr::getPost('Beschreibung_1'),
                'Beschreibung_2' => Arr::getPost('Beschreibung_1'),
                'Beschreibung_3' => Arr::getPost('Beschreibung_1'),
                'Datum'          => time(),
                'Autor'          => $_SESSION['benutzer_id'],
                'Tags'           => $tags,
                'Aktiv'          => 1);
            $this->_db->insert_query('galerie', $insert_array);
            $this->__object('AdminCore')->script('close');
        }

        $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_galerie_kategorien WHERE Id='" . $id . "' LIMIT 1");
        $gallery = array();
        $this->load(0, '', $gallery, $_SESSION['a_area'], '', '', '', '', $_REQUEST['id'], '1000');
        $this->_view->assign('Beschreibung_1', $this->__object('Editor')->load('admin', ' ', 'Beschreibung_1', 150, 'Basic'));
        $this->_view->assign('title', $this->_lang['Gallery']);
        $this->_view->assign('gallery', $gallery);
        $this->_view->assign('res', $res);
        $this->_view->content('/gallery/gallerynew.tpl');
    }

    public function included($id) {
        if (!perm('gallery_overview')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $area = $_SESSION['a_area'];
        $namesort = 'name_desc';
        $datesort = $activesort = $imgsort = $usersort = $def_search_n = $def_search = '';

        if (Arr::getPost('save') == 1 && perm('gallery_edit')) {
            foreach ($_POST['galleryid'] as $gid) {
                $this->_db->query("UPDATE " . PREFIX . "_galerie SET Aktiv='" . $this->_db->escape($_POST['Aktiv'][$gid]) . "' WHERE Id='" . intval($gid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_galerie_kategorien WHERE Id='" . $id . "' LIMIT 1");

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern . '. ');
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " AND (Name_1 LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Name_1 ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name_1 DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'date_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'img_asc':
                $db_sort = 'ORDER BY Bilder ASC';
                $nav_sort = '&amp;sort=img_asc';
                $imgsort = 'img_desc';
                break;
            case 'img_desc':
                $db_sort = 'ORDER BY Bilder DESC';
                $nav_sort = '&amp;sort=img_desc';
                $imgsort = 'img_asc';
                break;
            case 'active_asc':
                $db_sort = 'ORDER BY Aktiv ASC';
                $nav_sort = '&amp;sort=active_asc';
                $activesort = 'active_desc';
                break;
            case 'active_desc':
                $db_sort = 'ORDER BY Aktiv DESC';
                $nav_sort = '&amp;sort=active_desc';
                $activesort = 'active_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY Autor ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY Autor DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('imgsort', $imgsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('usersort', $usersort);
        $subg = (!empty($_REQUEST['subgallery'])) ? "WHERE Id='" . intval(Arr::getRequest('subgallery')) . "'" : "WHERE Parent_Id='0'";
        $subghn = (!empty($_REQUEST['subgallery'])) ? intval(Arr::getRequest('subgallery')) : 0;

        $limit = $this->__object('AdminCore')->limit(25);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
                Name_1,
                Id,
                Parent_Id,
                Datum,
                Autor,
                Aktiv,
                Kategorie,
                Bilder
        FROM
                " . PREFIX . "_galerie
                {$subg}
                {$def_search}
        AND Kategorie = '" . $id . "' AND Sektion = '" . $area . "' {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $gallery = array();
        while ($row = $sql->fetch_object()) {
            $img_count_1 = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Bilder FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $row->Id . "'");
            $this->_db->query("UPDATE " . PREFIX . "_galerie SET Bilder='" . $img_count_1->Bilder . "' WHERE Id = '" . $row->Id . "'");
            $subgallery = array();
            $row->subGalleries = $this->subGalls($row->Id, '&nbsp;-&nbsp;', $subgallery, $_SESSION['a_area'], '', '', '', '', '', '1000');
            $row->Bilder = $img_count_1->Bilder;
            $row->User = Tool::userName($row->Autor);
            $gallery[] = $row;
        }
        $sql->close();

        $navigation = $this->navigation($subghn, 'galerie', 'index.php?do=gallery', 'id', 'Id', 'Name_1', '', '', '', 'Parent_Id', $this->_lang['Gallery'], '1');

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=gallery&amp;sub=showincluded&amp;id=" . $_REQUEST['id'] . "&amp;pp={$limit}&amp;subgallery=" . $subghn . "{$nav_sort}{$def_search_n}&amp;page={s}\">{t}</a> "));
        }

        $gallery_dd = array();
        $this->load(0, '', $gallery_dd, $_SESSION['a_area'], '', '', '', '', $_REQUEST['id'], '1000');
        $this->_view->assign('navigation', $navigation);
        $this->_view->assign('title', $this->_lang['Gallery']);
        $this->_view->assign('gallery_dd', $gallery_dd);
        $this->_view->assign('gallery', $gallery);
        $this->_view->assign('res', $res);
        $this->_view->assign('limit', $limit);
        $this->_view->content('/gallery/galleryincluded.tpl');
    }

    protected function navigation($id, $table, $link, $key, $idtype, $nametype, $result = null, $extra = 0, $nav_op = '0', $parent_give = 'Parent_Id', $textra = '', $main_link = '', $linkname = '') {
        $sqlextra = '';
        $p = Arr::getRequest('do');
        $item = $this->_db->cache_fetch_object("SELECT Parent_id, Name_1 as Name, $idtype, $nametype, $parent_give FROM " . PREFIX . "_$table WHERE $idtype = '" . $this->_db->escape($id) . "' LIMIT 1");
        if (is_object($item) && !$item->$nametype) {
            $item->$nametype = $item->DefName;
        }
        if (is_object($item)) {
            if ($item->$parent_give == 0) {
                if ($p == 'gallery') {
                    $GalInf = $this->_db->cache_fetch_object("SELECT Id, Name_1 AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Id = '" . intval(Arr::getRequest('id')) . "' LIMIT 1");
                    return '<a href="' . $link . '">' . $textra . '</a> - <a href="index.php?do=gallery&amp;sub=showincluded&amp;id=' . $GalInf->Id . '">' . $GalInf->Name . '</a> - <a href="index.php?do=gallery&sub=showincluded&id=' . $_REQUEST['id'] . '&subgallery=' . $item->Id . '">' . $item->$nametype . '</a>' . $result;
                } else {
                    return '<a href="index.php?p=' . $link . '">' . $textra . '</a> / <a href="index.php?p=' . $link . '&amp;' . $key . '=' . $item->$idtype . '&amp;name=' . translit($item->Name) . '">' . $item->$nametype . '</a>' . $result;
                }
            }

            if ($extra == 1) {
                $sqlextra = ", $parent_give";
            }
            $q_parent = "SELECT Parent_id,Name_1,Name_1 as Name, $idtype, $nametype, $parent_give, $idtype, $nametype $sqlextra FROM " . PREFIX . "_$table WHERE $idtype = " . $item->$parent_give . " LIMIT 1";
            $parent = $this->_db->cache_fetch_object($q_parent);
            if (!$parent->$nametype) {
                $parent->$nametype = $parent->DefName;
            }
            if ($p == 'gallery') {
                $GalInf = $this->_db->cache_fetch_object("SELECT Id,Name_1,Name_1 AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Id = '" . intval(Arr::getRequest('id')) . "' LIMIT 1");
                $result = ' - <a href="index.php?do=gallery&sub=showincluded&id=' . $_REQUEST['id'] . '&subgallery=' . $item->Id . '">' . $item->$nametype . '</a>' . $result;
            } else {
                $result = ' / <a title="' . $item->$nametype . '" href="index.php?p=' . $link . '&amp;' . $key . '=' . $item->$idtype . '&amp;name=' . translit($item->Name) . '">' . $item->$nametype . '</a>' . $result;
            }
            return $this->navigation($item->$parent_give, $table, $link, $key, $idtype, $nametype, $result, $extra, $nav_op, $parent_give, $textra);
        }
        return '';
    }

    protected function subGalls($id, $prefix, &$subgallery, &$area, $active, $orderby, $ascdesc, $sorting = '0', $categ, $setlim = '0') {
        $id = intval($id);
        $img_count_1 = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Bilder FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $id . "'");
        $this->_db->query("UPDATE " . PREFIX . "_galerie SET Bilder='" . $img_count_1->Bilder . "' WHERE Id = '" . $id . "'");
        $query = $this->_db->query("SELECT Name_1,Id,Parent_Id,Datum,Autor,Aktiv,Kategorie,Bilder FROM " . PREFIX . "_galerie WHERE Parent_Id = '" . $id . "' ORDER BY Name_1 ASC");
        while ($item = $query->fetch_object()) {
            $item->User = Tool::userName($item->Autor);
            $item->Expander = $prefix;
            $item->LinkName = $item->Name_1;
            $subgallery[] = $item;
            $this->subGalls($item->Id, $prefix . ' - ', $subgallery, $area, $active, $orderby, $ascdesc, $sorting, $categ, $setlim);
        }
        $query->close();
        return $subgallery;
    }

    /* Метод формирования ссылок на изображения */
    protected function thumb($image, $id, $width = 140) {
        $result = NULL;
        if (!empty($image)) {
            $file = md5($image . '_' . $id . '_' . $width) . Tool::extension($image, true);
            if (is_file(SX_DIR . '/temp/cache/' . $file)) {
                $result = '../temp/cache/' . $file . '?' . time();
            } else {
                $result = '../lib/image.php?action=gallery&amp;width=' . $width . '&amp;image=' . $id . '&amp;time=' . time();
            }
        }
        return $result;
    }

    /* Метод формирования ссылок на изображения */
    protected function deleteThumb($image, $id) {
        $end = Tool::extension($image, true);
        $array = array(50, $this->_settings['Bilder_Klein'], $this->_settings['Bilder_Mittel'], $this->_settings['Bilder_Gross']);
        foreach ($array as $value) {
            $file = md5($image . '_' . $id . '_' . $value) . $end;
            File::delete(TEMP_DIR . '/cache/' . $file);
        }
    }

    public function addImages($id) {
        if (!perm('gallery_addimages')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getRequest('autosave') == 1) {
            $target = UPLOADS_DIR . '/galerie/';
            $source = UPLOADS_DIR . '/galerie_autoupload/' . $_REQUEST['source'] . '/';
            $allowed = array('.jpg', '.jpe', '.jpeg', '.png', '.gif');
            $img = array();
            $d = $c = 0;

            $handle = opendir($source);
            while (false !== ($file = readdir($handle))) {
                if (is_dir($source)) {
                    $end = Tool::extension($file, true);
                    if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && (in_array($end, $allowed))) {
                        $c++;
                    }
                }
            }
            closedir($handle);

            $handle = opendir($source);
            while (false !== ($file = readdir($handle))) {
                $obj = '';
                if (is_dir($source)) {
                    if (!in_array($file, array('.', '..', '.htaccess', 'index.php'))) {
                        $end = Tool::extension($file, true);
                        $name = Tool::uniqid($file) . $end;

                        if (in_array($end, $allowed)) {
                            copy($source . $file, $target . $name);
                            $d++;
                            $status = ($d * 100) / $c;

                            $insert_array = array(
                                'Galerie_Id' => intval(Arr::getRequest('thegal')),
                                'Bildname'   => $name,
                                'Datum'      => time(),
                                'Autor'      => $_SESSION['benutzer_id']);
                            $this->_db->insert_query('galerie_bilder', $insert_array);
                            $iid = $this->_db->insert_id();

                            $msgext = ($c == $d) ? 'showNotice(\'<h2>' . $this->_lang['Gallery_upStatusReady'] . '</h2>\', 4000);setTimeout(\'parent.frames.location.reload()\', 2000)' : '$.blockUI({message: \'' . str_replace('__NAME__', $name, $this->_lang['Gallery_upStatus']) . '<br /><br /><div><h2>' . round($status) . '%</h2> ' . $this->_lang['GlobalOk'] . '</div>\'}); ';
                            SX::output('<img style="display:none" class="gallery_categs_img" src="' . $this->thumb($name, $iid, $this->_settings['Bilder_Klein']) . '" border="0" alt="" />');
                            SX::output('<img style="display:none" class="gallery_categs_img" src="' . $this->thumb($name, $iid, $this->_settings['Bilder_Mittel']) . '" border="0" alt="" />');
                            SX::output('<img style="display:none" onload="' . $msgext . '"  class="gallery_categs_img" src="' . $this->thumb($name, $iid, $this->_settings['Bilder_Gross']) . '" border="0" alt="" />');
                        }
                    }
                    $img[] = $obj;
                }
            }
            closedir($handle);
            exit;
        }

        if (Arr::getRequest('manusave') == 1) {
            $options = array(
                'type'   => 'image',
                'result' => 'orig',
                'resize' => Arr::getPost('newsize'),
                'upload' => '/uploads/galerie/',
                'input'  => 'fileToUpload',
            );
            $array = SX::object('Upload')->load($options);
            if (!empty($array)) {
                $result = NULL;
                foreach ($array as $key => $arr) {
                    if ($arr['result'] === true) {
                        $Name = $_POST['Name'][$key];
                        $Text = $_POST['Beschreibung'][$key];

                        $insert_array = array(
                            'Galerie_Id'     => Arr::getRequest('thegal'),
                            'Bildname'       => $arr['load'],
                            'Datum'          => time(),
                            'Autor'          => $_SESSION['benutzer_id'],
                            'Name_1'         => $Name,
                            'Name_2'         => $Name,
                            'Name_3'         => $Name,
                            'Beschreibung_1' => $Text,
                            'Beschreibung_2' => $Text,
                            'Beschreibung_3' => $Text);
                        $this->_db->insert_query('galerie_bilder', $insert_array);
                        $iid = $this->_db->insert_id();
                        $result .= '<strong style="color:green">' . $arr['text'] . ': ' . $arr['load'] . '</strong><br />';
                    } else {
                        $result .= '<strong style="color:red">' . $arr['text'] . ': ' . $arr['file'] . '</strong><br />';
                    }
                }
                SX::output($result);
            }
            exit;
        }

        $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_galerie WHERE Id='" . intval($id) . "' LIMIT 1");
        $gallery = array();
        $this->load(0, '', $gallery, $_SESSION['a_area'], '', '', '', '', $_REQUEST['gid'], 1000);

        $galfolders = '';
        $verzname = UPLOADS_DIR . '/galerie_autoupload/';
        $handle = opendir($verzname);
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && is_dir($verzname . $datei)) {
                $galfolders .= '<option value="' . $datei . '">' . $datei . '</option>';
                $datei = '';
            }
        }
        closedir($handle);

        $this->_view->assign('post_max', $this->__object('AdminCore')->postMaxsize());
        $this->_view->assign('post_maxMb', $this->__object('AdminCore')->postMaxsizeMb());
        $this->_view->assign('galfolders', $galfolders);
        $this->_view->assign('title', $this->_lang['Gallery_AddImages']);
        $this->_view->assign('gallery', $gallery);
        $this->_view->assign('res', $res);
        $this->_view->content('/gallery/addimages.tpl');
    }

    public function editImage($id) {
        if (!perm('gallery_imageedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $res = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_galerie_bilder WHERE Id='" . $id . "' LIMIT 1");

        if (Arr::getPost('save') == 1) {
            $object = $this->__object('Image');
            $file = UPLOADS_DIR . '/galerie/' . $res->Bildname;
            if ($object->open($file)) {
                switch (Arr::getPost('edit_action')) {
                    case 'rotate':
                        $object->rotate(Arr::getPost('degrees', 90));
                        break;
                    case 'grayscale':
                        $object->grayscale();
                        break;
                    case 'brightness':
                        $object->brightness(Arr::getPost('percent'));
                        break;
                    case 'brightness2':
                        $object->brightness(Arr::getPost('percent2'));
                        break;
                    case 'contrast':
                        $object->contrast(Arr::getPost('percent'));
                        break;
                    case 'contrast2':
                        $object->contrast(Arr::getPost('percent2'));
                        break;
                    case 'emboss':
                        $object->emboss();
                        break;
                    case 'negate':
                        $object->negate();
                        break;
                    case 'border':
                        $object->border(Arr::getPost('color', '#000'), Arr::getPost('width', 5));
                        break;
                    case 'smooth':
                        $object->smooth(25);
                        break;
                    case 'flip':
                        $object->flip();
                        break;
                    case 'flop':
                        $object->flop();
                        break;
                    case 'meanremoval':
                        $object->meanremoval();
                        break;
                    case 'edgedetect':
                        $object->edgedetect();
                        break;
                    case 'sepia':
                        $object->sepia();
                        break;
                    case 'picture':
                        $object->picture();
                        break;
                    case 'blurgaussian':
                        $object->blur('gaussian');
                        break;
                    case 'blurselective':
                        $object->blur('selective');
                        break;
                    case 'corners':
                        $object->corners(Arr::getPost('pixel', 5));
                        break;
                }
                $object->save($file);
                $object->close();
            }
            usleep(300000);
            $this->deleteThumb($res->Bildname, $id);
            SX::output('<img style="position:relative" src="' . $this->thumb($res->Bildname, $id, $this->_settings['Bilder_Gross']) . '" border="0" alt="" />', true);
        }

        $imgsize = getimagesize(UPLOADS_DIR . '/galerie/' . $res->Bildname);
        $imgdim = $imgsize[0] . ' x ' . $imgsize[1];
        $res->Img = '<img style="position:relative" src="' . $this->thumb($res->Bildname, $id, $this->_settings['Bilder_Gross']) . '" border="0" alt="" />';

        if ($imgsize[0] > 1280 || $imgsize[1] > 1280) {
            $this->_view->assign('ImgWarning', $imgdim);
        }
        $this->_view->assign('res', $res);
        $this->_view->assign('ImgDim', $imgdim);
        $this->_view->content('/gallery/editimage.tpl');
    }

    protected function newName($string) {
        switch ($string) {
            case 'image/pjpeg':
            case 'image/jpeg':
            default:
                $end = '.jpg';
                break;
            case 'image/x-png':
            case 'image/png':
                $end = '.png';
                break;
            case 'image/gif':
                $end = '.gif';
                break;
        }
        return Tool::uniqid() . $end;
    }

    public function editImages($id) {
        if (!perm('gallery_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $datesort = $hitssort = $autorsort = $name1sort = $name2sort = $name3sort = '';
        $nav_sort = '&amp;sort=date_asc';
        $limit = $this->__object('AdminCore')->limit();

        if (Arr::getPost('save') == 1 && perm('gallery_edit') && isset($_POST['Id'])) {
            foreach ($_POST['Id'] as $imgid) {
                $imgid = intval($imgid);
                $n1 = $_POST['Name_1'][$imgid];
                $n2 = !empty($_POST['Name_2'][$imgid]) ? $_POST['Name_2'][$imgid] : $n1;
                $n3 = !empty($_POST['Name_3'][$imgid]) ? $_POST['Name_3'][$imgid] : $n1;
                $t1 = $_POST['Beschreibung_1'][$imgid];
                $t2 = !empty($_POST['Beschreibung_2'][$imgid]) ? $_POST['Beschreibung_2'][$imgid] : $t1;
                $t3 = !empty($_POST['Beschreibung_3'][$imgid]) ? $_POST['Beschreibung_3'][$imgid] : $t1;

                $array = array(
                    'Name_1'         => $n1,
                    'Name_2'         => $n2,
                    'Name_3'         => $n3,
                    'Beschreibung_1' => $t1,
                    'Beschreibung_2' => $t2,
                    'Beschreibung_3' => $t3,
                );
                $this->_db->update_query('galerie_bilder', $array, "Id='" . intval($imgid) . "'");
                if (isset($_POST['del'][$imgid]) && $_POST['del'][$imgid] == 1) {
                    $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_galerie_bilder WHERE Id='" . $imgid . "' LIMIT 1");
                    $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilder WHERE Id='" . $imgid . "'");
                    File::delete(UPLOADS_DIR . '/galerie/' . $res->Bildname);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
                $db_sort = 'ORDER BY Id DESC';
                $nav_sort = '&amp;sort=date_desc';
                break;
            case 'date_asc':
                $datesort = 'date_desc';
                $db_sort = 'ORDER BY Id ASC';
                $nav_sort = '&amp;sort=date_asc';
                break;
            case 'date_desc':
                $datesort = 'date_asc';
                $db_sort = 'ORDER BY Id DESC';
                $nav_sort = '&amp;sort=date_desc';
                break;
            case 'hits_asc':
                $hitssort = 'hits_desc';
                $db_sort = 'ORDER BY Klicks ASC';
                $nav_sort = '&amp;sort=hits_asc';
                break;
            case 'hits_desc':
                $hitssort = 'hits_asc';
                $db_sort = 'ORDER BY Klicks DESC';
                $nav_sort = '&amp;sort=hits_desc';
                break;
            case 'autor_asc':
                $autorsort = 'autor_desc';
                $db_sort = 'ORDER BY Autor ASC';
                $nav_sort = '&amp;sort=autor_asc';
                break;
            case 'autor_desc':
                $autorsort = 'autor_asc';
                $db_sort = 'ORDER BY Autor DESC';
                $nav_sort = '&amp;sort=autor_desc';
                break;
            case 'name3_asc':
                $name3sort = 'name3_desc';
                $db_sort = 'ORDER BY Name_3 ASC';
                $nav_sort = '&amp;sort=name3_asc';
                break;
            case 'name3_desc':
                $name3sort = 'name3_asc';
                $db_sort = 'ORDER BY Name_3 DESC';
                $nav_sort = '&amp;sort=name3_desc';
                break;
            case 'name2_asc':
                $name2sort = 'name2_desc';
                $db_sort = 'ORDER BY Name_2 ASC';
                $nav_sort = '&amp;sort=name2_asc';
                break;
            case 'name2_desc':
                $name2sort = 'name2_asc';
                $db_sort = 'ORDER BY Name_2 DESC';
                $nav_sort = '&amp;sort=name2_desc';
                break;
            case 'name1_asc':
                $name1sort = 'name1_desc';
                $db_sort = 'ORDER BY Name_1 ASC';
                $nav_sort = '&amp;sort=name1_asc';
                break;
            case 'name1_desc':
                $name1sort = 'name1_asc';
                $db_sort = 'ORDER BY Name_1 DESC';
                $nav_sort = '&amp;sort=name1_desc';
                break;
        }

        $res = $this->_db->fetch_object("SELECT Id, Name_1, Kategorie FROM " . PREFIX . "_galerie WHERE Id='" . $id . "' LIMIT 1");

        $limit = intval($limit);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id='" . $id . "' {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $images = array();
        while ($row = $sql->fetch_object()) {
            $query_c = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Comments FROM " . PREFIX . "_kommentare WHERE Objekt_Id='" . $row->Id . "' AND Bereich='galerie'");
            $row->Comments = $query_c->Comments;
            $row->User = Tool::userName($row->Autor);
            $row->Thumb = '<img class="gallery_categs_img" src="' . $this->thumb($row->Bildname, $row->Id, $this->_settings['Bilder_Mittel']) . '" border="0" alt="" />';
            $images[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=gallery&amp;sub=editimages&amp;id=$id&amp;noframes=1{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('hitssort', $hitssort);
        $this->_view->assign('autorsort', $autorsort);
        $this->_view->assign('name1sort', $name1sort);
        $this->_view->assign('name2sort', $name2sort);
        $this->_view->assign('name3sort', $name3sort);
        $this->_view->assign('res', $res);
        $this->_view->assign('ppformaction', "index.php?do=gallery&amp;sub=editimages&amp;id=$id&amp;noframes=1{$nav_sort}&amp;page=1");
        $this->_view->assign('limit', $limit);
        $this->_view->assign('images', $images);
        $this->_view->assign('title', $this->_lang['Gallery_ShowImages']);
        $this->_view->content('/gallery/imagesshow.tpl');
    }

    public function edit($id) {
        if (!perm('gallery_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $tags = '';
            if (!empty($_POST['Tags'])) {
                $tags = str_replace(array("\n", "\r\n"), '', $_POST['Tags']);
                $tags = $this->_db->escape(trim(str_replace(array(' ,', ', ', ',,'), ',', Tool::cleanSpace($tags))));
            }

            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $_POST['Name_1'];
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $_POST['Name_1'];
            $Beschreibung_1 = $_POST['Beschreibung_1'];
            $Beschreibung_2 = !empty($_POST['Beschreibung_2']) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = !empty($_POST['Beschreibung_3']) ? $_POST['Beschreibung_3'] : $Beschreibung_1;

            $array = array(
                'Name_1'         => Arr::getPost('Name_1'),
                'Name_2'         => $Name_2,
                'Name_3'         => $Name_3,
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
                'Aktiv'          => Arr::getPost('Aktiv'),
                'Tags'           => $tags,
            );
            $this->_db->update_query('galerie', $array, "Id='" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }

        $gallery = array();
        $this->load(0, '', $gallery, $_SESSION['a_area'], '', '', '', '', $id, 0);
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_galerie WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('Beschreibung_1', $this->__object('Editor')->load('admin', $res->Beschreibung_1, 'Beschreibung_1', 150, 'Basic'));
        $this->_view->assign('Beschreibung_2', $this->__object('Editor')->load('admin', $res->Beschreibung_2, 'Beschreibung_2', 150, 'Basic'));
        $this->_view->assign('Beschreibung_3', $this->__object('Editor')->load('admin', $res->Beschreibung_3, 'Beschreibung_3', 150, 'Basic'));
        $this->_view->assign('gallery', $gallery);
        $this->_view->assign('title', $this->_lang['Gallery_Edit']);
        $this->_view->content('/gallery/galleryedit.tpl');
    }

    protected function load($id, $prefix, &$gallery, &$area, $active, $orderby, $ascdesc, $sorting = '0', $categ, $setlim = '0') {
        $ex_categ = (!empty($categ)) ? " AND Kategorie = '" . intval($categ) . "'" : '';
        $query = $this->_db->query("SELECT Name_1, Id, Parent_Id FROM " . PREFIX . "_galerie WHERE Parent_Id = '" . intval($id) . "' {$ex_categ} AND Sektion = '" . intval($area) . "' ORDER BY Name_1 ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name_1;
            $gallery[] = $item;
            $this->load($item->Id, $prefix . ' - - ', $gallery, $area, $active, $orderby, $ascdesc, $sorting, $categ, $setlim);
        }
        $query->close();
        return;
    }

    public function delete($id, $close = 1) {
        if (perm('gallery_delete')) {
            $id = intval($id);
            $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_galerie WHERE Parent_Id = '" . $id . "'");
            while ($item = $query->fetch_object()) {
                $sql = $this->_db->query("SELECT Bildname FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $item->Id . "'");
                while ($row_g = $sql->fetch_object()) {
                    File::delete(UPLOADS_DIR . '/galerie/' . $row_g->Bildname);
                }
                $this->delete($item->Id);
            }
            $query->close();

            $sql = $this->_db->query("SELECT Bildname FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $id . "'");
            while ($row_g = $sql->fetch_object()) {
                File::delete(UPLOADS_DIR . '/galerie/' . $row_g->Bildname);
            }
            $sql->close();
            $this->_db->query("DELETE FROM " . PREFIX . "_galerie WHERE Id = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $id . "'");
            if ($close == 1) {
                $this->__object('AdminCore')->backurl();
            }
        }
    }

}