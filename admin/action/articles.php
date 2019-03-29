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
if (!permission('articles')) {
    SX::object('Core')->noAccess();
}
if (!get_active('articles')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    default:
        SX::object('Articles')->all();
        break;

    case 'displayarticle':
        SX::object('Articles')->get(Arr::getRequest('id'));
        break;
}
