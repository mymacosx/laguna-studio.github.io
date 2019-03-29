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

class AdminForums extends Magic {

    protected $status_closed = 1;

    public function settings() {
        if (!perm('forum')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'compres'  => Arr::getPost('compres'),
                'size'     => Arr::getPost('size'),
                'nofollow' => Arr::getPost('nofollow'),
            );
            if (perm('forum_attachments')) {
                $array['Max_Groesse'] = Arr::getPost('Max_Groesse');
                $array['Typen'] = implode('|', Arr::getPost('Typen'));
            }
            SX::save('forum', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки форума', '0', $this->UserId);
            $this->__object('AdminCore')->script('save');
            SX::load('forum');
        }
        $res = SX::get('forum');
        $res['possibles'] = explode('|', $res['Typen']);
        $possibles = explode('|', $res['TypenMoegliche']);
        asort($possibles);
        $this->_view->assign('res', $res);
        $this->_view->assign('possibles', $possibles);
        $this->_view->assign('title', $this->_lang['SettingsModule']);
        $this->_view->content('/forum/settings.tpl');
    }

    public function deleteHelp($id) {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_f_hilfetext WHERE Id = '" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function deleteHelpCateg($categ) {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        $categ = intval($categ);
        $this->_db->query("DELETE FROM " . PREFIX . "_f_hilfe WHERE Id = '" . $categ . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_f_hilfetext WHERE Kategorie = '" . $categ . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function addHelpCateg() {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1 && !empty($_POST['Name_1'])) {
            $n1 = $_POST['Name_1'];
            $n2 = empty($_POST['Name_2']) ? $n1 : $_POST['Name_2'];
            $n3 = empty($_POST['Name_3']) ? $n1 : $_POST['Name_3'];
            $insert_array = array(
                'Name_1'   => $n1,
                'Name_2'   => $n2,
                'Name_3'   => $n3,
                'Position' => 1,
                'Aktiv'    => 1);
            $this->_db->insert_query('f_hilfe', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новую категорию страниц помощи ' . $_POST['Name_1'], '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('title', $this->_lang['Global_NewCateg']);
        $this->_view->content('/forum/helpcateg_edit.tpl');
    }

    public function addHelp($categ) {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $n1 = Arr::getPost('Name_1');
            $n2 = empty($_POST['Name_2']) ? $n1 : $_POST['Name_2'];
            $n3 = empty($_POST['Name_3']) ? $n1 : $_POST['Name_3'];
            $t1 = Arr::getPost('Text_1');
            $t2 = empty($_POST['Text_2']) ? $t1 : $_POST['Text_2'];
            $t3 = empty($_POST['Text_3']) ? $t1 : $_POST['Text_3'];

            $insert_array = array(
                'Kategorie' => $categ,
                'Name_1'    => $n1,
                'Name_2'    => $n2,
                'Name_3'    => $n3,
                'Text_1'    => $t1,
                'Text_2'    => $t2,
                'Text_3'    => $t3,
                'Position'  => 1,
                'Klicks'    => '',
                'Aktiv'     => 1);
            $this->_db->insert_query('f_hilfetext', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новую страницу помощи ' . $n1, '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $this->_view->assign('Text_1', $this->__object('Editor')->load('admin', '', 'Text_1', 150, 'Basic'));
        $this->_view->assign('Text_2', $this->__object('Editor')->load('admin', '', 'Text_2', 150, 'Basic'));
        $this->_view->assign('Text_3', $this->__object('Editor')->load('admin', '', 'Text_3', 150, 'Basic'));
        $this->_view->assign('title', $this->_lang['Forums_Help_addhelp']);
        $this->_view->content('/forum/helppage_edit_new.tpl');
    }

    public function editHelp($id) {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);

        if (Arr::getPost('save') == 1 && !empty($_POST['Text_1'])) {
            $n1 = $_POST['Name_1'];
            $n2 = (empty($_POST['Name_2'])) ? $n1 : $_POST['Name_2'];
            $n3 = (empty($_POST['Name_3'])) ? $n1 : $_POST['Name_3'];
            $t1 = $_POST['Text_1'];
            $t2 = (empty($_POST['Text_2'])) ? $t1 : $_POST['Text_2'];
            $t3 = (empty($_POST['Text_3'])) ? $t1 : $_POST['Text_3'];

            $array = array(
                'Name_1' => $n1,
                'Name_2' => $n2,
                'Name_3' => $n3,
                'Text_1' => $t1,
                'Text_2' => $t2,
                'Text_3' => $t3,
            );
            $this->_db->update_query('f_hilfetext', $array, "Id = '" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал страницу помощи ' . $_POST['Name_1'], '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_hilfetext WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('Text_1', $this->__object('Editor')->load('admin', $res->Text_1, "Text_1", 150, 'Basic'));
        $this->_view->assign('Text_2', $this->__object('Editor')->load('admin', $res->Text_2, "Text_2", 150, 'Basic'));
        $this->_view->assign('Text_3', $this->__object('Editor')->load('admin', $res->Text_3, "Text_3", 150, 'Basic'));
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/forum/helppage_edit_new.tpl');
    }

    public function editHelpCateg($id) {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1 && !empty($_POST['Name_1'])) {
            $n1 = $_POST['Name_1'];
            $n2 = (empty($_POST['Name_2'])) ? $n1 : $_POST['Name_2'];
            $n3 = (empty($_POST['Name_3'])) ? $n1 : $_POST['Name_3'];
            $array = array(
                'Name_1' => $n1,
                'Name_2' => $n2,
                'Name_3' => $n3,
            );
            $this->_db->update_query('f_hilfe', $array, "Id = '" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал категорию страниц помощи ' . $_POST['Name_1'], '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_f_hilfe WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/forum/helpcateg_edit.tpl');
    }

    public function showHelp() {
        if (!perm('forum_helppages')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Fid'] as $id) {
                $array = array(
                    'Position' => $_POST['Position'][$id],
                    'Aktiv'    => $_POST['Aktiv'][$id],
                );
                $this->_db->update_query('f_hilfe', $array, "Id = '" . intval($id) . "'");

                if ($_POST['Aktiv'][$id] != 1) {
                    $this->_db->query("UPDATE " . PREFIX . "_f_hilfetext SET Aktiv='0' WHERE Kategorie='" . intval($id) . "'");
                }
            }

            foreach ($_POST['SFid'] as $id) {
                $this->_db->query("UPDATE " . PREFIX . "_f_hilfetext SET Position = '" . intval($_POST['SubPosition'][$id]) . "', Aktiv = '" . $this->_db->escape($_POST['SubAktiv'][$id]) . "' WHERE Id = '" . intval($id) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $topics = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_f_hilfe ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $row->subtopics = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_f_hilfetext WHERE Kategorie = '" . $row->Id . "' ORDER BY Position ASC");
            $topics[] = $row;
        }
        $sql->close();
        $this->_view->assign('topics', $topics);
        $this->_view->assign('title', $this->_lang['Forums_Help']);
        $this->_view->content('/forum/helppages.tpl');
    }

    public function show() {
        if (!perm('forum')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['c_id'] as $c_id) {
                $position = intval($_POST['c_position'][$c_id]);
                $this->_db->query("UPDATE " . PREFIX . "_f_category SET position = $position WHERE id = '" . intval($c_id) . "'");
            }

            foreach ($_POST['f_id'] as $f_id) {
                $position = intval($_POST['f_position'][$f_id]);
                $this->_db->query("UPDATE " . PREFIX . "_f_forum SET position = $position WHERE id = '" . intval($f_id) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        Arr::setGet('id', intval(Arr::getGet('id')));
        $categories = array();
        $this->categories(Arr::getGet('id'), $categories, ' - ');
        $this->_view->assign('categories', $categories);
        $this->_view->assign('title', $this->_lang['Forums_title']);
        $this->_view->content('/forum/forums_overview.tpl');
    }

    public function delRatings() {
        if (!perm('forum_deltopics')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('delete') == 1) {
            foreach ($_POST['fid_r'] as $fid) {
                $sql = $this->_db->query("SELECT id FROM " . PREFIX . "_f_topic WHERE forum_id = '" . intval($fid) . "'");
                while ($row = $sql->fetch_object()) {
                    $this->_db->query("UPDATE " . PREFIX . "_f_rating SET rating='',ip='',uid='' WHERE topic_id = '" . $row->id . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('forums', $this->showForums());
        $this->_view->assign('title', $this->_lang['Forums_Del_Ratings']);
        $this->_view->content('/forum/delete_rating.tpl');
    }

    public function delTopics() {
        if (!perm('forum_deltopics')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('delete') == 1) {
            $match = 0;
            $query = "SELECT id FROM " . PREFIX . "_f_topic AS t WHERE ";
            $where_clausel = array();

            if (Arr::getPost('date') > 0) {
                $where_clausel[] = " (status='0') AND (forum_id='" . intval(Arr::getPost('fid')) . "') AND ((UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "') - UNIX_TIMESTAMP(t.datum)) / 60 / 60 / 24) >= '" . $this->_db->escape(Arr::getPost('date')) . "'";
            }

            if (!empty($_POST['reply_count'])) {
                $where_clausel[] = "t.replies < '" . $this->_db->escape(Arr::getPost('reply_count')) . "'";
            }
            if (isset($_POST['topic_closed'])) {
                $where_clausel[] = "t.status = " . $this->status_closed;
            }
            if (!empty($_POST['hits_count'])) {
                $where_clausel[] = "t.views < '" . $this->_db->escape(Arr::getPost('hits_count')) . "'";
            }

            $query .= implode(' ' . $this->_db->escape(Arr::getPost('verkn')) . ' ', $where_clausel);

            if (count($where_clausel) > 0) {
                $result = $this->_db->query($query);
                while ($topic = $result->fetch_object()) {
                    $this->removeTopic($topic->id);
                    $match++;
                }
                $result->close();
            }
            $this->_view->assign('match', $match);
        }
        $this->_view->assign('forums', $this->showForums());
        $this->_view->assign('title', $this->_lang['Forums_title']);
        $this->_view->content('/forum/delete_topic.tpl');
    }

    protected function showForums() {
        $categs = array();
        $query = $this->_db->query("SELECT * FROM " . PREFIX . "_f_category ORDER BY position ASC");
        while ($row = $query->fetch_object()) {
            $row->forums = $this->_db->fetch_object_all("SELECT id, title FROM " . PREFIX . "_f_forum WHERE category_id='" . $row->id . "'");
            $categs[] = $row;
        }
        $query->close();
        return $categs;
    }

    public function modSearch($q) {
        $value = NULL;
        $q = urldecode($q);
        if (!empty($q) && $this->_text->strlen($q) >= 2) {
            $result = $this->_db->query("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Benutzername LIKE '" . $q . "%'");
            while ($row = $result->fetch_object()) {
                if ($this->_text->stripos($row->Benutzername, $q) !== false) {
                    $value .= $row->Benutzername . PE;
                }
            }
            $result->close();
        }
        SX::output($value, true);
    }

    public function mods($id) {
        if (!perm('forum_mods')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('delete') == 1) {
            $this->_db->query("DELETE FROM " . PREFIX . "_f_mods WHERE forum_id = '" . intval(Arr::getPost('id')) . "' AND user_id = '" . intval(Arr::getPost('user_id')) . "'");
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1 && !empty($_POST['q'])) {
            $user = $this->_db->fetch_object("SELECT Id FROM " . PREFIX . "_benutzer WHERE Benutzername = '" . $this->_db->escape(Arr::getPost('q')) . "' LIMIT 1");
            if (is_object($user) && !empty($user->Id) && !in_array($_POST['q'], Arr::getPost('mods'))) {
                $this->_db->insert_query('f_mods', array('forum_id' => intval(Arr::getPost('id')), 'user_id' => $user->Id));
                $this->__object('AdminCore')->script('save');
            } else {
                $this->_view->assign('error', 1);
            }
        }

        $mods = $this->_db->fetch_object_all("SELECT Id, Benutzername FROM " . PREFIX . "_benutzer AS u," . PREFIX . "_f_mods AS m WHERE Id = m.user_id AND m.forum_id = '" . intval($id) . "'");

        $this->_view->assign('mods', $mods);
        $this->_view->assign('title', $this->_lang['Forums_mods']);
        $this->_view->content('/forum/edit_mods.tpl');
    }

    protected function groups() {
        $groups = $this->_db->fetch_object_all("SELECT Id AS ugroup, Name AS groupname FROM  " . PREFIX . "_benutzer_gruppen");
        return $groups;
    }

    public function deleteCategory($id) {
        if (!perm('forum_delete')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->removeCategory($id);
        $this->__object('AdminCore')->backurl();
    }

    public function deleteForum($id) {
        if (!perm('forum_delete')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->removeForum($id);
        $this->__object('AdminCore')->backurl();
    }

    public function addCategory() {
        if (!perm('forum_categoryedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $insert_array = array(
                'title'     => Arr::getPost('title'),
                'position'  => Arr::getPost('position'),
                'comment'   => Arr::getPost('comment'),
                'parent_id' => intval(Arr::getPost('f_id')),
                'group_id'  => implode(',', Arr::getPost('group_id')));
            $this->_db->insert_query('f_category', $insert_array);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('groups', $this->groups());
        $this->_view->assign('title', $this->_lang['GlobalAddCateg']);
        $this->_view->content('/forum/edit_category.tpl');
    }

    public function editCategory($id) {
        if (!perm('forum_categoryedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $query = " UPDATE " . PREFIX . "_f_category SET position = " . intval(Arr::getPost('position'));
            $comment = (!empty($_POST['comment'])) ? $this->_db->escape(Arr::getPost('comment')) : '';
            if (isset($_POST['title'])) {
                $query .= ", title = '" . $this->_db->escape(sanitize($_POST['title'])) . "'";
            }
            if (isset($comment)) {
                $query .= ", comment = '" . $comment . "'";
            }
            if (isset($_POST['group_id'])) {
                $groups = $this->_db->escape(implode(',', $_POST['group_id']));
                $this->_db->query("UPDATE " . PREFIX . "_f_forum SET group_id = '$groups' WHERE category_id = '" . intval(Arr::getPost('c_id')) . "'");
                $query .= ", group_id = '$groups'";
            }
            $query .= " WHERE id = " . intval(Arr::getPost('c_id'));
            $this->_db->query($query);
            $this->__object('AdminCore')->script('save');
        }

        $category = $this->_db->cache_fetch_object("SELECT id, title, comment, position, group_id FROM " . PREFIX . "_f_category WHERE id = '" . intval($id) . "' LIMIT 1");
        $category->group_id = explode(',', $category->group_id);
        $this->_view->assign('groups', $this->groups());
        $this->_view->assign('category', $category);
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/forum/edit_category.tpl');
    }

    public function permissions($group, $forum) {
        if (!perm('forum_userperms')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $permissions = array();
            $permissions['FORUM_SEE'] = intval(Arr::getPost('can_see'));
            $permissions['FORUM_SEE_TOPIC'] = intval(Arr::getPost('can_see_topic'));
            $permissions['FORUM_SEE_DELETE_MESSAGE'] = $permissions['FORUM_SEARCH_FORUM'] = 1;
            $permissions['FORUM_DOWNLOAD_ATTACHMENT'] = intval(Arr::getPost('can_download_attachment'));
            $permissions['FORUM_CREATE_TOPIC'] = intval(Arr::getPost('can_create_topic'));
            $permissions['FORUM_REPLY_OWN_TOPIC'] = intval(Arr::getPost('can_reply_own_topic'));
            $permissions['FORUM_REPLY_OTHER_TOPIC'] = intval(Arr::getPost('can_reply_other_topic'));
            $permissions['FORUM_UPLOAD_ATTACHMENT'] = intval(Arr::getPost('can_upload_attachment'));
            $permissions['FORUM_RATE_TOPIC'] = intval(Arr::getPost('can_rate_topic'));
            $permissions['FORUM_EDIT_OWN_POST'] = intval(Arr::getPost('can_edit_own_post'));
            $permissions['FORUM_DELETE_OWN_POST'] = intval(Arr::getPost('can_delete_own_post'));
            $permissions['FORUM_MOVE_OWN_TOPIC'] = intval(Arr::getPost('can_move_own_topic'));
            $permissions['FORUM_CLOSE_OPEN_OWN_TOPIC'] = intval(Arr::getPost('can_close_open_own_topic'));
            $permissions['FORUM_DELETE_OWN_TOPIC'] = intval(Arr::getPost('can_delete_own_topic'));
            $permissions['FORUM_DELETE_OTHER_POST'] = intval(Arr::getPost('can_delete_other_post'));
            $permissions['FORUM_EDIT_OTHER_POST'] = intval(Arr::getPost('can_edit_other_post'));
            $permissions['FORUM_OPEN_TOPIC'] = intval(Arr::getPost('can_open_topic'));
            $permissions['FORUM_CLOSE_TOPIC'] = intval(Arr::getPost('can_close_topic'));
            $permissions['FORUM_CHANGE_TOPICTYPE'] = intval(Arr::getPost('can_change_topic_type'));
            $permissions['FORUM_MOVE_TOPIC'] = intval(Arr::getPost('can_move_topic'));
            $permissions['FORUM_DELETE_TOPIC'] = intval(Arr::getPost('can_delete_topic'));
            $permissions = implode(',', $permissions);

            $g_id = intval(Arr::getPost('g_id'));
            $f_id = intval(Arr::getPost('f_id'));

            if (Arr::getRequest('settoall') == 1) {
                $sql = $this->_db->query("SELECT forum_id FROM " . PREFIX . "_f_permissions");
                while ($row = $sql->fetch_object()) {
                    $this->load($row->forum_id, $g_id, $permissions);
                }
                $sql->close();
            } else {
                $this->load($f_id, $g_id, $permissions);
            }
            $this->__object('AdminCore')->script('save');
        }

        $forum = $this->_db->fetch_object("SELECT permissions FROM " . PREFIX . "_f_permissions WHERE forum_id = '" . intval($forum) . "' AND group_id = '" . intval($group) . "' LIMIT 1");
        $permissions = explode(',', $forum->permissions);
        $this->_view->assign('permissions', $permissions);
        $this->_view->assign('title', $this->_lang['Forums_GroupPerms']);
        $this->_view->content('/forum/edit_permissions.tpl');
    }

    protected function load($f_id, $g_id, $permissions) {
        $num_first = $this->_db->num_rows("SELECT forum_id FROM " . PREFIX . "_f_permissions WHERE forum_id = " . $f_id . " AND group_id = '" . $g_id . "'");

        if ($num_first < 1) {
            $this->_db->insert_query('f_permissions', array('forum_id' => $f_id, 'group_id' => $g_id, 'permissions' => $permissions));
        } else {
            $this->_db->query("UPDATE " . PREFIX . "_f_permissions SET permissions = '$permissions' WHERE forum_id = '" . $f_id . "' AND group_id = '" . $g_id . "'");
        }
    }

    public function editForum($id) {
        if (!perm('forum_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $password = empty($_POST['password']) ? '' : md5($_POST['password']);
            $moderated = Arr::getPost('moderated') == 1 ? 1 : 0;
            $moderated_posts = Arr::getPost('moderated_posts') == 1 ? 1 : 0;
            $array = array(
                'title'           => sanitize($_POST['title']),
                'comment'         => sanitize($_POST['comment']),
                'group_id'        => implode(',', $_POST['group_id']),
                'active'          => Arr::getPost('active'),
                'password'        => $password,
                'password_raw'    => Arr::getPost('password'),
                'moderated'       => $moderated,
                'moderated_posts' => $moderated_posts,
                'post_emails'     => Arr::getPost('post_emails'),
                'topic_emails'    => Arr::getPost('topic_emails'),
            );
            $this->_db->update_query('f_forum', $array, "id = '" . intval(Arr::getPost('f_id')) . "'");
            $forum_id = $this->_db->insert_id();
            $f_id = (empty($_POST['f_id'])) ? $forum_id : $this->_db->escape(Arr::getPost('f_id'));
            $this->switchStatus($f_id, $_POST['status']);
            $this->__object('AdminCore')->script('save');
        }

        $f_id = $id;
        $forum = $this->_db->cache_fetch_object("SELECT
                f.id,
                f.title,
                f.comment,
                f.active,
                c.group_id,
                f.category_id,
                f.status,
                f.password_raw,
                f.moderated,
                f.moderated_posts,
                f.post_emails,
                f.topic_emails
        FROM
                " . PREFIX . "_f_forum AS f,
                " . PREFIX . "_f_category AS c
        WHERE
                f.id = $f_id
        AND
                f.category_id = c.id LIMIT 1");

        $forum->group_id = explode(',', $forum->group_id);
        $this->_view->assign('forum', $forum);
        $this->_view->assign('groups', $this->groups());
        $this->_view->assign('title', $this->_lang['Forums_fEdit']);
        $this->_view->content('/forum/forums_edit.tpl');
    }

    public function addForum() {
        if (!perm('forum_add')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $password = empty($_POST['password']) ? '' : md5($_POST['password']);
            $password_raw = $_POST['password'];
            $c_groups = $this->_db->cache_fetch_object("SELECT group_id FROM " . PREFIX . "_f_category WHERE id = '" . $this->_db->escape(Arr::getPost('c_id')) . "' LIMIT 1");
            $title = sanitize($_POST['title']);
            $comment = sanitize($_POST['comment']);
            $moderated = (Arr::getPost('moderated') == 1) ? 1 : 0;
            $moderated_posts = (Arr::getPost('moderated_posts') == 1) ? 1 : 0;

            $insert_array = array(
                'title'           => $title,
                'comment'         => $comment,
                'category_id'     => intval(Arr::getPost('c_id')),
                'active'          => Arr::getPost('active'),
                'password'        => $password,
                'password_raw'    => $password_raw,
                'group_id'        => $c_groups->group_id,
                'moderated'       => $moderated,
                'moderated_posts' => $moderated_posts,
                'post_emails'     => Arr::getPost('post_emails'),
                'topic_emails'    => Arr::getPost('topic_emails'));
            $this->_db->insert_query('f_forum', $insert_array);
            $forum_id = $this->_db->insert_id();
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал форум ' . $title, '0', $_SESSION['benutzer_id']);

            // Добавляем задание на пинг
            $options = array(
                'name' => $title,
                'url'  => BASE_URL . '/index.php?p=showforum&fid=' . $forum_id . '&t=' . translit($title),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $title,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            $default_mask = $this->silence();
            $r_groups = $this->_db->query("SELECT Id AS ugroup FROM " . PREFIX . "_benutzer_gruppen");
            while ($group = $r_groups->fetch_object()) {
                $defmask = !empty($default_mask[$group->ugroup]) ? $default_mask[$group->ugroup] : $default_mask['2'];
                $insert_array = array(
                    'forum_id'    => $forum_id,
                    'group_id'    => $group->ugroup,
                    'permissions' => $defmask);
                $this->_db->insert_query('f_permissions', $insert_array);
            }
            $r_groups->close();

            $f_id = !empty($_POST['f_id']) ? $forum_id : $_POST['f_id'];
            $this->switchStatus($f_id, $_POST['status']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('title', $this->_lang['Forums_new']);
        $this->_view->assign('groups', $this->groups());
        $this->_view->content('/forum/forums_new.tpl');
    }

    protected function categories($id, &$categories, $prefix) {
        $r_cat = $this->_db->query("SELECT id, title, comment, position FROM " . PREFIX . "_f_category WHERE parent_id = '" . $this->_db->escape($id) . "' ORDER BY position ASC");
        while ($cat = $r_cat->fetch_object()) {
            $cat->forums = array();
            $result = $this->_db->query("SELECT
                    f.id,
                    f.comment,
                    f.category_id,
                    f.title,
                    f.active,
                    f.group_id,
                    f.position,
                    f.status
            FROM
                    " . PREFIX . "_f_forum AS f
            WHERE
                    category_id = '" . $cat->id . "'
            ORDER BY
                    f.position");

            while ($forum = $result->fetch_object()) {
                $forum->visible_title = $prefix . $forum->title;
                $r_sub_cat = $this->_db->query("SELECT id, title, comment FROM " . PREFIX . "_f_category WHERE parent_id = '" . $forum->id . "'");
                $forum->categories = array();
                while ($sub_cat = $r_sub_cat->fetch_object()) {
                    $forum->categories[] = $sub_cat;
                }

                $mods = $this->_db->cache_fetch_object("SELECT COUNT(forum_id) AS m_count FROM " . PREFIX . "_f_mods WHERE forum_id = '" . $forum->id . "'");
                $forum->mods = $mods->m_count;
                $cat->forums[] = $forum;
            }
            $categories[] = $cat;
        }
        $r_cat->close();
    }

    public function posticons() {
        if (!perm('comment_emoticons')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['smilie']) as $id) {
                if (!empty($_POST['path'][$id])) {
                    $array = array(
                        'active' => $_POST['active'][$id],
                        'path'   => $_POST['path'][$id],
                        'posi'   => $_POST['posi'][$id],
                        'title'  => $_POST['title'][$id],
                    );
                    $this->_db->update_query('posticons', $array, "id = '" . intval($id) . "'");
                }
            }

            if (Arr::getPost('del') >= 1) {
                foreach ($_POST['del'] as $id => $del) {
                    if (!empty($del)) {
                        $this->_db->query("DELETE FROM  " . PREFIX . "_posticons WHERE id = '" . intval($id) . "'");
                    }
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $a_area = $_SESSION['a_area'];
        if (Arr::getPost('new') == 1) {
            $smileys = array();
            for ($i = 0, $count_path = count($_POST['path']); $i < $count_path; $i++) {
                if (!empty($_POST['path'][$i])) {
                    $insert_array = array(
                        'active' => 1,
                        'path'   => trim($_POST['path'][$i]),
                        'area'   => $a_area,
                        'title'  => trim($_POST['title'][$i]));
                    $this->_db->insert_query('posticons', $insert_array);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $theme_a = $this->theme($a_area);
        $smileys = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_posticons WHERE area='" . $a_area . "' ORDER BY posi ASC");
        while ($row = $sql->fetch_object()) {
            $installed[] = $row->path;
            $row->Icon = '<img src="../theme/' . $theme_a . '/images/posticons/' . $row->path . '" alt="" border="0" />';
            $smileys[] = $row;
        }
        $sql->close();

        $d = SX_DIR . '/theme/' . $theme_a . '/images/posticons/';
        $handle = opendir($d);
        $smi = array();
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && !in_array($file, $installed) && is_file($d . $file)) {
                $f = new stdClass;
                $short = explode('.', $file);
                $f->Short = ':' . $short[0] . ':';
                $f->Name = $file;
                $smi[] = $f;
            }
        }
        closedir($handle);

        $this->_view->assign('smi_path', '../theme/' . $theme_a . '/images/posticons/');
        $this->_view->assign('smi', $smi);
        $this->_view->assign('icon_t_path', $theme_a);
        $this->_view->assign('smileys', $smileys);
        $this->_view->assign('title', $this->_lang['Forums_TIcons_title']);
        $this->_view->content('/forum/posticons.tpl');
    }

    public function emoticons() {
        if (!perm('comment_emoticons')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['smilie']) as $id) {
                if (!empty($_POST['path'][$id]) && !empty($_POST['code'][$id])) {
                    $array = array(
                        'active' => $_POST['active'][$id],
                        'code'   => $_POST['code'][$id],
                        'path'   => $_POST['path'][$id],
                        'posi'   => $_POST['posi'][$id],
                        'title'  => $_POST['title'][$id],
                    );
                    $this->_db->update_query('smileys', $array, "id = '" . intval($id) . "'");
                }
            }

            if (Arr::getPost('del') >= 1) {
                foreach ($_POST['del'] as $id => $del) {
                    if (!empty($del)) {
                        $this->_db->query("DELETE FROM  " . PREFIX . "_smileys WHERE id = '" . intval($id) . "'");
                    }
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $a_area = $_SESSION['a_area'];
        if (Arr::getPost('new') == 1) {
            for ($i = 0, $count_code = count($_POST['code']); $i < $count_code; $i++) {
                if (!empty($_POST['code'][$i]) && !empty($_POST['path'][$i])) {
                    $insert_array = array(
                        'active' => 1,
                        'code'   => trim($_POST['code'][$i]),
                        'path'   => trim($_POST['path'][$i]),
                        'area'   => $a_area,
                        'title'  => trim($_POST['title'][$i]));
                    $this->_db->insert_query('smileys', $insert_array);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $theme_a = $this->theme($a_area);
        $smileys = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_smileys WHERE area='" . $a_area . "' ORDER BY posi ASC");
        while ($row = $sql->fetch_object()) {
            $installed[] = $row->path;
            $row->Icon = '<img src="../theme/' . $theme_a . '/images/smilies/' . $row->path . '" alt="" border="0" />';
            $smileys[] = $row;
        }
        $sql->close();

        $d = SX_DIR . '/theme/' . $theme_a . '/images/smilies/';
        $handle = opendir($d);
        $smi = array();
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && !in_array($file, $installed) && is_file($d . $file)) {
                $f = new stdClass;
                $short = explode('.', $file);
                $f->Short = ':' . $short[0] . ':';
                $f->Name = $file;
                $smi[] = $f;
            }
        }
        closedir($handle);

        $this->_view->assign('smi_path', '../theme/' . $theme_a . '/images/smilies/');
        $this->_view->assign('smi', $smi);
        $this->_view->assign('smi_t_path', $theme_a);
        $this->_view->assign('smileys', $smileys);
        $this->_view->assign('title', $this->_lang['ForumsEmoticons']);
        $this->_view->content('/forum/emoticons.tpl');
    }

    public function userRanks() {
        if (!perm('forum_userrankings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getGet('del') == 1) {
            $this->_db->query("DELETE FROM " . PREFIX . "_f_rank WHERE id = '" . $this->_db->escape(Arr::getGet('id')) . "'");
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1 && ($_POST['count'] > 1) && (!empty($_POST['title']))) {
            $this->_db->insert_query('f_rank', array('title' => Arr::getPost('title'), 'count' => Arr::getPost('count')));
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            $rank_id = array_keys($_POST['count']);
            foreach ($rank_id as $id) {
                if (is_numeric($_POST['count'][$id]) && !empty($_POST['title'][$id])) {
                    $this->_db->query("UPDATE " . PREFIX . "_f_rank SET count = '" . $this->_db->escape($_POST['count'][$id]) . "',title = '" . $this->_db->escape($_POST['title'][$id]) . "' WHERE id = '" . intval($id) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $ranks = $this->_db->fetch_object_all("SELECT id, title, count FROM " . PREFIX . "_f_rank");

        $this->_view->assign('ranks', $ranks);
        $this->_view->assign('title', $this->_lang['Forums_URank_title']);
        $this->_view->content('/forum/user_ranks.tpl');
    }

    public function searchAttachment($q) {
        $value = NULL;
        if (perm('forum_attachments')) {
            $q = urldecode($q);
            if (!empty($q) && $this->_text->strlen($q) >= 2) {
                $result = $this->_db->query("SELECT id, orig_name FROM " . PREFIX . "_f_attachment WHERE orig_name LIKE '" . $this->_db->escape($q) . "%'");
                while ($row = $result->fetch_object()) {
                    if ($this->_text->stripos($row->orig_name, $q) !== false) {
                        $value .= $row->orig_name . PE;
                    }
                }
                $result->close();
            }
        }
        SX::output($value, true);
    }

    public function showAttachment() {
        $extra = $extra_ext = $def_search_n = '';
        if (!perm('forum_attachments')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            if ($_POST['del'] >= 1) {
                foreach ($_POST['del'] as $filename => $del) {
                    if (!empty($del)) {
                        $this->_db->query("DELETE FROM  " . PREFIX . "_f_attachment WHERE filename = '" . $this->_db->escape($filename) . "'");
                        File::delete(UPLOADS_DIR . '/forum/' . $filename);
                    }
                }
                $this->__object('AdminCore')->script('save');
            }
        }

        if (Arr::getRequest('dl') == 1) {
            $file = $this->_db->cache_fetch_object("SELECT filename, orig_name FROM " . PREFIX . "_f_attachment WHERE id = '" . intval(Arr::getGet('id')) . "' LIMIT 1");
            header('Cache-control: private');
            header('Content-type: application/octet-stream');
            header('Content-disposition:attachment; filename=' . $file->orig_name);
            header('Content-Length: ' . filesize(UPLOADS_DIR . '/forum/' . $file->filename));
            readfile(UPLOADS_DIR . '/forum/' . $file->filename);
            exit;
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name':
                $ord = " ORDER BY orig_name ASC";
                break;

            case 'hits':
                $ord = "ORDER BY hits DESC";
                break;

            default:
                $ord = "ORDER BY hits DESC";
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern)) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ' .,');
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $ext = $this->_db->escape(Arr::getRequest('ext'));
            if (!empty($_REQUEST['ext'])) {
                $extra_ext = " AND ((RIGHT(orig_name,3) = '$ext') OR (RIGHT(orig_name,4) = '$ext') OR (RIGHT(orig_name,5) = '$ext'))  ";
            }
            $extra = "WHERE orig_name like '%{$this->_db->escape($pattern)}%' $extra_ext ";
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $result = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_f_attachment $extra $ord LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $attachments = array();
        while ($row = $result->fetch_object()) {
            $row->sizes = File::filesize(filesize(UPLOADS_DIR . '/forum/' . $row->filename) / 1024);
            $attachments[] = $row;
        }
        $result->close();

        $res = SX::get('forum');
        $res['possibles'] = explode('|', $res['Typen']);
        $possibles = explode('|', $res['TypenMoegliche']);
        asort($possibles);
        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=forums&amp;ext=" . Arr::getRequest('ext') . $def_search_n . "&amp;sub=showattachments&amp;sort=" . $_REQUEST['sort'] . "&amp;pp=$limit&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('possibles', $possibles);
        $this->_view->assign('attachments', $attachments);
        $this->_view->content('/forum/attachments.tpl');
    }

    protected function silence() {
        $default_mask = array();
        $default_mask['1'] = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';
        $default_mask['2'] = '0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
        $default_mask['3'] = '1,1,0,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0';
        $default_mask['4'] = '1,1,0,1,1,1,1,1,1,1,1,0,1,1,0,0,1,1,1,1,1,0';
        return $default_mask;
    }

    public function switchStatus($id, $status) {
        $id = intval($id);
        $this->_db->query("UPDATE " . PREFIX . "_f_forum SET status = $status WHERE id = '" . $id . "'");
        $r_child = $this->_db->query("SELECT f.id FROM " . PREFIX . "_f_category AS c, " . PREFIX . "_f_forum AS f WHERE parent_id = '" . $id . "' AND f.category_id = c.id");
        while ($child = $r_child->fetch_object()) {
            $this->switchStatus($child->id, $status);
        }
        $r_child->close();
        return;
    }

    protected function removeCategory($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_forum WHERE category_id = '" . $id . "'");
        while ($forum = $result->fetch_object()) {
            $this->removeForum($forum->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_category WHERE id = '" . $id . "'");
    }

    protected function removeForum($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_topic WHERE forum_id = '" . $id . "'");
        while ($topic = $result->fetch_object()) {
            $this->removeTopic($topic->id);
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_f_permissions WHERE forum_id = '" . $id . "'");
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_category WHERE parent_id = '" . $id . "'");
        while ($category = $result->fetch_object()) {
            $this->removeCategory($category->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_forum WHERE id = '" . $id . "'");
    }

    protected function removeTopic($id) {
        $id = intval($id);
        $result = $this->_db->query("SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '" . $id . "'");
        while ($post = $result->fetch_object()) {
            $this->removePost($post->id);
        }
        $result->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_f_rating WHERE topic_id = '" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_f_topic WHERE id = '" . $id . "'");
    }

    protected function removePost($id) {
        $id = intval($id);
        $post = $this->_db->cache_fetch_object("SELECT uid, attachment, topic_id FROM " . PREFIX . "_f_post WHERE id = '" . $id . "' LIMIT 1");
        $attachments = explode(';', $post->attachment);
        foreach ($attachments as $attachment) {
            if (!empty($attachment)) {
                $this->_db->query("DELETE FROM " . PREFIX . "_f_attachment WHERE id = '" . $attachment . "'");
            }
        }
        $this->_db->query("UPDATE " . PREFIX . "_f_topic SET replies = replies - 1 WHERE id = " . $post->topic_id);
        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = Beitraege - 1 WHERE Id = " . $post->uid);
        $this->_db->query("DELETE FROM " . PREFIX . "_f_post WHERE id = '" . $id . "'");
    }

    protected function theme($area) {
        $res = $this->_db->cache_fetch_object("SELECT Template FROM " . PREFIX . "_sektionen WHERE Id='" . intval($area) . "' LIMIT 1");
        return $res->Template;
    }

}
