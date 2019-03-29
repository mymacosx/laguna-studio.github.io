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

class Forum extends Magic {

    protected $Lc;
    protected $_theme;
    protected $_user;
    protected $_group;
    protected $_uname;
    protected $LimitT;
    protected $LimitB;
    protected $max_age = '-2 weeks';
    protected $status_moved = 2;
    protected $status_closed = 1;
    protected $type_sticky = 1;
    protected $type_announce = 100;
    protected $double_post = 7200; // Срок в секундах, в течении которого сообщения от одного пользователя в топике склеиваются
    protected $ForumCategs = array();
    protected $ForumForums = array();
    protected $ForumCfAktive = false;
    protected $_settings = array();

    /* Инициализация класса */
    public function __construct() {
        $this->_settings = SX::get('system');
        $this->_db->query("DELETE FROM " . PREFIX . "_f_topic_read WHERE ReadOn < '" . date('Y-m-d', strtotime($this->max_age)) . "'");
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->_user = $_SESSION['benutzer_id'];
        $this->_group = $_SESSION['user_group'];
        $this->_uname = Tool::fullName();
        $this->_theme = SX::get('options.theme');
        $this->LimitT = Tool::userSettings('Forum_Themen_Limit', 15);
        $this->LimitB = Tool::userSettings('Forum_Beitraege_Limit', 15);
        $this->_view->assign('welcome', Tool::welcome());
        $this->_view->registerPlugin('function', 'posticon', array($this, 'posticon'));
    }

    /* Метод получения именинников на текущую дату */
    public function birthdays() {
        $query = "SELECT
            Id,
            Geburtstag,
            Benutzername,
            " . date('Y') . " - RIGHT(Geburtstag, 4) AS Age
        FROM
            " . PREFIX . "_benutzer
        WHERE
            Geburtstag != ''
        AND
            Aktiv = '1'
        AND
            Geburtstag_public = '1'
        HAVING
            LEFT(Geburtstag, 2) = " . date('d') . "
        AND
            LEFT(RIGHT(Geburtstag, 7), 2) = " . date('m') . "
        ORDER BY Age DESC";
        $sql = $this->_db->query($query);
        $result = array();
        while ($row = $sql->fetch_assoc()) {
            $result[] = '<a class="light" href="index.php?p=user&amp;id=' . $row['Id'] . '&amp;area=' . AREA . '">' . $row['Benutzername'] . '</a> (' . $row['Age'] . ')';
        }
        $sql->close();
        return !empty($result) ? implode(', ', $result) : $this->_lang['Birthdays_Today_No'];
    }

    /* Метод получения размера файла */
    public function filesize($file) {
        $file = 'uploads/forum/' . $file;
        if (is_file(SX_DIR . '/' . $file)) {
            $size = filesize($file);
            $size = $size / 1024;
            return File::filesize($size);
        }
        return '';
    }

    /* Метод вывода информации о статусе пользователя (онлайн|офлайн|невидимый) */
    public function onlineStatus($param) {
        $num = '';
        $sql = $this->_db->cache_fetch_assoc_all("SELECT * FROM " . PREFIX . "_benutzer_online WHERE Type = 'site'");
        foreach ($sql as $row) {
            if ($row['Benutzername'] == $param['uname']) {
                $num = '1';
                $unsichtbar = $row['Unsichtbar'];
                break;
            }
        }
        if ($num == '1') {
            if ($this->_group == 1 && $unsichtbar == 'INVISIBLE') {
                $img = 'user_invisible.png';
                $alt = $this->_lang['Forums_userisinvisible'];
            }
            if ($unsichtbar != 'INVISIBLE') {
                $img = 'user_online.png';
                $alt = $this->_lang['Forums_userisonline'];
            }
            if ($this->_group != 1 && $unsichtbar == 'INVISIBLE') {
                $img = 'user_offline.png';
                $alt = $this->_lang['Forums_userisoffline'];
            }
        } else {
            $img = 'user_offline.png';
            $alt = $this->_lang['Forums_userisoffline'];
        }
        return '<img title="' . $alt . '" class="absmiddle" src="theme/' . $this->_theme . '/images/forums/' . $img . '" alt="' . $alt . '" />';
    }

    /* Метод вывода статистики форума о количестве сообщений, тем, пользователей */
    public function forumStats() {
        $i = 86400;
        $array = array();
        $day = date('Y-m-d H:i:s', strtotime('-1 day'));
        $week = date('Y-m-d H:i:s', strtotime('-1 week'));
        $month = date('Y-m-d H:i:s', strtotime('-1 month'));
        $query = "
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_topic
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_topic WHERE datum > '" . $day . "'
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_topic WHERE datum > '" . $week . "'
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_topic WHERE datum > '" . $month . "'
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_post
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_post WHERE datum > '" . $day . "'
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_post WHERE datum > '" . $week . "'
		  UNION ALL
		 SELECT COUNT(id) AS statistic FROM " . PREFIX . "_f_post WHERE datum > '" . $month . "'
		  UNION ALL
		 SELECT COUNT(Id) AS statistic FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Gruppe!=2
		  UNION ALL
		 SELECT COUNT(Id) AS statistic FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Regdatum > " . (time() - $i) . " AND Gruppe!=2
		  UNION ALL
		 SELECT COUNT(Id) AS statistic FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Regdatum > " . (time() - ($i * 7)) . " AND Gruppe!=2
		  UNION ALL
		 SELECT COUNT(Id) AS statistic FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Regdatum > " . (time() - ($i * 30)) . " AND Gruppe!=2 ; ";
        $query .= "SELECT Id, Benutzername FROM " . PREFIX . "_benutzer WHERE Aktiv='1' AND Gruppe!=2 ORDER BY Id DESC LIMIT 1";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row = $result->fetch_assoc()) {
                    $array[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row_newest_member = $result->fetch_object();
                $result->close();
            }
        }

