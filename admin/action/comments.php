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

SX::setDefine('AJAX_OUTPUT', 1);
switch (Arr::getRequest('action')) {
    case 'change':
        SX::object('Comments')->change(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('Comments')->delete(Arr::getRequest('id'));
        break;
}
