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

switch (Arr::getRequest('action')) {
    default:
        SX::object('Links')->categs();
        break;

    case 'search':
        SX::object('Links')->search(Arr::getRequest('ql'));
        break;

    case 'brokenlink':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Links')->deadlink(Arr::getRequest('id'));
        break;

    case 'showdetails':
        SX::object('Links')->get(Arr::getRequest('id'));
        break;

    case 'updatehitcount':
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::object('Links')->update(Arr::getRequest('id'));
        break;

    case 'links_sent':
        SX::object('Links')->send();
        break;

    case 'uploadicon':
        if (permission('links_sent') && Arr::getSession('user_group') != '2') {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/links/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => 100,
            );
            SX::object('Upload')->load($options);
        }
        break;
}
