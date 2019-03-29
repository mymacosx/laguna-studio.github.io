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
if (!get_active('faq')) {
    SX::object('Core')->notActive();
}
if (!permission('faq')) {
    SX::object('Core')->noAccess();
}
$_REQUEST['faq_id'] = isset($_REQUEST['faq_id']) ? intval(Arr::getRequest('faq_id')) : '';

switch (Arr::getRequest('action')) {
    default:
        SX::object('Faq')->showcategs();
        break;

    case 'display':
        SX::object('Faq')->show();
        break;

    case 'mail':
        SX::object('Faq')->mail();
        break;

    case 'faq':
        SX::object('Faq')->get(Arr::getRequest('fid'));
        break;
}
