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

if (!get_active('calendar')) {
    SX::object('Core')->notActive();
}
SX::object('Calendar')->defParam();

switch (Arr::getRequest('action')) {
    default:
        SX::object('Calendar')->load();
        break;

    case 'week':
        if (!is_numeric(Arr::getRequest('weekstart', 0)) || !is_numeric(Arr::getRequest('weekend', 0))) {
            SX::object('Core')->message('Error', 'Error_notFound', BASE_URL . '/index.php?p=calendar&area=' . $_SESSION['area'], 5);
        }
        SX::object('Calendar')->week();
        break;

    case 'displayyear':
        SX::object('Calendar')->displayyear();
        break;

    case 'newevent':
        if (!permission('calendar_event_new') || !permission('calendar_event') || Arr::getSession('user_group') == 2) {
            SX::object('Core')->noAccess();
        }
        SX::object('Calendar')->newevent();
        break;

    case 'insertevent':
        if (!permission('calendar_event') || Arr::getSession('user_group') == 2) {
            SX::object('Core')->noAccess();
        }
        SX::object('Calendar')->insertevent();
        break;

    case 'events':
        if (Arr::getSession('user_group') == 2 && Arr::getRequest('show') == 'private') {
            SX::object('Core')->noAccess();
        }
        SX::object('Calendar')->events();
        break;

    case 'delevent':
        SX::object('Calendar')->delevent();
        break;

    case 'editevent':
        SX::object('Calendar')->editevent();
        break;

    case 'birthdays':
        SX::object('Calendar')->birthdays();
        break;

    case 'search':
        SX::object('Calendar')->search(Arr::getRequest('qc'));
        break;

    case 'myevents':
        if (Arr::getSession('user_group') == 2) {
            SX::object('Core')->noAccess();
        }
        SX::object('Calendar')->myevents();
        break;

    case 'switch':
        SX::object('Calendar')->switches();
        break;
}
