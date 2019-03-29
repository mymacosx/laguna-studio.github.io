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

if (!perm('roadmaps') || !admin_active('roadmap')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
        SX::object('AdminRoadmap')->start();
        break;

    case 'editroadmap':
        SX::object('AdminRoadmap')->editroadmap(Arr::getRequest('id'));
        break;

    case 'newroadmap':
        SX::object('AdminRoadmap')->newroadmap();
        break;

    case 'delroadmap':
        SX::object('AdminRoadmap')->delroadmap(Arr::getRequest('id'));
        break;

    case 'showtickets':
        SX::object('AdminRoadmap')->showtickets(Arr::getRequest('id'), Arr::getRequest('closed'));
        break;

    case 'newticket':
        SX::object('AdminRoadmap')->newticket(Arr::getRequest('id'));
        break;

    case 'editticket':
        SX::object('AdminRoadmap')->editticket(Arr::getRequest('id'));
        break;

    case 'delticket':
        SX::object('AdminRoadmap')->delticket(Arr::getRequest('id'), Arr::getRequest('rid'), Arr::getRequest('closed'));
        break;
}
