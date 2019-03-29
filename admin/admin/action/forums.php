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

if (!perm('forum')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminForums')->show();
        break;

    case 'settings':
        SX::object('AdminForums')->settings();
        break;

    case 'delratings':
        SX::object('AdminForums')->delRatings();
        break;

    case 'editcategory':
        SX::object('AdminForums')->editCategory(Arr::getGet('id'));
        break;

    case 'deleteforum':
        SX::object('AdminForums')->deleteForum(Arr::getGet('id'));
        break;

    case 'deletecategory':
        SX::object('AdminForums')->deleteCategory(Arr::getGet('id'));
        break;

    case 'addforum':
        SX::object('AdminForums')->addForum();
        break;

    case 'newcategory':
        SX::object('AdminForums')->addCategory();
        break;

    case 'editforum':
        SX::object('AdminForums')->editForum(Arr::getGet('id'));
        break;

    case 'userpermissions':
        SX::object('AdminForums')->permissions(Arr::getGet('g_id'), Arr::getGet('f_id'));
        break;

    case 'closeforum':
        SX::object('AdminForums')->switchStatus(Arr::getGet('id'), 1);
        SX::object('Redir')->redirect('index.php?do=forums' . (Arr::hasGet('fid') ? '&id=' . Arr::getGet('fid') : ''));
        break;

    case 'openforum':
        SX::object('AdminForums')->switchStatus(Arr::getGet('id'), 0);
        SX::object('Redir')->redirect('index.php?do=forums' . (Arr::hasGet('fid') ? '&id=' . Arr::getGet('fid') : ''));
        break;

    case 'editmods':
        SX::object('AdminForums')->mods(Arr::getGet('id'));
        break;

    case 'searchmod':
        $_REQUEST['noout'] = 1;
        SX::object('AdminForums')->modSearch(Arr::getRequest('q'));
        break;

    case 'deltopics':
        SX::object('AdminForums')->delTopics();
        break;

    case 'showattachments':
        SX::object('AdminForums')->showAttachment();
        break;

    case 'searchattachments':
        $_REQUEST['noout'] = 1;
        SX::object('AdminForums')->searchAttachment(Arr::getRequest('q'));
        break;

    case 'userrankings':
        SX::object('AdminForums')->userRanks();
        break;

    case 'emoticons':
        SX::object('AdminForums')->emoticons();
        break;

    case 'posticons':
        SX::object('AdminForums')->posticons();
        break;

    case 'forumshelp':
        SX::object('AdminForums')->showHelp();
        break;

    case 'forumshelpedit':
        SX::object('AdminForums')->editHelpCateg(Arr::getRequest('id'));
        break;

    case 'forumshelpnew':
        SX::object('AdminForums')->addHelp(Arr::getRequest('categ'));
        break;

    case 'delhelppage':
        SX::object('AdminForums')->deleteHelp(Arr::getRequest('id'));
        break;

    case 'forumshelppageedit':
        SX::object('AdminForums')->editHelp(Arr::getRequest('id'));
        break;

    case 'delhelpcateg':
        SX::object('AdminForums')->deleteHelpCateg(Arr::getRequest('categ'));
        break;

    case 'forumshelpnewcateg':
        SX::object('AdminForums')->addHelpCateg();
        break;
}
