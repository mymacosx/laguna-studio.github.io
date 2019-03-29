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

if (!perm('mediapool')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminMedia')->showVideo();
        break;

    case 'new':
        SX::object('AdminMedia')->addVideo();
        break;

    case 'view':
        SX::object('AdminMedia')->editVideo(Arr::getGet('id'));
        break;

    case 'videoupload':
        $options = array(
            'type'   => 'video',
            'result' => 'ajax',
            'upload' => '/uploads/videos/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'audio_overview':
        SX::object('AdminMedia')->showAudio();
        break;

    case 'audio_new':
        SX::object('AdminMedia')->addAudio();
        break;

    case 'audio_view':
        SX::object('AdminMedia')->editAudio(Arr::getGet('id'));
        break;

    case 'audioupload':
        $options = array(
            'type'   => 'audio',
            'result' => 'ajax',
            'upload' => '/uploads/audios/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;
}
