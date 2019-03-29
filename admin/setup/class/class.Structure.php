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

class Structure {

    public static function getData() {
        return "
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_artikel` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` int(10) unsigned DEFAULT '0',
  `Titel_1` varchar(255) DEFAULT NULL,
  `Titel_2` varchar(200) NOT NULL,
  `Titel_3` varchar(200) NOT NULL,
  `Untertitel_1` varchar(100) NOT NULL,
  `Untertitel_2` varchar(100) NOT NULL,
  `Untertitel_3` varchar(100) NOT NULL,
  `Inhalt_1` longtext NOT NULL,
  `Inhalt_2` longtext NOT NULL,
  `Inhalt_3` longtext NOT NULL,
  `Textbilder_1` text,
  `Textbilder_2` text,
  `Textbilder_3` text,
  `WertungsDaten` text,
  `Genre` smallint(3) unsigned DEFAULT NULL,
  `Hersteller` smallint(3) unsigned DEFAULT NULL,
  `Vertrieb` smallint(3) unsigned DEFAULT NULL,
  `Wertung` enum('0','1') DEFAULT '1',
  `Kommentare` enum('0','1') DEFAULT '1',
  `Asin` varchar(255) DEFAULT NULL,
  `Plattform` smallint(2) DEFAULT NULL,
  `Veroeffentlichung` varchar(50) DEFAULT NULL,
  `Preis` varchar(50) DEFAULT NULL,
  `Shop` varchar(255) DEFAULT NULL,
  `Bild_1` varchar(255) DEFAULT NULL,
  `Bild_2` varchar(255) NOT NULL,
  `Bild_3` varchar(255) NOT NULL,
  `Links` text,
  `Galerien` varchar(255) DEFAULT NULL,
  `Autor` int(10) unsigned DEFAULT '1',
  `Zeit` int(14) unsigned DEFAULT '0',
  `Hits` int(10) unsigned DEFAULT '0',
  `Typ` enum('preview','review','special') DEFAULT 'review',
  `Sektion` smallint(2) unsigned DEFAULT '1',
  `Druck` mediumint(8) unsigned DEFAULT '0',
  `Aktiv` tinyint(1) DEFAULT '1',
  `Top` text,
  `Flop` text,
  `Minimum` text,
  `Optimum` text,
  `ZeitStart` int(14) unsigned DEFAULT '0',
  `ZeitEnde` int(14) unsigned DEFAULT '0',
  `Suche` enum('0','1') DEFAULT '1',
  `Topartikel` enum('0','1') DEFAULT '0',
  `TopartikelBild_1` varchar(255) DEFAULT NULL,
  `TopartikelBild_2` varchar(255) DEFAULT NULL,
  `TopartikelBild_3` varchar(255) DEFAULT NULL,
  `AlleSektionen` enum('0','1') DEFAULT '0',
  `Bildausrichtung` enum('left','right') DEFAULT 'right',
  `Tags` varchar(255) DEFAULT NULL,
  `ShopArtikel` varchar(255) DEFAULT NULL,
  `Kennwort` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Start` (`ZeitStart`),
  KEY `Ende` (`ZeitEnde`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_artikel_kategorie` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(150) NOT NULL DEFAULT '',
  `Name_2` varchar(150) DEFAULT NULL,
  `Name_3` varchar(150) DEFAULT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Posi` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_banned` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `User_id` varchar(10) DEFAULT NULL,
  `Reson` varchar(255) DEFAULT NULL,
  `Type` enum('bann','autobann') NOT NULL DEFAULT 'bann',
  `TimeStart` int(11) unsigned NOT NULL DEFAULT '0',
  `TimeEnd` int(11) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Ip` varchar(25) DEFAULT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_banner` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(3) unsigned DEFAULT '1',
  `Kategorie` smallint(3) NOT NULL DEFAULT '1',
  `Name` varchar(255) DEFAULT NULL,
  `HTML_Code` text,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Gewicht` enum('1','2','3') NOT NULL DEFAULT '1',
  `Anzeigen` int(14) unsigned DEFAULT '0',
  `Anzeigen_Max` int(10) unsigned NOT NULL DEFAULT '0',
  `Click` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Aktiv` (`Aktiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_banner_kategorie` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Beschreibung` text,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gruppe` smallint(2) NOT NULL DEFAULT '0',
  `Team` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Regdatum` int(14) unsigned NOT NULL DEFAULT '0',
  `RegCode` varchar(10) NOT NULL DEFAULT '',
  `Email` varchar(200) NOT NULL DEFAULT '',
  `Kennwort` varchar(32) NOT NULL DEFAULT '',
  `KennwortTemp` varchar(50) DEFAULT NULL,
  `Benutzername` varchar(200) NOT NULL DEFAULT '',
  `Vorname` varchar(255) DEFAULT NULL,
  `Nachname` varchar(255) DEFAULT NULL,
  `Strasse_Nr` varchar(255) DEFAULT NULL,
  `Postleitzahl` varchar(50) DEFAULT NULL,
  `Ort` varchar(255) DEFAULT NULL,
  `Firma` varchar(100) DEFAULT NULL,
  `UStId` varchar(100) DEFAULT NULL,
  `Telefon` varchar(100) DEFAULT NULL,
  `Telefax` varchar(100) DEFAULT NULL,
  `Geburtstag` varchar(10) NOT NULL DEFAULT '',
  `Land` varchar(100) NOT NULL DEFAULT 'Россия',
  `LandCode` char(2) NOT NULL DEFAULT 'ru',
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Logins` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Profil_public` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Profil_Alle` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Geburtstag_public` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Ort_Public` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Unsichtbar` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Emailempfang` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Pnempfang` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `PnEmail` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `PnPopup` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Gaestebuch` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Gaestebuch_KeineGaeste` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Gaestebuch_Moderiert` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Gaestebuch_Zeichen` mediumint(5) unsigned NOT NULL DEFAULT '500',
  `Gaestebuch_imgcode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Gaestebuch_smilies` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Gaestebuch_bbcode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msn` varchar(100) DEFAULT NULL,
  `aim` varchar(100) DEFAULT NULL,
  `icq` varchar(100) DEFAULT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `Webseite` varchar(255) DEFAULT NULL,
  `Signatur` text,
  `Interessen` varchar(255) DEFAULT NULL,
  `Avatar_Default` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Avatar` varchar(255) NOT NULL DEFAULT '',
  `Beitraege` int(10) unsigned NOT NULL DEFAULT '0',
  `Profil_Hits` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Zuletzt_Aktiv` int(14) unsigned NOT NULL DEFAULT '0',
  `Geschlecht` enum('m','f','-') NOT NULL DEFAULT '-',
  `Beruf` varchar(255) DEFAULT NULL,
  `Hobbys` varchar(255) DEFAULT NULL,
  `Essen` varchar(255) DEFAULT NULL,
  `Musik` varchar(255) DEFAULT NULL,
  `Forum_Beitraege_Limit` smallint(2) unsigned NOT NULL DEFAULT '15',
  `Forum_Themen_Limit` smallint(2) unsigned NOT NULL DEFAULT '15',
  `Geloescht` enum('0','1') NOT NULL DEFAULT '0',
  `Fsk18` enum('0','1') NOT NULL DEFAULT '0',
  `MiddleName` VARCHAR(100) NOT NULL DEFAULT '',
  `BankName` text,
  `Films` VARCHAR(255) DEFAULT NULL,
  `Tele` VARCHAR(255) DEFAULT NULL,
  `Book` VARCHAR(255) DEFAULT NULL,
  `Game` VARCHAR(255) DEFAULT NULL,
  `Citat` VARCHAR(255) DEFAULT NULL,
  `Other` VARCHAR(255) DEFAULT NULL,
  `Status` VARCHAR(255) DEFAULT NULL,
  `Gravatar` ENUM('0','1') NOT NULL DEFAULT '0',
  `Vkontakte` VARCHAR(255) NOT NULL DEFAULT '',
  `Odnoklassniki` VARCHAR(255) NOT NULL DEFAULT '',
  `Facebook` VARCHAR(255) NOT NULL DEFAULT '',
  `Twitter` VARCHAR(255) NOT NULL DEFAULT '',
  `Google` VARCHAR(255) NOT NULL DEFAULT '',
  `Mymail` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  KEY `Gruppe` (`Gruppe`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer_gaestebuch` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `BenutzerId` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Titel` varchar(255) NOT NULL DEFAULT '',
  `Eintrag` text,
  `Datum` int(14) unsigned NOT NULL DEFAULT '0',
  `Autor` varchar(200) DEFAULT NULL,
  `Autor_Web` varchar(200) DEFAULT NULL,
  `Autor_Herkunft` varchar(200) DEFAULT NULL,
  `Autor_Ip` varchar(100) DEFAULT NULL,
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `BenutzerId` (`BenutzerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer_gruppen` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Name_Intern` varchar(200) NOT NULL DEFAULT '',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `VatByCountry` enum('1','2') NOT NULL DEFAULT '1',
  `Rabatt` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `ShopAnzeige` enum('b2b','b2c') NOT NULL DEFAULT 'b2c',
  `Avatar_Default` enum('0','1') NOT NULL DEFAULT '1',
  `Avatar` varchar(255) DEFAULT NULL,
  `Avatar_B` smallint(3) unsigned NOT NULL DEFAULT '120',
  `Avatar_H` smallint(3) unsigned NOT NULL DEFAULT '120',
  `MaxPn` smallint(3) unsigned NOT NULL DEFAULT '50',
  `MaxPn_Zeichen` mediumint(5) unsigned NOT NULL DEFAULT '1000',
  `MaxAnlagen` smallint(2) NOT NULL DEFAULT '5',
  `MaxZeichenPost` mediumint(5) unsigned NOT NULL DEFAULT '5000',
  `SysCode_Signatur` enum('0','1') NOT NULL DEFAULT '1',
  `Signatur_Laenge` mediumint(4) unsigned NOT NULL DEFAULT '250',
  `Signatur_Erlaubt` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer_logins` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '0',
  `Datum` int(14) unsigned NOT NULL DEFAULT '0',
  `Datum_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Ip` varchar(50) NOT NULL DEFAULT '',
  `Email` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer_online` (
  `Ip` int(10) unsigned NOT NULL,
  `Uid` int(10) unsigned DEFAULT '0',
  `Expire` int(11) DEFAULT '0',
  `Benutzername` varchar(255) DEFAULT NULL,
  `Unsichtbar` varchar(10) DEFAULT NULL,
  `Link` varchar(255) DEFAULT NULL,
  `Bots` ENUM('1','0') NOT NULL DEFAULT '0',
  `Type` ENUM('site','admin') NOT NULL DEFAULT 'site',
  UNIQUE KEY `Ip` (`Ip`),
  KEY `Benutzername` (`Benutzername`),
  KEY `Expire` (`Expire`),
  KEY `Type` (`Type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_benutzer_videos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned NOT NULL,
  `VideoSource` enum('youtube') NOT NULL DEFAULT 'youtube',
  `Video` varchar(100) NOT NULL,
  `Hits` mediumint(10) unsigned NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Benutzer` (`Benutzer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_berechtigungen` (
  `Id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Gruppe` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Rechte` text,
  `Rechte_Admin` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_bereiche` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Result` varchar(100) NOT NULL DEFAULT '',
  `Link` varchar(222) NOT NULL DEFAULT '',
  `Type` enum('modul','extmodul','widget') NOT NULL DEFAULT 'modul',
  `Settings` text,
  `Aktiv_Section_1` enum('0','1') NOT NULL DEFAULT '1',
  `Aktiv_Section_2` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT '0',
  `document` varchar(200) DEFAULT NULL,
  `doc_name` varchar(200) DEFAULT NULL,
  `bookmark_time` int(14) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_cheats` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Plattform` int(10) unsigned DEFAULT '0',
  `Sprache` char(2) DEFAULT 'ru',
  `Typ` enum('cheat','walkthrough','patch') DEFAULT 'cheat',
  `Benutzer` int(10) unsigned DEFAULT '1',
  `Hits` int(10) unsigned DEFAULT '0',
  `DatumUpdate` int(14) unsigned DEFAULT '0',
  `Name_1` varchar(200) DEFAULT NULL,
  `Name_2` varchar(255) NOT NULL,
  `Name_3` varchar(255) NOT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Bild` varchar(255) DEFAULT NULL,
  `Download` varchar(255) DEFAULT NULL,
  `DownloadHits` int(10) unsigned DEFAULT '0',
  `DownloadLink` varchar(255) DEFAULT NULL,
  `Hersteller` int(10) unsigned DEFAULT '0',
  `Webseite` varchar(255) DEFAULT NULL,
  `Galerien` varchar(255) DEFAULT NULL,
  `CheatLinks` text,
  `CheatProdukt` smallint(5) unsigned DEFAULT '0',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `DefektGemeldet` varchar(255) DEFAULT NULL,
  `DEmail` varchar(255) DEFAULT NULL,
  `DName` varchar(255) DEFAULT NULL,
  `DDatum` int(14) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Plattform` (`Plattform`),
  KEY `Aktiv` (`Aktiv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_codewidget` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) NOT NULL,
  `Inhalt` longtext NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL,
  `Datum` int(14) NOT NULL,
  `Gruppen` varchar(255) NOT NULL DEFAULT '',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  KEY `Id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_content` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Datum` int(14) unsigned NOT NULL,
  `Kennwort` varchar(100) DEFAULT NULL,
  `Autor` int(10) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Kategorie` mediumint(5) unsigned NOT NULL,
  `Titel1` varchar(200) NOT NULL,
  `Titel2` varchar(200) NOT NULL,
  `Titel3` varchar(200) NOT NULL,
  `Topcontent_Bild_1` varchar(255) DEFAULT NULL,
  `Topcontent_Bild_2` varchar(255) DEFAULT NULL,
  `Topcontent_Bild_3` varchar(255) DEFAULT NULL,
  `Inhalt1` longtext NOT NULL,
  `Inhalt2` longtext NOT NULL,
  `Inhalt3` longtext NOT NULL,
  `Bild1` varchar(255) NOT NULL,
  `Bild2` varchar(255) NOT NULL,
  `Bild3` varchar(255) NOT NULL,
  `Textbilder1` text,
  `Textbilder2` text,
  `Textbilder3` text,
  `BildAusrichtung` enum('left','right') NOT NULL DEFAULT 'right',
  `Bewertung` enum('0','1') NOT NULL DEFAULT '0',
  `Kommentare` enum('0','1') NOT NULL DEFAULT '0',
  `Tags` varchar(255) DEFAULT NULL,
  `Galerien` tinytext NOT NULL,
  `Hits` int(10) unsigned NOT NULL,
  `Topcontent` enum('0','1') NOT NULL DEFAULT '0',
  `Suche` enum('0','1') NOT NULL DEFAULT '1',
  `Gruppen` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_content_kategorien` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Tpl_Extra` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_counter_ips` (
  `ip` int(10) unsigned NOT NULL,
  `visit` datetime NOT NULL,
  UNIQUE KEY `ip` (`ip`),
  KEY `visit` (`visit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_counter_referer` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Os` varchar(20) DEFAULT NULL,
  `IPAdresse` varchar(200) NOT NULL,
  `Ua` varchar(35) DEFAULT NULL,
  `Referer` varchar(255) DEFAULT NULL,
  `Details` tinytext NOT NULL,
  `Datum` datetime DEFAULT '0000-00-00 00:00:00',
  `Datum_Int` int(14) unsigned NOT NULL DEFAULT '1213196791',
  `Words` varchar(255) DEFAULT NULL,
  `UserId` int(10) unsigned NOT NULL DEFAULT '0',
  `UserName` varchar(100) NOT NULL DEFAULT '',
  `Url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_counter_werte` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Tag` smallint(2) unsigned NOT NULL,
  `Jahr` smallint(4) unsigned NOT NULL,
  `Monat` smallint(2) unsigned NOT NULL,
  `Tag_Id` int(11) unsigned NOT NULL,
  `Tag_Wert` int(11) unsigned NOT NULL,
  `Wochen_Id` int(11) unsigned NOT NULL,
  `Wochen_Wert` int(11) unsigned NOT NULL,
  `Monat_Id` int(11) unsigned NOT NULL,
  `Monat_Wert` int(11) unsigned NOT NULL,
  `Jahr_Id` int(11) unsigned NOT NULL,
  `Jahr_Wert` int(11) unsigned NOT NULL,
  `Gesamt_Wert` int(11) unsigned NOT NULL,
  `Rekord_Datum` datetime NOT NULL,
  `Rekord_Wert` int(11) unsigned NOT NULL,
  `Hits` bigint(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Jahr` (`Jahr`),
  KEY `Tag_Id` (`Tag_Id`),
  KEY `Tag_Wert` (`Tag_Wert`),
  KEY `Rekord_Wert` (`Rekord_Wert`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_downloads` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Bild` varchar(255) DEFAULT NULL,
  `Name_1` varchar(200) NOT NULL,
  `Name_2` varchar(200) NOT NULL,
  `Name_3` varchar(200) NOT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Url` varchar(255) NOT NULL,
  `Url_Direct` varchar(255) NOT NULL,
  `Size_Direct` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `Mirrors` text,
  `Hits` int(10) unsigned NOT NULL DEFAULT '0',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Autor` int(10) unsigned NOT NULL DEFAULT '1',
  `DefektGemeldet` varchar(255) DEFAULT NULL,
  `DEmail` varchar(255) DEFAULT NULL,
  `DName` varchar(255) DEFAULT NULL,
  `DDatum` int(14) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Sprache` char(2) NOT NULL DEFAULT 'ru',
  `Sponsor` enum('0','1') NOT NULL DEFAULT '0',
  `BetriebsOs` text,
  `SoftwareTyp` varchar(255) NOT NULL,
  `Version` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Kategorie` (`Kategorie`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_downloads_kategorie` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name_1` varchar(200) NOT NULL,
  `Name_2` varchar(200) NOT NULL,
  `Name_3` varchar(200) NOT NULL,
  `Beschreibung_1` varchar(255) NOT NULL,
  `Beschreibung_2` varchar(255) NOT NULL,
  `Beschreibung_3` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Parent_Id` (`Parent_Id`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_attachment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `orig_name` varchar(200) DEFAULT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `hits` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `position` smallint(5) unsigned DEFAULT '0',
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `comment` text,
  `group_id` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_forum` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `category_id` int(11) unsigned DEFAULT '0',
  `statusicon` varchar(20) DEFAULT NULL,
  `comment` text,
  `status` tinyint(1) DEFAULT '0',
  `last_post` datetime DEFAULT NULL,
  `last_post_id` int(11) unsigned DEFAULT '0',
  `group_id` varchar(150) DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '0',
  `password` varchar(100) DEFAULT NULL,
  `password_raw` varchar(255) DEFAULT NULL,
  `moderator` int(11) DEFAULT NULL,
  `position` smallint(6) DEFAULT '0',
  `moderated` tinyint(1) DEFAULT '0',
  `moderated_posts` tinyint(1) DEFAULT '0',
  `topic_emails` text,
  `post_emails` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_hilfe` (
  `Id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name_1` varchar(150) NOT NULL DEFAULT '',
  `Name_2` varchar(150) NOT NULL DEFAULT '',
  `Name_3` varchar(150) NOT NULL DEFAULT '',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_hilfetext` (
  `Id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` smallint(3) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(200) NOT NULL DEFAULT '',
  `Name_2` varchar(200) NOT NULL DEFAULT '',
  `Name_3` varchar(200) NOT NULL DEFAULT '',
  `Text_1` text,
  `Text_2` text,
  `Text_3` text,
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Klicks` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_mods` (
  `forum_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_permissions` (
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `permissions` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`forum_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `topic_id` smallint(6) DEFAULT '0',
  `datum` datetime DEFAULT '0000-00-00 00:00:00',
  `uid` int(10) unsigned DEFAULT '0',
  `use_bbcode` tinyint(1) DEFAULT '0',
  `use_smilies` tinyint(1) DEFAULT '0',
  `use_sig` tinyint(1) DEFAULT '0',
  `message` text,
  `attachment` tinytext,
  `opened` tinyint(1) DEFAULT '1',
  `thanks` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `datum` (`datum`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_rank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_rating` (
  `topic_id` int(11) NOT NULL DEFAULT '0',
  `rating` text,
  `ip` text,
  `uid` text,
  PRIMARY KEY (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `rating` text,
  `forum_id` int(11) DEFAULT '0',
  `icon` smallint(5) unsigned DEFAULT NULL,
  `posticon` smallint(5) unsigned DEFAULT NULL,
  `datum` datetime DEFAULT '0000-00-00 00:00:00',
  `replies` int(10) unsigned DEFAULT '0',
  `uid` int(10) unsigned DEFAULT '0',
  `notification` text,
  `type` tinyint(1) DEFAULT '0',
  `last_post` datetime DEFAULT NULL,
  `last_post_id` int(11) DEFAULT NULL,
  `opened` tinyint(4) DEFAULT '1',
  `last_post_int` int(14) DEFAULT '0',
  `top_first_post` ENUM('0','1') NOT NULL DEFAULT '0',
  `first_post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `datum` (`datum`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_topic_read` (
  `Usr` int(11) NOT NULL DEFAULT '0',
  `Topic` int(11) NOT NULL DEFAULT '0',
  `ReadOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Usr`,`Topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_f_topic_viewing` (
  `Ip` varchar(100) NOT NULL DEFAULT '',
  `Topic` int(10) unsigned NOT NULL DEFAULT '0',
  `Expire` int(14) unsigned NOT NULL DEFAULT '0',
  KEY `Topic` (`Topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_faq` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name_1` varchar(255) DEFAULT NULL,
  `Name_2` varchar(255) NOT NULL,
  `Name_3` varchar(255) NOT NULL,
  `Antwort_1` text,
  `Antwort_2` text,
  `Antwort_3` text,
  `Textbilder_1` text,
  `Textbilder_2` text,
  `Textbilder_3` text,
  `Position` smallint(3) unsigned DEFAULT '0',
  `Datum` int(14) unsigned DEFAULT '0',
  `Benutzer` mediumint(5) unsigned DEFAULT '1',
  `Aktiv` tinyint(1) DEFAULT '1',
  `Sektion` smallint(3) unsigned DEFAULT '1',
  `Hits` int(10) unsigned DEFAULT '0',
  `Sender` varchar(150) DEFAULT NULL,
  `NewCat` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Aktiv` (`Aktiv`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_faq_kategorie` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(150) NOT NULL,
  `Name_2` varchar(150) DEFAULT NULL,
  `Name_3` varchar(150) DEFAULT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Posi` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_galerie` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` mediumint(5) unsigned NOT NULL DEFAULT '1',
  `Parent_Id` int(10) unsigned DEFAULT '0',
  `Sektion` smallint(3) unsigned DEFAULT '0',
  `Name_1` varchar(100) NOT NULL,
  `Name_2` varchar(100) NOT NULL,
  `Name_3` varchar(100) NOT NULL,
  `Aktiv` tinyint(1) DEFAULT '1',
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Datum` int(14) unsigned DEFAULT '0',
  `Autor` int(10) unsigned DEFAULT '1',
  `Tags` varchar(255) DEFAULT NULL,
  `Bilder` int(8) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Kategorie` (`Kategorie`),
  KEY `Parent_Id` (`Parent_Id`),
  KEY `Tags` (`Tags`),
  KEY `Name_1` (`Name_1`),
  KEY `Name_2` (`Name_2`),
  KEY `Name_3` (`Name_3`),
  KEY `Datum` (`Datum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_galerie_bilder` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Galerie_Id` int(10) unsigned DEFAULT '0',
  `Bildname` varchar(255) DEFAULT NULL,
  `Name_1` varchar(100) DEFAULT NULL,
  `Name_2` varchar(100) NOT NULL,
  `Name_3` varchar(100) NOT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Voting` int(10) unsigned DEFAULT '0',
  `Datum` int(14) unsigned DEFAULT '0',
  `Klicks` int(10) unsigned DEFAULT '0',
  `Autor` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_galerie_bilderfavoriten` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned NOT NULL,
  `Bild_Id` mediumint(5) unsigned NOT NULL,
  `Galerie_Id` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Benutzer` (`Benutzer`),
  KEY `Galerie_Id` (`Galerie_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_galerie_kategorien` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(2) NOT NULL DEFAULT '1',
  `Name_1` varchar(200) NOT NULL,
  `Name_2` varchar(200) NOT NULL,
  `Name_3` varchar(200) DEFAULT NULL,
  `Text_1` text,
  `Text_2` text,
  `Text_3` text,
  `Bild` varchar(50) DEFAULT NULL,
  `Tags` varchar(255) DEFAULT NULL,
  `Autor` int(10) unsigned NOT NULL DEFAULT '1',
  `Datum` int(14) unsigned NOT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Name_1` (`Name_1`),
  KEY `Name_2` (`Name_2`),
  KEY `Name_3` (`Name_3`),
  KEY `Tags` (`Tags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_genre` (
  `Id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_glossar` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Wort` varchar(200) DEFAULT NULL,
  `Beschreibung` text,
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Hits` int(10) unsigned NOT NULL DEFAULT '0',
  `Typ` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Wort` (`Wort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_hersteller` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(255) NOT NULL,
  `NameLang` varchar(255) DEFAULT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Bild` varchar(255) NOT NULL,
  `Gruendung` varchar(100) NOT NULL,
  `GruendungLand` varchar(25) DEFAULT NULL,
  `Personen` varchar(100) NOT NULL,
  `Homepage` varchar(255) NOT NULL,
  `Adresse` text,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Hits` int(10) unsigned NOT NULL DEFAULT '0',
  `Telefonkontakt` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_ignorierliste` (
  `BenutzerId` int(14) unsigned DEFAULT '0',
  `IgnorierId` int(14) unsigned DEFAULT '0',
  `Grund` varchar(75) DEFAULT NULL,
  `Datum` int(14) unsigned DEFAULT '0',
  KEY `BenutzerId` (`BenutzerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_kalender` (
  `Id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned DEFAULT '0',
  `Datum` varchar(10) DEFAULT NULL,
  `Titel` varchar(255) DEFAULT NULL,
  `Beschreibung` text,
  `Start` int(15) unsigned DEFAULT '0',
  `Ende` int(15) unsigned DEFAULT '0',
  `Typ` enum('public','private') DEFAULT 'public',
  `Gewicht` tinyint(1) unsigned DEFAULT '3',
  `wd` tinyint(1) unsigned DEFAULT '0',
  `tdays` int(15) unsigned DEFAULT '0',
  `Erledigt` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_kommentare` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Objekt_Id` bigint(14) unsigned NOT NULL,
  `Bereich` varchar(50) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Titel` varchar(255) NOT NULL,
  `Eintrag` text,
  `Autor` varchar(200) NOT NULL,
  `Autor_Id` int(10) unsigned DEFAULT NULL,
  `Autor_Web` varchar(200) NOT NULL,
  `Autor_Herkunft` varchar(200) NOT NULL,
  `Autor_Ip` varchar(100) NOT NULL,
  `Autor_Email` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Objekt_Id` (`Objekt_Id`),
  KEY `Bereich` (`Bereich`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_kontakt_form` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned NOT NULL,
  `Autor` int(10) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Titel1` varchar(200) NOT NULL,
  `Titel2` varchar(200) NOT NULL,
  `Titel3` varchar(200) NOT NULL,
  `Intro1` tinytext NOT NULL,
  `Intro2` tinytext NOT NULL,
  `Intro3` tinytext NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Anlage` smallint(1) NOT NULL DEFAULT '0',
  `Gruppen` varchar(200) NOT NULL DEFAULT '1,2,3,4,5,6,7,8,9,10',
  `Button_Name` varchar(200) DEFAULT NULL,
  `Email2` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_kontakt_form_felder` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Form_Id` int(10) unsigned NOT NULL,
  `Typ` enum('radio','checkbox','textfield','textarea','dropdown') NOT NULL DEFAULT 'textfield',
  `Pflicht` enum('0','1') NOT NULL DEFAULT '0',
  `Name1` varchar(200) NOT NULL,
  `Name2` varchar(200) NOT NULL,
  `Name3` varchar(200) NOT NULL,
  `Werte` text,
  `Posi` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Zahl` enum('0','1') NOT NULL DEFAULT '0',
  `Email` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Form_Id` (`Form_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_laender` (
  `Id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Code` char(2) DEFAULT 'RU',
  `Name` varchar(100) DEFAULT NULL,
  `Aktiv` tinyint(1) DEFAULT '1',
  `Ust` tinyint(1) DEFAULT '0',
  `VersandFreiAb` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Code` (`Code`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_links` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Bild` varchar(255) DEFAULT NULL,
  `Name_1` varchar(200) NOT NULL,
  `Name_2` varchar(200) NOT NULL,
  `Name_3` varchar(200) NOT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Url` varchar(255) NOT NULL,
  `Hits` int(10) unsigned NOT NULL DEFAULT '0',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Autor` int(10) unsigned NOT NULL DEFAULT '1',
  `DefektGemeldet` varchar(255) DEFAULT NULL,
  `DEmail` varchar(255) DEFAULT NULL,
  `DName` varchar(255) DEFAULT NULL,
  `DDatum` int(14) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Sprache` char(2) NOT NULL DEFAULT 'ru',
  `Sponsor` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Kategorie` (`Kategorie`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_links_kategorie` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name_1` varchar(200) NOT NULL,
  `Name_2` varchar(200) NOT NULL,
  `Name_3` varchar(200) NOT NULL,
  `Beschreibung_1` varchar(255) NOT NULL,
  `Beschreibung_2` varchar(255) NOT NULL,
  `Beschreibung_3` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Parent_Id` (`Parent_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_log` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned NOT NULL DEFAULT '0',
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '0',
  `Aktion` text,
  `Typ` tinyint(1) NOT NULL DEFAULT '1',
  `Ip` varchar(35) NOT NULL DEFAULT '',
  `Agent` tinytext NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_navi` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NaviCat` smallint(2) unsigned DEFAULT '1',
  `ParentId` int(10) unsigned DEFAULT NULL,
  `Titel_1` varchar(200) DEFAULT NULL,
  `Titel_2` varchar(200) DEFAULT NULL,
  `Titel_3` varchar(200) DEFAULT NULL,
  `Dokument` varchar(255) DEFAULT NULL,
  `DokumentRub` varchar(15) DEFAULT NULL,
  `Sektion` smallint(3) unsigned DEFAULT '1',
  `openonclick` tinyint(1) DEFAULT '1',
  `group_id` tinytext,
  `Position` tinyint(3) unsigned DEFAULT NULL,
  `Ziel` varchar(10) DEFAULT '_self',
  `Link_Titel_1` varchar(255) NOT NULL,
  `Link_Titel_2` varchar(255) NOT NULL,
  `Link_Titel_3` varchar(255) NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `NaviCat` (`NaviCat`),
  KEY `Dokument` (`Dokument`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_navi_cat` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Name_1` varchar(200) DEFAULT NULL,
  `Name_2` varchar(200) DEFAULT NULL,
  `Name_3` varchar(200) DEFAULT NULL,
  `Sektion` smallint(2) unsigned DEFAULT '1',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_news` (
  `Id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Autor` mediumint(5) unsigned NOT NULL DEFAULT '1',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Zeit` int(14) unsigned NOT NULL DEFAULT '0',
  `ZeitStart` int(14) unsigned NOT NULL DEFAULT '0',
  `ZeitEnde` int(14) unsigned NOT NULL DEFAULT '0',
  `Titel1` varchar(200) NOT NULL DEFAULT '',
  `Titel2` varchar(200) NOT NULL DEFAULT '',
  `Titel3` varchar(200) NOT NULL DEFAULT '',
  `Intro1` text,
  `Intro2` text,
  `Intro3` text,
  `News1` longtext NOT NULL,
  `News2` longtext NOT NULL,
  `News3` longtext NOT NULL,
  `Topnews_Bild_1` varchar(255) DEFAULT NULL,
  `Topnews_Bild_2` varchar(255) DEFAULT NULL,
  `Topnews_Bild_3` varchar(255) DEFAULT NULL,
  `Hits` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Bild1` varchar(255) DEFAULT NULL,
  `Bild2` varchar(255) DEFAULT NULL,
  `Bild3` varchar(255) DEFAULT NULL,
  `BildAusrichtung` enum('left','right') NOT NULL DEFAULT 'left',
  `Textbilder1` text,
  `Textbilder2` text,
  `Textbilder3` text,
  `AlleSektionen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Suche` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Bewertung` enum('0','1') NOT NULL DEFAULT '1',
  `Kommentare` enum('0','1') NOT NULL DEFAULT '1',
  `Tags` varchar(255) DEFAULT NULL,
  `Galerien` tinytext NOT NULL,
  `Topnews` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Topnews` (`Topnews`),
  KEY `ZeitEnde` (`ZeitEnde`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_news_kategorie` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(150) NOT NULL DEFAULT '',
  `Name_2` varchar(150) DEFAULT NULL,
  `Name_3` varchar(150) DEFAULT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Posi` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_newsletter` (
  `Id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) NOT NULL,
  `Info` text,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_newsletter_abos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Newsletter_Id` mediumint(5) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Format` enum('text','html') NOT NULL DEFAULT 'text',
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Code` varchar(10) NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_newsletter_archiv` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned DEFAULT '0',
  `Typ` enum('groups','abos') DEFAULT 'abos',
  `Titel` varchar(255) DEFAULT NULL,
  `Newsletter` longtext,
  `Email` varchar(255) DEFAULT NULL,
  `Absender` varchar(255) DEFAULT NULL,
  `Autor` int(10) unsigned DEFAULT '0',
  `Anlagen` text,
  `Gruppen` varchar(255) NOT NULL DEFAULT '',
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  `Code` longtext NOT NULL,
  `Sys` ENUM('one','later','more') NOT NULL DEFAULT 'one',
  `Noheader` ENUM('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_partner` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PartnerUrl` varchar(255) DEFAULT NULL,
  `Position` smallint(3) unsigned DEFAULT '0',
  `Hits` int(11) DEFAULT '0',
  `PartnerName` varchar(255) DEFAULT NULL,
  `Bild` varchar(255) DEFAULT NULL,
  `Sektion` smallint(3) unsigned DEFAULT '1',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Nofollow` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_phrases` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `phrase` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_plattformen` (
  `Id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) NOT NULL,
  `Icon` varchar(200) DEFAULT NULL,
  `Sektion` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_pn` (
  `pnid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `to_uid` mediumint(8) unsigned DEFAULT NULL,
  `from_uid` mediumint(8) unsigned DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `message` text,
  `is_readed` enum('yes','no') DEFAULT NULL,
  `pntime` int(11) DEFAULT '0',
  `typ` enum('inbox','outbox') DEFAULT 'inbox',
  `smilies` enum('yes','no') DEFAULT 'yes',
  `parseurl` enum('yes','no') DEFAULT 'no',
  `reply` enum('yes','no') DEFAULT 'no',
  `forward` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`pnid`),
  KEY `count` (`to_uid`,`typ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_posticons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posi` mediumint(5) DEFAULT '1',
  `active` tinyint(1) DEFAULT '1',
  `path` varchar(55) DEFAULT NULL,
  `area` smallint(2) unsigned NOT NULL DEFAULT '1',
  `title` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_produkte` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Datum_Veroffentlichung` int(14) unsigned NOT NULL,
  `Name1` varchar(255) DEFAULT NULL,
  `Name2` varchar(255) DEFAULT NULL,
  `Name3` varchar(255) DEFAULT NULL,
  `Beschreibung1` text,
  `Beschreibung2` text,
  `Beschreibung3` text,
  `Textbilder1` text,
  `Textbilder2` text,
  `Textbilder3` text,
  `Genre` mediumint(5) unsigned DEFAULT NULL,
  `Vertrieb` mediumint(5) unsigned DEFAULT NULL,
  `Hersteller` mediumint(5) unsigned DEFAULT NULL,
  `Wertung` smallint(3) unsigned DEFAULT NULL,
  `Asin` varchar(255) DEFAULT NULL,
  `Plattform` varchar(100) DEFAULT NULL,
  `Preis` varchar(100) DEFAULT NULL,
  `Shopurl` varchar(255) DEFAULT NULL,
  `Shop` varchar(255) DEFAULT NULL,
  `Bild` varchar(255) DEFAULT NULL,
  `Links` text,
  `Galerien` varchar(100) DEFAULT NULL,
  `Hits` int(10) unsigned DEFAULT '0',
  `Sektion` smallint(3) DEFAULT '1',
  `TopProduct` enum('0','1') NOT NULL DEFAULT '0',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Hersteller` (`Hersteller`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_quicknavi` (
  `Id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(2) unsigned NOT NULL,
  `Name_1` varchar(100) NOT NULL,
  `Name_2` varchar(100) NOT NULL,
  `Name_3` varchar(100) NOT NULL,
  `Dokument` varchar(255) NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Ziel` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `Position` smallint(3) unsigned NOT NULL,
  `Gruppe` varchar(25) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_sektionen` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Name_2` varchar(100) NOT NULL,
  `Name_3` varchar(100) NOT NULL,
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Passwort` varchar(15) DEFAULT NULL,
  `Meldung` text,
  `Template` varchar(100) NOT NULL DEFAULT 'standard',
  `CSS_Theme` varchar(25) NOT NULL DEFAULT 'standard',
  `LimitNews` smallint(2) unsigned NOT NULL DEFAULT '10',
  `LimitNewsArchive` smallint(2) unsigned NOT NULL DEFAULT '10',
  `LimitNewlinks` smallint(2) unsigned NOT NULL DEFAULT '3',
  `LimitNewDownloads` smallint(2) unsigned NOT NULL DEFAULT '3',
  `LimitNewProducts` smallint(2) unsigned NOT NULL DEFAULT '3',
  `LimitNewCheats` smallint(2) unsigned NOT NULL DEFAULT '3',
  `LimitNewGalleries` smallint(2) unsigned NOT NULL DEFAULT '3',
  `LimitLastPosts` smallint(2) unsigned NOT NULL DEFAULT '4',
  `LimitLastThreads` smallint(2) unsigned NOT NULL DEFAULT '4',
  `LimitTopArticles` smallint(2) unsigned NOT NULL DEFAULT '10',
  `LimitTopcontent` smallint(2) unsigned NOT NULL DEFAULT '10',
  `Tpl_shop` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_news` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_newsarchive` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_index` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_sitemap` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_useraction` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_calendar` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_faq` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_gallery` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_articles` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_products` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_downloads` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_links` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_register` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_misc` varchar(100) NOT NULL DEFAULT 'popup.tpl',
  `Tpl_forums` varchar(100) NOT NULL DEFAULT 'forum.tpl',
  `Tpl_members` varchar(100) NOT NULL DEFAULT 'forum.tpl',
  `Tpl_pn` varchar(100) NOT NULL DEFAULT 'forum.tpl',
  `Tpl_pwlost` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_manufacturer` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_cheats` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_poll` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_guestbook` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_content` varchar(100) NOT NULL DEFAULT 'main.tpl',
  `Tpl_newsletter` varchar(200) NOT NULL DEFAULT 'main.tpl',
  `Tpl_imprint` varchar(200) NOT NULL DEFAULT 'main.tpl',
  `Tpl_search` varchar(200) NOT NULL DEFAULT 'main.tpl',
  `StartText` longtext,
  `ZeigeStartText` enum('0','1') NOT NULL DEFAULT '0',
  `ZeigeStartTextNur` enum('0','1') NOT NULL DEFAULT '0',
  `Domains` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_sessions` (
  `Schluessel` varchar(32) NOT NULL DEFAULT '',
  `Ablauf` int(14) unsigned DEFAULT '0',
  `Wert` text,
  `Ip` varchar(35) NOT NULL DEFAULT '',
  PRIMARY KEY (`Schluessel`),
  KEY `Ablauf` (`Ablauf`),
  KEY `Ip` (`Ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_bestellungen` (
  `Id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `Datum` int(14) unsigned DEFAULT NULL,
  `Ip` varchar(25) DEFAULT NULL,
  `Benutzer` int(14) unsigned DEFAULT NULL,
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Betrag` decimal(10,2) DEFAULT '0.00',
  `Status` enum('wait', 'progress', 'ok', 'failed', 'oksend', 'oksendparts') DEFAULT 'wait',
  `Artikel` text,
  `Payment` tinyint(1) DEFAULT '0',
  `TransaktionsNummer` varchar(20) DEFAULT NULL,
  `USt` decimal(10,2) DEFAULT '0.00',
  `ZahlungsId` smallint(2) unsigned DEFAULT '1',
  `VersandId` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Rng_Firma` varchar(200) DEFAULT NULL,
  `Rng_Vorname` varchar(100) DEFAULT NULL,
  `Rng_Nachname` varchar(100) DEFAULT NULL,
  `Rng_Strasse` varchar(200) NOT NULL DEFAULT '',
  `Rng_Plz` varchar(15) NOT NULL DEFAULT '',
  `Rng_Ort` varchar(200) NOT NULL DEFAULT '',
  `Rng_Land` varchar(30) NOT NULL DEFAULT '',
  `Rng_Fon` varchar(100) NOT NULL DEFAULT '',
  `Rng_Fax` varchar(100) NOT NULL DEFAULT '',
  `Rng_Email` varchar(200) NOT NULL DEFAULT '',
  `Lief_Vorname` varchar(100) NOT NULL DEFAULT '',
  `Lief_Nachname` varchar(100) NOT NULL DEFAULT '',
  `Lief_Strasse` varchar(100) NOT NULL DEFAULT '',
  `Lief_Plz` varchar(100) NOT NULL DEFAULT '',
  `Lief_Ort` varchar(100) NOT NULL DEFAULT '',
  `Lief_Fon` varchar(100) NOT NULL DEFAULT '',
  `Lief_Fax` varchar(100) NOT NULL DEFAULT '',
  `Lief_Land` varchar(100) NOT NULL DEFAULT '',
  `Lief_Firma` varchar(100) NOT NULL DEFAULT '',
  `Bestellung` longtext,
  `Gewicht` int(10) DEFAULT '0',
  `GutscheinWert` decimal(10,2) DEFAULT '0.00',
  `GutscheinId` int(10) unsigned DEFAULT '0',
  `Geloescht` tinyint(1) unsigned DEFAULT '0',
  `KundenNachricht` text,
  `RechnungGesendet` tinyint(1) unsigned DEFAULT '0',
  `WareGesendet` tinyint(1) unsigned DEFAULT '0',
  `Bemerkung` text,
  `UStId` varchar(20) DEFAULT NULL,
  `WarenwertBrutto` decimal(8,2) unsigned NOT NULL,
  `WarenwertNetto` decimal(8,2) unsigned NOT NULL,
  `Versandkosten` decimal(8,2) unsigned NOT NULL,
  `ZuschlagZahlungsart` varchar(20) NOT NULL,
  `Tracking_Id` smallint(3) unsigned NOT NULL,
  `Tracking_Code` varchar(255) NOT NULL,
  `Rng_MiddleName` varchar(100) NOT NULL DEFAULT '',
  `Rng_BankName` text,
  `Lief_MiddleName` varchar(100) NOT NULL DEFAULT '',
  `Order_Type` longtext,
  `Verschickt` text,
  PRIMARY KEY (`Id`),
  KEY `Benutzer` (`Benutzer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_bestellungen_items` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(14) NOT NULL,
  `Vorname` varchar(100) NOT NULL,
  `Nachname` varchar(255) NOT NULL,
  `Firma` varchar(255) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Datum_TS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Bestellnummer` varchar(35) NOT NULL,
  `Artikelnummer` varchar(35) NOT NULL,
  `ArtikelName` varchar(255) NOT NULL,
  `Anzahl` smallint(3) NOT NULL,
  `Varianten` text,
  `Konfig_Frei` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_bewertung` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Produkt` int(10) unsigned NOT NULL,
  `Bewertung` text,
  `Bewertung_Punkte` tinyint(1) unsigned NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Ip` varchar(25) NOT NULL,
  `Offen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Produkt` (`Produkt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_download_log` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(14) unsigned NOT NULL,
  `Produkt` varchar(255) NOT NULL,
  `ProduktId` int(14) unsigned NOT NULL,
  `Datum` int(14) NOT NULL,
  `Ip` varchar(25) NOT NULL,
  `UrlLizenz` varchar(255) NOT NULL,
  `KommentarBenutzer` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_downloads` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ArtId` varchar(255) NOT NULL DEFAULT '',
  `Datei` varchar(255) NOT NULL DEFAULT '',
  `DateiTyp` enum('full','update','bugfix','other') NOT NULL DEFAULT 'full',
  `TageNachKauf` mediumint(5) NOT NULL DEFAULT '365',
  `Bild` varchar(255) NOT NULL DEFAULT '',
  `Titel` varchar(200) NOT NULL DEFAULT '',
  `Beschreibung` text,
  `Position` mediumint(3) unsigned NOT NULL DEFAULT '1',
  `Datum` int(14) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_downloads_user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Benutzer` int(11) NOT NULL DEFAULT '0',
  `PName` varchar(255) NOT NULL DEFAULT '',
  `ArtikelId` varchar(50) NOT NULL DEFAULT '',
  `DownloadBis` int(11) NOT NULL DEFAULT '0',
  `Lizenz` varchar(20) NOT NULL DEFAULT '',
  `Downloads` int(11) NOT NULL DEFAULT '0',
  `UrlLizenz` varchar(255) NOT NULL DEFAULT '',
  `KommentarBenutzer` text,
  `KommentarAdmin` text,
  `Gesperrt` tinyint(1) NOT NULL DEFAULT '0',
  `GesperrtGrund` text,
  `Position` smallint(2) NOT NULL DEFAULT '1',
  `UrlLizenz_Pflicht` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_eigenschaften` (
  `Id` smallint(2) NOT NULL AUTO_INCREMENT,
  `Name_1` varchar(100) NOT NULL DEFAULT '',
  `Name_2` varchar(100) NOT NULL DEFAULT '',
  `Name_3` varchar(100) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_einheiten` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Titel_1` varchar(100) DEFAULT NULL,
  `Titel_2` varchar(100) DEFAULT NULL,
  `Titel_3` varchar(100) DEFAULT NULL,
  `Mz_1` varchar(100) DEFAULT NULL,
  `Mz_2` varchar(100) DEFAULT NULL,
  `Mz_3` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_esd` (
  `id` bigint(14) NOT NULL AUTO_INCREMENT,
  `uid` bigint(14) DEFAULT '0',
  `dltimes` int(10) unsigned DEFAULT '0',
  `dlid` varchar(255) DEFAULT NULL,
  `dltimespan` int(14) DEFAULT '0',
  `itemkey` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_gutscheine` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Code` varchar(20) DEFAULT NULL,
  `Benutzer` text,
  `Wert` decimal(8,2) unsigned DEFAULT '0.00',
  `Erstellt` int(11) DEFAULT '0',
  `Eingeloest` int(11) DEFAULT '0',
  `Bestellnummer` text,
  `Typ` enum('wert','pro') DEFAULT 'wert',
  `Hersteller` text,
  `Endlos` tinyint(1) unsigned DEFAULT '0',
  `BenutzerMulti` text,
  `GueltigBis` varchar(15) DEFAULT NULL,
  `Bestellnummern` text,
  `MinBestellwert` decimal(8,2) unsigned DEFAULT '0.00',
  `Gastbestellung` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `CommentCupon` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_kategorie` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Parent_Id` mediumint(5) unsigned DEFAULT '0',
  `Name_1` varchar(100) DEFAULT NULL,
  `Name_2` varchar(100) DEFAULT NULL,
  `Name_3` varchar(100) DEFAULT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `posi` smallint(3) unsigned DEFAULT '1',
  `icon` varchar(255) DEFAULT NULL,
  `UstId` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Bild_Navi` varchar(120) DEFAULT NULL,
  `Bild_Kategorie` varchar(120) DEFAULT NULL,
  `Search` enum('0','1') NOT NULL DEFAULT '1',
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  `Gruppen` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  KEY `Name_1` (`Name_1`),
  KEY `Name_2` (`Name_2`),
  KEY `Name_3` (`Name_3`),
  KEY `Parent_Id` (`Parent_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_kategorie_spezifikation` (
  `Id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `Kategorie` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Spez_1` varchar(100) DEFAULT NULL,
  `Spez_2` varchar(100) DEFAULT NULL,
  `Spez_3` varchar(100) DEFAULT NULL,
  `Spez_4` varchar(100) DEFAULT NULL,
  `Spez_5` varchar(100) DEFAULT NULL,
  `Spez_6` varchar(100) DEFAULT NULL,
  `Spez_7` varchar(100) DEFAULT NULL,
  `Spez_8` varchar(100) DEFAULT NULL,
  `Spez_9` varchar(100) DEFAULT NULL,
  `Spez_10` varchar(100) DEFAULT NULL,
  `Spez_11` varchar(100) DEFAULT NULL,
  `Spez_12` varchar(100) DEFAULT NULL,
  `Spez_13` varchar(100) DEFAULT NULL,
  `Spez_14` varchar(100) DEFAULT NULL,
  `Spez_15` varchar(100) DEFAULT NULL,
  `Spez_1_2` varchar(100) DEFAULT NULL,
  `Spez_2_2` varchar(100) DEFAULT NULL,
  `Spez_3_2` varchar(100) DEFAULT NULL,
  `Spez_4_2` varchar(100) DEFAULT NULL,
  `Spez_5_2` varchar(100) DEFAULT NULL,
  `Spez_6_2` varchar(100) DEFAULT NULL,
  `Spez_7_2` varchar(100) DEFAULT NULL,
  `Spez_8_2` varchar(100) DEFAULT NULL,
  `Spez_9_2` varchar(100) DEFAULT NULL,
  `Spez_10_2` varchar(100) DEFAULT NULL,
  `Spez_11_2` varchar(100) DEFAULT NULL,
  `Spez_12_2` varchar(100) DEFAULT NULL,
  `Spez_13_2` varchar(100) DEFAULT NULL,
  `Spez_14_2` varchar(100) DEFAULT NULL,
  `Spez_15_2` varchar(100) DEFAULT NULL,
  `Spez_1_3` varchar(100) DEFAULT NULL,
  `Spez_2_3` varchar(100) DEFAULT NULL,
  `Spez_3_3` varchar(100) DEFAULT NULL,
  `Spez_4_3` varchar(100) DEFAULT NULL,
  `Spez_5_3` varchar(100) DEFAULT NULL,
  `Spez_6_3` varchar(100) DEFAULT NULL,
  `Spez_7_3` varchar(100) DEFAULT NULL,
  `Spez_8_3` varchar(100) DEFAULT NULL,
  `Spez_9_3` varchar(100) DEFAULT NULL,
  `Spez_10_3` varchar(100) DEFAULT NULL,
  `Spez_11_3` varchar(100) DEFAULT NULL,
  `Spez_12_3` varchar(100) DEFAULT NULL,
  `Spez_13_3` varchar(100) DEFAULT NULL,
  `Spez_14_3` varchar(100) DEFAULT NULL,
  `Spez_15_3` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_kategorie_zubehoer` (
  `Id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Kategorie` smallint(3) unsigned NOT NULL,
  `Teile_1_Name_1` varchar(100) NOT NULL DEFAULT 'Teile 1',
  `Teile_1_Name_2` varchar(100) NOT NULL DEFAULT 'Parts 1',
  `Teile_1_Name_3` varchar(100) NOT NULL DEFAULT 'Parts 1',
  `Teile_2_Name_1` varchar(100) NOT NULL DEFAULT 'Teile 2',
  `Teile_2_Name_2` varchar(100) NOT NULL DEFAULT 'Parts 2',
  `Teile_2_Name_3` varchar(100) NOT NULL DEFAULT 'Parts 2',
  `Teile_3_Name_1` varchar(100) NOT NULL DEFAULT 'Teile 3',
  `Teile_3_Name_2` varchar(100) NOT NULL DEFAULT 'Parts 3',
  `Teile_3_Name_3` varchar(100) NOT NULL DEFAULT 'Parts 3',
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Kategorie` (`Kategorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_kundendownloads` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Bestellung` int(10) unsigned NOT NULL,
  `Kunde` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Datei` varchar(255) NOT NULL,
  `Titel` varchar(200) NOT NULL DEFAULT '',
  `Beschreibung` text,
  `Downloads` int(5) unsigned NOT NULL,
  KEY `Id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_lizenzen` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(14) unsigned NOT NULL,
  `Lizenz` varchar(25) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Datum_Stamp` datetime NOT NULL,
  `Produktname` varchar(200) NOT NULL,
  `GueltigBis` varchar(100) NOT NULL,
  `ProuktId` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Lizenz` (`Lizenz`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_merkzettel` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(200) NOT NULL DEFAULT '',
  `Datum` int(14) unsigned NOT NULL DEFAULT '0',
  `Inhalt` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_packzeiten` (
  `Id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `Lieferzeit_1` tinytext NOT NULL,
  `Lieferzeit_2` tinytext NOT NULL,
  `Lieferzeit_3` tinytext NOT NULL,
  `Bemerkung` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_preisalarm` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ProdId` int(10) unsigned NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Ip` varchar(25) NOT NULL,
  `Preis` decimal(6,2) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_produkte` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Kategorie` int(11) DEFAULT '0',
  `Kategorie_Multi` varchar(255) DEFAULT NULL,
  `Schlagwoerter` varchar(255) DEFAULT NULL,
  `Zub_a` text,
  `Zub_b` text,
  `Zub_c` text,
  `Artikelnummer` varchar(35) NOT NULL DEFAULT '',
  `Preis` decimal(10,2) unsigned DEFAULT '0.00',
  `Preis_Liste` decimal(10,2) unsigned DEFAULT '0.00',
  `Preis_Liste_Gueltig` int(14) unsigned NOT NULL DEFAULT '0',
  `Preis_EK` decimal(10,2) NOT NULL,
  `Titel_1` varchar(255) DEFAULT NULL,
  `Titel_2` varchar(200) DEFAULT NULL,
  `Titel_3` varchar(200) DEFAULT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Beschreibung_lang_1` text,
  `Beschreibung_lang_2` text,
  `Beschreibung_lang_3` text,
  `Hat_ESD` enum('0','1') NOT NULL DEFAULT '0',
  `Aktiv` tinyint(1) DEFAULT '1',
  `Erstellt` int(11) DEFAULT '0',
  `Klicks` int(10) unsigned DEFAULT '0',
  `Bild` varchar(255) DEFAULT NULL,
  `Bilder` text,
  `Gewicht` int(10) unsigned DEFAULT '0',
  `Gewicht_Ohne` int(10) NOT NULL DEFAULT '0',
  `Abmessungen` varchar(100) DEFAULT NULL,
  `Hersteller` mediumint(5) unsigned DEFAULT '0',
  `EinheitCount` decimal(8,3) unsigned DEFAULT '1.000',
  `EinheitId` int(10) unsigned DEFAULT '0',
  `Startseite` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Lagerbestand` mediumint(5) unsigned NOT NULL DEFAULT '999',
  `Bestellt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Verfuegbar` smallint(2) unsigned NOT NULL DEFAULT '1',
  `EinzelBestellung` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Verkauft` mediumint(5) unsigned DEFAULT '0',
  `MaxBestellung` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `MinBestellung` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `Lieferzeit` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Spez_1` text,
  `Spez_2` text,
  `Spez_3` text,
  `Spez_4` text,
  `Spez_5` text,
  `Spez_6` text,
  `Spez_7` text,
  `Spez_8` text,
  `Spez_9` text,
  `Spez_10` text,
  `Spez_11` text,
  `Spez_12` text,
  `Spez_13` text,
  `Spez_14` text,
  `Spez_15` text,
  `Spez_1_2` text,
  `Spez_2_2` text,
  `Spez_3_2` text,
  `Spez_4_2` text,
  `Spez_5_2` text,
  `Spez_6_2` text,
  `Spez_7_2` text,
  `Spez_8_2` text,
  `Spez_9_2` text,
  `Spez_10_2` text,
  `Spez_11_2` text,
  `Spez_12_2` text,
  `Spez_13_2` text,
  `Spez_14_2` text,
  `Spez_15_2` text,
  `Spez_1_3` text,
  `Spez_2_3` text,
  `Spez_3_3` text,
  `Spez_4_3` text,
  `Spez_5_3` text,
  `Spez_6_3` text,
  `Spez_7_3` text,
  `Spez_8_3` text,
  `Spez_9_3` text,
  `Spez_10_3` text,
  `Spez_11_3` text,
  `Spez_12_3` text,
  `Spez_13_3` text,
  `Spez_14_3` text,
  `Spez_15_3` text,
  `Fsk18` enum('0','1') NOT NULL DEFAULT '0',
  `Frei_1` varchar(200) NOT NULL,
  `Frei_2` varchar(200) NOT NULL,
  `Frei_3` varchar(200) NOT NULL,
  `Frei_1_Pflicht` enum('0','1') NOT NULL DEFAULT '0',
  `Frei_2_Pflicht` enum('0','1') NOT NULL DEFAULT '0',
  `Frei_3_Pflicht` enum('0','1') NOT NULL DEFAULT '0',
  `Gruppen` varchar(255) NOT NULL DEFAULT '',
  `EinheitBezug` decimal(8,3) NOT NULL,
  `EAN_Nr` varchar(75) NOT NULL,
  `ISBN_Nr` varchar(75) NOT NULL,
  `SeitenTitel` varchar(255) NOT NULL,
  `Template` varchar(75) NOT NULL,
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  `PrCountry` VARCHAR(75) NOT NULL,
  `Yml` ENUM('0','1') NOT NULL DEFAULT '1',
  `MetaTags` VARCHAR(255) NOT NULL,
  `MetaDescription` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Artikelnummer` (`Artikelnummer`),
  KEY `Schlagwoerter` (`Schlagwoerter`),
  KEY `Titel_1` (`Titel_1`),
  KEY `Titel_2` (`Titel_2`),
  KEY `Titel_3` (`Titel_3`),
  KEY `Preis` (`Preis`),
  KEY `Kategorie_Multi` (`Kategorie_Multi`),
  KEY `Kategorie` (`Kategorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_shipper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipper_id` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '0',
  `fullname` varchar(255) DEFAULT NULL,
  `descr` text,
  `icon` varchar(255) DEFAULT NULL,
  `no_values` tinyint(1) unsigned DEFAULT '0',
  `allowed_payments` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_staffelpreise` (
  `Id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `ArtikelId` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `Von` smallint(3) unsigned DEFAULT NULL,
  `Bis` smallint(3) unsigned DEFAULT NULL,
  `Wert` decimal(6,2) DEFAULT NULL,
  `Operand` ENUM('pro','wert') NOT NULL DEFAULT 'pro',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_topseller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `artnumber` varchar(100) DEFAULT NULL,
  `amount` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_tracking` (
  `Id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Hyperlink` varchar(255) NOT NULL,
  `TrNr` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_ustzone` (
  `Id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Wert` decimal(6,2) unsigned DEFAULT '18.00',
  PRIMARY KEY (`Id`),
  KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_varianten` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `KatId` int(10) unsigned NOT NULL DEFAULT '0',
  `ArtId` int(10) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(255) NOT NULL DEFAULT '',
  `Name_2` varchar(255) NOT NULL DEFAULT '',
  `Name_3` varchar(255) NOT NULL DEFAULT '',
  `Wert` decimal(9,2) unsigned NOT NULL DEFAULT '0.00',
  `Operant` enum('+','-') NOT NULL DEFAULT '+',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  `Vorselektiert` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Gewicht` int(10) unsigned NOT NULL DEFAULT '0',
  `GewichtOperant` enum('+','-') NOT NULL DEFAULT '+',
  `Bestand` int(10) unsigned NOT NULL DEFAULT '1000',
  PRIMARY KEY (`Id`),
  KEY `KatId` (`KatId`),
  KEY `ArtId` (`ArtId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_varianten_kategorien` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `KatId` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `Name_1` varchar(200) NOT NULL DEFAULT '',
  `Name_2` varchar(200) NOT NULL DEFAULT '',
  `Name_3` varchar(200) NOT NULL DEFAULT '',
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `KatId` (`KatId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_verfuegbarkeit` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Titel_1` varchar(255) DEFAULT NULL,
  `Titel_2` varchar(255) DEFAULT NULL,
  `Titel_3` varchar(255) DEFAULT NULL,
  `Text_1` text,
  `Text_2` text,
  `Text_3` text,
  PRIMARY KEY (`Id`),
  KEY `Titel_1` (`Titel_1`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_versandarten` (
  `Id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name_1` varchar(100) DEFAULT NULL,
  `Name_2` varchar(100) DEFAULT NULL,
  `Name_3` varchar(100) DEFAULT NULL,
  `Beschreibung_1` text,
  `Beschreibung_2` text,
  `Beschreibung_3` text,
  `Gruppen` tinytext NOT NULL,
  `Laender` tinytext NOT NULL,
  `Gebuehr_Pauschal` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  `GewichtNull` tinyint(1) NOT NULL DEFAULT '0',
  `Aktiv` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `Versanddauer` varchar(255) NOT NULL,
  `Icon` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_versandarten_volumen` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `VersandId` mediumint(5) unsigned NOT NULL DEFAULT '1',
  `Von` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `Bis` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `Gebuehr` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_warenkorb` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Benutzer` int(14) unsigned NOT NULL,
  `ZeitBis` int(14) unsigned NOT NULL,
  `ZeitBisRaw` varchar(20) NOT NULL,
  `Inhalt` text,
  `InhaltKonf` longtext NOT NULL,
  `Gesperrt` enum('0','1') NOT NULL DEFAULT '0',
  `EingeloestAm` int(14) NOT NULL,
  `EingeloestAmRaw` varchar(20) NOT NULL,
  `Code` varchar(200) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_zahlungsmethoden` (
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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_smileys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posi` mediumint(5) DEFAULT '1',
  `active` tinyint(1) DEFAULT '1',
  `code` varchar(15) DEFAULT NULL,
  `path` varchar(55) DEFAULT NULL,
  `area` smallint(2) DEFAULT '1',
  `title` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_sprachen` (
  `Id` smallint(2) NOT NULL AUTO_INCREMENT,
  `Sprachcode` char(2) NOT NULL DEFAULT 'ru',
  `Locale` varchar(15) NOT NULL DEFAULT '',
  `Sprache` varchar(50) NOT NULL DEFAULT 'Русский',
  `Zeitformat` varchar(30) NOT NULL DEFAULT '%d.%m.%Y, %H:%M',
  `Stundenformat` varchar(15) NOT NULL DEFAULT '',
  `Aktiv` enum('1','2') NOT NULL DEFAULT '1',
  `Posi` smallint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_sprachen_admin` (
  `Id` smallint(2) NOT NULL AUTO_INCREMENT,
  `Sprachcode` char(2) NOT NULL DEFAULT 'ru',
  `Locale` varchar(15) NOT NULL DEFAULT '',
  `Aktiv` enum('1','2') NOT NULL DEFAULT '1',
  `Posi` smallint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_suche_log` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Suche` varchar(255) DEFAULT NULL,
  `Ip` varchar(32) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Suchort` varchar(50) NOT NULL,
  `UserId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Suchort` (`Suchort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_umfrage` (
  `Id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Titel_1` varchar(255) NOT NULL,
  `Titel_2` varchar(255) NOT NULL,
  `Titel_3` varchar(255) NOT NULL,
  `Sektion` smallint(3) unsigned NOT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  `Gruppen` tinytext NOT NULL,
  `IpLog` longtext NOT NULL,
  `UserLog` text,
  `Start` int(14) unsigned NOT NULL,
  `Ende` int(14) unsigned NOT NULL,
  `Kommentare` enum('0','1') NOT NULL DEFAULT '1',
  `Multi` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Sektion` (`Sektion`),
  KEY `Aktiv` (`Aktiv`),
  KEY `Multi` (`Multi`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_umfrage_fragen` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UmfrageId` mediumint(3) unsigned NOT NULL,
  `Frage_1` varchar(255) NOT NULL,
  `Frage_2` varchar(255) NOT NULL,
  `Frage_3` varchar(255) NOT NULL,
  `Farbe` varchar(15) NOT NULL DEFAULT 'rot',
  `Hits` int(10) unsigned NOT NULL DEFAULT '0',
  `Position` smallint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `UmfrageId` (`UmfrageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_videos` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(255) NOT NULL,
  `Video` varchar(200) NOT NULL,
  `Bild` varchar(200) NOT NULL,
  `Breite` varchar(10) NOT NULL DEFAULT '100%',
  `Hoehe` varchar(10) NOT NULL DEFAULT '400',
  `Datum` int(12) unsigned NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_wertung` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `Bereich` varchar(30) NOT NULL,
  `Objekt_Id` int(14) unsigned NOT NULL,
  `IPAdresse` varchar(50) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Wertung` int(14) unsigned NOT NULL,
  `Gesamt` int(14) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_description` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Text` varchar(70) DEFAULT NULL,
  `Aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_ping` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Dokument` varchar(255) NOT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_navi_flashtag` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL DEFAULT '',
  `Size` smallint(2) unsigned NOT NULL DEFAULT '10',
  `Dokument` varchar(255) NOT NULL,
  `Aktiv` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_sitemap_items` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `active` varchar(250) NOT NULL DEFAULT '1',
  `prio` varchar(250) NOT NULL DEFAULT '0.5',
  `changef` varchar(255) NOT NULL DEFAULT 'always',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_roadmap` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `Beschreibung` text,
  `Aktiv` tinyint(1) unsigned NOT NULL default '1',
  `Pos` varchar(250) NOT NULL DEFAULT '',
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_roadmap_tickets` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Rid` varchar(10) NOT NULL DEFAULT '',
  `Beschreibung` text,
  `Datum` varchar(250) NOT NULL DEFAULT '',
  `Fertig` tinyint(1) unsigned NOT NULL default '0',
  `Uid` varchar(250) NOT NULL DEFAULT '',
  `pr` varchar(10) NOT NULL DEFAULT '',
  `Sektion` smallint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_admin_notes` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `UserId` int(10) unsigned NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Text` text,
  `Type` enum('main','pub') NOT NULL DEFAULT 'main',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_user_friends` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(15) default NULL,
  `FreundId` varchar(25) NOT NULL default '0',
  `Aktiv` varchar(5) NOT NULL,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_user_gallery` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(12) NOT NULL,
  `Datum` varchar(25) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `Beschreibung` text,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_user_images` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `GalerieId` varchar(12) NOT NULL,
  `Datum` varchar(25) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Datei` varchar(150) NOT NULL,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_user_values` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `BenutzerId` varchar(15) NOT NULL default '',
  `Besucher` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_webpayment` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(25) NOT NULL DEFAULT '',
  `price_order` decimal(10,2),
  `user_order_date` varchar(40) NOT NULL DEFAULT '',
  `hashcode` varchar(40) NOT NULL DEFAULT '',
  `check_call` varchar(40) NOT NULL DEFAULT '',
  `system` varchar(40) NOT NULL DEFAULT '',
  `info` longtext NOT NULL,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_bestellungen_historie` (
  `Id` mediumint(10) unsigned NOT NULL auto_increment,
  `BestellNummer` int(10) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `Subjekt` varchar(255) NOT NULL,
  `Kommentar` text,
  `StatusText` text,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_produkte_downloads` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProduktId` int(10) unsigned NOT NULL,
  `Datei` varchar(255) NOT NULL,
  `Datum` int(14) unsigned NOT NULL,
  `DlName` varchar(200) NOT NULL,
  `Beschreibung` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_schedule` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_audios` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Sektion` smallint(3) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(255) NOT NULL,
  `Audio` varchar(200) DEFAULT NULL,
  `Width` varchar(10) NOT NULL DEFAULT '400',
  `Datum` int(12) unsigned NOT NULL,
  `Benutzer` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_shop_warenkorb_gaeste` (
  `Id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `BenutzerId` varchar(50) NOT NULL,
  `Ablauf` int(14) unsigned NOT NULL,
  `Inhalt` longtext NOT NULL,
  `InhaltConfig` text,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `BenutzerId` (`BenutzerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_seotags` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `canonical` varchar(255) NOT NULL DEFAULT '',
  `aktiv` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page` (`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_settings` (
  `Id` varchar(100) NOT NULL,
  `Modul` varchar(50) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Type` enum('int','string') NOT NULL DEFAULT 'string',
  `Value` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
__SX_CMS__CREATE TABLE IF NOT EXISTS `sx_collection` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(100) NOT NULL,
  `Text1` text,
  `Text2` text,
  `Text3` text,
  `Marker` VARCHAR(150) NOT NULL DEFAULT '',
  `Active` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
    }

}