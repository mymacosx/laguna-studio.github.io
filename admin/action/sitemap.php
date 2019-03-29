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

switch (Arr::getRequest('action')) {
    default:
        $seo_array = array(
            'headernav' => SX::$lang['Sitemap'],
            'pagetitle' => SX::$lang['Sitemap'],
            'content'   => SX::object('Navigation')->sitemap());
        View::get()->finish($seo_array);
        break;

    case 'full':
        SX::object('Navigation')->fullmap();
        break;
}
