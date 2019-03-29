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
if (!get_active('guestbook')) {
    SX::object('Core')->notActive();
}
if (!permission('guestbook')) {
    SX::object('Core')->noAccess();
}
$_REQUEST['action'] = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
SX::object('Guestbook')->get();
