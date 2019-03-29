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
if (!get_active('cheats')) {
    SX::object('Core')->notActive();
}
if (!permission('cheats')) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    default:
        SX::object('Cheats')->show();
        break;

    case 'getfile':
        SX::object('Cheats')->file(Arr::getRequest('id'));
        break;

    case 'showcheat':
        SX::object('Cheats')->get(Arr::getRequest('id'));
        break;

    case 'search':
        SX::object('Cheats')->search(Arr::getRequest('ql'));
        break;

    case 'brokenlink':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Cheats')->deadlink(Arr::getRequest('id'));
        break;
}
