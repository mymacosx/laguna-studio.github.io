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

if (get_active('shop') && SX::get('system.shop_is_startpage') == 1 && $_REQUEST['p'] == 'index' && strpos($_SERVER['REQUEST_URI'], '/admin') === false) {
    SX::object('Redir')->seoRedirect('index.php?p=shop');
}

$CS = View::get();

if (get_active('News')) {
    SX::object('News')->show(0, 1);
    $CS->assign('topnews', $CS->fetch(THEME . '/news/topnews.tpl'));
    SX::object('News')->show();
    $CS->assign('news', $CS->fetch(THEME . '/news/newsticker.tpl'));
}

if (get_active('content')) {
    SX::object('Content')->top(SX::get('section.LimitTopcontent'));
    $CS->assign('topcontent', $CS->fetch(THEME . '/content/topcontent.tpl'));
}

if (get_active('articles')) {
    SX::object('Articles')->top(SX::get('section.LimitTopArticles'));
    $CS->assign('toparticle', $CS->fetch(THEME . '/articles/toparticles.tpl'));
}

if (get_active('products') && get_active('products_startpage')) {
    $CS->assign('NewProducts', SX::object('Products')->recent());
}
if (get_active('downloads')) {
    $CS->assign('NewDownloads', SX::object('Downloads')->newDownloads());
}
if (get_active('links')) {
    $CS->assign('NewLinks', SX::object('Links')->newLinks());
}
if (get_active('cheats') && get_active('cheats_startpage')) {
    $CS->assign('NewCheats', SX::object('Cheats')->recent());
}
if (get_active('gallery') && get_active('gallery_startpage')) {
    $CS->assign('NewGalleries', SX::object('Gallery')->recent());
}

if (get_active('forums')) {

    function getAktivForum() {
        static $aktiv_forum;
        if (!empty($aktiv_forum['return'])) {
            return $aktiv_forum;
        }
        $aktiv_forum['return'] = 1;
        $array = array();
        $sql = DB::get()->query("SELECT SQL_CACHE id, group_id FROM " . PREFIX . "_f_forum WHERE active = '1'");
        while ($row = $sql->fetch_object()) {
            if (in_array(Arr::getSession('user_group'), explode(',', $row->group_id))) {
                $array[] = $row->id;
            }
        }
        $sql->close();

        if (!empty($array)) {
            $in = implode(',', $array);
            $aktiv_forum['topic'] = 'forum_id IN (' . $in . ')';
            $aktiv_forum['post'] = 'AND f.id IN (' . $in . ')';
        } else {
            $aktiv_forum['topic'] = $aktiv_forum['post'] = '';
        }
        return $aktiv_forum;
    }

    if (get_active('forums_newstartpage')) {

        function getLastPostsStartpage($CS) {
            $aktiv_forum = getAktivForum();
            if (!empty($aktiv_forum['post'])) {
                $sql = DB::get()->query("SELECT
                        p.id,
                        p.title,
                        p.topic_id,
                        p.datum,
                        p.message,
                        f.id AS forum_id
                FROM
                        " . PREFIX . "_f_post AS p
                LEFT JOIN
                        " . PREFIX . "_f_topic AS t
                ON
                        t.id = p.topic_id
                LEFT JOIN
                        " . PREFIX . "_f_forum AS f
                ON
                        t.forum_id = f.id " . $aktiv_forum['post'] . "
                ORDER BY p.datum DESC LIMIT " . intval(SX::get('section.LimitLastPosts')));
                $limit = Tool::userSettings('Forum_Beitraege_Limit', 15);
                $lastposts_sp = array();
                while ($row = $sql->fetch_object()) {
                    $perms = Tool::accessForum($row->forum_id);
                    if (isset($perms['FORUM_SEE_TOPIC']) && $perms['FORUM_SEE_TOPIC']) {
                        $numPages = Tool::countPost($row->id, $row->topic_id, $limit);
                        $row->Datum = $row->datum;
                        $row->message = Tool::cleanVideo(Tool::cleanTags($row->message, array('codewidget')));
                        $row->title = Tool::cleanVideo(Tool::cleanTags($row->title, array('codewidget')));
                        $row->LpLink = 'index.php?p=showtopic&amp;toid=' . $row->topic_id . '&amp;pp=' . $limit . '&amp;page=' . $numPages . '#pid_' . $row->id;
                        $row->LpTitle = empty($row->title) ? strip_tags($row->message) : $row->title;
                        $row->LpTitle = SX::object('Post')->hidden($row->LpTitle);
                        $lastposts_sp[] = $row;
                    }
                }
                $sql->close();
            }
            $CS->assign('last_post_array', (!empty($lastposts_sp) ? $lastposts_sp : ''));
            return $CS->fetch(THEME . '/forums/lastposts.tpl');
        }

        $CS->assign('NewForumPosts', getLastPostsStartpage($CS));
    }

    if (get_active('forums_topicstartpage')) {

        function getLastThreadsStartpage($CS) {
            $aktiv_forum = getAktivForum();
            if (!empty($aktiv_forum['topic'])) {
                $sql = DB::get()->query("SELECT
                        id,
                        title,
                        datum,
                        forum_id
                FROM
                        " . PREFIX . "_f_topic
                WHERE
                        " . $aktiv_forum['topic'] . "
                ORDER BY datum DESC LIMIT " . intval(SX::get('section.LimitLastThreads')));
                $last_threads = array();
                while ($row = $sql->fetch_object()) {
                    $row->tlink = 'index.php?p=showtopic&amp;toid=' . $row->id . '&amp;fid=' . $row->forum_id . '&amp;t=' . translit($row->title);
                    $last_threads[] = $row;
                }
                $sql->close();
            }
            $CS->assign('last_thread_array', (!empty($last_threads) ? $last_threads : ''));
            return $CS->fetch(THEME . '/forums/lastthreads.tpl');
        }

        $CS->assign('NewForumThreads', getLastThreadsStartpage($CS));
    }
}
$text = SX::get('section.StartText');
$CS->assign('DisplayStartText', (SX::get('section.ZeigeStartText') == 1 && !empty($text) ? $text : '0'));
$CS->assign('OnlyStartText', SX::get('section.ZeigeStartTextNur'));

$seo_array = array(
    'headernav' => SX::$lang['Startpage'],
    'pagetitle' => SX::$lang['Startpage'],
    'content'   => View::get()->fetch(THEME . '/start/start.tpl'));
View::get()->finish($seo_array);