        $tpl_array = array(
            'num_threads'       => $array[0]['statistic'],
            'num_threads_day'   => $array[1]['statistic'],
            'num_threads_week'  => $array[2]['statistic'],
            'num_threads_month' => $array[3]['statistic'],
            'num_posts'         => $array[4]['statistic'],
            'num_posts_day'     => $array[5]['statistic'],
            'num_posts_week'    => $array[6]['statistic'],
            'num_posts_month'   => $array[7]['statistic'],
            'num_members'       => $array[8]['statistic'],
            'num_members_day'   => $array[9]['statistic'],
            'num_members_week'  => $array[10]['statistic'],
            'num_members_month' => $array[11]['statistic'],
            'newestmember'      => $row_newest_member->Benutzername,
            'uid'               => $row_newest_member->Id);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/forums/stats_user.tpl');
    }

    /* Получение иконки сообщения на форуме */
    public function posticon($params) {
        $return = '&nbsp;';
        if (isset($params['icon'])) {
            $row = $this->_db->cache_fetch_assoc("SELECT SQL_CACHE path, title FROM " . PREFIX . "_posticons WHERE active='1' AND id='" . intval($params['icon']) . "' LIMIT 1");
            if (isset($row['path'])) {
                $return = '<img  class="stip absmiddle" src="theme/' . $this->_theme . '/images/posticons/' . $row['path'] . '" alt="' . $row['title'] . '" title="' . $row['title'] . '" />';
            }
        }
        return $return;
    }

    protected function getPosticons($icon = '0') {
        $posticons = '';
        $i = 1;
        $sql = $this->_db->query("SELECT SQL_CACHE id, path, title FROM " . PREFIX . "_posticons WHERE active='1' AND area = '" . AREA . "' ORDER BY posi ASC");
        while ($rows = $sql->fetch_object()) {
            $br = '&nbsp;';
            if (($i % 10) == 0) {
                $br = '<!-- <br /> -->';
            }
            $checked = ($icon == $rows->id) ? ' checked="checked"' : '';
            $posticons .= '<input class="noborder" type="radio" name="posticon" value="' . $rows->id . '" ' . $checked . ' /><img class="absmiddle" src="theme/' . $this->_theme . '/images/posticons/' . $rows->path . '" alt="' . $rows->title . '" />&nbsp;&nbsp; ' . $br;
            $i++;
        }
        $sql->close();
        if ($icon == 0) {
            $posticons .= '<input class="noborder" type="radio" name="posticon" value="0" checked="checked" />&nbsp;' . $this->_lang['Forums_no_posticon'];
        } else {
            $posticons .= '<input class="noborder" type="radio" name="posticon" value="0" />&nbsp;' . $this->_lang['Forums_no_posticon'];
        }
        return $posticons;
    }

    protected function getIcon($file, $alt) {
        return '<img class="stip absmiddle" src="theme/' . $this->_theme . '/images/statusicons/' . $file . '" alt="' . $alt . '" title="' . $alt . '" />';
    }

    protected function readTopic($topic) {
        if ($this->_user > 0) {
            $this->_db->query("REPLACE DELAYED INTO " . PREFIX . "_f_topic_read (Usr, Topic) VALUES ('" . $this->_user . "', '" . intval($topic) . "')");
        }
    }

    protected function lastPost($board) {
        $board = intval($board);
        $row = $this->_db->cache_fetch_object("SELECT
			b.id,
			b.datum
		FROM
			" . PREFIX . "_f_topic AS a
		INNER JOIN
			" . PREFIX . "_f_post AS b
		ON
			a.id = b.topic_id
		AND
			a.forum_id = '" . $board . "'
		ORDER BY b.datum DESC LIMIT 1");
        if (is_object($row)) {
            $this->_db->query("UPDATE  " . PREFIX . "_f_forum SET last_post = '" . $row->datum . "', last_post_id = '" . $row->id . "' WHERE id = '" . $board . "'");
        }
    }

    /* Удаляем тег модератора */
    protected function del_mod($is_moderator, $text) {
        return $is_moderator ? $text : str_ireplace(array('[mod]', '[/mod]'), '', $text);
    }

    protected function del_uid($string, $id) {
        $array = explode(';', $string);
        $string = '';
        foreach ($array as $arr) {
            if (!empty($arr) && $arr != $id) {
                $string .= ';' . $arr;
            }
        }
        return $string;
    }

    protected function permForum($forumid, $groupid) {
        if ($this->_group == 1) {
            return explode(',', '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1');
        }
        $sql = $this->_db->cache_fetch_assoc_all("SELECT permissions, forum_id, group_id FROM " . PREFIX . "_f_permissions");
        foreach ($sql as $row) {
            if ($row['forum_id'] == $forumid && $row['group_id'] == $groupid) {
                $res = $row['permissions'];
                break;
            }
        }
        return explode(',', $res);
    }

    protected function iconTopic(&$topic, $status = '') {
        $view = 250;
        $reply = 20;
        $new = false;
        $id = intval($topic['id']);
        $array = DB::get()->fetch_assoc_all("SELECT COUNT(*) AS num FROM " . PREFIX . "_f_post WHERE topic_id = '" . $id . "' AND uid = '" . $this->_user . "'
          UNION ALL
          SELECT
                  COUNT(*) AS num
          FROM
                  " . PREFIX . "_f_topic AS a
          LEFT JOIN
                  " . PREFIX . "_f_topic_read AS b
          ON
                  a.id = b.Topic
          AND
                  b.Usr = '" . $this->_user . "'
          WHERE
                  a.id = '" . $id . "'
          AND
                  (b.ReadOn < a.last_post OR b.ReadOn IS NULL)
          AND
                  a.last_post > '" . date('Y-m-d', strtotime($this->max_age)) . "'");

        $topic['replies'] = (isset($topic['replies'])) ? $topic['replies'] : '';
        $file = 'thread';
        if ($array[0]['num'] > 0) {
            $file .= '_dot';
        }
        if ($topic['replies'] > $reply || $topic['views'] > $view) {
            $file .= '_hot';
        }
        if ($topic['status'] == $this->status_closed || $status == $this->status_closed) {
            $file .= '_lock';
        }
        if ($array[1]['num'] > 0) {
            $file .= '_new';
            $new = true;
        }

        $file .= '.png';
        switch ($topic['type']) {
            case $this->type_announce:
                $topic['statusicon'] = ($new) ? $this->getIcon('announcement_new.png', $this->_lang['Forums_Announcement']) : $this->getIcon('announcement_old.png', $this->_lang['Forums_Announcement']);
                break;

            case $this->type_sticky:
                $topic['statusicon'] = ($new) ? $this->getIcon('important_new.png', $this->_lang['Forums_Sticky']) : $this->getIcon('important_old.png', $this->_lang['Forums_Sticky']);
                break;

            default:
                $topic['statusicon'] = $this->getIcon($file, '');
                break;
        }
    }

    protected function iconForum(&$forum) {
        $row = $this->_db->cache_fetch_object("SELECT
                COUNT(*) AS NewPostCount
        FROM
                " . PREFIX . "_f_topic AS a
        LEFT JOIN
                " . PREFIX . "_f_topic_read AS b
        ON
                a.id = b.Topic
        AND
                b.Usr = '" . $this->_user . "'
        WHERE
                a.forum_id = '" . intval($forum['id']) . "'
        AND
                (b.ReadOn < a.last_post OR b.ReadOn IS NULL)
        AND
                a.last_post > '" . date('Y-m-d', strtotime($this->max_age)) . "'");
        if ($row->NewPostCount > 0) {
            $icon = 'forum_new';
            $lang = $this->_lang['Forums_Icons_NewPosts'];
        } else {
            $icon = 'forum_old';
            $lang = $this->_lang['Forums_Icons_NoNewPosts'];
        }
        $icon .= $forum['status'] == $this->status_closed ? '_lock' : '';
        $icon .= '.png';
        $forum['statusicon'] = $this->getIcon($icon, $lang);
    }

    /* Получаем модераторов форума */
    protected function is_mod($fid) {
        if ($this->_group == 1) {
            return true;
        }
        $sql = $this->_db->cache_fetch_assoc_all("SELECT user_id, forum_id FROM " . PREFIX . "_f_mods");
        foreach ($sql as $row) {
            if ($row['forum_id'] == $fid && $row['user_id'] == $this->_user) {
                return true;
            }
        }
        return false;
    }

    /* Удаляем категорию форума */
    protected function deleteCategory($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_forum WHERE category_id = '$id'");
        while ($forum = $result->fetch_object()) {
            $this->deleteForum($forum->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_category WHERE id = '$id'");
    }

    /* Удаляем форум */
    protected function deleteForum($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_topic WHERE forum_id = '$id'");
        while ($topic = $result->fetch_object()) {
            $this->deleteTopic($topic->id);
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_f_permissions WHERE forum_id = '$id'");
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_category WHERE parent_id = '$id'");
        while ($category = $result->fetch_object()) {
            $this->deleteCategory($category->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_forum WHERE id = '$id'");
    }

    /* Удаляем топик */
    protected function deleteTopic($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '$id'");
        while ($post = $result->fetch_object()) {
            $this->deletePost($post->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_rating WHERE topic_id = '$id'");
        $this->_db->query("DELETE FROM " . PREFIX . "_f_topic WHERE id = '$id'");
    }

    /* Удаляем сообщение */
    protected function deletePost($id) {
        $id = intval($id);
        $post = $this->_db->cache_fetch_object("SELECT uid, attachment, topic_id FROM " . PREFIX . "_f_post WHERE id = '" . $id . "' LIMIT 1");
        $attachments = explode(';', $post->attachment);
        foreach ($attachments as $attachment) {
            if (!empty($attachment)) {
                $this->_db->query("DELETE FROM " . PREFIX . "_f_attachment WHERE id = '" . $attachment . "'");
            }
        }
        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET replies = replies - 1 WHERE id = '" . $post->topic_id . "'");
        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = Beitraege - 1 WHERE Id = '" . $post->uid . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_f_post WHERE id = '" . $id . "'");
    }

    /* Получаем все категории форума */
    protected function saveCategs() {
        $sql = $this->_db->query("SELECT
                a.*,
                b.id AS cat_id,
                b.title AS cat_title,
                b.position AS cat_position,
                b.parent_id AS cat_parent_id,
                b.comment AS cat_comment,
                b.group_id AS cat_group_id,
                b.parent_id
        FROM
                " . PREFIX . "_f_forum AS a,
                " . PREFIX . "_f_category AS b
        WHERE
                b.id = a.category_id
        AND
                a.active = '1'
        ORDER BY b.position ASC, a.position ASC");
        while ($row = $sql->fetch_assoc()) {
            $row['intersect'] = in_array($this->_group, explode(',', $row['group_id'])) ? 1 : 0;
            $row['cat_intersect'] = in_array($this->_group, explode(',', $row['cat_group_id'])) ? 1 : 0;
            $this->ForumForums[$row['id']] = $row;
            $this->ForumCategs[$row['cat_id']] = $row;
        }
        $sql->close();
    }

    /* Загружаем все категории форума */
    protected function getCategs() {
        if ($this->ForumCfAktive === false) {
            $this->ForumCfAktive = true;
            $this->saveCategs();
        }
    }

    /* Получаем категории форума */
    protected function categories() {
        $this->getCategs();
        $categories = array();
        $_categs = $this->ForumCategs;
        $_forums = $this->ForumForums;
        foreach ($_categs as $key => $categ) {
            if ($categ['cat_parent_id'] == 0 && $categ['cat_intersect'] == 1) {
                $new_categ = new stdClass;
                $new_categ->title = $categ['cat_title'];
                $forums = array();
                foreach ($_forums as $val => $forum) {
                    if ($forum['category_id'] == $categ['cat_id'] && $forum['intersect'] == 1) {
                        $new_forum = new stdClass;
                        $new_forum->id = $forum['id'];
                        $new_forum->category_id = 1;
                        $new_forum->visible_title = $forum['title'];
                        $forums[] = $new_forum;
                        unset($_forums[$val]);
                    }
                }
                $forums = array_merge($forums, $this->getForums($categ['cat_id'], $_categs, $_forums, ' - ', ' - '));
                $new_categ->forums = $forums;
                $categs[] = $new_categ;
                unset($_categs[$key]);
            }
        }
        $categories = $categs;
        return $categories;
    }

    /* Получаем список всех форумов */
    protected function getForums($id, $_categs, $_forums, $prefix, $sep) {
        $forums = array();
        foreach ($_categs as $key => $categ) {
            if ($categ['cat_parent_id'] == $id && $categ['cat_intersect'] == 1) {
                $new_forum = '';
                $new_forum->id = $categ['cat_id'];
                $new_forum->category_id = 0;
                $new_forum->visible_title = $sep . $prefix . $categ['cat_title'];
                $forums[] = $new_forum;
                foreach ($_forums as $val => $forum) {
                    if ($forum['category_id'] == $categ['cat_id'] && $forum['intersect'] == 1) {
                        $new_forum = '';
                        $new_forum->id = $forum['id'];
                        $new_forum->category_id = 1;
                        $new_forum->visible_title = $sep . $sep . $prefix . $forum['title'];
                        $forums[] = $new_forum;
                        unset($_forums[$val]);
                    }
                }
                $forums = array_merge($forums, $this->getForums($categ['cat_id'], $_categs, $_forums, $sep . $sep . $prefix, $sep));
                unset($_categs[$key]);
            }
        }
        return $forums;
    }

    protected function topicExists($id) {
        $result = $this->_db->cache_num_rows("SELECT id FROM " . PREFIX . "_f_topic WHERE id = '" . intval($id) . "'");
        return $result > 0 ? true : false;
    }

    /* Строим меню навигации */
    protected function navigation($id, $type, $result = null) {
        switch ($type) {
            case 'category':
                $parent_id = 'parent_id';
                $types = 'forum';
                $res = '';
                break;

            case 'forum':
                $parent_id = 'category_id';
                $types = 'category';
                $res = 'p=showforums&amp;cid=';
                break;

            case 'topic':
                $parent_id = 'forum_id';
                $types = 'forum';
                $res = 'p=showforum&amp;fid=';
                break;

            case 'post':
                $parent_id = 'topic_id';
                $types = 'topic';
                $res = 'p=showtopic&amp;tid=';
                break;
        }

        $navi = $this->_db->cache_fetch_object("SELECT id, title, $parent_id AS pid FROM " . PREFIX . "_f_" . $type . " WHERE id = '" . intval($id) . "' LIMIT 1");
        if (!is_object($navi) || $navi->pid == 0) {
            return '<a class="forum_links_navi" href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>' . $result;
        }

        $parent = $this->_db->cache_fetch_object("SELECT id, title FROM " . PREFIX . "_f_$types WHERE id = '" . $navi->pid . "' LIMIT 1");

        if (!empty($res)) {
            $result = $this->_lang['PageSep'] . '<a title="' . sanitize($parent->title) . '" class="forum_links_navi" href="index.php?' . $res . $parent->id . '&amp;t=' . translit($parent->title) . '">' . sanitize($parent->title) . '</a>' . $result;
        }
        return $this->navigation($navi->pid, $types, $result);
    }

    protected function dropDown($uid, $send_email = 0, $name = '') {
        $is_ignore = $ignore_options = 0;
        if ($this->_group != 2 && $uid != $this->_user) {
            $ignore = $this->_db->cache_fetch_object("SELECT IgnorierId FROM " . PREFIX . "_ignorierliste WHERE BenutzerId = '" . $this->_user . "' AND IgnorierId = '" . intval($uid) . "' LIMIT 1");
            $is_ignore = is_object($ignore) && $uid == $ignore->IgnorierId ? 1 : 0;
            $ignore_options = 1;
        }

        $tpl_array = array(
            'send_email'     => $send_email,
            'is_ignore'      => $is_ignore,
            'ignore_options' => $ignore_options,
            'theuid'         => $uid,
            'theuname'       => $name);
        $this->_view->assign($tpl_array);
        $out = $this->_view->fetch(THEME . '/forums/user_onclick_options.tpl');
        return $out;
    }

    /* Выводим иконку типа вложения */
    protected function attachmentImg($name) {
        $endg = strtolower(substr($name, -3));
        switch ($endg) {
            case 'jpg':
            case 'gif':
            case 'png':
            case 'zip':
            case 'rar':
            case 'tar':
            case 'php':
            case 'rtf':
            case 'jpe':
            case 'doc':
            case 'pdf':
            case 'bmp':
            case 'psd':
            case 'txt':
                $file = BASE_PATH . 'theme/' . $this->_theme . '/images/attachment/' . $endg . '.gif';
                break;

            default:
                $file = BASE_PATH . 'theme/' . $this->_theme . '/images/attachment/attach.gif';
                break;
        }
        return !empty($file) ? '<img class="absmiddle" src="' . $file . '" alt="" border="0" />' : '';
    }

    protected function get_mods($fid) {
        if ($fid) {
            $fid = intval($fid);
            $sql = $this->_db->cache_fetch_assoc_all("SELECT user_id, forum_id FROM " . PREFIX . "_f_mods");
            foreach ($sql AS $row) {
                if ($row['forum_id'] == $fid) {
                    $mods[] = '<a class="forum_links_small" href="index.php?p=user&amp;id=' . $row['user_id'] . '">' . Tool::userName($row['user_id']) . '</a>';
                }
            }
        }
        return !empty($mods) ? implode(', ', $mods) : '';
    }

    /* Метод высчитывает текущую страницу форума */
    protected function numPage($repliesCount, $limit) {
        $limit = (!$limit) ? 15 : $limit;
        if (($repliesCount % $limit) == 0) {
            return $repliesCount / $limit;
        }
        return ($repliesCount + ($limit - ($repliesCount % $limit))) / $limit;
    }

    protected function setRead($Board = '') {
        if ($this->_user > 0) {
            $BoardAll = isset($_REQUEST['ReadAll']) && $_REQUEST['ReadAll'] == 1 ? "WHERE a.forum_id != 0" : "WHERE a.forum_id = " . intval($Board);
            $sql_48h = "a.last_post > '" . date('Y-m-d', strtotime($this->max_age)) . "'";
            $this->_db->query("REPLACE DELAYED INTO " . PREFIX . "_f_topic_read (Usr, Topic) SELECT " . $this->_user . ", a.id FROM  " . PREFIX . "_f_topic AS a " . $BoardAll . " AND " . $sql_48h);
        }
    }

    protected function help($categ = '') {
        $faq_categ = array();
        $faq_categ_db = (!empty($categ) && is_numeric($categ)) ? " AND Id = '" . intval($categ) . "'" : '';
        $sql = $this->_db->query("SELECT *, Name_" . $this->Lc . " AS Name FROM " . PREFIX . "_f_hilfe WHERE Aktiv='1' {$faq_categ_db} ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $row->Items = $this->_db->fetch_object_all("SELECT *, Name_" . $this->Lc . " AS FaqName FROM " . PREFIX . "_f_hilfetext WHERE Kategorie = '" . $row->Id . "' AND Aktiv='1' ORDER BY Position ASC");
            $faq_categ[] = $row;
        }
        $sql->close();
        $this->_view->assign('faq_categ', $faq_categ);
        if (empty($categ)) {
            $seo_array = array(
                'headernav' => $this->_lang['Help_General_Forums'],
                'pagetitle' => $this->_lang['Help_General_Forums'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/forums_help.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    protected function helpDetail($id = 1) {
        $id = intval($id);
        $row_faq = $this->_db->cache_fetch_object("SELECT *, Name_" . $this->Lc . " AS FaqName, Text_" . $this->Lc . " AS FaqText FROM " . PREFIX . "_f_hilfetext WHERE Id = '" . $id . "' AND Aktiv='1' LIMIT 1");
        if (!is_object($row_faq)) {
            $this->__object('Core')->message('Global_error', 'Error404', BASE_URL . '/index.php?p=forum&action=help', 5);
        }

        $this->_db->query("UPDATE " . PREFIX . "_f_hilfetext SET Klicks = Klicks+1 WHERE Id = '" . $id . "'");
        $this->help($row_faq->Kategorie);
        $row_faq->FaqText = $this->__object('Glossar')->get($row_faq->FaqText);
        $this->_view->assign('row_faq', $row_faq);

        $seo_array = array(
            'headernav' => $this->_lang['Help_General_Forums'],
            'pagetitle' => sanitize($row_faq->FaqName . $this->_lang['PageSep'] . $this->_lang['Help_General_Forums'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title']),
            'generate'  => $row_faq->FaqName . ' ' . $row_faq->FaqText,
            'content'   => $this->_view->fetch(THEME . '/forums/forums_help_detail.tpl'));
        $this->_view->finish($seo_array);
    }

    public function postCount() {
        if (empty($_REQUEST['pid'])) {
            $this->__object('Core')->message('Forums_Title', 'NoAccess', BASE_URL . '/index.php?p=showforums');
        }
        $pid = intval($_REQUEST['pid']);
        $postcount = $this->_db->cache_fetch_object("SELECT topic_id FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "' LIMIT 1");
        $page = Tool::countPost($pid, $postcount->topic_id, $this->LimitB);
        $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $postcount->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid);
    }

    /* Добавляем благодарность пользователю */
    public function addThanks() {
        if ($this->_group == 2 || empty($_REQUEST['pid'])) {
            $this->__object('Core')->message('Forums_Title', 'NoAccess', BASE_URL . '/index.php?p=showforums');
        }
        $pid = intval($_REQUEST['pid']);
        $res_thanks = $this->_db->cache_fetch_object("SELECT uid, topic_id, thanks FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "' LIMIT 1");
        $page = Tool::countPost($pid, $res_thanks->topic_id, $this->LimitB);

        if ($res_thanks->uid == $this->_user) {
            $this->__object('Core')->message('Forums_Title', 'NoThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
        }
        if (empty($res_thanks->thanks)) {
            $upd_thanks = $this->_user;
        } else {
            $arr_thanks = explode(';', $res_thanks->thanks);
            if (in_array($this->_user, $arr_thanks)) {
                $this->__object('Core')->message('Forums_Title', 'DoubleThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
            }
            $arr_thanks[] = $this->_user;
            $upd_thanks = implode(';', $arr_thanks);
        }
        $this->_db->query("UPDATE " . PREFIX . "_f_post SET thanks = '" . $upd_thanks . "' WHERE id = '" . $pid . "'");
        $this->__object('Core')->message('Forums_Title', 'OkThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
    }

    /* Удаляем благодарность пользователя */
    public function delThanks() {
        if ($this->_group == 2 || empty($_REQUEST['pid'])) {
            $this->__object('Core')->message('Forums_Title', 'NoAccess', BASE_URL . '/index.php?p=showforums');
        }
        $pid = intval($_REQUEST['pid']);
        $res_thanks = $this->_db->cache_fetch_object("SELECT thanks, topic_id FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "' LIMIT 1");
        $page = Tool::countPost($pid, $res_thanks->topic_id, $this->LimitB);

        if (empty($res_thanks->thanks)) {
            $this->__object('Core')->message('Forums_Title', 'DelNoThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
        }
        $arr_thanks = explode(';', $res_thanks->thanks);
        if (!in_array($this->_user, $arr_thanks)) {
            $this->__object('Core')->message('Forums_Title', 'DelNoThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
        }
        $upd_thank = array();
        foreach ($arr_thanks as $arr_thank) {
            if ($arr_thank != $this->_user) {
                $upd_thank[] = $arr_thank;
            }
        }

        $upd_thanks = implode(';', $upd_thank);
        $this->_db->query("UPDATE " . PREFIX . "_f_post SET thanks = '" . $upd_thanks . "' WHERE id = '" . $pid . "'");
        $this->__object('Core')->message('Forums_Title', 'DelOkThanks', BASE_URL . '/index.php?p=showtopic&toid=' . $res_thanks->topic_id . '&pp=' . $this->LimitB . '&page=' . $page . '#pid_' . $pid, 2);
    }

    /* Перемещаем топик */
    public function moveTopic() {
        if (empty($_REQUEST['id'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }
        $fid = intval($_REQUEST['fid']);
        $id = intval(Arr::getRequest('id'));
        $row = $this->_db->cache_fetch_object("SELECT uid, id, title, forum_id FROM " . PREFIX . "_f_topic WHERE id='" . $id . "' LIMIT 1");

        $this->getCategs();
        $permissions = Tool::accessForum($fid);

        if ($row->uid != $this->_user) {
            if ($permissions['FORUM_MOVE_TOPIC'] == 0) {
                $this->__object('Core')->message('Forums_PermissionDenied', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        } else {
            if ($permissions['FORUM_MOVE_OWN_TOPIC'] == 0) {
                $this->__object('Core')->message('Forums_PermissionDenied', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        }

        if (Arr::getRequest('subaction') == 'commit') {
            $this->_db->query("UPDATE " . PREFIX . "_f_topic SET forum_id = '" . intval($_REQUEST['dest']) . "' WHERE id = '" . $id . "'");
            $this->lastPost($_REQUEST['dest']);
            $this->lastPost($row->forum_id);
            $this->__object('Core')->message('Forums_Title', 'Forums_ItemMoved', BASE_URL . '/index.php?p=showforums');
        } else {
            $tpl_array = array(
                'categories_dropdown' => $this->categories(),
                'item'                => $row,
                'navigation'          => $this->navigation($id, 'topic'));
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Mess_Move'],
                'pagetitle' => $this->_lang['Mess_Move'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/move.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Перемещаем сообщение */
    public function movePost() {
        if (empty($_REQUEST['pid'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }
        $fid = intval($_REQUEST['fid']);
        $pid = intval($_REQUEST['pid']);
        $pid_row = $this->_db->cache_fetch_object("SELECT uid FROM " . PREFIX . "_f_post WHERE id='" . $pid . "' LIMIT 1");

        $this->getCategs();
        $permissions = Tool::accessForum($fid);

        if ($pid_row->uid != $this->_user) {
            if ($permissions['FORUM_MOVE_TOPIC'] == 0) {
                $this->__object('Core')->message('Forums_PermissionDenied', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        } else {
            if ($permissions['FORUM_MOVE_OWN_TOPIC'] == 0) {
                $this->__object('Core')->message('Forums_PermissionDenied', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        }

        if (Arr::getRequest('subaction') == 'postmove') {
            $this->_db->query("UPDATE " . PREFIX . "_f_post SET topic_id = '" . intval($_REQUEST['post_m']) . "' WHERE id = '" . $pid . "'");
            $this->__object('Core')->message('Forums_Title', 'Forums_ItemMoved', BASE_URL . '/index.php?p=showforums');
        } else {
            $post_destinations = array();
            $query = "SELECT id, topic_id, title, message FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "' ; ";
            $query .= "SELECT id, title, forum_id FROM " . PREFIX . "_f_topic";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $post_item = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    while ($post_destination = $result->fetch_object()) {
                        $post_destinations[] = $post_destination;
                    }
                    $result->close();
                }
            }

            $post_item->message = $this->__object('Post')->codes($post_item->message);
            $tpl_array = array(
                'categories_dropdown' => $this->categories(),
                'post_item'           => $post_item,
                'post_destinations'   => $post_destinations);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_MoveTopic'],
                'pagetitle' => $this->_lang['Forums_MoveTopic'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/movepost.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    public function actionForum() {
        switch ($_REQUEST['action']) {
            case 'help':
                if (empty($_REQUEST['hid'])) {
                    $this->help();
                } else {
                    $this->helpDetail($_REQUEST['hid']);
                }
                break;

            case 'ignorelist':
                $this->ignorelist();
                break;

            case 'search_mask':
                $this->search_mask();
                break;

            case 'xsearch':
                $this->xsearch(Arr::getRequest('pattern'));
                break;

            case 'change_type':
                $this->change_type();
                break;

            case 'del_rating':
                $this->del_rating();
                break;

            case 'save_type':
                $this->save_type();
                break;

            case 'rating':
                $this->rating();
                break;

            case 'addsubscription':
                $this->addsubscription();
                break;

            case 'unsubscription':
                $this->unsubscription();
                break;

            case 'show':
                $this->show();
                break;

            case 'related':
                $this->relatedTopic();
                break;

            case 'friendsend':
                $this->sendFriend();
                break;

            case 'print':
                switch ($_REQUEST['what']) {
                    case 'subscription':
                        $this->subscription();
                        break;

                    case 'posting':
                        $this->posting();
                        break;

                    case 'topicsempty':
                        $this->topicsempty();
                        break;

                    case 'lastposts':
                        $this->lastposts();
                        break;
                }
                break;

            case 'login':
                $this->passForum();
                break;

            case 'getfile':
                $this->file();
                break;

            case 'getimage':
                $this->image();
                break;

            case 'markread':
                if (Arr::getRequest('what') == 'forum') {
                    $id = Arr::getRequest('id');
                    if (!empty($id)) {
                        $this->setRead($id);
                        $this->__object('Redir')->seoRedirect('index.php?p=showforum&fid=' . $id);
                    } else {
                        $this->setRead();
                        $this->__object('Redir')->seoRedirect('index.php?p=showforums');
                    }
                }
                break;

            default:
                $this->__object('Redir')->seoRedirect('index.php?p=showforums');
                break;
        }
    }

    protected function ignorelist() {
        if ($this->_group == 2) {
            $this->__object('Core')->message('Ignorelist', 'NoAccess', BASE_URL . '/index.php?p=showforums');
        }

        if (!empty($_REQUEST['sub'])) {
            switch ($_REQUEST['sub']) {
                case 'del':
                    $this->_db->query("DELETE FROM " . PREFIX . "_ignorierliste WHERE BenutzerId = '" . $this->_user . "' AND IgnorierId = '" . intval(Arr::getRequest('id')) . "'");
                    $this->__object('Core')->message('Ignorelist', 'Ignorelist_DelMsg', $this->__object('Redir')->referer(true));
                    break;

                case 'add':
                    $user = Tool::cleanAllow(Arr::getRequest('UserName'), '. ');
                    $reason = Tool::cleanAllow(Arr::getRequest('Reason'), '. ');

                    if (is_numeric(Arr::getGet('id'))) {
                        $uid = intval(Arr::getGet('id'));
                        $una = $this->_db->cache_fetch_object("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Id='" . $uid . "' AND Id!='" . $this->_user . "' LIMIT 1");
                        $user = $una->Benutzername;
                    }

                    $check = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_benutzer WHERE Benutzername='" . $user . "' AND Id!='" . $this->_user . "' LIMIT 1");
                    if (is_object($check)) {
                        $check_exists = $this->_db->cache_fetch_object("SELECT IgnorierId FROM " . PREFIX . "_ignorierliste WHERE BenutzerId='" . $this->_user . "' AND IgnorierId='" . $check->Id . "' LIMIT 1");
                        if (!is_object($check_exists)) {
                            $insert_array = array(
                                'BenutzerId' => $this->_user,
                                'IgnorierId' => $check->Id,
                                'Grund'      => $reason,
                                'Datum'      => time());
                            $this->_db->insert_query('ignorierliste', $insert_array);
                        }
                        $this->__object('Core')->message('Ignorelist', 'Ignorelist_AddMsg', $this->__object('Redir')->referer(true));
                    } else {
                        $this->__object('Core')->message('Ignorelist', 'Ignorelist_ErrMsg', $this->__object('Redir')->referer(true));
                    }
                    break;

                case 'del_multi':
                    $array = Arr::getPost('del');
                    if (is_array($array) && count($array) >= 1) {
                        foreach (array_keys($array) as $id) {
                            $this->_db->query("DELETE FROM " . PREFIX . "_ignorierliste WHERE BenutzerId = '" . $this->_user . "' AND IgnorierId = '" . intval($id) . "'");
                        }
                        $this->__object('Core')->message('Ignorelist', 'Ignorelist_DelMsg2', 'index.php?p=forum&action=ignorelist');
                    }
                    break;
            }
        }

        $data = array();
        $sql = $this->_db->query("SELECT
                a.BenutzerId,
                a.IgnorierId,
                a.Grund,
                a.Datum,
                b.Gruppe,
                b.Benutzername,
                b.Avatar,
                b.Avatar_Default,
                b.Status,
                b.Email,
                b.Gravatar,
                b.Emailempfang
        FROM
                " . PREFIX . "_ignorierliste AS a,
                " . PREFIX . "_benutzer AS b
        WHERE
                a.BenutzerId = '" . $this->_user . "'
        AND
                b.Id = a.IgnorierId
        ORDER BY
                Benutzername ASC");
        while ($row = $sql->fetch_object()) {
            $row->Avatar = $this->__object('Avatar')->load($row->Gravatar, $row->Email, $row->Gruppe, $row->Avatar, $row->Avatar_Default, 50);
            $row->UserPop = $this->dropDown($row->IgnorierId, $row->Emailempfang, base64_encode($row->Benutzername));
            $data[] = $row;
        }
        $sql->close();
        $this->_view->assign('data', $data);

        $seo_array = array(
            'headernav' => $this->_lang['Ignorelist'],
            'pagetitle' => $this->_lang['Ignorelist'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/ignorelist.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function search_mask() {
        $tpl_array = array(
            'forums_dropdown' => $this->categories(),
            'navigation'      => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>');
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
            'pagetitle' => $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/search_mask.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function xsearch($pattern) {
        $pattern = urldecode($pattern);
        if (empty($pattern) || $this->_text->strlen($pattern) < 2) {
            $this->__object('Core')->message('Forums_Title', 'Forums_ErrorNoPattern', BASE_URL . '/index.php?p=forum&amp;action=search_mask');
        }

        $this->__object('Core')->monitor($pattern, 'forum');
        $this->getCategs();
        $_REQUEST['b4after'] = !empty($_REQUEST['b4after']) ? $_REQUEST['b4after'] : '0';
        $_REQUEST['date'] = !empty($_REQUEST['date']) ? $_REQUEST['date'] : '0';
        $search_sort = !empty($_REQUEST['search_sort']) ? $_REQUEST['search_sort'] : '1';
        $search_post = Arr::getRequest('search_post', 0);
        $order = (Arr::getRequest('ascdesc') == 'DESC') ? 'DESC' : 'ASC';

        $search_type = Arr::getRequest('type');
        switch ($search_type) {
            case 1:
                $type = ' AND t.type = ' . $this->type_sticky;
                break;
            case 2:
                $type = ' AND t.type = ' . $this->type_announce;
                break;
            case 3:
                $type = ' AND t.status = ' . $this->status_moved;
            default:
                $type = '';
                break;
        }

        $p_and_array = array();
        $pattern_or = str_ireplace(array(' или ', ' и '), array(' or ', ' and '), $pattern);
        $pattern_or = explode(' or ', $pattern_or);
        foreach ($pattern_or as $part) {
            $sub_pattern = array();
            $pattern_and = explode(' and ', $part);
            foreach ($pattern_and as $sub_part) {
                $sub_part = $this->_db->escape(trim($sub_part));
                switch ($search_post) {
                    case 1:
                        $sub_pattern[] = "(p.title LIKE '%" . $sub_part . "%' OR p.message LIKE '%" . $sub_part . "%' OR t.title LIKE '%" . $sub_part . "%')";
                        break;
                    case 2:
                        $sub_pattern[] = "(p.title LIKE '%" . $sub_part . "%' OR p.message LIKE '%" . $sub_part . "%')";
                        break;
                    default:
                        $sub_pattern[] = "(p.title LIKE '%" . $sub_part . "%' OR t.title LIKE '%" . $sub_part . "%')";
                        break;
                }
            }
            $p_and_array[] = implode(' AND ', $sub_pattern);
        }

        switch ($search_sort) {
            default:
            case 1:
                $order_by = 't.title';
                break;
            case 2:
                $order_by = 't.replies';
                break;
            case 3:
                $order_by = 'autor';
                break;
            case 4:
                $order_by = 'forum';
                break;
            case 5:
                $order_by = 'views';
                break;
            case 6:
                $order_by = 'datum';
                break;
        }

        $user_opt = Arr::getRequest('user_opt');
        $user_name = urldecode(Arr::getRequest('user_name'));
        switch ($user_opt) {
            case 1:
                $search_by_user = " AND (u.Benutzername LIKE '" . $this->_db->escape($user_name) . "')";
                break;
            case 2:
                $search_by_user = " AND (u.Benutzername LIKE '%" . $this->_db->escape($user_name) . "%')";
                break;
            default:
                $search_by_user = '';
                break;
        }

        $in_forums = !Arr::nilPost('search_in_forums') && is_array(Arr::getPost('search_in_forums')) ? Arr::getPost('search_in_forums') : array(0);
        $in_forums = !Arr::nilGet('search_in_forums') ? Arr::getGet('search_in_forums') : implode(',', $in_forums);
        $in_forums_arr = !empty($in_forums) ? explode(',', $in_forums) : array(0);
        $allowed_forums = array();
        if (empty($in_forums_arr) || in_array(0, $in_forums_arr)) {
            foreach ($this->ForumForums as $forum) {
                $f_perms = $this->permForum($forum['id'], $this->_group);
                if ($f_perms[3] == 1 && $f_perms[0] == 1) {
                    $allowed_forums[] = intval($forum['id']);
                }
            }
        } else {
            foreach ($in_forums_arr as $forum) {
                $f_perms = $this->permForum($forum, $this->_group);
                if ($f_perms[3] == 1 && $f_perms[0] == 1) {
                    $allowed_forums[] = intval($forum);
                }
            }
        }
        $search_in_forums = !empty($allowed_forums) ? " AND (f.id = " . implode(' OR f.id = ', $allowed_forums) . ")" : '';
        $date_comparator = ($_REQUEST['b4after'] == 0) ? ' <= ' : ' >= ';
        $divisor = 60 * 60 * 24;
        $search_by_date = ($_REQUEST['date'] == 0) ? '' : " AND ((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') / $divisor - (UNIX_TIMESTAMP(t.datum) / $divisor)) $date_comparator " . $this->_db->escape(Arr::getRequest('date')) . ")";

        $limit = Tool::getLim(15);
        $a = Tool::getLimit($limit);

        if ($search_post == 2) {
            $result = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS DISTINCT
                    t.id,
                    t.forum_id,
                    t.title,
                    t.type,
                    t.datum,
                    f.title AS f_title,
                    u.Benutzername AS autor,
                    f.title AS forum,
                    t.opened,
                    p.uid,
                    p.opened,
                    p.attachment,
                    p.thanks,
                    p.id AS pid,
                    p.title AS ptitle,
                    p.message AS pmessage,
                    p.datum AS pdatum
            FROM
                    " . PREFIX . "_f_topic AS t,
                    " . PREFIX . "_f_post AS p,
                    " . PREFIX . "_f_forum AS f,
                    " . PREFIX . "_benutzer AS u
            WHERE
                    (" . implode(' OR ', $p_and_array) . ") $type
            AND
                    (t.id = p.topic_id AND t.forum_id = f.id AND u.Id = p.uid)
            AND
                    f.active = '1'
                    $search_by_user $search_in_forums $search_by_date
            ORDER BY
                    t.type DESC, $order_by $order LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $matches = array();
            while ($hit = $result->fetch_assoc()) {
                if ($this->is_mod($hit['forum_id']) || ($hit['opened'] == 1)) {
                    $hit['attachment'] = !empty($hit['attachment']) ? count(explode(';', $hit['attachment'])) : 0;
                    $hit['thanks'] = !empty($hit['thanks']) ? count(explode(';', $hit['thanks'])) : 0;
                    $hit['link'] = 'index.php?p=showtopic&amp;toid=' . $hit['id'];
                    $hit['pmessage'] = $this->__object('Post')->hidden(strip_tags($hit['pmessage']));
                    $hit['ptitle'] = !empty($hit['ptitle']) ? $hit['ptitle'] : $hit['pmessage'];
                    $hit['postlink'] = 'index.php?p=showtopic&amp;print_post=' . $hit['pid'] . '&amp;toid=' . $hit['id'] . '&amp;t=' . translit($this->_text->chars($hit['ptitle'], 60, ''));
                    $hit['autorlink'] = 'index.php?p=user&amp;id=' . $hit['uid'];
                    $matches[] = $hit;
                }
            }
            $tplout = 'search_result.tpl';
        } else {
            $result = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS DISTINCT
                    t.id,
                    t.forum_id,
                    t.title,
                    t.replies,
                    t.views,
                    t.type,
                    t.datum,
                    t.status,
                    t.posticon,
                    t.uid,
                    r.rating,
                    f.status AS f_status,
                    f.title AS f_title,
                    u.Benutzername AS autor,
                    f.title AS forum,
                    t.opened,
                    p.opened
            FROM
                    " . PREFIX . "_f_topic AS t,
                    " . PREFIX . "_f_post AS p,
                    " . PREFIX . "_f_forum AS f,
                    " . PREFIX . "_benutzer AS u,
                    " . PREFIX . "_f_rating AS r
            WHERE
                    (" . implode(' OR ', $p_and_array) . ") $type
            AND
                    (t.id = p.topic_id AND t.forum_id = f.id AND u.Id = t.uid AND r.topic_id = t.id)
            AND
                    f.active = '1' $search_by_user $search_in_forums $search_by_date
            ORDER BY
                    t.type DESC, $order_by $order LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $matches = array();
            while ($hit = $result->fetch_assoc()) {
                if ($this->is_mod($hit['forum_id']) || ($hit['opened'] == 1)) {
                    $rating_array = explode(',', $hit['rating']);
                    $hit['rating'] = (int) (array_sum($rating_array) / count($rating_array));
                    $hit['link'] = 'index.php?p=showtopic&amp;toid=' . $hit['id'];

                    if ($hit['status'] == $this->status_moved) {
                        $hit['statusicon'] = $this->getIcon('thread_moved.png', $this->_lang['Forums_ThreadMoved']);
                    } else {
                        if ($_SESSION['loggedin'] != 1 || $hit['f_status'] == $this->status_closed) {
                            $hit['statusicon'] = $this->getIcon('thread_lock.png', $this->_lang['Forums_TopicClosed']);
                        } else {
                            $this->iconTopic($hit, $this->ForumForums[$hit['forum_id']]['status']);
                        }
                    }
                    $hit['autorlink'] = 'index.php?p=user&amp;id=' . $hit['uid'];
                    $matches[] = $hit;
                }
            }
            $tplout = 'result.tpl';
        }

        if ($num < 1) {
            $this->__object('Core')->message('Forums_NoMatchesTitle', 'Forums_NoMatchesBody', BASE_URL . '/index.php?p=forum&amp;action=search_mask');
        }
        if ($limit < $num) {
            $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=forum&amp;action=xsearch" . "&amp;type=" . $search_type . "&amp;pattern=" . urlencode($pattern) . "&amp;user_name=" . urlencode(Arr::getRequest('user_name')) . "&amp;search_in_forums=" . $in_forums . "&amp;user_opt=" . $user_opt . "&amp;search_post=" . $search_post . "&amp;date=" . $_REQUEST['date'] . "&amp;b4after=" . $_REQUEST['b4after'] . "&amp;search_sort=" . $search_sort . "&amp;ascdesc=" . $order . "&amp;pp=" . $limit . "&amp;page={s}\">{t}</a> "));
        }

        $tpl_array = array(
            'matches'      => $matches,
            'navigation'   => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
            'title_result' => $this->_lang['Forums_Header_search_sort'] . ': ' . $pattern . ' ' . '(' . $num . ')');
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
            'pagetitle' => $this->_lang['Search'] . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/' . $tplout));
        $this->_view->finish($seo_array);
    }

    protected function change_type() {
        $permissions = Tool::accessForum(intval($_REQUEST['fid']));

        if ($permissions['FORUM_CHANGE_TOPICTYPE'] == 0) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        } else {
            $id = intval(Arr::getRequest('id'));
            $topic = $this->_db->cache_fetch_object("SELECT id, title, type FROM " . PREFIX . "_f_topic WHERE id = '" . $id . "' LIMIT 1");

            $tpl_array = array(
                'topic'      => $topic,
                'navigation' => $this->navigation($id, 'topic'));
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_ChangeTypePost'],
                'pagetitle' => $this->_lang['Forums_ChangeTypePost'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/change_type.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    protected function del_rating() {
        $fid = intval($_REQUEST['fid']);
        $permissions = Tool::accessForum($fid);

        if ($permissions['FORUM_CHANGE_TOPICTYPE'] == 0) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        } else {
            $id = intval(Arr::getRequest('id'));
            $res_title = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $id . "' LIMIT 1");
            $res_title = translit($res_title->title);
            $this->_db->query("UPDATE " . PREFIX . "_f_rating SET rating='', ip='', uid='' WHERE topic_id = '" . $id . "'");
            $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $id . '&fid=' . $fid . '&page=1&t=' . $res_title);
        }
    }

    protected function save_type() {
        $f_id = intval($_REQUEST['f_id']);
        $permissions = Tool::accessForum($f_id);

        if (($_SESSION['loggedin'] != 1) || ($permissions['FORUM_CHANGE_TOPICTYPE'] == 0)) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        } else {
            $topic_id = intval($_REQUEST['t_id']);
            $this->_db->query("UPDATE " . PREFIX . "_f_topic SET type = " . intval($_REQUEST['type']) . " WHERE id = '" . $topic_id . "'");
            $res_title = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $topic_id . "' LIMIT 1");
            $res_title = translit($res_title->title);
            $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $f_id . '&page=1&t=' . $res_title);
        }
    }

    protected function rating() {
        $t_id = intval($_REQUEST['t_id']);
        $forum = $this->_db->cache_fetch_object("SELECT f.id FROM " . PREFIX . "_f_forum AS f, " . PREFIX . "_f_topic AS t WHERE t.id = '" . $t_id . "' AND t.forum_id = f.id LIMIT 1");
        $permissions = Tool::accessForum($forum->id);

        if ($permissions['FORUM_RATE_TOPIC'] == 0) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        } else {
            $rating = $this->_db->cache_fetch_object("SELECT uid, rating, ip FROM " . PREFIX . "_f_rating WHERE topic_id = '" . $t_id . "' LIMIT 1");
            $r_uid = explode(',', $rating->uid);
            $ip = explode(',', $rating->ip);

            if ($_REQUEST['rating'] < 1) {
                $_REQUEST['rating'] = 1;
            } elseif ($_REQUEST['rating'] > 5) {
                $_REQUEST['rating'] = 5;
            }

            $rating = intval($_REQUEST['rating']);

            if ($this->_user > 0) {
                if (!in_array($this->_user, $r_uid)) {
                    if (!empty($rating->rating)) {
                        $q_rating = "UPDATE " . PREFIX . "_f_rating SET rating = '" . $rating . "', ip = '" . IP_USER . "', uid = '" . $this->_user . "' WHERE topic_id = " . $t_id;
                    } else {
                        $q_rating = "UPDATE " . PREFIX . "_f_rating SET rating = CONCAT(rating, ',', '" . $rating . "'), uid = CONCAT(uid, ',', '" . $this->_user . "'), ip = CONCAT(ip, ',', '" . IP_USER . "') WHERE topic_id = " . $t_id;
                    }
                    $this->_db->query($q_rating);
                }
            } else {
                if (!in_array(IP_USER, $ip)) {
                    if (empty($rating->rating)) {
                        $q_rating = "UPDATE " . PREFIX . "_f_rating SET rating = '" . $rating . "', ip = '" . IP_USER . "', uid = '" . $this->_user . "' WHERE topic_id = " . $t_id;
                    } else {
                        $q_rating = "UPDATE " . PREFIX . "_f_rating SET rating = CONCAT(rating, ',', '" . $rating . "'), ip = CONCAT(ip, ',', '" . IP_USER . "'), uid = CONCAT(uid, ',', '" . $this->_user . "'), WHERE topic_id = " . $t_id;
                    }
                    $this->_db->query($q_rating);
                }
            }
            $this->__object('Core')->message('Forums_rating_thread_message', 'Forums_VoteThankyou', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $t_id);
        }
    }

    protected function addsubscription() {
        $t_id = intval($_REQUEST['t_id']);
        if ($t_id >= 1) {
            $subscriptions = $this->_db->cache_fetch_object("SELECT notification FROM " . PREFIX . "_f_topic WHERE id = '" . $t_id . "' LIMIT 1");
            $user_id = preg_split('/,/', $subscriptions->notification);
            if (!in_array($this->_user, $user_id)) {
                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET notification = CONCAT(notification, ';', '" . $this->_user . "') WHERE id = '" . $t_id . "'");
            }
        }

        $t_id_url = isset($_SERVER['HTTP_REFERER']) ? $this->__object('Redir')->referer(true) : BASE_URL . '/index.php?p=showtopic&amp;toid=' . $t_id;
        $this->__object('Core')->message('Forums_AboMessage', 'Forums_TopicAboOk', $t_id_url);
    }

    protected function unsubscription() {
        $t_id = intval($_REQUEST['t_id']);
        if ($t_id >= 1) {
            $row = $this->_db->cache_fetch_object("SELECT notification FROM " . PREFIX . "_f_topic WHERE id = '" . $t_id . "' LIMIT 1");
            $new_insert = $this->del_uid($row->notification, $this->_user);
            $this->_db->query("UPDATE " . PREFIX . "_f_topic set notification='$new_insert' WHERE id = '" . $t_id . "'");
        }

        $t_id_url = isset($_SERVER['HTTP_REFERER']) ? $this->__object('Redir')->referer(true) : BASE_URL . '/index.php?p=showtopic&amp;toid=' . $t_id;
        $this->__object('Core')->message('Forums_AboMessage', 'Forums_TopicAboEndOk', $t_id_url);
    }

    protected function show() {
        $this->getCategs();
        $period = empty($_REQUEST['period']) ? 24 : intval($_REQUEST['period']);
        $period = $period > 72 ? 72 : $period;
        $fid = intval(Arr::getRequest('fid'));
        $forum_stat = !empty($fid) ? " AND t.forum_id = $fid" : '';
        $sort = (Arr::getRequest('sort') == 'asc') ? 'ASC' : 'DESC';

        switch (Arr::getRequest('unit')) {
            default:
            case 'h':
                $divisor = 60 * 60;
                $where_time_stat = "((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') / $divisor) - (UNIX_TIMESTAMP(p.datum) / $divisor)) <= $period";
                break;

            case 'd':
                $divisor = 60 * 60 * 24;
                $where_time_stat = "((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') / $divisor) - (UNIX_TIMESTAMP(p.datum) / $divisor)) <= $period";
                break;

            case 'm':
                $divisor = 60 * 60 * 24 * 30;
                $where_time_stat = "((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') / $divisor) - (UNIX_TIMESTAMP(p.datum) / $divisor)) <= $period";
                break;

            case 'all':
                $where_time_stat = "1";
                break;
        }

        $r_last_active = $this->_db->query("SELECT DISTINCT
			t.id,
			t.forum_id,
			t.title,
			t.status,
			t.type,
			t.datum,
			t.views,
			t.posticon,
			t.uid,
			t.replies,
			u.Benutzername AS uname,
			r.rating,
			f.group_id,
			f.title AS f_title,
			f.status AS fstatus
		FROM
			" . PREFIX . "_f_topic AS t,
			" . PREFIX . "_benutzer AS u,
			" . PREFIX . "_f_rating AS r,
			" . PREFIX . "_f_post AS p,
			" . PREFIX . "_f_forum AS f
		WHERE
			$where_time_stat
		AND
			u.Id = t.uid
		AND
			r.topic_id = t.id
		AND
			p.topic_id = t.id
		AND
			f.id = t.forum_id
                        $forum_stat
		ORDER BY
			t.datum $sort");

        $matches = array();
        while ($topic = $r_last_active->fetch_assoc()) {
            $group_ids = explode(',', $topic['group_id']);
            $forum_id = $topic['forum_id'];

            if (in_array($this->_group, $group_ids)) {
                $permissions = $this->permForum($forum_id, $this->_group);
                if ($permissions[1] == 1) {
                    $topic['autorlink'] = 'index.php?p=user&amp;id=' . $topic['uid'];
                    $topic['autor'] = $topic['uname'];
                    $topic['link'] = 'index.php?p=showtopic&amp;toid=' . $topic['id'];
                    $rating = explode(',', $topic['rating']);
                    $topic['rating'] = (int) (array_sum($rating) / count($rating));
                    $limit = 50;
                    $post = $this->_db->cache_fetch_object("SELECT COUNT(id) AS count FROM " . PREFIX . "_f_post WHERE topic_id = '" . $topic['id'] . "'");
                    $count = (($post->count / $limit) > ((int) ($post->count / $limit))) ? ((int) ($post->count / $limit)) + 1 : ((int) ($post->count / $limit));
                    $topic['navigation_count'] = ($count == 1) ? 0 : $count;

                    if ($topic['status'] == $this->status_moved) {
                        $topic['statusicon'] = $this->getIcon('thread_moved.png', $this->_lang['Forums_ThreadMoved']);
                    } else {
                        if ($_SESSION['loggedin'] != 1 || $topic['fstatus'] == $this->status_closed || $topic['status'] == $this->status_closed) {
                            $topic['statusicon'] = $this->getIcon('thread_lock.png', $this->_lang['Forums_TopicClosed']);
                        } else {
                            $this->iconTopic($topic, $this->ForumForums[$topic['forum_id']]['status']);
                        }
                    }
                    $matches[] = $topic;
                }
            }
        }
        $r_last_active->close();

        $tpl_array = array(
            'matches'      => $matches,
            'title_result' => $this->_lang['Forums_ShowLastActiveShort']);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['Forums_ShowLastActive'],
            'pagetitle' => $this->_lang['Forums_ShowLastActive'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/result.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function subscription() {
        if ($this->_user > 0) {
            $this->getCategs();
            $f_id = (!empty($_REQUEST['id'])) ? intval(Arr::getRequest('id')) : 't.forum_id';
            $query = "SELECT
                    t.id,
                    t.title,
                    t.forum_id,
                    t.views,
                    t.type,
                    t.status,
                    t.posticon,
                    t.rating,
                    t.uid,
                    f.title AS f_title,
                    t.notification,
                    t.replies,
                    t.status,
                    u.Benutzername AS uname
            FROM
                    " . PREFIX . "_f_topic AS t,
                    " . PREFIX . "_f_forum AS f,
                    " . PREFIX . "_benutzer AS u
            WHERE
                    f.id = $f_id
            AND
                    t.forum_id = f.id
            AND
                    u.Id = t.uid";
            $result = $this->_db->query($query);
            $matches = array();
            while ($topic = $result->fetch_assoc()) {
                $notification = explode(';', $topic['notification']);
                if (in_array($this->_user, $notification)) {
                    if ($topic['status'] == $this->status_moved) {
                        $topic['statusicon'] = $this->getIcon('thread_moved.png', $this->_lang['Forums_ThreadMoved']);
                    } else {
                        if ($_SESSION['loggedin'] != 1 || $topic['status'] == $this->status_closed) {
                            $topic['statusicon'] = $this->getIcon('thread_lock.png', $this->_lang['Forums_TopicClosed']);
                        } else {
                            $this->iconTopic($topic, $this->ForumForums[$topic['id']]['status']);
                        }
                    }

                    $topic['autorlink'] = 'index.php?p=user&amp;id=' . $topic['uid'];
                    $topic['link'] = 'index.php?p=showtopic&amp;toid=' . $topic['id'] . '&amp;fid=' . $topic['forum_id'];
                    $topic['autor'] = $topic['uname'];
                    $rating = explode(',', $topic['rating']);
                    $topic['rating'] = (int) (array_sum($rating) / count($rating));
                    $matches[] = $topic;
                }
            }
            $result->close();

            $tpl_array = array(
                'matches'      => $matches,
                'title_result' => $this->_lang['Forums_ShowAllAbos']);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_ShowAllAbos'],
                'pagetitle' => $this->_lang['Forums_ShowAllAbos'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/result.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Выводим мои сообщения */
    protected function posting() {
        if (is_numeric(Arr::getRequest('id'))) {
            $id = intval(Arr::getRequest('id'));
            $limit = Tool::getLim(15);
            $a = Tool::getLimit($limit);
            $result = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
				p.id,
				p.title,
				p.topic_id,
				p.datum,
				p.use_bbcode,
				p.use_smilies,
				p.use_sig,
				p.message,
				p.attachment,
				f.id AS forum_id,
				f.title AS forum_title,
				t.title AS topic_title
			FROM
				" . PREFIX . "_f_post AS p,
				" . PREFIX . "_f_topic AS t,
				" . PREFIX . "_f_forum AS f
			WHERE
				p.uid = " . $id . "
			AND
				t.id = p.topic_id
			AND
				t.forum_id = f.id
			AND
				f.active = '1'
			ORDER BY
				datum DESC LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $matches = array();
            while ($post = $result->fetch_object()) {
                $post->datum = strtotime($post->datum);
                $permissions = Tool::accessForum($post->forum_id);
                if ($permissions['FORUM_SEE_TOPIC'] == 1) {
                    $post->message = ($post->use_bbcode == 1) ? $this->__object('Post')->codes($post->message) : nl2br($post->message);
                    $post->topic_title = !empty($post->title) ? $post->title : $this->_text->substr(strip_tags($post->message), 0, 80) . '...';
                    $matches[] = $post;
                } else {
                    $post->topic_title = !empty($post->title) ? $post->title : $this->_text->substr(strip_tags($post->message), 0, 80) . '...';
                    $post->message = 'denied';
                    $matches[] = $post;
                }
            }
            $result->close();

            if ($limit < $num) {
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=forum&amp;action=print&amp;what=posting&amp;id=$id&amp;page={s}&amp;pp=$limit\">{t}</a> "));
            }

            $user = Tool::userName($id);
            $tpl_array = array(
                'navigation' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
                'post_count' => $num,
                'user_name'  => $user,
                'matches'    => $matches);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
                'pagetitle' => $this->_lang['UserPosts'] . ': ' . sanitize($user) . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/showpost.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Выводим темы без ответов */
    protected function topicsempty() {
        $limit = Tool::getLim(15);
        $a = Tool::getLimit($limit);
        $query = "SELECT SQL_CALC_FOUND_ROWS
			p.id,
			p.title,
			p.uid AS Autor,
			p.topic_id,
			p.datum,
			p.use_bbcode,
			p.use_smilies,
			p.use_sig,
			p.message,
			p.attachment,
			f.id AS forum_id,
			f.title AS forum_title,
			t.title AS topic_title
		FROM
			" . PREFIX . "_f_post AS p,
			" . PREFIX . "_f_topic AS t,
			" . PREFIX . "_f_forum AS f
		WHERE
			t.replies < 2
		AND
			t.id = p.topic_id
		AND
			t.forum_id = f.id
		AND
			f.active = '1'
		ORDER BY
			datum DESC LIMIT $a, $limit";

        $result = $this->_db->query($query);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $matches = array();
        while ($post = $result->fetch_object()) {
            $post->Uname = Tool::userName($post->Autor);
            $post->datum = strtotime($post->datum);
            $permissions = Tool::accessForum($post->forum_id);
            if ($permissions['FORUM_SEE_TOPIC'] == 1) {
                $post->message = ($post->use_bbcode == 1) ? $this->__object('Post')->codes($post->message) : nl2br($post->message);
                $post->topic_title = !empty($post->title) ? $post->title : $this->_text->substr(strip_tags($post->message), 0, 80) . '...';
                $matches[] = $post;
            } else {
                $post->topic_title = !empty($post->title) ? $post->title : $this->_text->substr(strip_tags($post->message), 0, 80) . '...';
                $post->message = 'denied';
                $matches[] = $post;
            }
        }
        $result->close();

        if ($limit < $num) {
            $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=forum&amp;action=print&amp;what=topicsempty&amp;page={s}&amp;pp=$limit\">{t}</a> "));
        }

        $tpl_array = array(
            'navigation' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
            'post_count' => $num,
            'matches'    => $matches);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_Title'] . '</a>',
            'pagetitle' => $this->_lang['Forums_ThreadsEmpty'] . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/showpost_without_replies.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Выводим последние новые сообщения */
    protected function lastposts() {
        $limit = 25;
        $result = $this->_db->query("SELECT
			p.id,
			p.title,
			p.uid AS Autor,
			p.topic_id,
			p.datum,
			p.use_bbcode,
			p.use_smilies,
			p.use_sig,
			p.message,
			p.attachment,
			f.id AS forum_id,
			f.title AS forum_title,
			t.title AS topic_title
		FROM
			" . PREFIX . "_f_post AS p,
			" . PREFIX . "_f_topic AS t,
			" . PREFIX . "_f_forum AS f
		WHERE
			t.id = p.topic_id
		AND
			t.forum_id = f.id
		AND
			f.active = '1'
		ORDER BY
			datum DESC LIMIT $limit");
        $matches = array();
        while ($post = $result->fetch_object()) {
            $post->topic_title_raw = $post->topic_title;
            $post->Uname = Tool::userName($post->Autor);
            $post->datum = strtotime($post->datum);
            $permissions = Tool::accessForum($post->forum_id);
            if ($permissions['FORUM_SEE_TOPIC'] == 1) {
                $post->message = ($post->use_bbcode == 1) ? $this->__object('Post')->codes($post->message) : nl2br($post->message);
                $post->post_title = !empty($post->title) ? $post->title : $post->message;
                $page_num = Tool::countPost($post->id, $post->topic_id, $this->LimitB);
                $post->PostLink = 'index.php?p=showtopic&amp;toid=' . $post->topic_id . '&amp;pp=' . $this->LimitB . '&amp;page=' . $page_num . '#pid_' . $post->id;
            } else {
                $post->post_title = !empty($post->title) ? $post->title : $post->message;
                $post->message = 'denied';
            }
            $post->post_title = $this->_text->substr(strip_tags($this->__object('Post')->clean($post->post_title)), 0, 120) . '...';
            $matches[] = $post;
        }
        $result->close();

        $tpl_array = array(
            'navigation' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_LastPostsT'] . '</a>',
            'matches'    => $matches);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=showforums">' . $this->_lang['Forums_LastPostsT'] . '</a>',
            'pagetitle' => $this->_lang['Forums_LastPostsT'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/lastposts_forum.tpl'));
        $this->_view->finish($seo_array);
    }

    public function newPost() {
        if ($_SESSION['loggedin'] != 1 || $this->_group == 2) {
            $this->__object('Core')->message('Forums_PostErrorNew', 'Forums_ErrorNewPostings', BASE_URL . '/index.php?p=showforums');
        }

        $toid = intval($_REQUEST['toid']);
        if (!$this->topicExists($toid)) {
            $this->__object('Core')->message('Forums_Title', 'NoPerm', BASE_URL . '/index.php?p=showforums');
        }

        $closed = $this->_db->cache_fetch_object("SELECT f.id, f.status AS fstatus, t.status AS tstatus, t.uid FROM " . PREFIX . "_f_forum AS f, " . PREFIX . "_f_topic AS t WHERE t.id = " . $toid . " AND f.id = t.forum_id LIMIT 1");
        if (!isset($closed->id)) {
            $this->__object('Core')->message('Forums_Title', 'NoPerm', BASE_URL . '/index.php?p=showforums');
        }

        $is_moderator = $this->is_mod($closed->id);
        if (!$is_moderator) {
            if ($closed->fstatus == $this->status_closed) {
                $this->__object('Core')->message('Forums_Title', 'Forums_MsgisClosed', $this->__object('Redir')->referer(true));
            }
            if ($closed->tstatus == $this->status_closed) {
                $this->__object('Core')->message('Forums_Title', 'Forums_TopicClosed', $this->__object('Redir')->referer(true));
            }
        }

        $this->getCategs();
        $permissions = Tool::accessForum($closed->id);
        if ($this->ForumForums[$closed->id]['intersect'] == 1) {
            $permissions['FORUM_SEE'] = 1;
        }

        if ($permissions['FORUM_SEE'] == 0) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }

        if ($closed->uid == $this->_user && $permissions['FORUM_REPLY_OWN_TOPIC'] != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
        }
        if ($closed->uid != $this->_user && $permissions['FORUM_REPLY_OTHER_TOPIC'] != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
        }

        $pid = intval(Arr::getRequest('pid'));
        if (Arr::getRequest('action') == 'edit') {
            $information = $this->_db->cache_fetch_object("SELECT
                    u.Id,
                    UNIX_TIMESTAMP(p.datum) AS datum,
                    UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') AS today
                FROM
                    " . PREFIX . "_f_post AS p
                LEFT OUTER JOIN
                    " . PREFIX . "_benutzer AS u
                ON
                    u.Id = p.uid
                WHERE
                    p.id = '" . $pid . "'
                LIMIT 1");

            $curr_unix_stamp = $information->today;
            $post_unix_stamp = $information->datum;
            $time_diff = (int) (($curr_unix_stamp - $post_unix_stamp) / 60 / 60);

            if (!$is_moderator) {
                if (!isset($information->Id)) {
                    $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
                }
                if ($information->Id != $this->_user && $permissions['FORUM_EDIT_OTHER_POST'] != 1) {
                    $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
                }
                if ($information->Id == $this->_user && $permissions['FORUM_EDIT_OWN_POST'] != 1) {
                    $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
                }
                if ($time_diff >= 672) {
                    $this->__object('Core')->message('Forums_EditPost', 'Forums_ErrorPostEditEnd', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
                }
            }
        }

        if ($this->_settings['SysCode_Smilies'] == 1) {
            $this->_view->assign('smilie', 1);
        }

        if (!empty($pid)) {
            $attachment = $_REQUEST['action'] == 'quote' ? '' : 'p.attachment,';
            $message = $this->_db->cache_fetch_object("SELECT
                        u.Benutzername AS uname,
                        p.message,
                        p.id,
                        p.title,
                        $attachment
                        t.title AS topic,
                        t.posticon,
                        t.uid,
                        t.id AS toid,
                        t.forum_id
                FROM
                        " . PREFIX . "_f_topic AS t,
                        " . PREFIX . "_f_post AS p
                LEFT OUTER JOIN
                        " . PREFIX . "_benutzer AS u
                ON
                        p.uid = u.Id
                WHERE
                        p.id = '" . $pid . "'
                AND
                        t.id = p.topic_id
                LIMIT 1");

            if ($_REQUEST['action'] == 'quote') {
                $message->message = preg_replace('#\[hide=(.*?)\](.*?)\[\/hide\]#siu', $this->_lang['reg'], $message->message);
                $message->message = "[QUOTE][B]" . $this->_lang['GlobalAutor'] . ": " . $message->uname . "[/B]\n" . $message->message . "[/QUOTE]\r\n\r\n";
            }

            if (empty($message->attachment)) {
                $message->attachment = '';
            }
            $attach = explode(';', $message->attachment);
            if ($message->attachment) {
                $h_attachments_only_show = array();
                foreach ($attach as $attachment) {
                    $row_a = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_attachment WHERE id='" . $attachment . "' LIMIT 1");
                    if (is_object($row_a)) {
                        $attach_img = $this->attachmentImg($row_a->orig_name);
                        $h_attachments_only_show[] = '<div id="div_' . $row_a->id . '"><input type="hidden" name="attach_hidden[]" id="att_' . $row_a->id . '" value="' . $row_a->id . '" />' . $attach_img . ' <a href="index.php?p=forum&amp;action=getfile&amp;id=' . $row_a->id . '&amp;f_id=' . $message->forum_id . '&amp;t_id=' . $message->toid . '">' . $row_a->orig_name . '</a>&nbsp;<a href="?p=misc&amp;do=delattach&amp;id=' . $row_a->id . '&amp;file=' . $row_a->filename . '" target="attachment_frame" onclick="document.getElementById(\'att_' . $row_a->id . '\').value=\'\';document.getElementById(\'div_' . $row_a->id . '\').style.display=\'none\';document.getElementById(\'hidden_count\').value = document.getElementById(\'hidden_count\').value - 1;"><img class="absmiddle" src="theme/' . $this->_theme . '/images/forums/delete_small.png" alt="" border="0" hspace="2" /></a></div>';
                    }
                }
                $this->_view->assign('h_attachments_only_show', $h_attachments_only_show);
            }

            $tpl_array = array(
                'attachments_hidden' => $message->attachment,
                'message'            => $message,
                'f_id'               => $closed->id);
            $this->_view->assign($tpl_array);
        }

        $row = $this->_db->cache_fetch_object("SELECT title, notification FROM " . PREFIX . "_f_topic WHERE id = '" . $toid . "' LIMIT 1");
        $notifactions = explode(';', $row->notification);

        if (in_array($this->_user, $notifactions)) {
            $this->_view->assign('notification', 1);
        }

        $navigation = $this->navigation($toid, 'topic') . $this->_lang['PageSep'] . '<a class="forum_links_navi" href="index.php?p=showtopic&amp;toid=' . $toid . '&amp;fid=' . $closed->id . '&amp;page=1&amp;t=' . translit(sanitize($row->title)) . '">' . sanitize($row->title) . '</a>' . $this->_lang['PageSep'] . ((Arr::getRequest('action') == 'edit') ? $this->_lang['GlobalEdit'] : $this->_lang['Forums_Header_postreply_form']);

        $tpl_array = array(
            'navigation' => $navigation,
            'treeview'   => explode($this->_lang['PageSep'], $navigation),
            'top_title'  => $this->_lang['Forums_Header_postreply_form']);
        $this->_view->assign($tpl_array);

        if (Arr::getRequest('preview') == 1) {
            $tpl_array = array(
                'subject' => $_REQUEST['subject'],
                'text'    => $_REQUEST['text']);
            $this->_view->assign($tpl_array);
        }

        $items = array();
        $extra = !$this->is_mod($toid) ? " AND opened='1'" : '';
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_f_post WHERE topic_id = '" . $toid . "' {$extra} ORDER BY id DESC LIMIT 10");
        while ($row = $sql->fetch_object()) {
            $row->user = Tool::userName($row->uid);
            $row->message = $row->use_bbcode == 1 ? $this->__object('Post')->codes($row->message) : nl2br($row->message);
            if ($row->use_smilies == 1 && $this->_settings['SysCode_Smilies'] == 1) {
                $row->message = $this->__object('Post')->smilies($row->message);
            }
            $row->message = Tool::censored($row->message);
            $items[] = $row;
        }
        $sql->close();

        $_REQUEST['fid'] = $closed->id;

        $tpl_array = array(
            'aid'            => $closed->uid,
            'items'          => $items,
            'permissions'    => $permissions,
            'maxlength_post' => SX::get('user_group.MaxZeichenPost'),
            'bbcodes'        => $this->_settings['SysCode_Aktiv'],
            'navigation'     => $navigation,
            'listfonts'      => $this->__object('Post')->font(),
            'sizedropdown'   => $this->__object('Post')->fontsize(),
            'colordropdown'  => $this->__object('Post')->color(),
            'posticons'      => (isset($message->posticon) ? $this->getPosticons($message->posticon) : ''),
            'listemos'       => $this->__object('Post')->listsmilies(),
            'topic_id'       => $toid,
            'action'         => 'index.php?p=addpost');
        $this->_view->assign($tpl_array);
        $this->_view->assign('threadform', $this->_view->fetch(THEME . '/forums/threadform.tpl'));

        if (Arr::getRequest('action') == 'edit') {
            $this->_view->assign('top_title', $this->_lang['GlobalEdit']);
            if ($message->uid == $this->_user || $is_moderator) {
                $this->_view->assign('topic', $message->topic);
                $this->_view->assign('topicform', $this->_view->fetch(THEME . '/forums/topicform.tpl'));
            }
        }

        $seo_array = array(
            'headernav' => $this->_lang['Forums_NewTopic'],
            'pagetitle' => Tool::repeat($navigation) . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/addtopic.tpl'));
        $this->_view->finish($seo_array);
    }

    public function addTopic() {
        $forum_id = intval($_REQUEST['forum_id']);
        $this->getCategs();
        if ($this->ForumForums[$forum_id]['intersect'] == 1) {
            $permissions = Tool::accessForum($forum_id);
        }

        if ($permissions['FORUM_CREATE_TOPIC'] == 0) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        }

        $error_array = array();
        if (empty($_REQUEST['topic'])) {
            array_push($error_array, str_replace("{0}", $this->_lang['GlobalTheme'], $this->_lang['Forums_ErrorMissing']));
        }
        if (empty($_REQUEST['text'])) {
            array_push($error_array, str_replace("{0}", $this->_lang['GlobalMessage'], $this->_lang['Forums_ErrorMissing']));
        }

        if (count($error_array) || ($_REQUEST['preview'] == 1)) {
            $this->_view->assign('smilie', $this->_settings['SysCode_Smilies']);

            if (Arr::getRequest('preview') == 1) {
                $preview_text = $_REQUEST['text'];
                if (($_REQUEST['parseurl']) == 1) {
                    $preview_text = $this->__object('Post')->parseUrl($preview_text);
                }
                $preview_text = $this->_settings['SysCode_Aktiv'] == 1 && !isset($_REQUEST['disablebb']) ? $this->__object('Post')->codes($preview_text) : nl2br($preview_text);
                if ($this->_settings['SysCode_Smilies'] == 1 && !isset($_REQUEST['disablesmileys'])) {
                    $preview_text = $this->__object('Post')->smilies($preview_text);
                }
                if (Arr::getRequest('attach_hidden') >= 1) {
                    $h_attachments_only_show = array();
                    foreach ($_REQUEST['attach_hidden'] as $attachment) {
                        $row_a = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_attachment WHERE id='" . intval($attachment) . "' LIMIT 1");

                        if (is_object($row_a)) {
                            $attach_img = $this->attachmentImg($row_a->orig_name);
                            $h_attachments_only_show[] = '<div id="div_' . $row_a->id . '" style=""><input type="hidden" name="attach_hidden[]" id="att_' . $row_a->id . '" value="' . $row_a->id . '" />' . $attach_img . ' <a href="index.php?p=forum&amp;action=getfile&amp;id=' . $row_a->id . '&amp;f_id=' . $forum_id . '&amp;t_id=">' . $row_a->orig_name . '</a>&nbsp;<a href="?p=misc&amp;do=delattach&amp;id=' . $row_a->id . '&amp;file=' . $row_a->filename . '" target="attachment_frame" onclick="document.getElementById(\'att_' . $row_a->id . '\').value=\'\';document.getElementById(\'div_' . $row_a->id . '\').style.display=\'none\';document.getElementById(\'hidden_count\').value = document.getElementById(\'hidden_count\').value - 1;"><img class="absmiddle" src="theme/' . $this->_theme . '/images/forums/delete_small.png" alt="" border="0" hspace="2" /></a></div>';
                        }
                    }
                    $this->_view->assign('h_attachments_only_show', $h_attachments_only_show);
                }
                $items = array();
                $tpl_array = array(
                    'preview_text'      => $preview_text,
                    'preview_text_form' => $_REQUEST['text'],
                    'items'             => $items);
                $this->_view->assign($tpl_array);
            }

            $tpl_array = array(
                'permissions'    => $permissions,
                'forum_id'       => $forum_id,
                'new_topic'      => 1,
                'navigation'     => $this->navigation($forum_id, 'topic'),
                'bbcodes'        => $this->_settings['SysCode_Aktiv'],
                'maxlength_post' => SX::get('user_group.MaxZeichenPost'),
                'posticons'      => $this->getPosticons(),
                'listemos'       => $this->__object('Post')->listsmilies(),
                'topic'          => sanitize($_REQUEST['topic']),
                'subject'        => sanitize($_REQUEST['subject']),
                'message'        => sanitize($_REQUEST['text']),
                'listfonts'      => $this->__object('Post')->font(),
                'sizedropdown'   => $this->__object('Post')->fontsize(),
                'colordropdown'  => $this->__object('Post')->color(),
                'errors'         => $error_array);
            $this->_view->assign($tpl_array);

            $tpl_array = array(
                'topicform'  => $this->_view->fetch(THEME . '/forums/topicform.tpl'),
                'threadform' => $this->_view->fetch(THEME . '/forums/threadform.tpl'));
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_NewTopic'],
                'pagetitle' => $this->_lang['Forums_NewTopic'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'generate'  => $this->_lang['Forums_NewTopic'],
                'content'   => $this->_view->fetch(THEME . '/forums/addtopic.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $status = !empty($_REQUEST['status']) ? intval($_REQUEST['status']) : '0';
            $posticon = !empty($_REQUEST['posticon']) ? intval($_REQUEST['posticon']) : '0';
            $topic = Tool::cleanAllow($_REQUEST['topic'], ' ,.!?()');
            $notification = !empty($_REQUEST['notification']) ? ';' . $this->_user : '';
            $opened = $this->ForumForums[$forum_id]['moderated'] == 1 ? 2 : 1;
            $topic_emails = $this->ForumForums[$forum_id]['topic_emails'];
            $is_moderator = $this->is_mod($forum_id);
            if ($is_moderator) {
                $opened = 1;
            }
            $type = 0;
            if ($permissions['FORUM_CHANGE_TOPICTYPE'] == 1) {
                if (Arr::getRequest('subaction') == 'announce') {
                    $type = 100;
                }
                if (Arr::getRequest('subaction') == 'attention') {
                    $type = 1;
                }
            }

            if ($permissions['FORUM_CLOSE_TOPIC'] == 1) {
                if (Arr::getRequest('subaction') == 'close') {
                    $status = 1;
                }
            }

            $stime = time();
            $insert_array = array(
                'type'          => $type,
                'title'         => $topic,
                'status'        => $status,
                'replies'       => 1,
                'datum'         => date('Y-m-d H:i:s'),
                'views'         => 1,
                'forum_id'      => $forum_id,
                'posticon'      => $posticon,
                'uid'           => $this->_user,
                'notification'  => $notification,
                'opened'        => $opened,
                'last_post_int' => $stime);
            $this->_db->insert_query('f_topic', $insert_array);

            $topic_id = $this->_db->insert_id();
            if ($opened == 2) {
                $sql = $this->_db->query("SELECT user_id FROM " . PREFIX . "_f_mods WHERE forum_id = '$forum_id'");
                while ($row = $sql->fetch_object()) {
                    if (!empty($row->user_id)) {
                        $row2 = $this->_db->cache_fetch_object("SELECT Benutzername, Email FROM " . PREFIX . "_benutzer WHERE Id = '$row->user_id' LIMIT 1");
                        $link = BASE_URL . '/index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id;
                        $mail_array = array(
                            '__USER__'    => $this->_uname,
                            '__MODS__'    => $row2->Benutzername,
                            '__DATUM__'   => $stime,
                            '__BETREFF__' => $topic,
                            '__LINK__'    => $link);
                        $body = $this->_text->replace($this->_lang['body_to_mods_moderated'], $mail_array);
                        $body = str_replace("\n", "\r\n", $body);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $row2->Email,
                            'to_name'   => $row2->Benutzername,
                            'text'      => $body,
                            'subject'   => $this->_lang['NewMessage'],
                            'fromemail' => $this->_settings['Mail_Absender'],
                            'from'      => $this->_settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 3));
                    }
                }
                $sql->close();
            }

            $insert_array = array(
                'topic_id' => $topic_id,
                'rating'   => '',
                'ip'       => '');
            $this->_db->insert_query('f_rating', $insert_array);

            $message = $this->_text->substr($_REQUEST['text'], 0, SX::get('user_group.MaxZeichenPost'));
            if ($_REQUEST['parseurl']) {
                $message = $this->__object('Post')->parseUrl($message);
            }
            $disable_bbcode = (Arr::getRequest('disablebb') == 1) ? 0 : 1;
            $disable_smilies = (Arr::getRequest('disablesmileys') == 1) ? 0 : 1;
            $use_sig = (Arr::getRequest('usesig') == 1) ? 1 : 0;

            if (Arr::getPost('attach_hidden') >= 1) {
                foreach ($_POST['attach_hidden'] as $file) {
                    if (!empty($file)) {
                        $attached_files[] = $file;
                    }
                }
                $attachments = implode(';', $attached_files);
            } else {
                $attachments = (isset($_REQUEST['attachment'])) ? implode(';', $_REQUEST['attachment']) : '';
            }

            $insert_array = array(
                'title'       => Tool::cleanTags(Arr::getRequest('subject'), array('codewidget')),
                'message'     => Tool::cleanTags($this->del_mod($is_moderator, $message), array('codewidget')),
                'datum'       => date('Y-m-d H:i:s'),
                'topic_id'    => $topic_id,
                'uid'         => $this->_user,
                'use_bbcode'  => $disable_bbcode,
                'use_smilies' => $disable_smilies,
                'use_sig'     => $use_sig,
                'attachment'  => $attachments,
                'opened'      => $opened,
                'thanks'      => '');
            $db_result = $this->_db->insert_query('f_post', $insert_array);
            $last_post_id = $this->_db->insert_id();

            if (!empty($topic_emails)) {
                $message = str_ireplace('[quote]', PE . '----------' . $this->_lang['MailQuoteStart'] . '-----------' . PE, $message);
                $message = str_ireplace('[/quote]', PE . '----------' . $this->_lang['MailQuoteEnd'] . '-----------' . PE, $message);
                $message = $this->__object('Post')->clean($message);
                $mails = explode(',', $topic_emails);
                $link = BASE_URL . '/index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id;
                $body_s = ($opened == 2) ? $this->_lang['body_to_mods_moderated'] : $this->_lang['f_msg_newtopic_body_adminnotification'];
                $mail_array = array(
                    '__MODS__'    => '',
                    '__DATUM__'   => date('d-m-Y, H:i', $stime),
                    '__USER__'    => $this->_uname,
                    '__SUBJECT__' => $topic,
                    '__BETREFF__' => $topic,
                    '__LINK__'    => $link,
                    '__MESSAGE__' => $message);
                $body_s = $this->_text->replace($body_s, $mail_array);
                foreach ($mails as $send_mail) {
                    if (!empty($send_mail)) {
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $send_mail,
                            'to_name'   => '',
                            'text'      => $body_s,
                            'subject'   => $this->_lang['Forums_NewTopicmsg'],
                            'fromemail' => $this->_settings['Mail_Absender'],
                            'from'      => $this->_settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 3));
                    }
                }
            }

            if (!$db_result) {
                $this->__object('Core')->message('Forums_PostErrorNew', 'Forums_PostErrorNewmsg', $_REQUEST['redir']);
            } else {
                if ($this->_user > 0) {
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = Beitraege + 1 WHERE Id = '" . $this->_user . "'");
                }

                // Добавляем задание на пинг
                $options = array(
                    'name' => $_POST['topic'],
                    'url'  => BASE_URL . '/index.php?p=showtopic&print_post=' . $last_post_id . '&toid=' . $topic_id . '&t=' . translit($_POST['topic']),
                    'lang' => $_SESSION['lang']);

                $cron_array = array(
                    'datum'   => $stime,
                    'type'    => 'sys',
                    'modul'   => 'ping',
                    'title'   => $topic,
                    'options' => serialize($options),
                    'aktiv'   => 1);
                $this->__object('Cron')->add($cron_array);

                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET first_post_id = '" . $last_post_id . "' WHERE id = '" . $topic_id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET last_post = '" . date('Y-m-d H:i:s') . "', last_post_int ='" . $stime . "' WHERE id = '" . $topic_id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_f_forum SET last_post = '" . date('Y-m-d H:i:s') . "', last_post_id = " . $last_post_id . " WHERE id = '" . $forum_id . "'");

                $this->readTopic($topic_id);
                if ($opened == 2) {
                    $msg = 'Forums_NewTopicmsg_moderated';
                    $this->__object('Core')->message('Forums_NewTopic', $msg, BASE_URL . '/index.php?p=showforum&amp;fid=' . $forum_id);
                } else {
                    $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id . '&page=1&t=' . translit($_POST['topic']));
                }
            }
        }
    }

    public function newTopic() {
        $perm = false;
        $fid = intval(Arr::getRequest('fid'));
        $this->getCategs();

        if (empty($this->ForumForums[$fid]['id'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }
        if ($this->ForumForums[$fid]['status'] == $this->status_closed && $this->_group != 1) {
            $this->__object('Core')->message('Forums_Title', 'Forums_MsgisClosed', $this->__object('Redir')->referer(true));
        }
        if ($this->ForumForums[$fid]['intersect'] == 1) {
            $permissions = Tool::accessForum($fid);
            if ($permissions['FORUM_CREATE_TOPIC'] == 1) {
                $perm = true;
            }
        }

        if (!$perm && $this->_group != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforum&amp;fid=' . $fid);
        }

        if ($this->_settings['SysCode_Smilies'] == 1) {
            $this->_view->assign('smilie', 1);
        }
        $navigation = $this->navigation($fid, 'forum') . $this->_lang['PageSep'] . '<a class="forum_links_navi" href="index.php?p=showforum&amp;fid=' . $fid . '">' . $this->ForumForums[$fid]['title'] . '</a>';

        $tpl_array = array(
            'top_title'      => $this->_lang['Forums_NewTopic'],
            'treeview'       => explode($this->_lang['PageSep'], $navigation),
            'permissions'    => $permissions,
            'bbcodes'        => $this->_settings['SysCode_Aktiv'],
            'new_topic'      => 1,
            'maxlength_post' => SX::get('user_group.MaxZeichenPost'),
            'listfonts'      => $this->__object('Post')->font(),
            'sizedropdown'   => $this->__object('Post')->fontsize(),
            'colordropdown'  => $this->__object('Post')->color(),
            'posticons'      => $this->getPosticons(),
            'listemos'       => $this->__object('Post')->listsmilies(),
            'forum_id'       => $fid,
            'action'         => 'index.php?p=addtopic');
        $this->_view->assign($tpl_array);

        $tpl_array = array(
            'topicform'  => $this->_view->fetch(THEME . '/forums/topicform.tpl'),
            'threadform' => $this->_view->fetch(THEME . '/forums/threadform.tpl'));
        $this->_view->assign($tpl_array);

        $navigation = strip_tags($navigation);
        $seo_array = array(
            'headernav' => $navigation,
            'pagetitle' => Tool::repeat($navigation . $this->_lang['PageSep'] . $this->_lang['Forums_NewTopic']),
            'generate'  => $this->ForumForums[$fid]['title'],
            'content'   => $this->_view->fetch(THEME . '/forums/addtopic.tpl'));
        $this->_view->finish($seo_array);
    }

    public function addPost() {
        if (!isset($_REQUEST['toid']) || empty($_REQUEST['toid'])) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        }

        $no_count_post = false;
        $topic_id = intval($_REQUEST['toid']);
        $forum = $this->_db->cache_fetch_object("SELECT f.id, t.uid FROM " . PREFIX . "_f_forum AS f," . PREFIX . "_f_topic AS t WHERE t.id = '" . $topic_id . "' AND t.forum_id = f.id LIMIT 1");

        $this->getCategs();
        if ($this->ForumForums[$forum->id]['intersect'] == 1) {
            $permissions = Tool::accessForum($forum->id);
        }

        $is_moderator = $this->is_mod($forum->id);
        if ($forum->uid == $this->_user) {
            if ($permissions['FORUM_REPLY_OWN_TOPIC'] == 0) {
                $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $topic_id);
            }
        } else {
            if ($permissions['FORUM_REPLY_OTHER_TOPIC'] == 0 && !$is_moderator) {
                $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $topic_id);
            }
        }

        $error_array = array();
        if (empty($_REQUEST['text'])) {
            $error_array[] = $this->_lang['Forums_ErrorNoComment'];
        }
        if (count($error_array) || $_REQUEST['preview'] == 1) {
            $this->_view->assign('smilie', $this->_settings['SysCode_Smilies']);
            $row_t = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $topic_id . "' LIMIT 1");
            $navigation = $this->navigation($topic_id, 'topic') . $this->_lang['PageSep'] . '<a class="forum_links_navi" href="index.php?p=showtopic&amp;toid=' . $topic_id . '">' . $row_t->title . '</a>' . $this->_lang['PageSep'] . $this->_lang['Forums_Header_postreply_form'];

            if ($_REQUEST['preview'] == 1) {
                $preview_text = $_REQUEST['text'];
                if (($_REQUEST['parseurl']) == 1) {
                    $preview_text = $this->__object('Post')->parseUrl($preview_text);
                }
                $preview_text = ($this->_settings['SysCode_Aktiv'] == 1 && (!isset($_REQUEST['disablebb']) || $_REQUEST['disablebb'] != 1)) ? $this->__object('Post')->codes($preview_text) : nl2br($preview_text);
                if (($this->_settings['SysCode_Smilies'] == 1) && (!isset($_REQUEST['disablesmileys']) || $_REQUEST['disablesmileys'] != 1)) {
                    $preview_text = $this->__object('Post')->smilies($preview_text);
                }
                if (Arr::getRequest('attach_hidden') >= 1) {
                    $h_attachments_only_show = array();
                    foreach ($_REQUEST['attach_hidden'] as $attachment) {
                        $row_a = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_attachment WHERE id='" . intval($attachment) . "' LIMIT 1");
                        if (is_object($row_a)) {
                            $attach_img = $this->attachmentImg($row_a->orig_name);
                            $h_attachments_only_show[] = '<div id="div_' . $row_a->id . '" style=""><input type="hidden" name="attach_hidden[]" id="att_' . $row_a->id . '" value="' . $row_a->id . '" />' . $attach_img . ' <a href="index.php?p=forum&amp;action=getfile&amp;id=' . $row_a->id . '&amp;f_id=' . $forum->id . '&amp;t_id=">' . $row_a->orig_name . '</a>&nbsp;<a href="?p=misc&amp;do=delattach&amp;id=' . $row_a->id . '&amp;file=' . $row_a->filename . '" target="attachment_frame" onclick="document.getElementById(\'att_' . $row_a->id . '\').value=\'\';document.getElementById(\'div_' . $row_a->id . '\').style.display=\'none\';document.getElementById(\'hidden_count\').value = document.getElementById(\'hidden_count\').value - 1;"><img class="absmiddle" src="theme/' . $this->_theme . '/images/forums/delete_small.png" alt="" border="0" hspace="2" /></a></div>';
                        }
                    }
                    $this->_view->assign('h_attachments_only_show', $h_attachments_only_show);
                }

                $tpl_array = array(
                    'permissions'       => $permissions,
                    'pre_error'         => 1,
                    'preview_text'      => $preview_text,
                    'preview_text_form' => $_REQUEST['text'],
                    'f_id'              => $_REQUEST['f_id']);
                $this->_view->assign($tpl_array);

                $items = array();
                $extra = !$this->is_mod($topic_id) ? " AND opened='1'" : '';
                $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_f_post WHERE topic_id = '" . $topic_id . "' {$extra} ORDER BY id DESC LIMIT 10");
                while ($row = $sql->fetch_object()) {
                    $row->user = Tool::userName($row->uid);
                    $row->message = ($row->use_bbcode == 1) ? $this->__object('Post')->codes($row->message) : nl2br($row->message);
                    if ($row->use_smilies == 1 && $this->_settings['SysCode_Smilies'] == 1) {
                        $row->message = $this->__object('Post')->smilies($row->message);
                    }
                    $row->message = Tool::censored($row->message);
                    $items[] = $row;
                }
                $sql->close();
                $this->_view->assign('items', $items);
            }

            $tpl_array = array(
                'topic_id'      => $topic_id,
                'navigation'    => $navigation,
                'bbcodes'       => $this->_settings['SysCode_Aktiv'],
                'posticons'     => $this->getPosticons(),
                'listemos'      => $this->__object('Post')->listsmilies(),
                'subject'       => sanitize($_REQUEST['subject']),
                'message'       => $_REQUEST['text'],
                'listfonts'     => $this->__object('Post')->font(),
                'sizedropdown'  => $this->__object('Post')->fontsize(),
                'colordropdown' => $this->__object('Post')->color(),
                'errors'        => $error_array);
            $this->_view->assign($tpl_array);
            $this->_view->assign('threadform', $this->_view->fetch(THEME . '/forums/threadform.tpl'));

            $seo_array = array(
                'headernav' => $this->_lang['Forums_NewTopic'],
                'pagetitle' => $this->_lang['Forums_NewTopic'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/addtopic.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $title = Arr::getRequest('subject');
            $message = $this->_text->substr($_REQUEST['text'], 0, SX::get('user_group.MaxZeichenPost'));
            if (Arr::getRequest('parseurl') == 1) {
                $message = $this->__object('Post')->parseUrl($message);
            }
            $disable_bbcode = (Arr::getRequest('disablebb') == 1) ? 0 : 1;
            $disable_smilies = (Arr::getRequest('disablesmileys') == 1) ? 0 : 1;
            $use_sig = Arr::getRequest('usesig') ? 1 : 0;
            $notification = Arr::getRequest('notification') ? 1 : 0;
            $r_notification = $this->_db->cache_fetch_object("SELECT title, notification, forum_id FROM " . PREFIX . "_f_topic WHERE id = '$topic_id' LIMIT 1");
            $forum_id = $r_notification->forum_id;
            $user_ids = explode(';', $r_notification->notification);

            if (Arr::getRequest('attach_hidden') >= 1) {
                foreach ($_REQUEST['attach_hidden'] as $file) {
                    if (!empty($file)) {
                        $attached_files[] = $file;
                    }
                }
                $attachments = implode(';', $attached_files);
            } else {
                $attachments = (isset($_REQUEST['attachment'])) ? implode(';', $_REQUEST['attachment']) : '';
            }

            if (Arr::getRequest('action') == 'edit') {
                $announce = $status = '';
                if ($permissions['FORUM_CHANGE_TOPICTYPE'] == 1) {
                    switch (Arr::getRequest('subaction')) {
                        case 'announce':
                            $announce = 'type = 100';
                            break;
                        case 'attention':
                            $announce = 'type = 1';
                            break;
                    }
                }

                if ($permissions['FORUM_CLOSE_TOPIC'] == 1 && $_REQUEST['subaction'] == 'close') {
                    $status = 'status = 1';
                }

                if (!empty($status) || !empty($announce)) {
                    if (!empty($announce) && !empty($status)) {
                        $sep = ',';
                    }
                    $sql = $this->_db->query("UPDATE " . PREFIX . "_f_topic SET $announce $sep $status WHERE id = '" . $topic_id . "'");
                }

                if (!$is_moderator) {
                    $redact = '[i]' . $this->_lang['Forums_PostEdit'] . ' [b]' . $this->_uname . '[/b]: ' . date('d.m.Y, H:i:s') . '[/i]';
                    $redact_mask = '!\[i\]' . $this->_lang['Forums_PostEdit'] . ' \[b\](.*?)\[/b\]:(.*?)\[/i\]!u';
                    if (preg_match($redact_mask, $message)) {
                        $message = preg_replace($redact_mask, $redact, $message);
                    } else {
                        $message = $message . "\n\n" . $redact;
                    }
                }

                $array = array(
                    'title'       => Tool::cleanTags($title, array('codewidget')),
                    'message'     => Tool::cleanTags($this->del_mod($is_moderator, $message), array('codewidget')),
                    'use_bbcode'  => $disable_bbcode,
                    'use_smilies' => $disable_smilies,
                    'use_sig'     => $use_sig,
                );
                if (!empty($attachments)) {
                    $array['attachment'] = $attachments;
                }
                $db_result = $this->_db->update_query('f_post', $array, "id = '" . intval($_REQUEST['p_id']) . "'");

                $topic = $this->_db->cache_fetch_object("SELECT t.uid, t.id FROM " . PREFIX . "_f_topic AS t, " . PREFIX . "_f_post AS p WHERE p.id = " . intval($_REQUEST['p_id']) . " AND t.id = p.topic_id LIMIT 1");
                if ($topic->uid == $this->_user || $is_moderator) {
                    $topic_title = Arr::getRequest('topic');
                    if (!empty($topic_title)) {
                        $array = array(
                            'title'    => $topic_title,
                            'posticon' => Arr::getRequest('posticon'),
                        );
                        $db_result = $this->_db->update_query('f_topic', $array, "id = '" . $topic->id . "'");
                    }
                }
            } else {
                $opened = ($this->ForumForums[$forum_id]['moderated_posts'] == 1) ? 2 : 1;
                if ($this->is_mod($forum_id)) {
                    $opened = 1;
                }
                if ($permissions['FORUM_CLOSE_TOPIC'] == 1) {
                    if ($_REQUEST['subaction'] == 'close') {
                        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET status='1' WHERE id = '" . $topic_id . "'");
                    }
                }

                if ($permissions['FORUM_CHANGE_TOPICTYPE'] == 1) {
                    if ($_REQUEST['subaction'] == 'announce') {
                        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET type='100' WHERE id = '" . $topic_id . "'");
                    }
                    if ($_REQUEST['subaction'] == 'attention') {
                        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET type='1' WHERE id = '" . $topic_id . "'");
                    }
                }

                $last_post = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_post WHERE topic_id = $topic_id ORDER BY id DESC LIMIT 1");
                $last_post_id = $last_post->id;

                // Если автор текущего и предыдущего сообщения в одном топике один - склеиваем сообщения
                if ($last_post->uid === $this->_user && strtotime($last_post->datum) > (time() - $this->double_post) && !$is_moderator) {
                    $no_count_post = true;
                    $new_mess = PE . PE . $this->_lang['AddedOn'] . ' [b]' . $this->_uname . '[/b]: ' . date('d.m.Y, H:i:s') . PE;
                    $new_title = !empty($title) ? ' [b]' . Tool::cleanTags($title, array('codewidget')) . '[/b]' . PE : '';
                    $double_message = $last_post->message . $new_mess . $new_title . $message;
                    $last_post->attachment = (!empty($last_post->attachment)) ? $last_post->attachment . ';' : '';

                    $array = array(
                        'message'     => Tool::cleanTags($this->del_mod($is_moderator, $double_message), array('codewidget')),
                        'use_bbcode'  => $disable_bbcode,
                        'use_smilies' => $disable_smilies,
                        'use_sig'     => $use_sig,
                        'opened'      => $opened,
                        'datum'       => date('Y-m-d H:i:s'),
                    );
                    if (!empty($attachments)) {
                        $array['attachment'] = $attachments;
                    }
                    $db_result = $this->_db->update_query('f_post', $array, "id = '" . $last_post_id . "'");
                } else {
                    // Или добавляем новое сообщение
                    $insert_array = array(
                        'title'       => Tool::cleanTags($title, array('codewidget')),
                        'message'     => Tool::cleanTags($this->del_mod($is_moderator, $message), array('codewidget')),
                        'datum'       => date('Y-m-d H:i:s'),
                        'topic_id'    => $topic_id,
                        'uid'         => $this->_user,
                        'use_bbcode'  => $disable_bbcode,
                        'use_smilies' => $disable_smilies,
                        'use_sig'     => $use_sig,
                        'attachment'  => $attachments,
                        'opened'      => $opened,
                        'thanks'      => '');
                    $db_result = $this->_db->insert_query('f_post', $insert_array);
                    $new_id = $this->_db->insert_id();
                }
            }

            if ($notification) {
                if (!in_array($this->_user, $user_ids)) {
                    $this->_db->query("UPDATE " . PREFIX . "_f_topic SET notification = CONCAT(notification, ';', '" . $this->_user . "') WHERE id = '$topic_id'");
                }
            } else {
                $row = $this->_db->cache_fetch_object("SELECT notification FROM " . PREFIX . "_f_topic WHERE id = '$topic_id' LIMIT 1");
                $new = $this->del_uid($row->notification, $this->_user);
                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET notification='$new' WHERE id = '$topic_id'");
            }

            if (!$db_result) {
                $this->__object('Core')->message('Forums_PostErrorNew', 'Forums_PostErrorNewmsg', $_REQUEST['redir']);
            }

            if (!empty($new_id)) {
                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET last_post_id = '" . $new_id . "' WHERE id = '$topic_id'");
            } else {
                $new_id = $last_post_id;
            }

            $count = $this->_db->cache_num_rows("SELECT topic_id FROM " . PREFIX . "_f_post WHERE topic_id = '$topic_id'");
            if ($_REQUEST['action'] != 'edit') {
                $datum = date('d.m.Y H:i');
                if (!empty($this->ForumForums[$forum_id]['post_emails'])) {
                    $mails = explode(',', $this->ForumForums[$forum_id]['post_emails']);
                    foreach ($mails as $send_mail) {
                        if (!empty($send_mail)) {
                            $row_s = $this->_db->cache_fetch_object("SELECT Forum_Beitraege_Limit FROM " . PREFIX . "_benutzer WHERE Email = '$send_mail' LIMIT 1");
                            $page = $this->numPage($count, $row_s->Forum_Beitraege_Limit);
                            $link = BASE_URL . '/index.php?p=showtopic&toid=' . $topic_id . '&pp=' . $row_s->Forum_Beitraege_Limit . '&page=' . $page . '#pid_' . $new_id;
                            $body = ($opened == 2) ? $this->_lang['Forums_msg_newpost_body_adminnotification_toactivate'] : $this->_lang['Forums_msg_newpost_body_adminnotification'];
                            $subject_msg = ($opened == 2) ? $this->_lang['NewMessage'] . ' ' . $this->_lang['Forums_ThreadMustUnlock_post'] : $this->_lang['NewMessage'];
                            $adm_mes = str_ireplace('[quote]', "\r\n----------" . $this->_lang['MailQuoteStart'] . "-----------\r\n", $message);
                            $adm_mes = str_ireplace('[/quote]', "\r\n----------" . $this->_lang['MailQuoteEnd'] . "-----------\r\n", $adm_mes);
                            $adm_mes = $this->__object('Post')->clean($adm_mes);
                            $mail_array = array(
                                '__DATUM__'   => $datum,
                                '__USER__'    => $this->_uname,
                                '__SUBJECT__' => $title,
                                '__LINK__'    => $link,
                                '__MESSAGE__' => $adm_mes);
                            $body = $this->_text->replace($body, $mail_array);
                            $body = str_replace("\n", "\r\n", $body);
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $send_mail,
                                'to_name'   => '',
                                'text'      => $body,
                                'subject'   => $subject_msg,
                                'fromemail' => $this->_settings['Mail_Absender'],
                                'from'      => $this->_settings['Mail_Name'],
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 3));
                        }
                    }
                }

                $users = explode(';', $this->del_uid($r_notification->notification, $this->_user));
                foreach ($users as $mail_to) {
                    if (!empty($mail_to)) {
                        $row_u = $this->_db->cache_fetch_object("SELECT Benutzername AS uname, Email AS email, Forum_Beitraege_Limit FROM " . PREFIX . "_benutzer WHERE Id = '$mail_to' LIMIT 1");
                        $page = $this->numPage($count, $row_u->Forum_Beitraege_Limit);
                        $link = BASE_URL . '/index.php?p=showtopic&toid=' . $topic_id . '&pp=' . $row_u->Forum_Beitraege_Limit . '&page=' . $page . '#pid_' . $new_id;
                        $user_mes = preg_replace("!\[hide=(.*?)\](.*?)\[\/hide\]!siu", $this->_lang['reg'], $message);
                        $user_mes = str_ireplace('[quote]', "\r\n----------" . $this->_lang['MailQuoteStart'] . "-----------\r\n", $user_mes);
                        $user_mes = str_ireplace('[/quote]', "\r\n----------" . $this->_lang['MailQuoteEnd'] . "-----------\r\n", $user_mes);
                        $user_mes = $this->__object('Post')->clean($user_mes);
                        $mail_array = array(
                            '__DATUM__'   => $datum,
                            '__USER__'    => $row_u->uname,
                            '__AUTOR__'   => $this->_uname,
                            '__SUBJECT__' => $title,
                            '__LINK__'    => $link,
                            '__MESSAGE__' => $user_mes);
                        $n_body = $this->_text->replace($this->_lang['Forums_msg_newpost_body'], $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $row_u->email,
                            'to_name'   => $row_u->uname,
                            'text'      => $n_body,
                            'subject'   => $this->_lang['NewMessage'],
                            'fromemail' => $this->_settings['Mail_Absender'],
                            'from'      => $this->_settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 3));
                    }
                }
                if ($no_count_post !== true) {
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = Beitraege + 1 WHERE Id = '" . $this->_user . "'");
                    $this->_db->query("UPDATE " . PREFIX . "_f_topic SET replies = replies + 1, last_post = '" . date('Y-m-d H:i:s') . "', last_post_int ='" . time() . "' WHERE id = '" . $topic_id . "'");
                }
                $this->_db->query("UPDATE " . PREFIX . "_f_forum SET last_post = '" . date('Y-m-d H:i:s') . "', last_post_id = " . $new_id . " WHERE id = '" . $forum_id . "'");
            }

            $this->readTopic($topic_id);
            $page = $this->numPage($count, $this->LimitB);
            $page = ($page < 1) ? 1 : $page;
            $res_title = translit($r_notification->title);

            // Добавляем задание на пинг
            $title_ping = (!empty($title)) ? $title : $this->_text->chars(Tool::cleanAllow($this->__object('Post')->clean($message), ' '), 60, '');
            $url_ping = BASE_URL . '/index.php?p=showtopic&print_post=' . $new_id . '&toid=' . $topic_id . '&t=' . translit($title_ping);
            $options = array(
                'name' => $title_ping,
                'url'  => $url_ping,
                'lang' => $_SESSION['lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $title_ping,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            if (!empty($_REQUEST['p_id'])) {
                $lp = ($_REQUEST['action'] == 'edit') ? intval($_REQUEST['p_id']) : $last_post_id;
                $page_num = Tool::countPost($lp, $topic_id, $this->LimitB);
                $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id . '&page=' . $page_num . '&t=' . $res_title . '#pid_' . $lp);
            } else {
                if ($opened == 2) {
                    $this->__object('Core')->message('NewMessage', 'Forumns_InfoModerated', BASE_URL . '/index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id . '&page=' . $page . '&t=' . $res_title . '#pid_' . $new_id);
                } else {
                    $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $topic_id . '&fid=' . $forum_id . '&page=' . $page . '&t=' . $res_title . '#pid_' . $new_id);
                }
            }
        }
    }

    public function showForums() {
        $show_only_own_topics = $q_tcount_extra = '';
        $category_array = $forums_array = array();

        if (empty($_REQUEST['cid'])) {
            $navigation = $this->navigation(0, 'category', null);
            $cat_query = "SELECT id, title, position, comment, parent_id, group_id FROM " . PREFIX . "_f_category WHERE parent_id = 0 ORDER BY position";
        } else {
            $cid = intval($_REQUEST['cid']);
            $navigation = $this->navigation($cid, 'category', null);
            $cat_query = "SELECT id, title, position, comment, parent_id, group_id FROM " . PREFIX . "_f_category WHERE id = '" . $cid . "'";
        }
        $categories = $this->_db->query($cat_query);
        while ($category = $categories->fetch_assoc()) {
            $groups = explode(',', $category['group_id']);
            if (in_array($this->_group, $groups)) {
                if (!empty($_REQUEST['cid'])) {
                    $navigation .= $this->_lang['PageSep'] . $category['title'];
                }
                $category['link'] = 'index.php?p=showforums&amp;cid=' . $category['id'];
                $category_array[] = $category;
                $foren = $this->_db->query("SELECT SQL_CACHE
                        f.id,
                        f.title,
                        f.comment,
                        f.status,
                        f.position
                FROM
                        " . PREFIX . "_f_forum AS f
                WHERE
                        f.category_id = '" . $category['id'] . "'
                AND
                        f.active = '1'
                ORDER BY f.position");
                $forums_array[$category['title']] = array();
                while ($forum = $foren->fetch_assoc()) {
                    $permissions = Tool::accessForum($forum['id']);
                    if ($permissions['FORUM_SEE'] == 1) {
                        $show_only_own_topics = '';
                        if (!$permissions['FORUM_SEE_TOPIC'] || $permissions['FORUM_SEE_TOPIC'] == 0) {
                            $show_only_own_topics = " AND uid = " . $this->_user;
                        }

                        if (!$this->is_mod($forum['id'])) {
                            $q_tcount_extra .= " AND opened = 1 ";
                        }
                        $r_tcount = $this->_db->query("SELECT id, replies FROM " . PREFIX . "_f_topic WHERE forum_id = '" . $forum['id'] . "' $q_tcount_extra $show_only_own_topics");

                        $ids = array();
                        $forum['tcount'] = 0;
                        while ($tid = $r_tcount->fetch_object()) {
                            $ids[] = $tid->id;
                            $forum['tcount'] ++;
                        }
                        $r_tcount->close();

                        $last_post = '';
                        $forum['pcount'] = 0;
                        if (!empty($ids)) {
                            $query = "SELECT COUNT(*) AS PostCount FROM " . PREFIX . "_f_post WHERE topic_id IN(" . implode(',', $ids) . ") ; ";
                            $query .= "SELECT DISTINCT
                                    b.id,
                                    b.uid,
                                    b.topic_id,
                                    b.datum,
                                    c.title,
                                    c.replies,
                                    d.Regdatum AS user_regdate,
                                    d.Benutzername
                            FROM
                                    " . PREFIX . "_f_forum AS a
                            INNER JOIN
                                    " . PREFIX . "_f_post AS b
                            ON
                                    a.last_post_id = b.id
                            AND
                                    a.id = " . $forum['id'] . "
                            INNER JOIN
                                    " . PREFIX . "_f_topic AS c
                            ON
                                    b.topic_id = c.id
                            INNER JOIN
                                    " . PREFIX . "_benutzer AS d
                            ON
                                    d.Id = b.uid LIMIT 1";
                            if ($this->_db->multi_query($query)) {
                                if (($result = $this->_db->store_result())) {
                                    $pcount = $result->fetch_object();
                                    $result->close();
                                }
                                if (($result = $this->_db->store_next_result())) {
                                    $last_post = $result->fetch_object();
                                    $result->close();
                                }
                            }
                            $forum['pcount'] = $pcount->PostCount;

                            if (is_object($last_post)) {
                                $last_post->page = $this->numPage($last_post->replies, $this->LimitB);
                                $forum['last_post'] = $last_post;
                            }
                        }

                        $forum['link'] = 'index.php?p=showforum&amp;fid=' . $forum['id'];
                        if ($_SESSION['loggedin'] == 0) {
                            $forum['statusicon'] = $this->getIcon('forum_old_lock.png', $this->_lang['Forums_IsClosed']);
                        } else {
                            $this->iconForum($forum);
                        }

                        $subforums_array = array();
                        $this->getCategs();
                        foreach ($this->ForumCategs as $subcategory) {
                            if ($subcategory['parent_id'] == $forum['id']) {
                                $subforums_result = $this->_db->query("SELECT group_id, id, title FROM " . PREFIX . "_f_forum WHERE category_id = '" . $subcategory['id'] . "' AND active = 1");
                                while ($subforum = $subforums_result->fetch_assoc()) {
                                    $subforum['link'] = 'index.php?p=showforum&amp;fid=' . $subforum['id'];
                                    $subforums_array[] = $subforum;
                                }
                                $subforums_result->close();
                            }
                        }
                        $forum['subforums'] = $subforums_array;
                        $forums_array[$category['title']][] = $forum;
                    }
                }
                $foren->close();
            }
        }
        $categories->close();

        $tpl_array = array(
            'uid'        => $this->_user,
            'navigation' => $navigation,
            'f_id'       => 0,
            'categories' => $category_array,
            'forums'     => $forums_array);
        $this->_view->assign($tpl_array);

        $navigation = strip_tags($navigation);
        $seo_array = array(
            'headernav' => $navigation,
            'pagetitle' => Tool::repeat($navigation),
            'content'   => $this->_view->fetch(THEME . '/forums/showforums.tpl'));
        $this->_view->finish($seo_array);
    }

    public function showForum() {
        $only_own_topics = $subforum_array = '';
        $fid = intval($_REQUEST['fid']);
        if (empty($fid)) {
            $this->__object('Core')->message('Global_NoPermission', 'NoPerm', BASE_URL . '/index.php?p=showforums');
        }
        $this->getCategs();
        $forum_obj = $this->ForumForums[$fid];
        if (!is_array($forum_obj) || empty($forum_obj['id'])) {
            $this->__object('Core')->message('Forums_Title', 'NoPerm', BASE_URL . '/index.php?p=showforums');
        }
        $navigation = $this->navigation($fid, 'forum', null);
        $tmp_navi = $navigation . $this->_lang['PageSep'] . $forum_obj['title'];
        $this->_view->assign('navigation', $tmp_navi);
        $this->_view->assign('treeview', explode($this->_lang['PageSep'], $tmp_navi));
        $pass = false;

        if (!empty($forum_obj['password'])) {
            if (Arr::getCookie('f_pass_id_' . $fid) == $forum_obj['password']) {
                $pass = true;
            } else {
                $this->_view->assign('fid', $fid);

                $seo_array = array(
                    'headernav' => $this->_lang['Forums_ForumTitle_login'],
                    'pagetitle' => $this->_lang['Forums_ForumTitle_login'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                    'content'   => $this->_view->fetch(THEME . '/forums/forumlogin.tpl'));
                $this->_view->finish($seo_array);
            }
        } else {
            $pass = true;
        }

        if ($pass) {
            if ($this->ForumForums[$fid]['intersect'] == 1) {
                $forum_obj['permissions'] = Tool::accessForum($forum_obj['id']);
            } else {
                $this->__object('Core')->message('Forums_PermissionDenied', 'NoPerm', BASE_URL . '/index.php?p=showforums');
            }

            $period = !empty($_REQUEST['period']) ? Tool::cleanAllow($_REQUEST['period']) : '';
            $order = isset($_REQUEST['sort']) && strtolower($_REQUEST['sort']) == 'asc' ? 'ASC' : 'DESC';
            $order_orig = $order == 'DESC' ? 'ASC' : 'DESC';

            $divisor = 60 * 60 * 24;
            $where_time_stat = !preg_match('/[\d]/u', $period) ? '' : " AND ((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') / $divisor) - (UNIX_TIMESTAMP(p.datum) / $divisor)) <= $period";
            $allowed_sort = array('last_post', 'title', 'replies', 'uname', 'views', 'rating');
            $order_by = !empty($_REQUEST['sortby']) && in_array($_REQUEST['sortby'], $allowed_sort) ? Tool::cleanAllow($_REQUEST['sortby']) : 'last_post';
            $this->_db->query("DELETE FROM " . PREFIX . "_f_topic_viewing WHERE Expire <= '" . time() . "'");
            $limit = ($this->LimitT >= 1) ? $this->LimitT : $_REQUEST['pp'];
            $a = Tool::getLimit($limit);
            $topic_query_extra = (!$this->is_mod($fid)) ? " AND t.opened = 1 " : '';
            $topic_query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT
				t.id,
				t.title,
				t.status,
				t.datum,
				t.type,
				t.views,
				t.posticon,
				t.uid,
				u.Benutzername AS uname,
				u.Regdatum AS user_regdate,
				t.last_post,
				r.rating,
				t.opened
			FROM
				" . PREFIX . "_f_topic AS t,
				" . PREFIX . "_benutzer AS u,
				" . PREFIX . "_f_rating AS r,
				" . PREFIX . "_f_post AS p
			WHERE
				(t.forum_id = '" . $fid . "' AND u.Id = t.uid AND r.topic_id = t.id)
			AND
				p.topic_id = t.id
				$topic_query_extra $where_time_stat $only_own_topics
			ORDER BY type DESC,
				$order_by $order LIMIT $a, $limit";

            $topic_result = $this->_db->query($topic_query);
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $topic_array = array();
            while ($topic = $topic_result->fetch_assoc()) {
                $topic['link'] = 'index.php?p=showtopic&amp;toid=' . $topic['id'] . '&amp;fid= ' . $fid;
                $topic['closelink'] = 'index.php?p=forums&amp;action=closetopic&amp;fid=' . $fid . '&amp;toid=' . $topic['id'];
                $topic['openlink'] = 'index.php?p=forums&amp;action=opentopic&amp;fid=' . $fid . '&amp;toid=' . $topic['id'];
                $topic['dellink'] = 'index.php?p=forums&amp;action=deltopic&amp;fid=' . $fid . '&amp;toid=' . $topic['id'];
                $topic['movelink'] = 'index.php?p=forums&amp;action=move&amp;item=t&amp;id=' . $topic['id'];
                $topic['typelink'] = 'index.php?p=forum&amp;action=change_type&amp;id=' . $topic['id'] . '&amp;fid=' . $fid;
                $rating = explode(',', $topic['rating']);
                $topic['rating'] = (int) (array_sum($rating) / count($rating));
                $topic['autorlink'] = 'index.php?p=user&amp;id=' . $topic['uid'];
                $topic['autor'] = $topic['uname'];

                if ($topic['status'] == $this->status_moved) {
                    $topic['statusicon'] = $this->getIcon('thread_moved.png', $this->_lang['Forums_ThreadMoved']);
                } else {
                    if ($_SESSION['loggedin'] != 1 || ($forum_obj['status'] == $this->status_closed)) {
                        $topic['statusicon'] = $this->getIcon('thread_lock.png', $this->_lang['Forums_TopicClosed']);
                    } else {
                        $this->iconTopic($topic, $forum_obj['status']);
                    }
                }

                $query = "SELECT Ip FROM " . PREFIX . "_f_topic_viewing WHERE Topic='" . $topic['id'] . "' ; ";
                $query .= "SELECT COUNT(id) AS count FROM " . PREFIX . "_f_post WHERE topic_id = '" . $topic['id'] . "' ; ";
                $query .= "SELECT p.id, datum, p.topic_id, p.uid, u.Benutzername AS uname, u.Regdatum AS user_regdate FROM " . PREFIX . "_f_post AS p, " . PREFIX . "_benutzer AS u WHERE p.topic_id = " . $topic['id'] . " AND u.Id = p.uid GROUP BY p.id ORDER BY datum DESC LIMIT 1";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $topic['viewing'] = $result->num_rows();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $post = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $last_post_row = $result->fetch_object();
                        $result->close();
                    }
                }

                $last_post_row->link = ($last_post_row->user_regdate < 2) ? $this->_lang['Guest'] : '<a title="' . $this->_lang['Forums_ShowUserProfile'] . '" class="stip forum_links_small" href="index.php?p=user&amp;id=' . $last_post_row->uid . '">' . $last_post_row->uname . '</a>';
                $topic['lastposter'] = $last_post_row;
                $limit = ($this->LimitB >= 5) ? $this->LimitB : 15;
                $numPages = $this->numPage($post->count, $limit);
                $topic['navigation_page'] = ($numPages == 1) ? 0 : $numPages;
                $topic['next_page'] = $numPages;
                $topic['replies'] = $post->count;
                $topic_array[] = $topic;
            }
            $topic_result->close();

            $subcat_query = "SELECT id, title, position, comment, parent_id, group_id FROM " . PREFIX . "_f_category WHERE parent_id = " . $fid . " ORDER BY position";
            $subcat_result = $this->_db->query($subcat_query);
            $subcat_array = array();
            while ($subcategory = $subcat_result->fetch_assoc()) {
                $groups = explode(',', $subcategory['group_id']);
                if (in_array($this->_group, $groups)) {
                    $subcategory['link'] = 'index.php?p=showforums&amp;cid=' . $subcategory['id'];
                    $subcat_array[] = $subcategory;
                    $subforum_query = "SELECT group_id, id, title, comment, status FROM " . PREFIX . "_f_forum WHERE category_id = " . $subcategory['id'] . " AND active = 1 ORDER BY position";
                    $subforum_result = $this->_db->query($subforum_query);
                    $subforum_array[$subcategory['title']] = array();
                    while ($subforum = $subforum_result->fetch_assoc()) {
                        $pcount = 0;
                        $r_tcount = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS id FROM " . PREFIX . "_f_topic WHERE forum_id = '" . intval($subforum['id']) . "'");
                        $subforum['tcount'] = $this->_db->found_rows();
                        $ids = '';
                        while ($tid = $r_tcount->fetch_object()) {
                            $ids .= empty($ids) ? $tid->id : " OR topic_id = " . $tid->id;
                        }

                        if (!empty($ids)) {
                            $last_post = $this->_db->cache_fetch_object("SELECT p.datum, p.topic_id, p.uid, p.id FROM " . PREFIX . "_f_post AS p, " . PREFIX . "_f_topic AS t WHERE p.topic_id = $ids AND t.id = p.topic_id ORDER BY p.datum DESC LIMIT 1");

                            $query = "SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '$ids' ; ";
                            $query .= "SELECT COUNT(id) AS replies FROM " . PREFIX . "_f_post WHERE topic_id = '" . $last_post->topic_id . "' ; ";
                            $query .= "SELECT Benutzername, Regdatum FROM " . PREFIX . "_benutzer WHERE Id = '" . $last_post->uid . "' ; ";
                            $query .= "SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $last_post->topic_id . "'";
                            if ($this->_db->multi_query($query)) {
                                if (($result = $this->_db->store_result())) {
                                    $pcount = $result->num_rows();
                                    $result->close();
                                }
                                if (($result = $this->_db->store_next_result())) {
                                    $replies = $result->fetch_object();
                                    $result->close();
                                }
                                if (($result = $this->_db->store_next_result())) {
                                    $last_user = $result->fetch_object();
                                    $result->close();
                                }
                                if (($result = $this->_db->store_next_result())) {
                                    $topic_title = $result->fetch_object();
                                    $result->close();
                                }
                            }

                            $last_post->replies = $replies->replies;
                            $last_post->regdate = $last_user->Regdatum;
                            $last_post->uname = $last_user->Benutzername;
                            $last_post->title = $topic_title->title;
                            $last_post->page = $this->numPage($last_post->replies, $this->LimitT);
                            $subforum['lastpost'] = $last_post;
                        }

                        if ($_SESSION['loggedin'] != 1) {
                            $subforum['statusicon'] = $this->getIcon('forum_old_lock.png', $this->_lang['Forums_IsClosed']);
                        } else {
                            $this->iconForum($subforum);
                        }

                        $subforum['pcount'] = $pcount;
                        $subfors = array();
                        $r_subcat = $this->_db->cache_fetch_assoc_all("SELECT id FROM " . PREFIX . "_f_category WHERE parent_id = '" . intval($subforum['id']) . "'");
                        foreach ($r_subcat as $subcat) {
                            $r_subfor = $this->_db->query("SELECT id, title FROM " . PREFIX . "_f_forum WHERE category_id = '" . $subcat['id'] . "'");
                            while ($subfor = $r_subfor->fetch_object()) {
                                $subfors[] = $subfor;
                            }
                        }

                        $subforum['link'] = 'index.php?p=showforum&amp;fid=' . $subforum['id'];
                        $subforum['subforums'] = $subfors;
                        $subforum_array[$subcategory['title']][] = $subforum;
                    }
                    $subforum_result->close();
                }
            }
            $subcat_result->close();
            if ($limit < $num) {
                $period = (empty($period)) ? 0 : $period;
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=showforum&amp;fid=$fid&amp;period=$period&amp;sortby=$order_by&amp;sort=$order_orig&amp;pp=$limit&amp;page={s}\">{t}</a> "));
            }

            $tpl_array = array(
                'categories_dropdown'   => $this->categories(),
                'sort_by_theme_link'    => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=title&amp;sort=' . $order_orig,
                'sort_by_reply_link'    => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=replies&amp;sort=' . $order_orig,
                'sort_by_author_link'   => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=uname&amp;sort=' . $order_orig,
                'sort_by_hits_link'     => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=views&amp;sort=' . $order_orig,
                'sort_by_rating_link'   => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=rating&amp;sort=' . $order_orig,
                'sort_by_lastpost_link' => 'index.php?p=showforum&amp;fid=' . $fid . '&amp;sortby=last_post&amp;sort=' . $order_orig,
                'forums_name'           => $forum_obj['title'],
                'forum'                 => $forum_obj,
                'f_id'                  => $forum_obj['id'],
                'categories'            => $subcat_array,
                'forums'                => $subforum_array,
                'topics'                => $topic_array,
                'get_mods'              => $this->get_mods($fid),
                'type_sticky'           => $this->type_sticky,
                'type_announce'         => $this->type_announce);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $forum_obj['title'],
                'pagetitle' => Tool::repeat($navigation . $this->_lang['PageSep'] . $forum_obj['title'] . Tool::numPage()),
                'content'   => $this->_view->fetch(THEME . '/forums/showforum.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    public function showTopic() {
        if (!empty($_REQUEST['toid'])) {
            $toid = intval($_REQUEST['toid']);
            if (!$this->topicExists($toid)) {
                $this->__object('Core')->message('Forums_Title', 'NoPerm', BASE_URL . '/index.php?p=showforums');
            }
            $stime = time();
            $expire = $stime + 300;
            $this->_db->query("DELETE FROM " . PREFIX . "_f_topic_viewing WHERE Expire <= '" . $stime . "'");
            $num = $this->_db->cache_num_rows("SELECT Ip FROM " . PREFIX . "_f_topic_viewing WHERE Ip='" . IP_USER . "' AND Topic = '" . $toid . "' LIMIT 1");

            if ($num < 1) {
                $insert_array = array(
                    'Ip'     => IP_USER,
                    'Topic'  => $toid,
                    'Expire' => $expire);
                $this->_db->insert_query('f_topic_viewing', $insert_array);
            } else {
                $this->_db->query("UPDATE " . PREFIX . "_f_topic_viewing SET Expire = '$expire' WHERE Topic = '" . $toid . "' AND Ip = '" . IP_USER . "' ");
            }

            $navigation = $this->navigation($toid, 'topic');
            $pass = $this->_db->cache_fetch_object("SELECT f.id, f.password FROM " . PREFIX . "_f_forum AS f, " . PREFIX . "_f_topic AS t WHERE t.id = '" . $toid . "' AND t.forum_id = f.id LIMIT 1");
            $pass_b = false;

            if (is_object($pass) && !empty($pass->password)) {
                if (Arr::getCookie('f_pass_id_' . $pass->id) == $pass->password) {
                    $pass_b = true;
                } else {
                    $this->_view->assign('fid', $pass->id);

                    $seo_array = array(
                        'headernav' => $this->_lang['Forums_ForumTitle_login'],
                        'pagetitle' => $this->_lang['Forums_ForumTitle_login'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                        'content'   => $this->_view->fetch(THEME . '/forums/forumlogin.tpl'));
                    $this->_view->finish($seo_array);
                }
            } else {
                $pass_b = true;
            }

            if ($pass_b) {
                $this->getCategs();
                if ($this->ForumForums[$pass->id]['cat_intersect'] != 1) {
                    $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
                }

                $permissions = Tool::accessForum($pass->id);
                if ($permissions['FORUM_SEE'] != 1 || !$permissions['FORUM_SEE_TOPIC'] || $permissions['FORUM_SEE_TOPIC'] == 0) {
                    $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
                }

                if (Arr::getRequest('open') == 1) {
                    $this->mailModer($toid);
                }
                if (!empty($_REQUEST['print_post'])) {
                    $print_post = intval($_REQUEST['print_post']);
                    $only_postid = " AND id = '" . $print_post . "'";
                } else {
                    $print_post = $only_postid = '';
                }

                $meta = '';
                $this->readTopic($toid);
                $post_query_extra = (isset($_REQUEST['fid']) && !$this->is_mod($_REQUEST['fid'])) ? " AND opened = 1 " : '';
                $limit = ($this->LimitB >= 1) ? $this->LimitB : $_REQUEST['pp'];
                $a = Tool::getLimit($limit);
                $post_result = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS id, title, message, datum, use_bbcode, use_smilies, use_sig, uid, attachment, opened, thanks FROM " . PREFIX . "_f_post WHERE topic_id = '" . $toid . "' $only_postid $post_query_extra ORDER BY datum ASC LIMIT $a, $limit");
                $num = $this->_db->found_rows();
                $seiten = $this->numPage($num, $limit);
                if ($num < 1) {
                    $this->__object('Core')->message('Forums_Title', 'NoPerm', BASE_URL . '/index.php?p=showforums');
                }
                $post_array = array();
                while ($post = $post_result->fetch_object()) {
                    $post->anchorId = $post->id;
                    $q_user = "SELECT
                            u.Gruppe AS ugroup,
                            u.Avatar_Default AS usedefault_avatar,
                            u.Id AS uid,
                            u.Vorname AS name,
                            u.Benutzername AS uname,
                            u.Email AS email,
                            u.Ort AS gorod,
                            u.Ort_Public AS gorod_all,
                            u.Webseite AS url,
                            u.Avatar AS user_avatar,
                            u.Regdatum AS user_regdate,
                            u.Signatur AS user_sig,
                            u.Unsichtbar AS invisible,
                            u.Team,
                            u.Status,
                            u.Email,
                            u.Gravatar,
                            u.Beitraege AS user_posts,
                            u.Emailempfang,
                            ug.Name AS groupname_single,
                            ug.Signatur_Erlaubt,
                            ug.Signatur_Laenge,
                            ug.SysCode_Signatur
                    FROM
                            " . PREFIX . "_benutzer AS u,
                            " . PREFIX . "_benutzer_gruppen AS ug
                    WHERE
                            u.Id = '" . $post->uid . "'
                    AND
                            ug.Id = u.Gruppe LIMIT 1";
                    $poster = $this->_db->cache_fetch_object($q_user);

                    if (is_object($poster)) {
                        $rank = $this->_db->cache_fetch_object("SELECT title, count FROM " . PREFIX . "_f_rank WHERE count <= '" . $poster->user_posts . "' ORDER BY count DESC LIMIT 1");
                        $poster->avatar = $this->__object('Avatar')->load($poster->Gravatar, $poster->Email, $poster->ugroup, $poster->user_avatar, $poster->usedefault_avatar);

                        if ($poster->Signatur_Erlaubt == 1) {
                            $poster->user_sig_length = $poster->Signatur_Laenge;
                            $poster->user_sig = Tool::censored($poster->user_sig);
                            $poster->user_sig = ($poster->SysCode_Signatur == 1) ? $this->__object('Post')->codes($this->_text->substr($poster->user_sig, 0, $poster->user_sig_length)) : nl2br($this->_text->substr($poster->user_sig, 0, $poster->user_sig_length));
                        } else {
                            $poster->user_sig = '';
                        }
                        $poster->rank = isset($rank->title) ? $rank->title : '';
                        $poster->UserPop = $this->dropDown($poster->uid, $poster->Emailempfang, base64_encode($poster->uname));
                        if ($this->_settings['SysCode_Smilies'] == 1) {
                            $poster->user_sig = $this->__object('Post')->smilies($poster->user_sig);
                        }
                        $post->poster = $poster;
                    } else {
                        $poster = new stdClass;
                        $poster->DeletedUser = 1;
                        $post->poster = $poster;
                    }

                    $meta .= $post->message . ' ';
                    if ($post->use_bbcode == 1) {
                        $post->message = !empty($post->message) ? $this->__object('Post')->codes($post->message) : '';
                    } else {
                        $post->message = nl2br($post->message);
                    }

                    if ($post->use_smilies == 1 && $this->_settings['SysCode_Smilies'] == 1) {
                        $post->message = $this->__object('Post')->smilies($post->message);
                    }
                    $post->message = $this->__object('Glossar')->get($post->message);
                    $post->message = Tool::censored($post->message);
                    $post->message = Tool::highlight($post->message);
                    $post->postlink = !empty($post->title) ? $post->title : $this->_text->chars(strip_tags($post->message), 60, '');
                    $post->array_datum = $this->data($post->datum);

                    list($post->files, $post->images) = $this->attachments($post->attachment, $pass->id, $toid); // Разбираем вложения по типу

                    $post->user_thanks = array();
                    if (!empty($post->thanks)) {
                        $thanks = explode(';', $post->thanks);
                        $thanks = array_map('intval', $thanks);
                        $post->user_del_thanks = array();
                        if (in_array($this->_user, $thanks)) {
                            $post->user_del_thanks['del'] = 1;
                        }
                        $sub_thanks = implode(',', $thanks);
                        $r_thanks = $this->_db->query("SELECT Id, Benutzername FROM " . PREFIX . "_benutzer WHERE Id IN(" . $sub_thanks . ")");
                        while ($user_thanks = $r_thanks->fetch_object()) {
                            $post->user_thanks[] = $user_thanks;
                        }
                        $r_thanks->close();
                    }
                    $post_array[] = $post;
                }
                $post_result->close();

                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET views = views + 1 WHERE id = '" . $toid . "'");

                $query = "SELECT type, notification, id, title, status, forum_id, uid FROM " . PREFIX . "_f_topic WHERE id = '" . $toid . "' ; ";
                $query .= "SELECT uid, ip FROM " . PREFIX . "_f_rating WHERE topic_id = '" . $toid . "' ; ";
                $query .= "SELECT title, id FROM " . PREFIX . "_f_topic WHERE id < '" . $toid . "' AND forum_id = '" . $pass->id . "' ORDER BY id DESC LIMIT 1 ; ";
                $query .= "SELECT title, id FROM " . PREFIX . "_f_topic WHERE id > '" . $toid . "' AND forum_id = '" . $pass->id . "' ORDER BY id ASC LIMIT 1";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $topic = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $row_ip = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $next_topic = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $prev_topic = $result->fetch_object();
                        $result->close();
                    }
                }

                $user_id = preg_split('/;/', $topic->notification);
                $v_uid = explode(',', $row_ip->uid);
                $ip = explode(',', $row_ip->ip);

                if (!in_array($this->_user, $v_uid) && !in_array(IP_USER, $ip)) {
                    $this->_view->assign('display_rating', 1);
                }
                $topic->next_topic = $next_topic;
                $topic->prev_topic = $prev_topic;
                $navigation = $this->navigation($toid, 'topic');
                $tmp_navi = $navigation . $this->_lang['PageSep'] . '<span class="forum_topictile">' . sanitize($topic->title) . '</span>';

                if ($limit < $num) {
                    $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?" . ((!empty($_REQUEST['high'])) ? 'high=' . sanitize($_REQUEST['high']) . '&amp;' : '') . "p=showtopic&amp;toid=$toid&amp;fid=1&amp;page={s}&amp;t=$_REQUEST[t]\">{t}</a> "));
                }

                if (isset($_REQUEST['fid']) && $this->is_mod($_REQUEST['fid'])) {
                    $this->_view->assign('ismod', 1);
                }
                $link = 'index.php?p=showtopic&amp;toid=' . $toid . '&amp;fid=' . $pass->id . '&amp;page=' . Arr::getRequest('page', 1) . '&amp;t=' . translit(sanitize($topic->title));

                $tpl_array = array(
                    'canabo'              => (in_array($this->_user, $user_id) ? 0 : 1),
                    'navigation'          => $tmp_navi,
                    'treeview'            => explode($this->_lang['PageSep'], $tmp_navi),
                    'categories_dropdown' => $this->categories(),
                    'permissions'         => $permissions,
                    'navigation'          => $navigation,
                    'next_site'           => $seiten,
                    'topic'               => $topic,
                    'postings'            => $post_array,
                    'origlink'            => $this->__object('Redir')->referer(true));
                $this->_view->assign($tpl_array);

                $navigation = strip_tags($navigation);
                $pagetitle = $navigation . (!empty($print_post) ? $this->_lang['PageSep'] . $this->_lang['GlobalMessage'] : '') . $this->_lang['PageSep'] . $topic->title . Tool::numPage();
                $seo_array = array(
                    'canonical' => (empty($print_post) ? $link : NULL),
                    'headernav' => $navigation,
                    'pagetitle' => Tool::repeat($pagetitle),
                    'generate'  => $topic->title . ' ' . $meta,
                    'content'   => $this->_view->fetch(THEME . '/forums/showtopic.tpl'));
                $this->_view->finish($seo_array);
            }
        } else {
            $this->__object('Core')->message('Global_NoPermission', 'NoPerm', BASE_URL . '/index.php?p=showforums');
        }
    }

    /* Метод сортировки вложений */
    public function attachments($value, $id, $toid) {
        $files = $images = array();
        if (!empty($value)) {
            $where = array();
            foreach (explode(';', $value) as $value) {
                if (!empty($value)) {
                    $where[] = 'id = ' . $this->_db->escape($value);
                }
            }
            if (!empty($where)) {
                $permissions = Tool::accessForum($id);
                $sql = $this->_db->query("SELECT id, hits, orig_name, filename FROM " . PREFIX . "_f_attachment WHERE " . implode(' OR ', $where));
                while ($row = $sql->fetch_assoc()) {
                    $ext = Tool::extension($row['orig_name']);
                    if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                        if ($permissions['FORUM_DOWNLOAD_ATTACHMENT'] == 1) {
                            $row['filesize'] = $this->filesize($row['filename']);
                            $row['access'] = 1;
                            $row['popup'] = $this->_lang['GlobalTitle'] . ': ' . $row['orig_name'] . '<br />' . $this->_lang['GlobalSize'] . ': ' . $this->filesize($row['filename']) . '<br />' . $this->_lang['Global_Hits'] . ': ' . $row['hits'];
                        } else {
                            $row['popup'] = $this->_lang['Global_NoPermission'];
                        }
                        $width = SX::get('forum.size');
                        $file = '/temp/cache/' . md5($row['orig_name'] . '_' . $row['filename'] . '_' . $width) . Tool::extension($row['orig_name'], true);
                        if (is_file(SX_DIR . $file)) {
                            $row['link'] = BASE_URL . $file;
                        } else {
                            $row['link'] = BASE_URL . '/lib/image.php?action=forum&amp;width=' . $width . '&amp;image=' . $row['id'];
                        }
                        $images[] = $row;
                    } else {
                        if ($permissions['FORUM_DOWNLOAD_ATTACHMENT'] == 1 && $ext == 'mp3') {
                            $row['musik'] = $this->__object('Media')->audio('0', '/index.php?p=forum&action=getfile&id=' . $row['id'] . '&f_id=' . $id . '&t_id=' . $toid);
                        }
                        $row['filesize'] = $this->filesize($row['filename']);
                        if (empty($row['filesize'])) {
                            $row['filesize'] = $this->_lang['UploadFileError'];
                        }
                        $row['link'] = 'index.php?p=forum&amp;action=getfile&amp;id=' . $row['id'] . '&amp;f_id=' . $id . '&amp;t_id=' . $toid;
                        $files[] = $row;
                    }
                }
                $sql->close();
            }
        }
        return array($files, $images);
    }

    /* Выводим похожие темы */
    public function relatedTopic() {
        $toid = intval($_REQUEST['t_id']);
        $row = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $toid . "' LIMIT 1");
        $i = 0;
        $like = '';
        $array = explode(' ', Tool::cleanAllow($row->title, ' .@'));
        foreach ($array as $word) {
            if (!empty($word) && $this->_text->strlen($word) >= 4) {
                $i++;
                $word = $this->_db->escape($word);
                $like .= ( $i == 1) ? "t.title LIKE '%$word%'" : " OR t.title LIKE '%$word%'";
            }
        }

        $topic_array = array();
        if (!empty($like)) {
            $topic_query = "SELECT DISTINCT
                    t.id,
                    t.title,
                    t.status,
                    t.datum,
                    t.type,
                    t.views,
                    t.posticon,
                    t.uid,
                    t.replies,
                    t.forum_id,
                    u.Benutzername AS uname,
                    u.Regdatum AS user_regdate,
                    t.last_post,
                    r.rating,
                    t.opened,
                    f.id AS fid,
                    f.status AS fstatus
            FROM
                    " . PREFIX . "_f_topic AS t,
                    " . PREFIX . "_f_forum AS f,
                    " . PREFIX . "_benutzer AS u,
                    " . PREFIX . "_f_rating AS r
            WHERE
                    t.forum_id = f.id
            AND
                    t.id != $toid
            AND
                    (u.Id = t.uid AND r.topic_id = t.id)
            AND
                    f.active = '1'
            AND
                    t.opened = '1'
            AND
                    ($like)
            ORDER BY t.replies DESC LIMIT 15";
            $topic_result = $this->_db->query($topic_query);
            while ($topic = $topic_result->fetch_assoc()) {
                $rating = explode(',', $topic['rating']);
                $topic['rating'] = (int) (array_sum($rating) / count($rating));
                if ($topic['status'] == $this->status_moved) {
                    $topic['statusicon'] = $this->getIcon('thread_moved.png', $this->_lang['Forums_ThreadMoved']);
                } else {
                    if ($_SESSION['loggedin'] != 1 || ($topic['fstatus'] == $this->status_closed)) {
                        $topic['statusicon'] = $this->getIcon('thread_lock.png', $this->_lang['Forums_TopicClosed']);
                    } else {
                        $this->iconTopic($topic, $topic['fstatus']);
                    }
                }
                $topic['autorlink'] = 'index.php?p=user&amp;id=' . $topic['uid'];
                $topic['autor'] = $topic['uname'];
                $query = "SELECT Ip FROM " . PREFIX . "_f_topic_viewing WHERE Topic='" . $topic['id'] . "' ; ";
                $query .= "SELECT COUNT(id) AS count FROM " . PREFIX . "_f_post WHERE topic_id = '" . $topic['id'] . "' ; ";
                $query .= "SELECT p.id, datum, p.topic_id, p.uid, u.Benutzername AS uname, u.Regdatum AS user_regdate FROM " . PREFIX . "_f_post AS p, " . PREFIX . "_benutzer AS u WHERE p.topic_id = " . $topic['id'] . " AND u.Id = p.uid GROUP BY p.id ORDER BY datum DESC LIMIT 1";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $topic['viewing'] = $result->num_rows();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $post = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $last_post_row = $result->fetch_object();
                        $result->close();
                    }
                }

                $last_post_row->link = ($last_post_row->user_regdate < 2) ? $this->_lang['Guest'] : '<a title="' . $this->_lang['Forums_ShowUserProfile'] . '" class="stip forum_links_small" href="index.php?p=user&amp;id=' . $last_post_row->uid . '">' . $last_post_row->uname . '</a>';
                $topic['lastposter'] = $last_post_row;
                $limit = ($this->LimitB >= 5) ? $this->LimitB : 15;
                $numPages = $this->numPage($post->count, $limit);
                $topic['navigation_page'] = ($numPages == 1) ? 0 : $numPages;
                $topic['next_page'] = $numPages;
                $topic['replies'] = $post->count;
                $topic_array[] = $topic;
            }
            $topic_result->close();
        }

        $tpl_array = array(
            'topic_title'   => $row->title,
            'topics'        => $topic_array,
            'type_sticky'   => $this->type_sticky,
            'type_announce' => $this->type_announce);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['Forums_Related'] . ' - ' . $row->title,
            'pagetitle' => Tool::repeat($this->_lang['Forums_Title'] . $this->_lang['PageSep'] . $this->_lang['Forums_Related'] . $this->_lang['PageSep'] . $row->title),
            'content'   => $this->_view->fetch(THEME . '/forums/related.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Удаляем топик */
    public function delTopic() {
        $this->getCategs();
        $fid = intval($_REQUEST['fid']);
        $toid = intval($_REQUEST['toid']);
        $row = $this->_db->cache_fetch_object("SELECT uid, forum_id FROM " . PREFIX . "_f_topic WHERE id='" . $toid . "' LIMIT 1");

        $permissions = Tool::accessForum($fid);
        if ($row->uid != $this->_user) {
            if ($permissions['FORUM_DELETE_TOPIC'] == 0) {
                $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        } else {
            if ($permissions['FORUM_DELETE_OWN_TOPIC'] == 0) {
                $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
            }
        }

        if (empty($_REQUEST['toid']) || empty($_REQUEST['fid'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }

        $this->deleteTopic($toid);
        $this->lastPost($row->forum_id);
        $this->__object('Redir')->seoRedirect('index.php?p=showforum&fid=' . $fid);
    }

    /* Закрываем топик */
    public function closeTopic() {
        $this->getCategs();
        $fid = intval($_REQUEST['fid']);
        if ($this->ForumForums[$fid]['intersect'] == 1) {
            $permissions = Tool::accessForum($fid);
        }
        if (($_SESSION['loggedin'] != 1) || ($permissions['FORUM_CLOSE_TOPIC'] == 0)) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        }
        if (empty($_REQUEST['toid']) || empty($_REQUEST['fid'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }
        if ($this->ForumForums[$fid]['status'] == $this->status_closed) {
            $this->__object('Core')->message('Forums_Title', 'Forums_MsgisClosed', BASE_URL . '/index.php?p=showforum&amp;fid=' . $fid);
        }
        $toid = intval($_REQUEST['toid']);
        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET status = " . $this->status_closed . " WHERE id = '" . $toid . "'");
        $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $toid . '&fid=' . $fid);
    }

    /* Удаляем сообщение */
    public function delPost() {
        $pid = intval($_REQUEST['pid']);
        $toid = intval($_REQUEST['toid']);
        if ($_SESSION['loggedin'] != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
        }

        $forum = $this->_db->cache_fetch_object("SELECT f.id, p.uid FROM " . PREFIX . "_f_forum AS f, " . PREFIX . "_f_topic AS t, " . PREFIX . "_f_post AS p WHERE t.id = '" . $toid . "' AND t.forum_id = f.id AND p.id = '" . $pid . "' LIMIT 1");
        $permissions = Tool::accessForum($forum->id);

        if ($permissions['FORUM_DELETE_OTHER_POST'] == 0) {
            if ($forum->uid == $this->_user && $permissions['FORUM_DELETE_OWN_POST'] == 0) {
                $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
            }

            if ($forum->uid != $this->_user && !$this->is_mod($forum->id)) {
                $this->__object('Core')->message('Global_NoPermission', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $toid);
            }
            if (empty($_REQUEST['pid'])) {
                $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
            }
        }

        $topic_check = $this->_db->cache_fetch_object("SELECT uid, topic_id FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "' LIMIT 1");
        $this->_db->query("DELETE FROM " . PREFIX . "_f_post WHERE id = '" . $pid . "'");

        $post_check = $this->_db->cache_fetch_object("SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '" . $topic_check->topic_id . "' LIMIT 1");
        if (!is_object($post_check)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_f_topic WHERE id = '" . $topic_check->topic_id . "'");
        }

        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET replies = replies - 1 WHERE id = '" . $toid . "'");
        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = Beitraege - 1 WHERE Id = '" . $topic_check->uid . "'");

        $this->lastPost($forum->id);
        $topic = $this->_db->cache_fetch_object("SELECT replies FROM " . PREFIX . "_f_topic WHERE id = '" . $toid . "' LIMIT 1");

        if ($topic->replies == 0) {
            $this->deleteTopic($toid);
            $this->__object('Redir')->seoRedirect('index.php?p=showforum&fid=' . $forum->id);
        } else {
            $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $toid);
        }
    }

    /* Открываем топик */
    public function openTopic() {
        $fid = intval($_REQUEST['fid']);
        $permissions = Tool::accessForum($fid);
        if (($_SESSION['loggedin'] != 1) || ($permissions['FORUM_OPEN_TOPIC'] == 0)) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        }
        if (empty($_REQUEST['toid'])) {
            $this->__object('Core')->message('Forums_Title', 'Forums_PermissionDenied', BASE_URL . '/index.php?p=showforums');
        }
        $toid = intval($_REQUEST['toid']);
        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET status = '0' WHERE id = '" . $toid . "'");
        $this->__object('Redir')->seoRedirect('index.php?p=showtopic&toid=' . $toid . '&fid=' . $fid);
    }

    /* Доступ к запароленному форуму */
    protected function passForum() {
        $this->getCategs();
        $fid = intval($_REQUEST['fid']);
        if (md5($_REQUEST['pass']) == $this->ForumForums[$fid]['password']) {
            Arr::setCookie('f_pass_id_' . $fid, md5($_REQUEST['pass']), 86400);
            $this->__object('Redir')->seoRedirect('index.php?p=showforum&fid=' . $fid);
        } else {
            $tpl_array = array(
                'navigation' => $this->navigation($fid, 'forum') . $this->_lang['PageSep'] . $this->ForumForums[$fid]['title'],
                'fid'        => $fid,
                'error'      => $this->_lang['Forums_Locked_WrongPass']);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_ForumTitle_login'],
                'pagetitle' => $this->_lang['Forums_ForumTitle_login'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
                'content'   => $this->_view->fetch(THEME . '/forums/forumlogin.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Отдаем вложенный файл */
    protected function file() {
        $permissions = Tool::accessForum(intval($_REQUEST['f_id']));
        if ($permissions['FORUM_DOWNLOAD_ATTACHMENT'] == 1) {
            $id = intval(Arr::getRequest('id'));
            $file = $this->_db->cache_fetch_object("SELECT filename, orig_name FROM " . PREFIX . "_f_attachment WHERE id = '" . $id . "' LIMIT 1");
            $this->_db->query("UPDATE " . PREFIX . "_f_attachment set hits=hits+1 WHERE id='" . $id . "'");
            File::read(UPLOADS_DIR . '/forum/' . $file->filename, $file->orig_name);
        }
        $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $_REQUEST['t_id']);
    }

    /* Вывод изображения из вложений */
    protected function image() {
        $permissions = Tool::accessForum($_REQUEST['f_id']);
        if ($permissions['FORUM_DOWNLOAD_ATTACHMENT'] == 1) {
            $id = intval(Arr::getRequest('id'));
            $file = $this->_db->cache_fetch_object("SELECT filename, orig_name FROM " . PREFIX . "_f_attachment WHERE id = '" . $id . "' LIMIT 1");
            $this->_db->query("UPDATE " . PREFIX . "_f_attachment set hits=hits+1 WHERE id='" . $id . "'");
            $type = Tool::extension($file->orig_name);

            if (in_array($type, array('jpeg', 'jpe', 'jpg', 'png', 'gif'))) {
                File::read(UPLOADS_DIR . '/forum/' . $file->filename, $file->orig_name, NULL, 'inline');
            }
        }
        $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showtopic&amp;toid=' . $_REQUEST['t_id']);
    }

    /* Уведомляем по почте о размещении если есть премодерация */
    protected function mailModer($toid) {
        if (isset($_REQUEST['fid']) && $this->is_mod($_REQUEST['fid'])) {
            if (Arr::getRequest('ispost') == 1) {
                $id = intval(Arr::getRequest('id'));
                $this->_db->query("UPDATE " . PREFIX . "_f_post SET opened = '1' WHERE id='" . $id . "'");
                $row = $this->_db->cache_fetch_object("SELECT
                        a.uid,
                        b.Id,
                        b.Forum_Beitraege_Limit,
                        b.Benutzername,
                        b.Email
                FROM
                        " . PREFIX . "_f_post AS a,
                        " . PREFIX . "_benutzer AS b
                WHERE
                        a.id = '" . $id . "'
                AND
                        b.Id = a.uid LIMIT 1");

                $mail_array = array(
                    '__USER__' => $row->Benutzername,
                    '__LINK__' => BASE_URL . '/index.php?p=showtopic&toid=' . $toid . '&pp=' . $row->Forum_Beitraege_Limit);
                $body = $this->_text->replace($this->_lang['Body_to_postautor_moderated'], $mail_array);
            } else {
                $this->_db->query("UPDATE " . PREFIX . "_f_topic SET opened = '1' WHERE id='" . $toid . "'");
                $this->_db->query("UPDATE " . PREFIX . "_f_post SET opened = '1' WHERE topic_id='" . $toid . "'");
                $row = $this->_db->cache_fetch_object("SELECT
                a.uid,
                        b.Id,
                        b.Benutzername,
                        b.Email
                FROM
                        " . PREFIX . "_f_topic AS a,
                        " . PREFIX . "_benutzer AS b
                WHERE
                        a.id = '" . $id . "'
                AND
                        b.Id = a.uid LIMIT 1");

                $mail_array = array(
                    '__USER__' => $row->Benutzername,
                    '__LINK__' => BASE_URL . '/index.php?p=showtopic&toid=' . $toid . '&fid=' . intval($_REQUEST['fid']));
                $body = $this->_text->replace($this->_lang['Forums_body_to_autor_moderated'], $mail_array);
            }
            SX::setMail(array(
                'globs'     => '1',
                'to'        => $row->Email,
                'to_name'   => $row->Benutzername,
                'text'      => $body,
                'subject'   => $this->_lang['Forums_subject_to_autor_moderated'],
                'fromemail' => $this->_settings['Mail_Absender'],
                'from'      => $this->_settings['Mail_Name'],
                'type'      => 'text',
                'attach'    => '',
                'html'      => '',
                'prio'      => 1));
        }
    }

    /* Жалоба на сообщение */
    public function complaint() {
        SX::setDefine('OUT_TPL', 'popup.tpl');
        $id = intval($_REQUEST['pid']);
        $res = $this->_db->fetch_object("SELECT title, message FROM " . PREFIX . "_f_post WHERE id = '" . $id . "' LIMIT 1");
        $send_post = $this->__object('Post')->clean(strip_tags($res->title . ' ' . $res->message));

        if (Arr::getRequest('send') == 1) {
            $error = array();
            if (!Tool::isMail($_POST['email'])) {
                $error[] = $this->_lang['Validate_email'];
            }
            if (empty($_REQUEST['body'])) {
                $error[] = $this->_lang['No_Message'];
            }

            if ($this->__object('Captcha')->check($error)) {
                $send = 0;
                $body = sanitize($_REQUEST['body']);
                $email = Tool::cleanMail($_POST['email']);

                $sql = $this->_db->query("SELECT user_id FROM " . PREFIX . "_f_mods WHERE forum_id = '" . intval($_REQUEST ['fid']) . "' LIMIT 1");
                while ($row = $sql->fetch_object()) {
                    if (!empty($row->user_id)) {
                        $send++;
                        $row_2 = $this->_db->cache_fetch_object("SELECT Benutzername, Email FROM " . PREFIX . "_benutzer WHERE Id = '$row->user_id'");
                        $mail_array = array(
                            '__TEXT__'   => $body,
                            '__USER__'   => $row_2->Benutzername,
                            '__MESAGE__' => $send_post,
                            '__MAIL__'   => $email,
                            '__IP__'     => IP_USER,
                            '__URL__'    => BASE_URL . '/index.php?p=forums&action=postcount&pid=' . $id);
                        $message = $this->_text->replace($this->_lang['NewComplaintSend'], $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $row_2->Email,
                            'to_name'   => $row_2->Benutzername,
                            'text'      => $message,
                            'subject'   => $this->_lang['NewComplaintSubj'],
                            'fromemail' => $this->_settings['Mail_Absender'],
                            'from'      => $this->_settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 1));
                    }
                }
                $sql->close();

                if ($send == 0) {
                    $mail_array = array(
                        '__TEXT__'   => $body,
                        '__USER__'   => 'Admin',
                        '__MESAGE__' => $send_post,
                        '__MAIL__'   => $email,
                        '__IP__'     => IP_USER,
                        '__URL__'    => BASE_URL . '/index.php?p=forums&action=postcount&pid=' . $id);
                    $message = $this->_text->replace($this->_lang['NewComplaintSend'], $mail_array);
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $this->_settings['Mail_Absender'],
                        'to_name'   => $this->_settings['Mail_Name'],
                        'text'      => $message,
                        'subject'   => $this->_lang['NewComplaintSubj'],
                        'fromemail' => $this->_settings['Mail_Absender'],
                        'from'      => $this->_settings['Mail_Name'],
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => '',
                        'prio'      => 1));
                }
                SX::syslog('Пользователь с адреса: ' . $email . ', пожаловался на сообщение: ' . $send_post . PE . 'Жалоба: ' . $body, '6', $this->_user);
            }
        }
        $this->__object('Captcha')->start(); // Инициализация каптчи
        $this->_view->assign('title', $this->_text->substr($send_post, 0, 200) . '...');

        $seo_array = array(
            'headernav' => $this->_lang['Forums_Complaint'],
            'pagetitle' => $this->_lang['Forums_Complaint'] . $this->_lang ['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/complaint.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function data($datum) {
        $datum = strtotime($datum);
        $array['tag'] = date('d', $datum);
        $array['jahr'] = date('Y', $datum);
        $array['monat'] = date('m', $datum);
        return $array;
    }

    /* Вывод списка учавствовавших пользователей в топике форума */
    public function showposter() {
        if (!empty($_REQUEST['id'])) {
            $r_poster = $this->_db->query("SELECT
                COUNT(u.Benutzername) AS ucount,
                u.Id AS uid,
                u.Benutzername AS uname,
                t.title AS topic_title
            FROM
                " . PREFIX . "_benutzer AS u,
                " . PREFIX . "_f_post AS p,
                " . PREFIX . "_f_topic AS t
            WHERE
                p.topic_id = " . intval(Arr::getRequest('id')) . "
            AND
                p.uid = u.Id
            AND
                t.id = p.topic_id
            GROUP BY u.Benutzername ORDER BY ucount DESC");
            $poster = array();
            while ($post = $r_poster->fetch_object()) {
                $topic_title = $post->topic_title;
                $poster[] = $post;
            }
            $r_poster->close();

            $tpl_array = array(
                'forum_load' => 1,
                'poster'     => $poster);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => $this->_lang['Forums_TheseUserReplies'],
                'pagetitle' => Tool::repeat($this->_lang['Forums_Title'] . $this->_lang ['PageSep'] . $this->_lang['Forums_TheseUserReplies'] . $this->_lang ['PageSep'] . $topic_title),
                'content'   => $this->_view->fetch(THEME . '/forums/poster.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t', BASE_URL . '/index.php?p=showforums');
        }
    }

    /* Загружаем вложение на форум */
    public function upload() {
        if (!empty($_REQUEST['toid'])) {
            $q_closed = "SELECT f.id, f.status AS fstatus, t.status AS tstatus, t.uid FROM " . PREFIX . "_f_forum AS f, " . PREFIX . "_f_topic AS t WHERE t.id = '" . intval($_REQUEST ['toid']) . "' AND f.id = t.forum_id LIMIT 1";
            $closed = $this->_db->cache_fetch_object($q_closed);
            $permissions = Tool::accessForum($closed->id);
        } else {
            $permissions = Tool::accessForum($_REQUEST['fid']);
        }
        $error = array();
        if ($permissions['FORUM_UPLOAD_ATTACHMENT'] == 0) {
            $error[] = $this->_lang['Global_NoPermission_t'];
        }

        $res = SX::get('forum');
        $allowed = explode('|', $res['Typen']);
        asort($allowed);

        $files = array();
        if (empty($error) && Arr::getRequest('action') == 'upload') {
            $options = array(
                'rand'   => true,
                'type'   => 'load',
                'size'   => $res['Max_Groesse'],
                'result' => 'orig',
                'upload' => '/uploads/forum/',
                'input'  => 'attachment',
            );
            $object = SX::object('Upload');
            $object->extensions('load', $allowed);
            $array = $object->load($options);
            if (!empty($array)) {
                foreach ($array as $arr) {
                    if ($arr['result'] === true) {
                        $db_file = $this->_text->lower(Tool::cleanAllow(str_replace(' ', '-', $arr['file']), '.'));
                        $this->_db->insert_query('f_attachment', array('orig_name' => $db_file, 'filename' => $arr['load']));
                        $file['id'] = $this->_db->insert_id();
                        $file['orig_name'] = $arr['file'];
                        $file['file_name'] = $arr['load'];
                        $file['fid'] = $_REQUEST['fid'];
                        $files[] = $file;
                    } else {
                        $error[] = $arr['text'] . ': ' . $arr['file'];
                    }
                }
            } else {
                $error[] = $this->_lang['UploadFileError'];
            }
        }
        $tpl_array = array(
            'files'   => $files,
            'UpError' => $error,
            'res'     => $res,
            'allowed' => $allowed);
        $this->_view->assign($tpl_array);
        $seo_array = array(
            'headernav' => $this->_lang['Forums_AddNewAttachments'],
            'pagetitle' => $this->_lang['Forums_AddNewAttachments'] . $this->_lang ['PageSep'] . $this->_lang['Forums_Title'],
            'content'   => $this->_view->fetch(THEME . '/forums/attachment.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Удаляем вложение на форуме */
    public function delattach($id, $file) {
        $id = intval($id);
        $file = Tool::cleanAllow($file, '. ');
        $res = $this->_db->cache_fetch_object("SELECT * FROM  " . PREFIX . "_f_attachment WHERE id = '$id' AND filename = '$file' LIMIT 1");
        if (is_object($res)) {
            $this->_db->query("DELETE FROM  " . PREFIX . "_f_attachment WHERE id = '$id' AND filename = '$file'");
            File::delete(UPLOADS_DIR . '/forum/' . $file);
        }
    }

    /* Отправляем ссылку на топик другу */
    public function sendFriend() {
        $id = intval($_REQUEST['t_id']);
        $url = BASE_URL . '/index.php?p=showtopic&amp;toid=' . $id;
        if ($_SESSION['loggedin'] != 1) {
            $this->__object('Core')->message('Forums_Title', 'NoAccess', $url);
        }
        $error = array();
        $topic = $this->_db->fetch_assoc("SELECT title FROM " . PREFIX . "_f_topic WHERE id = '" . $id . "' LIMIT 1");
        $topic['message'] = $this->_text->replace($this->_lang['FriendMessage'], array('__URL__' => $url, '__USER__' => $_SESSION['user_name']));

        if (Arr::getPost('send') == 1) {
            if (empty($_POST['User'])) {
                $error[] = $this->_lang['Profile_NoFirstName'];
            }
            if (!Tool::isMail($_POST['Email'])) {
                $error[] = $this->_lang['Validate_email'];
            }
            if (empty($_POST['Title'])) {
                $error[] = $this->_lang['No_Subject'];
            }
            if (empty($_POST['Message'])) {
                $error[] = $this->_lang['No_Message'];
            }
            if (empty($error)) {
                $mail_array = array(
                    '__USER__' => $this->_uname,
                    '__TEXT__' => $_POST['Message'],
                    '__URL__'  => BASE_URL);
                $body = $this->_text->replace($this->_lang['ForumFriendSend'], $mail_array);
                $body = str_replace("\n", "\r\n", $body);
                $subject = $this->_text->replace($this->_lang['ForumFriendSendSubj'], '__URL__', BASE_URL);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $_POST['Email'],
                    'to_name'   => $_POST['User'],
                    'text'      => $body,
                    'subject'   => $subject,
                    'fromemail' => $this->_settings['Mail_Absender'],
                    'from'      => $this->_settings['Mail_Name'],
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 3));
                SX::syslog('Пользователь ' . $_SESSION ['user_name'] . ' отправил рекомендацию по адресу: ' . $_POST['Email'] . ', текст: ' . $_POST['Message'], '6', $this->_user);
                $this->__object('Core')->message('Forums_Title', 'SendEmail_Ok', $url, 5);
            }
        }
        $this->_view->assign(array('error' => $error, 'topic' => $topic));

        $seo_array = array(
            'headernav' => $this->_lang['FriendSend'],
            'pagetitle' => $this->_lang['FriendSend'] . $this->_lang['PageSep'] . $this->_lang['Forums_Title'],
            'generate'  => $this->_lang['FriendSend'],
            'content'   => $this->_view->fetch(THEME . '/forums/friendsend.tpl'));
        $this->_view->finish($seo_array);
    }

}
