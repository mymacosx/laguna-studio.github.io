<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################

error_reporting(0);
if (!defined('SX_DIR')) {
    define('SX_DIR', realpath(dirname(dirname(__FILE__))));
    require_once SX_DIR . '/class/class.SX.php'; // Подключаем основной класс системы
    SX::preload('user');                         // Инициализируем систему
    SX::setLocale($_SESSION['lang']);            // Устанавливаем локаль PHP
}

header('Content-type: text/html; charset=' . CHARSET);
SX::setDefine('AJAX_OUTPUT', 1);

switch (Arr::getRequest('action')) {
    case 'shop';
        $value = NULL;
        $object = Text::get();
        $query = urldecode($_REQUEST['q']);
        if (!empty($query) && $object->strlen($query) >= 2) {
            $LC = intval(Arr::getSession('Langcode', 1));
            $group = $_SESSION['user_group'];

            $like = DB::get()->escape($query);
            $result = DB::get()->query("SELECT
	    a.Titel_{$LC} AS Name,
	    a.Artikelnummer
	FROM
	    " . PREFIX . "_shop_produkte AS a,
	    " . PREFIX . "_shop_kategorie AS b
	WHERE
            a.Kategorie = b.Id
	AND
	    b.Aktiv = '1'
	AND
	    b.Search = '1'
	AND
	    (a.Titel_{$LC} LIKE '%" . $like . "%' OR a.Artikelnummer LIKE '%" . $like . "%')
	AND
	    a.Aktiv = '1'
	AND
	    (a.Gruppen = '' OR a.Gruppen LIKE '%,$group' OR a.Gruppen LIKE '$group,%' OR a.Gruppen LIKE '%,$group,%' OR a.Gruppen = '$group')
	ORDER BY a.Titel_{$LC} ASC LIMIT 50");
            while ($row = $result->fetch_assoc()) {
                if ($object->stripos($row['Name'], $query) !== false) {
                    $value .= '"' . sanitize($row['Name']) . '"' . PE;
                }
                if ($object->stripos($row['Artikelnummer'], $query) !== false) {
                    $value .= '"' . sanitize($row['Artikelnummer']) . '"' . PE;
                }
            }
            $result->close();
        }
        SX::output($value, true);
        break;

    case 'other';
        // Еще какой нить аякс
        break;
}
