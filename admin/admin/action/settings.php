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

if (!perm('settings')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'global':
        SX::object('AdminSettings')->settingsSystem();
        break;

    case 'secure':
        SX::object('AdminSettings')->settingsSecure();
        break;

    case 'sectionnew':
        SX::object('AdminSettings')->addSection(Arr::getPost('Name'));
        break;

    case 'sectionsettings':
        SX::object('AdminSettings')->settingsSection();
        break;

    case 'widgets':
        SX::object('AdminSettings')->settingsWidgets();
        break;

    case 'widgetedit':
        SX::object('AdminSettings')->editWidget(Arr::getRequest('widget_id'));
        break;

    case 'widgetdel':
        SX::object('AdminSettings')->delWidget(Arr::getRequest('widget_id'));
        break;

    case 'widgetinstall':
        SX::object('AdminSettings')->installWidget(Arr::getRequest('widget'));
        break;

    case 'moduldel':
        SX::object('AdminSettings')->delModul(Arr::getRequest('name'));
        break;

    case 'modulinstall':
        SX::object('AdminSettings')->installModul(Arr::getRequest('modul'));
        break;

    case 'emailcheck':
        SX::object('AdminSettings')->emailCheck();
        break;

    case 'delsection':
        SX::object('AdminSettings')->deleteSection(Arr::getGet('id'));
        break;

    case 'checkcsspath':
        $_REQUEST['noout'] = 1;
        SX::output((is_dir(SX_DIR . '/theme/' . Arr::getRequest('tcs') . '/css/' . Arr::getRequest('CSS_Theme'))) ? 'true' : 'false');
        break;

    case 'sectionsdisplay':
        SX::object('AdminSettings')->showSection(Arr::getRequest('section'));
        break;

    case 'admin_global':
        SX::object('AdminSettings')->settingsAdmin();
        break;

    case 'languages':
        SX::object('AdminSettings')->languages();
        break;

    case 'adminlanguages':
        SX::object('AdminSettings')->languages('sprachen_admin', 'adminlanguages');
        break;

    case 'logs':
        SX::object('AdminSettings')->logs();
        break;

    case 'money':
        SX::object('AdminSettings')->money();
        break;

    case 'phpedit':
        SX::object('AdminPhpEdit')->get();
        break;

    case 'htaccess':
        SX::object('AdminHtaccess')->get();
        break;

    case 'lang_edit':
        SX::object('AdminLangEdit')->get();
        break;

    case 'cron':
        SX::object('AdminCron')->get();
        break;

}
