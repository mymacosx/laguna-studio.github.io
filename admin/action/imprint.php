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
$CS = View::get();
$contact = SX::get('system');
$link = SHEME_URL . $_SERVER['HTTP_HOST'];
$array = array(
    '%%COMPANY%%'  => $contact['Firma'],
    '%%TOWN%%'     => $contact['Stadt'],
    '%%ZIP%%'      => $contact['Zip'],
    '%%ADRESS%%'   => $contact['Strasse'],
    '%%MAIL%%'     => '<a href="mailto:' . $contact['Mail_Absender'] . '">' . $contact['Mail_Absender'] . '</a>',
    '%%TELEFON%%'  => '<a href="tel:' . preg_replace('/[^\d\+]/', '', $contact['Telefon']) . '">' . $contact['Telefon'] . '</a>',
    '%%FAX%%'      => $contact['Fax'],
    '%%HTTP%%'     => '<a href="' . $link . '">' . $link . '</a>',
    '%%INN%%'      => $contact['Inn'],
    '%%KPP%%'      => $contact['Kpp'],
    '%%BIK%%'      => $contact['Bik'],
    '%%BANK%%'     => $contact['Bank'],
    '%%KSCHET%%'   => $contact['Kschet'],
    '%%RSCHET%%'   => $contact['Rschet'],
    '%%DIREKTOR%%' => $contact['Seitenbetreiber'],
    '%%BUH%%'      => $contact['Buh']);
$Imprint = Text::get()->replace($contact['Impressum'], $array);
$CS->assign('Imprint', $Imprint);

$seo_array = array(
    'headernav' => SX::$lang['Imprint'],
    'pagetitle' => SX::$lang['Imprint'],
    'content'   => View::get()->fetch(THEME . '/other/imprint.tpl'));
View::get()->finish($seo_array);
