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

switch (Arr::getRequest('sub')) {
    case 'send_error':
        SX::object('AdminSettings')->error(Arr::getRequest('id'));
        break;

    case 'send_order':
        SX::object('AdminSettings')->order();
        break;
}
