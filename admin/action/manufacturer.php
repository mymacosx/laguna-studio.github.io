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
if (!get_active('manufacturer')) {
    SX::object('Core')->notActive();
}
if (!permission('manufacturer')) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    default:
    case 'overview':
        SX::object('Manufacturer')->show(Arr::getRequest('q'));
        break;

    case 'showdetails':
        SX::object('Manufacturer')->get(Arr::getRequest('id'));
        break;

    case 'updatehitcount':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Manufacturer')->update(Arr::getRequest('id'));
        break;

    case 'quicksearch':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Manufacturer')->search(Arr::getRequest('q'));
        break;
}
