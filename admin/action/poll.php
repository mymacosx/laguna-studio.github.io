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
if (!get_active('poll')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    default:
        SX::object('Poll')->current(Arr::getRequest('id'));
        break;

    case 'archive':
        SX::object('Poll')->archive();
        break;

    case 'smallpoll':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Poll')->result(Arr::getRequest('polloption'), 0, Arr::getRequest('intern'));
        break;
}
