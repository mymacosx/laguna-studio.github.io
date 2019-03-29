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
if (!get_active('forums')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    default:
        SX::object('Redir')->seoRedirect('index.php?p=showforums');
        break;

    case 'show':
        SX::object('Forum')->showForum();
        break;

    case 'postcount':
        SX::object('Forum')->postCount();
        break;

    case 'addthanks':
        SX::object('Forum')->addThanks();
        break;

    case 'delthanks':
        SX::object('Forum')->delThanks();
        break;

    case 'delpost':
        SX::object('Forum')->delPost();
        break;

    case 'opentopic':
        SX::object('Forum')->openTopic();
        break;

    case 'closetopic':
        SX::object('Forum')->closeTopic();
        break;

    case 'deltopic':
        SX::object('Forum')->delTopic();
        break;

    case 'movepost':
        SX::object('Forum')->movePost();
        break;

    case 'move':
        SX::object('Forum')->moveTopic();
        break;

    case 'newtopic':
        SX::object('Forum')->newTopic();
        break;

    case 'complaint':
        SX::object('Forum')->complaint();
        break;
}
