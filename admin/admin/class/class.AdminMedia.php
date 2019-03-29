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

class AdminMedia extends Magic {

    /* Вывод видео-файла */
    public function editVideo($id) {
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_videos WHERE Id = '" . intval($id) . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Videos']);
        $this->_view->content('/media/video_view.tpl');
    }

    /* Вывод всех видео-файлов */
    public function showVideo() {
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Name']) as $lid) {
                $Name = (!empty($_POST['Name'][$lid])) ? $_POST['Name'][$lid] : '';
                if (!empty($Name)) {
                    $array = array(
                        'Name'   => $_POST['Name'][$lid],
                        'Hoehe'  => $this->clean($_POST['Hoehe'][$lid]),
                        'Breite' => $this->clean($_POST['Breite'][$lid]),
                    );
                    $this->_db->update_query('videos', $array, "Id = '" . intval($lid) . "'");
                }
                if (!empty($_POST['del'][$lid])) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_videos WHERE Id = '" . intval($lid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $db_sort = " ORDER BY Name ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $activesort = $imgsort = $usersort = $def_search_n = $def_search = $namesort = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name DESC';
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
            case 'user_asc':
                $db_sort = 'ORDER BY Benutzer ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY Benutzer DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $def_search = " AND (Name LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('imgsort', $imgsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('usersort', $usersort);
        $a_area = $_SESSION['a_area'];

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_videos WHERE Sektion = '" . $a_area . "' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $videos = array();
        while ($row = $sql->fetch_object()) {
            $row->BenutzerName = Tool::userName($row->Benutzer);
            $videos[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=media&amp;sub=overview{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('videos', $videos);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('title', $this->_lang['Videos']);
        $this->_view->content('/media/video_overview.tpl');
    }

    /* Добавление видео */
    public function addVideo() {
        if (Arr::getPost('save') == 1) {
            $file = '';
            $file = (!empty($_POST['Datei']) && empty($_POST['newFile_1'])) ? $_POST['Datei'] : $_POST['newFile_1'];

            if (!empty($file)) {
                $insert_array = array(
                    'Sektion'  => $_SESSION['a_area'],
                    'Name'     => Arr::getPost('Name'),
                    'Video'    => $file,
                    'Bild'     => '',
                    'Breite'   => $this->clean(Arr::getPost('Breite')),
                    'Hoehe'    => $this->clean(Arr::getPost('Hoehe')),
                    'Datum'    => time(),
                    'Benutzer' => $_SESSION['benutzer_id']);
                $this->_db->insert_query('videos', $insert_array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый видео-файл (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('close');
            }
        }
        $this->_view->assign('folderVideo', $this->folderVideo());
        $this->_view->assign('can_upload', ((is_writable(UPLOADS_DIR . '/videos/')) ? 1 : 0));
        $this->_view->assign('title', $this->_lang['Videos']);
        $this->_view->content('/media/video_new.tpl');
    }

    /* Чистка параметра размеров */
    protected function clean($given) {
        $NummerG = $this->_text->lower(trim(str_replace(' ', '', $given)));
        $NummerG = explode('px', $NummerG);
        if (is_numeric($NummerG[0])) {
            $wert = ($NummerG[0] < 100) ? '400px' : $NummerG[0] . 'px';
        } else {
            $NummerG = $this->_text->lower(trim(str_replace(' ', '', $given)));
            $NummerG = explode('%', $NummerG);
            $wert = (is_numeric($NummerG[0])) ? ($NummerG[0] <= 100 && $NummerG[0] > 10) ? $NummerG[0] . '%' : '100%' : '500px';
        }
        return $wert;
    }

    /* Проверка наличия видео-файла в папке и используется ли файл */
    protected function folderVideo() {
        $vids = array();
        $verzname = UPLOADS_DIR . '/videos/';
        $handle = opendir($verzname);
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && is_file($verzname . $datei)) {
                if (Tool::extension($datei) == 'flv') {
                    $video = '';
                    $video->Name = '???';
                    $video->File = $datei;
                    $c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_videos WHERE Sektion = '" . $_SESSION['a_area'] . "' AND Video = '" . $this->_db->escape($datei) . "' LIMIT 1");
                    if (is_object($c)) {
                        $video->FileInDb = $c->Name;
                    }
                    $vids[] = $video;
                }
            }
        }
        closedir($handle);
        return $vids;
    }

    /* Редактирование аудио */
    public function editAudio($id) {
        if (Arr::getPost('save') == 1) {
            if (!empty($_POST['Name'])) {
                $array = array(
                    'Name'  => Arr::getPost('Name'),
                    'Width' => Tool::cleanDigit($_POST['Width']),
                );
                $this->_db->update_query('audios', $array, "Id = '" . intval($id) . "'");
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал аудио-файл (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_audios WHERE Id = '" . intval($id) . "' LIMIT 1");
        $this->_view->assign('playAudio', $this->__object('Media')->audio('', '/uploads/audios/' . $res->Audio, $res->Width));
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Audios']);
        $this->_view->content('/media/audio_view.tpl');
    }

    /* Вывод всех аудио-файлов */
    public function showAudio() {
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Name']) as $lid) {
                $Name = (!empty($_POST['Name'][$lid])) ? $_POST['Name'][$lid] : '';
                if (!empty($Name)) {
                    $array = array(
                        'Name'  => $_POST['Name'][$lid],
                        'Width' => $_POST['Width'][$lid],
                    );
                    $this->_db->update_query('audios', $array, "Id = '" . intval($lid) . "'");
                }
                if (!empty($_POST['del'][$lid])) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_audios WHERE Id = '" . intval($lid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $db_sort = " ORDER BY Name ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $activesort = $imgsort = $usersort = $def_search_n = $def_search = $namesort = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name DESC';
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
            case 'user_asc':
                $db_sort = 'ORDER BY Benutzer ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY Benutzer DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $def_search = " AND (Name LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('imgsort', $imgsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('usersort', $usersort);
        $a_area = $_SESSION['a_area'];

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_audios WHERE Sektion = '" . $a_area . "' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $audios = array();
        while ($row = $sql->fetch_object()) {
            $row->BenutzerName = Tool::userName($row->Benutzer);
            $audios[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=media&amp;sub=audio_overview{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('audios', $audios);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('title', $this->_lang['Audios']);
        $this->_view->content('/media/audio_overview.tpl');
    }

    /* Добавление аудио */
    public function addAudio() {
        if (Arr::getPost('save') == 1) {
            $file = '';
            $file = (!empty($_POST['Datei']) && empty($_POST['newFile_1'])) ? $_POST['Datei'] : $_POST['newFile_1'];

            if (!empty($file)) {
                $insert_array = array(
                    'Sektion'  => $_SESSION['a_area'],
                    'Name'     => Arr::getPost('Name'),
                    'Audio'    => $file,
                    'Width'    => Tool::cleanDigit(Arr::getPost('Width')),
                    'Datum'    => time(),
                    'Benutzer' => $_SESSION['benutzer_id']);
                $this->_db->insert_query('audios', $insert_array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый аудио-файл (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('close');
            }
        }
        $this->_view->assign('folderAudio', $this->folderAudio());
        $this->_view->assign('can_upload', ((is_writable(UPLOADS_DIR . '/audios/')) ? 1 : 0));
        $this->_view->assign('title', $this->_lang['Audios']);
        $this->_view->content('/media/audio_new.tpl');
    }

    /* Проверка наличия аудио-файла в папке и используется ли файл */
    protected function folderAudio() {
        $vids = array();
        $verzname = UPLOADS_DIR . '/audios/';
        $handle = opendir($verzname);
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && is_file($verzname . $datei)) {
                if (Tool::extension($datei) == 'mp3') {
                    $audio = new stdClass;
                    $audio->Name = '???';
                    $audio->File = $datei;
                    $c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_audios WHERE Sektion = '" . $_SESSION['a_area'] . "' AND Audio = '" . $this->_db->escape($datei) . "' LIMIT 1");
                    if (is_object($c)) {
                        $audio->FileInDb = $c->Name;
                    }
                    $vids[] = $audio;
                }
            }
        }
        closedir($handle);
        return $vids;
    }

}
