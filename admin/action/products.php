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
if (!get_active('products')) {
    SX::object('Core')->notActive();
}
if (!permission('products')) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    default:
    case 'overview':
        SX::object('Products')->show();
        break;

    case 'showproduct':
        SX::object('Products')->get(Arr::getRequest('id'));
        break;

    case 'rate':
        break;

    case 'quicksearch':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Products')->search(Arr::getRequest('q'));
        break;
}
