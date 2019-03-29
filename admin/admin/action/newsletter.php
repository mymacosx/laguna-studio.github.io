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

if (!perm('newsletter') || !admin_active('newsletter')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'new':
        if (!perm('newslettersend')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminNewsletter')->add();
        break;

    case 'showabos':
        SX::object('AdminNewsletter')->subscribers();
        break;

    case 'deletecateg':
        SX::object('AdminNewsletter')->delCateg(Arr::getRequest('id'));
        break;

    case 'categs':
        SX::object('AdminNewsletter')->getCategs();
        break;

    case 'archive':
        SX::object('AdminNewsletter')->archive();
        break;

    case 'view':
        SX::object('AdminNewsletter')->show(Arr::getRequest('id'));
        break;

    case 'getattachment':
        if (!perm('newsletter_attachdownload')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminNewsletter')->attachment(Arr::getRequest('att'));
        break;
}
