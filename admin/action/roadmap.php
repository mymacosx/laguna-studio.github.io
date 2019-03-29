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
if (!get_active('roadmap')) {
    SX::object('Core')->notActive();
}
if (!permission('roadmaps')) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    case 'display':
        SX::object('Roadmap')->get(Arr::getRequest('rid'), Arr::getRequest('closed'));
        break;

    default:
        SX::object('Roadmap')->show();
        break;
}
