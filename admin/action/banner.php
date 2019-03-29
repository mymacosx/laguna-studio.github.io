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
if (!get_active('links')) {
    SX::object('Core')->notActive();
}
if (!permission('links')) {
    SX::object('Core')->noAccess();
}

SX::setDefine('AJAX_OUTPUT', 1);
SX::object('Banner')->click(Arr::getRequest('click'));
