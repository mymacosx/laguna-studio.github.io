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

define('NOLOGGED', true);
ignore_user_abort(true);
set_time_limit(600);

$DB = DB::get();

$DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_collection` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(100) NOT NULL,
  `Text1` text,
  `Text2` text,
  `Text3` text,
  `Marker` VARCHAR(150) NOT NULL DEFAULT '',
  `Active` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sprachen` ADD `Locale` VARCHAR(15) NOT NULL DEFAULT ''");
$DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sprachen_admin` ADD `Locale` VARCHAR(15) NOT NULL DEFAULT ''");

$locale = array(
    'ru' => 'ru_RU',
    'en' => 'en_US',
    'de' => 'de_DE',
    'es' => 'es_ES',
    'fi' => 'fi_FI',
    'fr' => 'fr_FR',
    'tr' => 'tr_TR',
);
$sql = $DB->query("SELECT `Id`, `Sprachcode`, `Locale` FROM `" . PREFIX . "_sprachen`");
while ($row = $sql->fetch_assoc()) {
    if (empty($row['Locale']) && isset($locale[$row['Sprachcode']])) {
        $DB->query("UPDATE `" . PREFIX . "_sprachen` SET `Locale` = '" . $locale[$row['Sprachcode']] . "' WHERE `Id` = '" . $row['Id'] . "'");
    }
}
$sql = $DB->query("SELECT `Id`, `Sprachcode`, `Locale` FROM `" . PREFIX . "_sprachen_admin`");
while ($row = $sql->fetch_assoc()) {
    if (empty($row['Locale']) && isset($locale[$row['Sprachcode']])) {
        $DB->query("UPDATE `" . PREFIX . "_sprachen_admin` SET `Locale` = '" . $locale[$row['Sprachcode']] . "' WHERE `Id` = '" . $row['Id'] . "'");
    }
}

switch (Arr::getRequest('repair')) {
// /admin/index.php?do=update&repair=correct

    case 'correct': // Корректирум поля в базе под UTF
        /* Метод перекодировки из UTF-8 в windows-1251 */
        function encodeUtf(&$value) {
            if (!preg_match('//u', $value)) {
                $value = iconv('windows-1251', 'UTF-8', $value);
            }
        }

        function convertUtf($value) {
            $result = $value;
            if (unserialize($value) === false) {
                $result = '';
                $value = iconv('UTF-8', 'windows-1251', $value);
                $value = unserialize($value);
                if ($value !== false) {
                    array_walk_recursive($value, 'encodeUtf');
                    $value = serialize($value);
                    if ($value !== false) {
                        $result = $value;
                    }
                }
            }
            return $result;
        }

        $array = array(
            'artikel'  => 'Textbilder_',
            'content'  => 'Textbilder',
            'faq'      => 'Textbilder_',
            'news'     => 'Textbilder',
            'produkte' => 'Textbilder'
        );
        foreach ($array as $table => $field) {
            $sql = $DB->query("SELECT * FROM `" . PREFIX . "_" . $table . "`");
            while ($row = $sql->fetch_assoc()) {
                $update = array();
                for ($i = 1; $i <= 3; $i++) {
                    if (!empty($row[$field . $i])) {
                        $update[] = "`" . $field . $i . "` = '" . $DB->escape(convertUtf($row[$field . $i])) . "' ";
                    }
                }

                if (!empty($update)) {
                    $DB->query("UPDATE `" . PREFIX . "_" . $table . "` SET " . implode(',', $update) . " WHERE `Id` = '" . $row['Id'] . "'");
                }
            }
        }

        $sql = $DB->query("SELECT * FROM `" . PREFIX . "_shop_bestellungen`");
        while ($row = $sql->fetch_assoc()) {
            $update = array();
            if (!empty($row['Artikel'])) {
                $update[] = "`Artikel` = '" . $DB->escape(convertUtf($row['Artikel'])) . "' ";
            }

            if (!empty($row['Bestellung'])) {
                $row['Bestellung'] = base64_decode($row['Bestellung']);
                encodeUtf($row['Bestellung']);
                $row['Bestellung'] = base64_encode($row['Bestellung']);
                $update[] = "`Bestellung` = '" . $DB->escape($row['Bestellung']) . "' ";
            }

            if (!empty($update)) {
                $DB->query("UPDATE `" . PREFIX . "_shop_bestellungen` SET " . implode(',', $update) . " WHERE `Id` = '" . $row['Id'] . "'");
            }
        }

        $sql = $DB->query("SELECT `Id`, `Settings` FROM `" . PREFIX . "_bereiche`");
        while ($row = $sql->fetch_assoc()) {
            if (!empty($row['Settings'])) {
                $DB->query("UPDATE `" . PREFIX . "_bereiche` SET `Settings` = '" . $DB->escape(convertUtf($row['Settings'])) . "' WHERE `Id` = '" . $row['Id'] . "'");
            }
        }

        $DB->query("DELETE FROM " . PREFIX . "_schedule WHERE Modul='ping'");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_antivirus`");

        Tool::cleanTable('shop_warenkorb_gaeste');
        Tool::cleanTable('shop_warenkorb');
        Tool::cleanTable('shop_merkzettel');
        Tool::cleanTable('sessions');
        break;

    case '1.05':  // апдейт для версии младше 1.06
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_settings` DROP COLUMN `Type`");
        $DB->query("DELETE FROM `" . PREFIX . "_settings` WHERE Modul = 'admin' AND Name = 'Admin_Corners'");
        $DB->query("DELETE FROM `" . PREFIX . "_settings` WHERE Modul = 'system' AND Name = 'RoundeCorners'");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_cheaper', 'shop', 'cheaper', '1')");

        $DB->query("RENAME TABLE `" . PREFIX . "_codeblock` TO `" . PREFIX . "_codewidget`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` CHANGE `Type` `Type` ENUM('modul','extmodul','widget') NOT NULL DEFAULT 'modul'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` CHANGE `Settings` `Settings` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` ADD `Result` varchar(100) NOT NULL DEFAULT ''");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_update', 'system', 'Update', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('galerie_watermark_position', 'galerie', 'Watermark_Position', 'bottom_right')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('galerie_watermark_file', 'galerie', 'Watermark_File', 'watermark.png')");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` DROP COLUMN `GruppeMisc`");

        $DB->query("UPDATE " . PREFIX . "_shop SET shop_wasserzeichen_position = 'bottom_right'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` DROP INDEX `Name`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` ADD UNIQUE `Name` (`Name`)");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_kategorie` ADD `Gruppen` varchar(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Payment` tinyint(1) DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_gutscheine` ADD `Hersteller` text");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_active', 'secure', 'active', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_gd', 'secure', 'gd', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_ttf_font', 'secure', 'ttf_font', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_max_calc1', 'secure', 'max_calc1', '9')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_max_calc2', 'secure', 'max_calc2', '9')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_min_text', 'secure', 'min_text', '3')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_max_text', 'secure', 'max_text', '4')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_type', 'secure', 'type', 'auto')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('secure_text', 'secure', 'text', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('forum_nofollow', 'forum', 'nofollow', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('forum_compres', 'forum', 'compres', '80')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('forum_size', 'forum', 'size', '80')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('news_compres', 'news', 'compres', '80')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('news_size', 'news', 'size', '80')");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_usergallery', 'users', 'UserGallery', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitalbom', 'users', 'LimitAlbom', '10')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitfotos', 'users', 'LimitFotos', '20')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitfotosstr', 'users', 'LimitFotosStr', '8')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_widthfotos', 'users', 'WidthFotos', '600')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_userfriends', 'users', 'UserFriends', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitfriends', 'users', 'LimitFriends', '8')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitfriendsstr', 'users', 'LimitFriendsStr', '8')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_avatarfriends', 'users', 'AvatarFriends', '60')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_imagecompres', 'users', 'ImageCompres', '80')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_useractions', 'users', 'UserActions', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitactions', 'users', 'LimitActions', '8')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_uservisits', 'users', 'UserVisits', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_limitvisits', 'users', 'LimitVisits', '8')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_avatarwidth', 'users', 'AvatarWidth', '100')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('users_avatarcompres', 'users', 'AvatarCompres', '80')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_all', 'rss', 'all', '10')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_all_typ', 'rss', 'all_typ', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_news', 'rss', 'news', '15')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_news_typ', 'rss', 'news_typ', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_articles', 'rss', 'articles', '15')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_articles_typ', 'rss', 'articles_typ', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_forum', 'rss', 'forum', '15')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('rss_forum_typ', 'rss', 'forum_typ', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_auto', 'htaccess', 'auto', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_www', 'htaccess', 'www', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_lich', 'htaccess', 'lich', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_exts', 'htaccess', 'exts', 'gif|jpg|jpeg|bmp|png|swf|flv|mp3')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_expires', 'htaccess', 'expires', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_headers', 'htaccess', 'headers', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('htaccess_rewrite', 'htaccess', 'rewrite', '1')");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_allowed', 'system', 'allowed', '<br><br />')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_сomb_js', 'system', 'сomb_js', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_сomb_css', 'system', 'сomb_css', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_shipping_info', 'shop', 'shipping_info', '1')");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_laender` DROP COLUMN `KeineUstFirma`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_laender` ADD UNIQUE `Code` (`Code`)");

        $string = "'system_upd_sys', 'system_htaccess_auto', 'system_usergallery', 'system_limitalbom', 'system_limitfotos', 'system_limitfotosstr', 'system_widthfotos', 'system_userfriends', 'system_limitfriends', 'system_limitfriendsstr', 'system_avatarfriends', 'system_useractions', 'system_uservisits', 'system_limitvisits', 'system_sicherheitscode', 'system_limitactions'";
        $DB->query("DELETE FROM `" . PREFIX . "_settings` WHERE Id IN($string)");
        break;

    case 'birthdays': // Нормализуем даты рождения пользователей
        $sql = $DB->query("SELECT `Id`, `Geburtstag` FROM `" . PREFIX . "_benutzer`");
        while ($row = $sql->fetch_assoc()) {
            $DB->query("UPDATE `" . PREFIX . "_benutzer` SET `Geburtstag` = '" . Tool::formatDate($row['Geburtstag']) . "' WHERE `Id` = '" . $row['Id'] . "'");
        }
        break;

    case 'payment': // Восстанавливаем методы оплаты
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_shop_zahlungsmethoden`");
        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_zahlungsmethoden` (
        `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `Aktiv` tinyint(1) unsigned DEFAULT '1',
        `Name_1` varchar(100) DEFAULT NULL,
        `Name_2` varchar(100) NOT NULL,
        `Name_3` varchar(100) NOT NULL,
        `Beschreibung_1` text,
        `Beschreibung_2` text,
        `Beschreibung_3` text,
        `BeschreibungLang_1` text,
        `BeschreibungLang_2` text,
        `BeschreibungLang_3` text,
        `disabled_noweight` tinyint(1) unsigned DEFAULT '0',
        `Betreff` varchar(255) DEFAULT NULL,
        `Testmodus` varchar(45) DEFAULT '0',
        `Install_Id` varchar(255) DEFAULT NULL,
        `KostenOperant` enum('-','+') NOT NULL DEFAULT '-',
        `Kosten` decimal(8,2) unsigned DEFAULT '0.00',
        `KostenTyp` enum('wert','pro') DEFAULT 'wert',
        `Gruppen` varchar(255) NOT NULL DEFAULT '1,2,3,4,5,6',
        `Laender` text,
        `Versandarten` varchar(255) NOT NULL DEFAULT '1,2,3,4,5,6',
        `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
        `DetailInfo` text,
        `Icon` varchar(200) DEFAULT NULL,
        `ZTyp` enum('Sys','Eigen') NOT NULL DEFAULT 'Sys',
        `MaxWert` decimal(8,2) NOT NULL DEFAULT '0.00',
        PRIMARY KEY (`Id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (1,1,'Безналичный расчет для юридических лиц','','','Безналичный расчет для юридических лиц','','','<p>Оплата заказов клиентами - юридическими лицами возможна только по безналичному расчету. После оформления заказа будет автоматически сформирован счет на оплату, который Вы можете распечатать и оплатить.</p>\r\n<p>Все необходимые для бухгалтерии документы (оригинал счета на оплату, счет-фактура, накладная) выдаются вместе с заказом при получении.<br />\r\n&nbsp;</p>\r\n<h4>Внимание!</h4>\r\n<p>В связи с требованиями законодательства оплата не может быть принята, если:<br />\r\n&nbsp;</p>\r\n<ol>\r\n    <li>Заказ оформлен под регистрацией физического лица, а оплачивает юридическое лицо.</li>\r\n    <li>Заказ оформлен под регистрацией юридического лица &quot;А&quot;, а оплату производит юридическое лицо &quot;В&quot;.</li>\r\n    <li>Заказ оформлен под регистрацией юридического лица, а оплату производит физическое лицо.</li>\r\n</ol>\r\n<p>В указанных случаях заказ не будет принят в обработку.</p>','','',0,'','','','+',0,'pro','7,1,2,4,5,3,6','AZ,BE,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',2,'<strong>Здесь типа банковские реквизиты</strong><br />\r\n&nbsp;__ORDER__','beznal.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (2,1,'Оплата наличными','','','<p>Оплата наличными</p>','','','<div style=\"text-align: justify\">Оплата заказа наличными при его получении. Вместе с заказом Вы получаете товарный чек, содержащий все товарные позиции Вашего заказа.</div>','','',1,'','','','+',0,'wert','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',1,'<br />','nal.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (3,0,'Способ 1','','','','','','','','',1,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',3,'<br />','','Eigen',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (4,0,'Способ 2','','','','','','','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',4,'<br />','','Eigen',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (5,0,'Способ 3','','','','','','','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',5,'<br />','','Eigen',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (6,0,'Способ 4','','','','','','','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',6,'<br />','','Eigen',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (7,1,'Оплата через СБЕРБАНК, форма ПД-4','','','Оплата через <strong>СБЕРБАНК</strong>, форма ПД-4<br />','','','<p>Бланк квитанции - извещения по форме ПД-4 используется для осуществления платежей государственных пошлин, штрафов в ГИБДД или безналичной оплаты товаров и услуг.</p>\r\n<p>Порядок и условия осуществления структурными подразделениями Сбербанка России переводов денежных средств (платежей) по поручению физических лиц без открытия банковских счетов в валюте Российской Федерации</p>\r\n<p>Руководствуясь Гражданским кодексом Российской Федерации, Федеральным законом &laquo;О банках и банковской деятельности&raquo;, Уставом Сбербанка России и Генеральной лицензией на осуществление банковских операций № 1481, выданной 03.10.2002г. Центральным банком Российской Федерации, структурные подразделения Сбербанка России осуществляют прием платежей клиентов-физических лиц наличными деньгами в валюте Российской Федерации для перечисления на счета юридических лиц в следующем порядке и на следующих условиях:</p>\r\n<ol>\r\n    <li>Прием платежей осуществляется при условии предъявления клиентами-физическими лицами платежных документов с заполненными реквизитами, необходимыми для перечисления платежей по назначению. Платежные документы заполняются с применением средств оргтехники, электронно-вычислительных машин или от руки ручкой с пастой или чернилами черного, синего или фиолетового цвета.</li>\r\n    <li>Прием банком платежей наличными деньгами осуществляется только при предъявлении документа, удостоверяющего личность, за исключением следующих платежей, если их сумма не превышает 30 000 рублей:\r\n    <ul>\r\n        <li>связанных с расчетами с бюджетами всех уровней бюджетной системы Российской Федерации (включая предусмотренные законодательством Российской Федерации о налогах и сборах федеральные, региональные и местные налоги и сборы, а также пени и штрафы);</li>\r\n        <li>связанных с оплатой услуг, оказываемых бюджетными учреждениями, находящимися в ведении федеральных органов исполнительной власти, органов исполнительной власти субъектов Российской Федерации и органов местного самоуправления;</li>\r\n        <li>связанных с осуществлением платы за жилое помещение, коммунальные услуги, с оплатой услуг по охране квартир и установке охранной сигнализации, а также с осуществлением платежей за услуги связи;</li>\r\n        <li>связанных с уплатой взносов членами садоводческих, огороднических, дачных некоммерческих объединений граждан, гаражно-строительных кооперативов, оплатой услуг платных автомобильных стоянок;</li>\r\n        <li>связанных с уплатой алиментов.</li>\r\n    </ul>\r\n    </li>\r\n    <li>В подтверждении приема платежа клиентам-физическим лицам выдаются квитанции платежных документов.</li>\r\n    <li>Перечисление принятых от клиентов-физических лиц сумм платежей юридическим лицам производится в сроки, установленные договорами, заключенными с юридическими лицами, либо законодательством Российской Федерации.</li>\r\n    <li>Прием платежей клиентов-физических лиц в пользу юридических лиц, с которыми заключены договоры на прием платежей, осуществляется в соответствии с условиями договоров и настоящими условиями. При отсутствии договоров- в соответствии с настоящими условиями с взиманием платы, установленной Сборником тарифов на услуги, предоставляемые Сбербанком России, на день оказания услуги.</li>\r\n    <li>Прием платежей, при отсутствии в платежных документах реквизитов, необходимых для перечисления платежей по назначению, либо в случае отсутствия у клиентов-физических лиц денежной наличности в сумме, указанной в платежных документах, не производится.</li>\r\n    <li>По просьбе клиентов-физических лиц в течение трех лет с даты приема платежей выдаются справки о произведенных платежах и датах их перечисления в адрес юридических лиц на основании предъявленных клиентами-физическими лицами платежных документов об оплате. Данная услуга оказывается физическим лицам на условиях, предусмотренных Сборником тарифов на услуги, предоставляемые Сбербанком России, на день оказания услуги.</li>\r\n</ol>\r\n<br />\r\n<p>Порядок и условия осуществления структурными подразделениями Сбербанка России переводов денежных средств (платежей) по поручению клиентов-физических лиц без открытия банковских счетов в валюте Российской Федерации считаются принятыми клиентами-физическими лицами при подписании ими платежных документов на перечисление денежных средств.</p>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',7,'<br />','sberbank.jpg','Eigen',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (8,1,'Оплата через систему INTERKASSA','','','Оплата через систему <strong>INTERKASSA<br />\r\n</strong>','','','Система приема платежей <strong><a href=\"http://www.interkassa.com/\">INTERKASSA</a></strong> представляет собой универсальный аппаратно-программный комплекс, посредством которого, осуществляется обработка операций от основных платежных интернет-систем.','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',8,'<br />','interkassa.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (9,1,'Оплата через систему WebMoney','','','<div style=\"text-align: justify\">Оплата через систему <strong>WebMoney</strong></div>','','','<div style=\"text-align: justify\">Учётная система <strong><a href=\"http://www.webmoney.ru/\">WebMoney</a> </strong>обеспечивает проведение расчётов в реальном времени посредством учётных единиц &mdash; титульных знаков WebMoney (WM-units). Система является не банковской. Управление движением титульных знаков осуществляется пользователями с помощью клиентской программы WM Keeper или с помощью веб-интерфейса (WM Keeper Light).</div>','','',0,'','3','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',9,'<br />','webmoney.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (10,1,'Оплата через систему PayPal','','','<div style=\"text-align: justify\">Оплата через систему <strong>PayPal<br />\r\n</strong>&nbsp;</div>','','','<p style=\"text-align: justify\"><strong><a href=\"https://www.paypal.com/\">PayPal</a></strong> &mdash; крупнейшая в мире дебетовая электронная платёжная система. В настоящее время PayPal работает в 190 странах и имеет более 164 миллионов зарегистрированных пользователей. PayPal работает с 18 национальными валютами. C октября 2002 года является подразделением компании eBay.<br />\r\n<br />\r\nПлатежи осуществляются через защищённое соединение после введения e-mail и пароля, указанных после подтверждения аккаунта. В понятие аккаунт входит адрес, по которому будут доставляться покупки. Пользователи PayPal могут переводить деньги друг другу.</p>\r\n<p style=\"text-align: justify\">Подтверждение аккаунта включает в себя процедуру снятия денег с карты пользователя с указанием кода, который необходимо сообщить PayPal, что подтверждает идентичность владельца карты, имеющего доступ к истории платежей, личности, вводящей пароль и остальные данные в систему Paypal.</p>\r\n<p style=\"text-align: justify\">В 2007 году был открыт доступ в эту систему и для жителей стран СНГ (без права приёма платежей). До 2005 года при обнаружении таких аккаунтов счета обычно замораживались. Разницы между статусами в странах СНГ, в отличие от большинства других стран, нет. С ноября 2008 года в PayPal появился русскоязычный интерфейс.</p>\r\n<p style=\"text-align: justify\">Использование системы PayPal осуществляется на бесплатной основе: регистрация в системе бесплатна, за отправление денежных средств комиссия с пользователя не снимается, за исключением привилегированных статусов (Premier и Business). Комиссия взимается с получателя платежа, размер комиссии зависит от местоположения страны пользователя и статуса.</p>','','',0,'','','','-',0,'wert','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',10,'<br />','paypal.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (11,1,'Оплата через систему Moneybookers','','','<div style=\"text-align: justify\">Оплата через систему <strong>Moneybookers</strong></div>','','','<div style=\"text-align: justify\"><strong><a href=\"http://www.moneybookers.com/\">Moneybookers</a></strong> &mdash; компания денежных переводов, которая позволяет посылать и получать деньги через электронную почту. Основана 18 июня 2001 года в Лондоне.<br />\r\n<br />\r\nПользователи могут послать деньги с кредитной или дебетовой карты и переводить деньги со счета в банке в большинстве стран &mdash; членов Организации экономического сотрудничества и развития. Moneybookers почти бесплатен для участников с личными счетами (перевод между участниками стоит максимум 0,5 евро). Деловые счета доступны для квалифицированных претендентов и принимаемые платежи облагаются комиссией. Снятие денег со счета может быть произведено банковским чеком, на дебетную или кредитную карту VISA или международным SWIFT-переводом во многие страны.</div>\r\n<p style=\"text-align: justify\">Как мера безопасности Moneybookers по умолчанию ограничивает переводы со счетов пользователей тысячей долларов или евро. В отличие от многих конкурирующих систем услуг (передачи средств онлайн), Moneybookers требует проверки личности перед использованием их обслуживания; это минимизирует мошенничество и предотвращает отмывание денег. Дополнительные шаги проверки поднимают максимальное передаваемое количество денег и лимит выходящих транзакций может составлять более 35 000 USD (или их эквивалента) в пределах 90-дневного периода. В качестве способов проверки используются входящий и исходящий банковский переводы, передача копии паспорта, тестовый платёж с карточки. Moneybookers обычно не участвует в финансовых спорах, и использование чарджбэков по карточкам может быть ограничено.</p>\r\n<p style=\"text-align: justify\">Moneybookers &mdash; одна из нескольких систем обслуживания оплаты, которую могут предлагать продавцы на eBay по принятой политике платежей eBay. Кроме того, платежная система Moneybookers занимает лидирующие позиции в сфере платежей, связанных с онлайн-казино, букмекерскими компаниями и другими сервисами индустрии азартных игр в интернете.<br />\r\n&nbsp;</p>','','',0,'','','','-',0,'wert','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',11,'<br />','moneybookers.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (12,1,'Оплата через систему Worldpay','','','Оплата через систему <strong>Worldpay</strong><br />','','','<div style=\"text-align: justify\">Глобальная платежная система <strong><a href=\"http://www.rbsworldpay.com/\">RBS WorldPay</a></strong> предоставляет комплексную платежную систему и является частью Royal Bank of Scotland Group.</div>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',12,'<br />','worldpay.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (13,1,'Оплата через систему Z-Payment','','','Оплата через систему <strong>Z-Payment<br />\r\n</strong>','','','<div style=\"text-align: justify\"><strong><a href=\"http://www.z-payment.ru/\">Z-PAYMENT</a></strong> - это универсальный платежный инструмент, интегрирующий множество видов оплаты в единый унифицированный алгоритм. Клиенты получают гибкую и надежную систему, с помощью которой можно осуществить любые онлайн платежи: оплатить через интернет услуги любого сервиса, магазина; совершить перевод электронных денег; оплату игр; купить электронные деньги и многое другое <br />\r\nZ-PAYMENT - эволюционный продукт, получивший свое начало в 2003 году, когда была реализована первая технологическая платформа TRANSACTOR. Эта узкоспециализированная разработка для автоматизации бизнес процесса обменного сервиса. Все это время система совершенствовалась и развивалась, отрабатывались алгоритмы, вопросы безопасности, надежности и стабильности. Весь накопленный опыт, технические и организационные решения были использованы для создания Z-PAYMENT.<br />\r\nЛюбой пользователь Интернет может бесплатно открыть себе электронный счет в Z-PAYMENT и получить возможность круглосуточно совершать интернет расчеты - без выходных, праздников и перерывов на обед. Все операции в Z-PAYMENT проводятся в режиме on-line и обрабатываются по факту поступления запроса от клиента, т.е. мгновенно. <br />\r\nПользователь электронного терминала Z-PAYMENT не ограничивается строго определенным видом расчета, он получает в свое распоряжение целый арсенал средств и видов расчета.<br />\r\nБазовая единица расчетов в системе является Рубль Российской Федерации, с точностью до копейки. 1 zp = 1 рубль. Все остальные расчеты ведутся согласно тарифам системы и курсам Центрального Банка России.<br />\r\n&nbsp;</div>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',13,'<br />','zpayment.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (14,1,'Оплата через систему ASSIST','','','Оплата через систему <strong>ASSIST</strong><br />','','','<p style=\"text-align: justify\"><strong><a href=\"http://www.assist.ru/\">ASSIST</a></strong> - это мультибанковская система платежей по пластиковым и виртуальным картам через интернет, позволяющая в реальном времени производить авторизацию и обработку транcакций.</p>\r\n<p style=\"text-align: justify\">В дополнение к стандартному набору карт VISA, MasterCard, <strong>ASSIST</strong> также предоставляет возможность оплаты электронной наличностью &ndash; WebMoney, Яндекс.Деньги, e-port, Kredit Pilot в рамках единого пользовательского интерфейса.</p>\r\n<p style=\"text-align: justify\">Пользователями <strong>ASSIST</strong> являются интернет-магазины и покупатели. Интернет-магазин получает возможность принимать через интернет пластиковые карты и электронную наличность без приобретения специального программно-аппаратного комплекса. Покупатель приобретает надежный и безопасный способ оплаты.</p>\r\n<p style=\"text-align: justify\">Расчеты, проводимые с использованием системы <strong>ASSIST</strong>, полностью соответствуют законодательству РФ и регулируются соответствующими статьями Гражданского Кодекса Российской Федерации (ГК РФ). Платежи с использованием банковских кредитных карточек проводятся по схеме MOTO (Mail Order Telephone Order) в строгом соответствии правилам платежных систем (VISA, Europay и др.).<br />\r\n&nbsp;</p>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',14,'<br />','assist.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (15,1,'Оплата через систему RBK Money','','','<p style=\"text-align: justify\">Оплата через систему <strong>RBK Money</strong></p>','','','<p style=\"text-align: justify\"><strong><a href=\"https://rbkmoney.ru/\">RBK Money</a></strong> (в прошлом &mdash; RUpay) &mdash; электронная платёжная система, главной целью которой является облегчение и унификация совершения торговых операций в интернете резидентами России. Система рассчитана на российского пользователя.<br />\r\n<br />\r\nОбщий принцип функционирования RBK Money обычен для подобных систем: любой желающий может открыть счёт в этой системе, отражающий баланс средств, которыми пользователь может располагать для оплаты покупки товаров и оплаты услуг в интернете (при условии, что приёмник платежей работает с системой RBK Money). Пользователь может пополнять счёт множеством различных способов (наличными, банковским переводом, переводом из других электронных платёжных систем, принимать платежи через систему RBK Money (например, являясь владельцем интернет-магазина); выводить средства со своего счёта, также множеством способов. За операции со средствами взимается комиссия.</p>\r\n<p style=\"text-align: justify\">Единицей измерения денежных средств в RBK Money является некая условная единица, эквивалентная рублю.</p>\r\n<p style=\"text-align: justify\">Через многообразие способов ввода/вывода средств реализуется цель основателей RBK Money создать платёжную систему, в которой для совершения/приёма платежей большинством популярных способов требуется всего один счёт.</p>\r\n<p style=\"text-align: justify\">Работа с RBK Money полностью реализована через веб-интерфейс, что позиционируется как преимущество системы, поскольку не требуется устанавливать специальные программные продукты, и система в равной степени доступна для всех операционных систем.</p>','','',0,'','RUR','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',15,'<br />','rbk.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (16,1,'Оплата через систему ROBOKASSA','','','Оплата через систему <strong>ROBOKASSA<br />\r\n</strong>','','','<p style=\"text-align: justify\"><strong><a href=\"http://www.robokassa.ru/\">ROBOKASSA</a></strong> - это сервис, позволяющий Продавцам (интернет-магазинам, поставщикам услуг) принимать платежи от клиентов в любой электронной валюте, с помощью sms-сообщений, через систему денежных переводов Contact, и через терминалы мгновенной оплаты. При этом интернет-магазинам вовсе не нужно подключать к себе платежные системы самостоятельно - мы уже сделали это за Вас. Мы просто примем средства от Ваших клиентов и перечислим их на Ваш расчетный счет.</p>\r\n<p style=\"text-align: justify\"><strong>ROBOKASSA</strong> - проект сервиса ROBOXchange.com, мирового лидера в сфере онлайнового обмена и эквайринга электронных валют. С момента запуска проекта к ROBOKASSе подключилось более 6500 действующих интернет-магазинов.<br />\r\n&nbsp;</p>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',16,'<br />','robokassa.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (17,1,'Оплата через систему LiqPAY','','','Оплата через систему <strong>LiqPAY<br />\r\n</strong>','','','<div style=\"text-align: justify\"><strong><a href=\"http://www.liqpay.com/\">LiqPAY</a></strong> &mdash; открытая платежная система, которая позволяет перевести деньги с помощью мобильного телефона, Интернета и платежных карт во всём мире.<br />\r\n<br />\r\nБезопасность реализуется технологией OTP (One-time Password - одноразовый пароль), а также технологией 3D secure code. Операции подтверждаются динамическим одноразовым паролем, который высылается в SMS-сообщении.</div>','','',0,'','RUR','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',17,'<br />','liqpay.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (18,1,'Оплата через систему Pay2Pay','','','Оплата через систему <strong>Pay2Pay<br /></strong>','','','<div style=\"text-align: justify\"><b>Система электронных платежей <a href=\"http://www.pay2pay.com/\">Pay2Pay</a></b> — это широкий спектр выбора способов оплаты, таких как банковские карты, системы электронных денег, терминалы самообслуживания, зарубежные платежные системы. Платежные данные вводятся на защищенной форме оплаты, что гарантирует конфиденциальность передаваемой информации.</div>','','',0,'','1','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',18,'<br />','pay2pay.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (19,1,'Оплата через систему Единый кошелек','','','Оплата через систему <strong>Единый кошелек</strong><br />','','','<div style=\"text-align: justify\"><b>Платежный сервис <a href=\"http://www.walletone.com/?ref=157063999778\">Единый кошелек</a></b> &ndash; это быстрый и удобный способ оплаты услуг с мобильного телефона или компьютера через Интернет. Оплатить заказ интернет-магазина, подключенного к Единой кассе, можно более чем в 300 000 пунктах приема наличных &mdash; в терминалах, салонах связи, гипермаркетах электроники, торговых сетях, банкоматах, отделениях Почты России, Сбербанка и любых российских банков.</div>','','',0,'','643','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',19,'<br />','w1.jpg','Sys',0)");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (20,1,'Оплата через систему EasyPay.by','','','Оплата через систему <strong>EasyPay.by<br /></strong>','','','<div style=\"text-align: justify\"><b>EasyPay</b> — первая белорусская система электронных денег, предназначенная для осуществления платежей в Интернете и с помощью SMS-сообщений. Электронные деньги EasyPay предназначены для пользователей различных услуг и покупателей интернет-магазинов как удобное средство платежей через Интернет. Вместо отнимающих время поездок в пункты приема платежей и очередей, все услуги и покупки можно совершать в удобное время и в одном месте – на сайте www.easypay.by. Денежной единицей в системе является белорусский рубль.</div>','','',0,'','','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',20,'<br />','easypay.jpg','Sys', '0')");
        break;

    case '1.04': // апдейт для всех релизов младше 1.05
        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_eigenschaften` (
  `Id` smallint(2) NOT NULL AUTO_INCREMENT,
  `Name_1` VARCHAR(100) NOT NULL DEFAULT '',
  `Name_2` VARCHAR(100) NOT NULL DEFAULT '',
  `Name_3` VARCHAR(100) NOT NULL DEFAULT '',
  `StartText_1` text,
  `StartText_2` text,
  `StartText_3` text,
  `StartText_1_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `StartText_2_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `StartText_3_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `Name_1_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `Name_2_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `Name_3_zeigen` enum('0','1') NOT NULL DEFAULT '0',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $eigenschaften = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_eigenschaften LIMIT 1");
        if (empty($eigenschaften['Id'])) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_eigenschaften` (`Id`) VALUES ('1')");
        }

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_tracking` (
  `Id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(100) NOT NULL,
  `Hyperlink` VARCHAR(255) NOT NULL,
  `TrNr` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_kundendownloads` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Bestellung` int(10) unsigned NOT NULL,
  `Kunde` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Datei` VARCHAR(255) NOT NULL,
  `Titel` VARCHAR(200) NOT NULL DEFAULT '',
  `Beschreibung` text,
  `Downloads` int(5) unsigned NOT NULL,
  KEY Id (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_warenkorb` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(14) unsigned NOT NULL,
  `ZeitBis` int(14) unsigned NOT NULL,
  `ZeitBisRaw` VARCHAR(20) NOT NULL,
  `Inhalt` text,
  `InhaltKonf` longtext,
  `Gesperrt` enum('0','1') NOT NULL DEFAULT '0',
  `EingeloestAm` int(14) NOT NULL,
  `EingeloestAmRaw` VARCHAR(20) NOT NULL,
  `Code` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT '0',
  `document` VARCHAR(200) DEFAULT NULL,
  `doc_name` VARCHAR(200) DEFAULT NULL,
  `bookmark_time` int(14) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_banned` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `User_id` VARCHAR(10) DEFAULT NULL,
  `Reson` VARCHAR(255) DEFAULT NULL,
  `Type` enum('bann','autobann') NOT NULL DEFAULT 'bann',
  `TimeStart` int(11) unsigned NOT NULL DEFAULT '0',
  `TimeEnd` int(11) unsigned NOT NULL DEFAULT '0',
  `Name` VARCHAR(255) DEFAULT NULL,
  `Email` VARCHAR(200) DEFAULT NULL,
  `Ip` VARCHAR(25) DEFAULT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_phrases` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `phrase` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_description` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Text` VARCHAR(70) DEFAULT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_ping` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Dokument` varchar(255) NOT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_navi_flashtag` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL DEFAULT '',
  `Size` smallint(2) unsigned NOT NULL DEFAULT '10',
  `Dokument` varchar(255) NOT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_sitemap_items` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `active` varchar(250) NOT NULL DEFAULT '1',
  `prio` varchar(250) NOT NULL DEFAULT '0.5',
  `changef` varchar(255) NOT NULL DEFAULT 'always',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_roadmap` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL default '',
  `Beschreibung` text,
  `Aktiv` tinyint(1) unsigned NOT NULL default '1',
  `Pos` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_roadmap_tickets` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Rid` varchar(10) NOT NULL default '',
  `Beschreibung` text,
  `Datum` varchar(250) NOT NULL default '',
  `Fertig` tinyint(1) unsigned NOT NULL default '0',
  `Uid` varchar(250) NOT NULL default '',
  `pr` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_admin_notes` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `UserId` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Text` text,
  `Type` enum('main','pub') NOT NULL DEFAULT 'main',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_user_friends` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(15) default NULL,
  `FreundId` varchar(25) NOT NULL default '0',
  `Aktiv` varchar(5) NOT NULL,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_user_gallery` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(12) NOT NULL,
  `Datum` varchar(25) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `Beschreibung` text,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_user_images` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `GalerieId` varchar(12) NOT NULL,
  `Datum` varchar(25) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Datei` varchar(150) NOT NULL,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_user_values` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(15) NOT NULL default '',
  `Besucher` varchar(250) NOT NULL default '',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_faq_kategorie` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(150) NOT NULL,
  `Name_2` varchar(150) DEFAULT NULL,
  `Name_3` varchar(150) DEFAULT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Posi` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_webpayment` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(25) NOT NULL DEFAULT '',
  `price_order` decimal(10,2),
  `user_order_date` varchar(40) NOT NULL DEFAULT '',
  `hashcode` varchar(40) NOT NULL DEFAULT '',
  `check_call` varchar(40) NOT NULL DEFAULT '',
  `system` varchar(40) NOT NULL DEFAULT '',
  `info` longtext NOT NULL,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` DROP COLUMN `Posi`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` DROP COLUMN `Aktiv`");

        $query = $DB->query("SELECT * FROM " . PREFIX . "_bereiche");
        while ($row = $query->fetch_object()) {
            $row->Link = str_replace('area=1', 'area=__SECTION__', $row->Link);
            $DB->query("UPDATE " . PREFIX . "_bereiche SET Link = '" . $DB->escape($row->Link) . "' WHERE Id = '" . $DB->escape($row->Id) . "'");
        }

        $phrases = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'phrases' LIMIT 1");
        if (!is_array($phrases)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`, `Link`, `Type`) VALUES ('phrases','','modul')");
        }
        $banned = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'banned' LIMIT 1");
        if (!is_array($banned)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`, `Link`, `Type`) VALUES ('banned','','modul')");
        }
        $seo = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'seomod' LIMIT 1");
        if (!is_array($seo)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`, `Link`, `Type`) VALUES ('seomod','','modul')");
        }
        $ping = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'ping' LIMIT 1");
        if (!is_array($ping)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`, `Link`, `Type`) VALUES ('ping','','modul')");
        }
        $flashtag = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'flashtag' LIMIT 1");
        if (!is_array($flashtag)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`, `Link`, `Type`) VALUES ('flashtag','','modul')");
        }
        $roadmap = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'roadmap' LIMIT 1");
        if (!is_array($roadmap)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`,`Link`, `Type`) VALUES ('roadmap','index.php?p=roadmap&area=__SECTION__','modul')");
        }
        $highlighter = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'highlighter' LIMIT 1");
        if (!is_array($highlighter)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`,`Link`, `Type`) VALUES ('highlighter','','modul')");
        }
        $forums_topicstartpage = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_bereiche WHERE Name = 'forums_topicstartpage' LIMIT 1");
        if (!is_array($forums_topicstartpage)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_bereiche` (`Name`,`Link`, `Type`) VALUES ('forums_topicstartpage','','modul')");
        }

        $query = $DB->query("SELECT Id FROM " . PREFIX . "_sektionen");
        while ($row = $query->fetch_object()) {
            $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_bereiche` ADD `Aktiv_Section_" . $DB->escape($row->Id) . "` ENUM('0','1') NOT NULL DEFAULT '1'");
        }

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_roadmap` ADD `Sektion` SMALLINT(3) NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_roadmap_tickets` ADD `Sektion` SMALLINT(3) NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `MiddleName` VARCHAR(100) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `BankName` text");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Gruppen` TEXT NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_1` VARCHAR(200) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_2` VARCHAR(200) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_3` VARCHAR(200) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_1_Pflicht` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_2_Pflicht` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Frei_3_Pflicht` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `EinheitBezug` DECIMAL(8, 3) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `EAN_Nr` VARCHAR(75) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `ISBN_Nr` VARCHAR(75) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Fsk18` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `SeitenTitel` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Template` VARCHAR(75) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Sektion` SMALLINT(3) NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Preis_EK` DECIMAL(10, 2) NOT NULL AFTER `Preis_Liste_Gueltig`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `PrCountry` VARCHAR(75) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `Yml` ENUM('0','1') NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_gutscheine` ADD `CommentCupon` text");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `UStId` VARCHAR(20) NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Tracking_Id` SMALLINT(3) UNSIGNED NOT NULL ");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Tracking_Code` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Rng_MiddleName` VARCHAR(100) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Rng_BankName` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Lief_MiddleName` VARCHAR(100) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Order_Type` longtext");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_cheats` ADD `DefektGemeldet` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_cheats` ADD `DEmail` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_cheats` ADD `DName` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_cheats` ADD `DDatum` INT(14) UNSIGNED NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_navi` ADD `Link_Titel_1` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_navi` ADD `Link_Titel_2` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_navi` ADD `Link_Titel_3` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_navi` ADD `Aktiv` ENUM('0','1') NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_laender` ADD `VersandFreiAb` DECIMAL(8, 2) NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer_online` ADD `Link` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer_online` ADD `Bots` ENUM('1','0') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_kategorie` ADD `Aktiv` ENUM('0','1') NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_kategorie` ADD `Sektion` SMALLINT(3) NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_kategorie` ADD `Search` ENUM('0','1') NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_post` ADD `thanks` TINYTEXT NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_glossar` ADD `Typ` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_posticons` ADD `title` VARCHAR(25) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sessions` ADD `Ip` VARCHAR(35) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_smileys` ADD `title` VARCHAR(25) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_quicknavi` ADD `Gruppe` VARCHAR(25) NOT NULL ");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_varianten` ADD `Bestand` INT(10) UNSIGNED NOT NULL DEFAULT '1000'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_varianten` ADD `GewichtOperant` enum('+','-')  NOT NULL DEFAULT '+'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_kategorie_zubehoer` ADD `Sektion` SMALLINT(3) NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_content` ADD `Gruppen` TINYTEXT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Fsk18` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sektionen` ADD `LimitLastThreads` smallint(2) unsigned NOT NULL DEFAULT '4'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sektionen` ADD `Domains` VARCHAR(50) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_faq` ADD `Sender` VARCHAR(150) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_faq` ADD `NewCat` VARCHAR(150) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_faq` ADD `Kategorie` smallint(3) unsigned NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_faq` DROP COLUMN `Parent_Id`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Films` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Tele` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Book` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Game` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Citat` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Other` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Status` VARCHAR(255) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Gravatar` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Vkontakte` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Odnoklassniki` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Facebook` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Twitter` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Google` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` ADD `Mymail` VARCHAR(255) NOT NULL DEFAULT ''");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_log` CHANGE `Aktion` `Aktion` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_zahlungsmethoden` CHANGE `Kosten` `Kosten` DECIMAL(8,2) UNSIGNED DEFAULT '0.00'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_versandarten` CHANGE `Gebuehr_Pauschal` `Gebuehr_Pauschal` DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT '0.00'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_varianten` CHANGE `Wert` `Wert` DECIMAL(9,2) UNSIGNED NOT NULL DEFAULT '0.00'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_referer` CHANGE `Ua` `Ua` VARCHAR(35) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_news` CHANGE `Intro1` `Intro1` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_news` CHANGE `Intro2` `Intro2` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_news` CHANGE `Intro3` `Intro3` text");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` CHANGE `Ort_Public` `Ort_Public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` CHANGE `Gaestebuch` `Gaestebuch` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sprachen` CHANGE `Zeitformat` `Zeitformat` VARCHAR(30) NOT NULL DEFAULT '%d.%m.%Y, %H:%M'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` CHANGE `EinheitCount` `EinheitCount` DECIMAL(8, 3) UNSIGNED NULL DEFAULT '1.000'");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer` DROP COLUMN `Geloescht_Email`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `Rng_Anrede`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `Lief_Anrede`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `BankName`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `BankLeitzahl`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `BankKonto`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` DROP COLUMN `BankKontoInhaber`");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (1,'News','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (2,'Articles','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (3,'Global_Shop','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (4,'Products','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (5,'Downloads','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (6,'Forums_nt','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (7,'Gallery','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (8,'Faq','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (9,'Content','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (10,'Polls','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (11,'User_nameS','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (12,'Links','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (13,'Gaming_cheats','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (14,'Manufacturer','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (15,'Calendar','1','0.5','always')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_sitemap_items` VALUES (16,'Roadmaps','1','0.5','always')");

        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_cheats_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_links_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_downloads_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_produkte_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_gaestebuch_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_fckinserts`");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_bestellungen_historie` (
  `Id` mediumint(10) unsigned NOT NULL auto_increment,
  `BestellNummer` int(10) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Subjekt` varchar(255) NOT NULL,
  `Kommentar` text,
  `StatusText` text,
  PRIMARY KEY  (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `MetaTags` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `MetaTags` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_produkte` ADD `MetaDescription` VARCHAR(255) NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` ADD `Verschickt` TEXT NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_bestellungen` CHANGE `Status` `Status` ENUM('wait', 'progress', 'ok', 'failed', 'oksend', 'oksendparts') NULL DEFAULT 'wait'");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_produkte_downloads` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProduktId` int(10) unsigned NOT NULL,
  `Datei` varchar(255) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `DlName` varchar(200) NOT NULL,
  `Beschreibung` text,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_schedule` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned NOT NULL,
  `PrevTime` int(14) unsigned NOT NULL,
  `NextTime` int(14) unsigned NOT NULL,
  `Type` enum('one','more','sys') NOT NULL DEFAULT 'sys',
  `Modul` varchar(50) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Func` varchar(255) NOT NULL,
  `Options` text,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Error` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`,`Datum`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_audios` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(255) NOT NULL,
  `Audio` varchar(200) DEFAULT NULL,
  `Width` varchar(10) NOT NULL DEFAULT '400',
  `Datum` int(12) unsigned NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_zahlungsmethoden` CHANGE `Install_Id` `Install_Id` VARCHAR(255) DEFAULT NULL");

        $res = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_verfuegbarkeit WHERE Id = '5' LIMIT 1");
        if (!is_array($res)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_verfuegbarkeit` VALUES (5,'Производится доставка на склад','','','','','')");
        }

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_referer` ADD `Words` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_referer` ADD `Url` varchar(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_topic` ADD `top_first_post` ENUM('0','1') NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_topic` ADD `first_post_id` INT(11) DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_ips` CHANGE `ip` `ip` INT(10) unsigned NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer_online` CHANGE `Ip` `Ip` INT(10) unsigned NOT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_benutzer_online` ADD `Type` ENUM('site','admin') NOT NULL DEFAULT 'site'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_suche_log` ADD `UserId` int(10) unsigned NOT NULL DEFAULT '0'");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_shop_warenkorb_gaeste` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `BenutzerId` varchar(50) NOT NULL,
  `Ablauf` int(14) unsigned NOT NULL,
  `Inhalt` longtext NOT NULL,
  `InhaltConfig` text,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `BenutzerId` (`BenutzerId`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $query = $DB->query("SELECT id FROM " . PREFIX . "_f_topic");
        while ($row = $query->fetch_object()) {
            $res = $DB->fetch_object("SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '" . $row->id . "' ORDER BY id ASC LIMIT 1");
            $DB->query("UPDATE " . PREFIX . "_f_topic SET first_post_id = '" . $res->id . "' WHERE id = '" . $row->id . "'");
        }

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_staffelpreise` DROP COLUMN `Prozent`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_staffelpreise` ADD `Wert` DECIMAL(6,2) NULL DEFAULT NULL");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_staffelpreise` ADD `Operand` ENUM('pro','wert') NOT NULL DEFAULT 'pro'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_shop_zahlungsmethoden` ADD `MaxWert` DECIMAL(8,2) NOT NULL DEFAULT '0.00'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_kontakt_form` ADD `Email2` VARCHAR(150) NOT NULL AFTER `Email`");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_referer` ADD `UserId` int(10) unsigned NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_counter_referer` ADD `UserName` VARCHAR(100) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_sektionen` DROP COLUMN `Tpl_denied`");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_seotags` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `canonical` varchar(255) NOT NULL DEFAULT '',
  `aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page` (`page`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $DB->query("CREATE TABLE IF NOT EXISTS `" . PREFIX . "_settings` (
  `Id` varchar(100) NOT NULL,
  `Modul` varchar(50) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Value` text,
  PRIMARY KEY (`Id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

        $tables = array();
        $sql = $DB->query("SHOW TABLES LIKE '" . PREFIX . "_%'");
        while ($row = $sql->fetch_array()) {
            $tables[] = $row[0];
        }

        if (in_array(PREFIX . '_admin_settings', $tables)) {
            SX::save('admin', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_admin_settings LIMIT 1"));
        }
        if (in_array(PREFIX . '_einstellungen', $tables)) {
            SX::save('system', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_einstellungen LIMIT 1"));
        }
        if (in_array(PREFIX . '_galerie_einstellungen', $tables)) {
            SX::save('galerie', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_galerie_einstellungen LIMIT 1"));
        }
        if (in_array(PREFIX . '_shop_einstellungen', $tables)) {
            SX::save('shop', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_einstellungen LIMIT 1"));
        }
        if (in_array(PREFIX . '_f_allowed_files', $tables)) {
            SX::save('forum', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_f_allowed_files LIMIT 1"));
        }
        if (in_array(PREFIX . '_sitemap', $tables)) {
            SX::save('sitemap', $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_sitemap LIMIT 1"));
        }
        if (in_array(PREFIX . '_modul_settings', $tables)) {
            $query = $DB->query("SELECT * FROM " . PREFIX . "_modul_settings");
            while ($res = $query->fetch_assoc()) {
                SX::save($res['Modul'], $res);
            }
        }

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('admin_editarea', 'admin', 'EditArea', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_popup_product', 'shop', 'popup_product', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_menu_low_amount', 'shop', 'menu_low_amount', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_seen_cat', 'shop', 'seen_cat', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_vat_info_cat', 'shop', 'vat_info_cat', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_similar_product', 'shop', 'similar_product', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_vat_info_product', 'shop', 'vat_info_product', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_sortable_produkte', 'shop', 'Sortable_Produkte', 'date_asc')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_update', 'system', 'Update', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_reg_address', 'system', 'Reg_Address', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_reg_addressfill', 'system', 'Reg_AddressFill', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_reg_datapflichtfill', 'system', 'Reg_DataPflichtFill', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_error_email', 'system', 'Error_Email', '0')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_availtype', 'shop', 'AvailType', '1')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('shop_onlyfhrase', 'shop', 'OnlyFhrase', '1')");

        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_counttitle', 'system', 'CountTitle', '150')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_countkeywords', 'system', 'CountKeywords', '250')");
        $DB->query("INSERT IGNORE INTO `" . PREFIX . "_settings` VALUES ('system_countdescription', 'system', 'CountDescription', '250')");

        $DB->query("UPDATE `" . PREFIX . "_settings` SET Type = 'string' WHERE Name = 'Inn' OR Name = 'Kpp' OR Name = 'Bik' OR Name = 'Kschet' OR Name = 'Rschet'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_newsletter_archiv` ADD `Sys` ENUM('one','later','more') NOT NULL DEFAULT 'one'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_newsletter_archiv` ADD `Noheader` ENUM('0','1') NOT NULL DEFAULT '1'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_banner` ADD `Click` int(11) unsigned NOT NULL DEFAULT '0'");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_seotags` ADD `canonical` VARCHAR(255) NOT NULL DEFAULT ''");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_partner` ADD `Nofollow` tinyint(1) NOT NULL DEFAULT '1'");

        $array = array(
            'Max_Groesse'    => 6144,
            'Typen'          => '.3gp|.7z|.aif|.avi|.bmp|.bzip|.cfg|.doc|.gif|.gz|.gzip|.htm|.html|.jpg|.mov|.mp3|.pdf|.pdf|.png|.psd|.rar|.sql|.tar|.tgz|.tpl|.txt|.vmf|.wav|.wmv|.xls|.xml|.zip',
            'TypenMoegliche' => '.jpg|.png|.gif|.zip|.rar|.exe|.pdf|.txt|.php|.tar|.wav|.mp3|.mp4|.3gp|.psd|.xls|.doc|.bmp|.bz2|.tgz|.7z|.gzip|.bzip|.gz|.inc|.cfg|.aif|.mov|.rmp|.vmf|.avi|.ram|.wmv|.asf|.tpl|.htm|.html|.cgi|.asp|.cfm|.xml|.sql|.8bi|.pdd|.pdf|.cd4');
        SX::save('forum', $array);

        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_admin_settings`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_galerie_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_shop_einstellungen`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_modul_settings`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_f_allowed_files`");
        $DB->query("DROP TABLE IF EXISTS `" . PREFIX . "_sitemap`");

        $pay2pay = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '18' LIMIT 1");
        if (!is_array($pay2pay)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (18,1,'Оплата через систему Pay2Pay','','','Оплата через систему <strong>Pay2Pay<br /></strong>','','','<div style=\"text-align: justify\"><b>Система электронных платежей <a href=\"http://www.pay2pay.com/\">Pay2Pay</a></b> — это широкий спектр выбора способов оплаты, таких как банковские карты, системы электронных денег, терминалы самообслуживания, зарубежные платежные системы. Платежные данные вводятся на защищенной форме оплаты, что гарантирует конфиденциальность передаваемой информации.</div>','','',0,'','1','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',18,'<br />','pay2pay.jpg','Sys',0)");
        }
        $w1 = $DB->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '19' LIMIT 1");
        if (!is_array($w1)) {
            $DB->query("INSERT IGNORE INTO `" . PREFIX . "_shop_zahlungsmethoden` VALUES (19,1,'Оплата через систему Единый кошелек','','','Оплата через систему <strong>Единый кошелек</strong><br />','','','<div style=\"text-align: justify\"><b>Платежный сервис <a href=\"http://www.walletone.com/?ref=157063999778\">Единый кошелек</a></b> &ndash; это быстрый и удобный способ оплаты услуг с мобильного телефона или компьютера через Интернет. Оплатить заказ интернет-магазина, подключенного к Единой кассе, можно более чем в 300 000 пунктах приема наличных &mdash; в терминалах, салонах связи, гипермаркетах электроники, торговых сетях, банкоматах, отделениях Почты России, Сбербанка и любых российских банков.</div>','','',0,'','643','','-',0,'pro','7,1,2,4,5,3,6','AZ,BY,KZ,KG,LV,LT,MD,RU,TJ,TM,UZ,UA,EE','8,3,6,1,7,2,4,5,9,10,11,12,13',19,'<br />','w1.jpg','Sys',0)");
        }

        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_topic` ADD INDEX `datum` (`datum`)");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_topic` ADD INDEX `uid` (`uid`)");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_post` ADD INDEX `datum` (`datum`)");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_f_post` ADD INDEX `uid` (`uid`)");
        $DB->query("ALTER IGNORE TABLE `" . PREFIX . "_pn` ADD INDEX `count` (`to_uid`,`typ`)");

        $DB->query("DELETE FROM `" . PREFIX . "_settings` WHERE Modul = 'shop' AND (Name = 'Email_Antwort' OR Name = 'Email_Wertung' OR Name = 'Email_Produktanfrage')");
        break;
}

SX::output('<script type="text/javascript"> alert("' . SX::$lang['GlobalUpdateOk'] . '"); location.href = "index.php";</script>', true);
