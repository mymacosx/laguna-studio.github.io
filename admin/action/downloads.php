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
if (!get_active('downloads')) {
    SX::object('Core')->notActive();
}
if (!permission('downloads')) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    default:
        SX::object('Downloads')->categs();
        break;

    case 'getfile':
        SX::object('Downloads')->file(Arr::getRequest('id'));
        break;

    case 'search':
        SX::object('Downloads')->search(Arr::getRequest('ql'));
        break;

    case 'brokenlink':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Downloads')->deadlink(Arr::getRequest('id'));
        break;

    case 'showdetails':
        SX::object('Downloads')->get(Arr::getRequest('id'));
        break;

    case 'updatehitcount':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Downloads')->update(Arr::getRequest('id'));
        break;
}
