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

if (!perm('seo')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'description':
        SX::object('AdminSeo')->showKey();
        break;

    case 'edit_d':
        SX::object('AdminSeo')->editKey(Arr::getRequest('id'), Arr::getRequest('text_edit'));
        break;

    case 'new_d':
        SX::object('AdminSeo')->addKey(Arr::getRequest('text'));
        break;

    case 'del_d':
        SX::object('AdminSeo')->deleteKey(Arr::getRequest('id'));
        break;

    case 'delall_d':
        SX::object('AdminSeo')->cleanKey();
        break;

    case 'aktiv_d':
        SX::object('AdminSeo')->activeKey(Arr::getRequest('type'), Arr::getRequest('id'));
        break;

    case 'edit_show_d':
        SX::object('AdminSeo')->getKey(Arr::getRequest('id'));
        break;

    case 'import_d':
        SX::object('AdminSeo')->importKey();
        break;

    case 'export_d':
        SX::object('AdminSeo')->exportKey();
        break;

    case 'send_p':
        SX::object('AdminSeo')->sendPing(Arr::getRequest('name_p'), Arr::getRequest('link_p'));
        break;

    case 'edit_p':
        SX::object('AdminSeo')->editPing(Arr::getRequest('id'), Arr::getRequest('text_edit'));
        break;

    case 'new_p':
        SX::object('AdminSeo')->addPing(Arr::getRequest('text'));
        break;

    case 'del_p':
        SX::object('AdminSeo')->deletePing(Arr::getRequest('id'));
        break;

    case 'delall_p':
        SX::object('AdminSeo')->cleanPing();
        break;

    case 'aktiv_p':
        SX::object('AdminSeo')->activePing(Arr::getRequest('type'), Arr::getRequest('id'));
        break;

    case 'edit_show_p':
        SX::object('AdminSeo')->getPing(Arr::getRequest('id'));
        break;

    case 'ping':
        SX::object('AdminSeo')->showPing();
        break;

    case 'sitemap':
        SX::object('AdminSeo')->showSitemap();
        break;

    case 'sitemap_save':
        SX::object('AdminSeo')->saveSitemap();
        break;

    case 'sitemap_archive':
        SX::object('AdminSeo')->startSitemap('1');
        break;

    case 'seotags':
        SX::object('AdminSeo')->showTags();
        break;

    case 'add_seotags':
        SX::object('AdminSeo')->addTags();
        break;

    case 'edit_seotags':
        SX::object('AdminSeo')->editTags(Arr::getRequest('id'));
        break;

    case 'del_seotags':
        SX::object('AdminSeo')->deleteTags(Arr::getRequest('id'));
        break;

    case 'aktiv_seotags':
        SX::object('AdminSeo')->activeTags(Arr::getRequest('id'));
        break;

    case 'del_all_seotags':
        SX::object('AdminSeo')->cleanTags();
        break;
}
