<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
error_reporting(0);
define('SX_DIR', realpath(dirname(dirname(__FILE__))));
define('PE', PHP_EOL);

require_once SX_DIR . '/setup/class/class.SX.php';
SX::getInit();

$_REQUEST['step'] = isset($_REQUEST['step']) ? $_REQUEST['step'] : '';
switch ($_REQUEST['step']) {
    default:
        Setup::get();
        break;

    case '1':
        Setup::getStep1();
        break;

    case '2':
        Setup::getStep2();
        break;

    case '3':
        Setup::getStep3();
        break;

    case '4':
        Setup::getStep4();
        break;
}
