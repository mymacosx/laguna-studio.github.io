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

ignore_user_abort(true);
set_time_limit(600);
function importUmlaut($text) {
    return str_replace(array('Ђ', '»', '«', '©', '®', '™', '„', '“'), array('&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;'), $text);
}

function importUpload() {
    $options = array(
        'type'   => 'load',
        'rand'   => true,
        'result' => 'data',
        'upload' => '/temp/cache/',
        'input'  => 'csvfile',
    );
    $object = SX::object('Upload');
    $object->extensions('load', array('txt', 'csv', 'xls', 'xlsx'));
    $array = $object->load($options);
    return !empty($array) ? $array : '';
}

function importCateg($db) {
    static $result = false;
    if ($result === false) {
        $dn = 'Категория создана при импорте товаров ' . date('d.m.Y H:i:s') . ' (неактивно)';
        $insert_array = array(
            'Parent_Id' => '',
            'Name_1'    => $dn,
            'Name_2'    => $dn,
            'Name_3'    => $dn,
            'UstId'     => 1,
            'Sektion'   => 1,
            'Aktiv'     => 0);
        $db->insert_query('shop_kategorie', $insert_array);
        $result = $db->insert_id();
    }
    return $result;
}

function importPrise($value) {
    static $result = false;
    if ($result === false) {
        if (Arr::getRequest('netto_to_brutto') == 1) {
            if (Arr::getRequest('operand') == '-') {
                $mp1 = Tool::cleanDigit(Arr::getRequest('mpli'));
                $mp2 = '0.' . $mp1;
                $result = 1 - $mp2;
            } else {
                $mp1 = '1.';
                $mp2 = Tool::cleanDigit(Arr::getRequest('mpli'));
                $result = $mp1 . $mp2;
            }
        } else {
            $result = 'none';
        }
    }
    $value = str_replace(',', '.', $value);
    if ($result != 'none') {
        $value = numf($value * $result);
    }
    return $value;
}

$TempDir = TEMP_DIR . '/cache/';
$array_fields = array(
    'Id'                  => 'ID товара (не менять!) | в базе Id',
    'Artikelnummer'       => 'Артикул товара | в базе Artikelnummer',
    'Kategorie'           => 'Основная категория | в базе Kategorie',
    'Kategorie_Multi'     => 'Дополнительные категории | в базе Kategorie_Multi',
    'Schlagwoerter'       => 'Ключевые слова товара | в базе Schlagwoerter',
    'Zub_a'               => 'Аксессуары (1 - опция) | в базе Zub_a',
    'Zub_b'               => 'Аксессуары (2 - опция) | в базе Zub_b',
    'Zub_c'               => 'Аксессуары (3 - опцияl) | в базе Zub_c',
    'Preis_Liste'         => 'Стоимость товара | в базе Preis_Liste',
    'Preis'               => 'Стоимость товара (Специальное предложение) | в базе Preis',
    'Preis_Liste_Gueltig' => 'Дата завершения Специального предложения | в базе Preis_Liste_Gueltig',
    'Preis_EK'            => 'Закупочная цена, отображается только в админ панели | в базе Preis_EK',
    'Titel_1'             => 'Наименование товара (1. Язык) | в базе Titel_1',
    'Titel_2'             => 'Наименование товара (2. Язык) | в базе Titel_2',
    'Titel_3'             => 'Наименование товара (3. Язык) | в базе Titel_3',
    'Beschreibung_1'      => 'Краткое описание товара (1. Язык) | в базе Beschreibung_1',
    'Beschreibung_2'      => 'Краткое описание товара (2. Язык) | в базе Beschreibung_2',
    'Beschreibung_3'      => 'Краткое описание товара (3. Язык) | в базе Beschreibung_3',
    'Beschreibung_lang_1' => 'Полное описание товара (1. Язык) | в базе Beschreibung_lang_1',
    'Beschreibung_lang_2' => 'Полное описание товара (2. Язык) | в базе Beschreibung_lang_2',
    'Beschreibung_lang_3' => 'Полное описание товара (3. Язык) | в базе Beschreibung_lang_3',
    'Hat_ESD'             => 'Товар имеет файлы для скачивания | в базе Hat_ESD',
    'Aktiv'               => 'Товар активен для продажи (1/0) | в базе Aktiv',
    'Erstellt'            => 'Дата размещения товара | в базе Erstellt',
    'Klicks'              => 'Количество просмотров товара | в базе Klicks',
    'Bild'                => 'Основное изображение товара | в базе Bild',
    'Bilder'              => 'Дополнительные изображения товара | в базе Bilder',
    'Gewicht'             => 'Вес товара в граммах | в базе Gewicht',
    'Gewicht_Ohne'        => 'Вес товара без упаковки | в базе Gewicht_Ohne',
    'Abmessungen'         => 'Габариты товара (Высота / Ширина / Длина) | в базе Abmessungen',
    'Hersteller'          => 'Производитель товара | в базе Hersteller',
    'EinheitCount'        => 'Количество единиц в товаре | в базе EinheitCount',
    'EinheitId'           => 'Единица измерения товара | в базе EinheitId',
    'Startseite'          => 'Показывать товар на Главной магазина (1/0) | в базе Startseite',
    'Lagerbestand'        => 'Количество товара на складе | в базе Lagerbestand',
    'Bestellt'            => 'Товар поставляется под заказ (1/0) | в базе Bestellt',
    'Verfuegbar'          => 'Статус наличия товара | в базе Verfuegbar',
    'EinzelBestellung'    => 'Однократный заказ (1/0) | в базе EinzelBestellung',
    'Verkauft'            => 'Количество продаж товара | в базе Verkauft',
    'MaxBestellung'       => 'Максимальное количество для заказа | в базе MaxBestellung',
    'MinBestellung'       => 'Минимальное количество для заказа | в базе MinBestellung',
    'Lieferzeit'          => 'Срок поставки товара | в базе Lieferzeit',
    'Spez_1'              => 'Спецификация 1 (1.Язык) | в базе Spez_1',
    'Spez_2'              => 'Спецификация 2 (1.Язык) | в базе Spez_2',
    'Spez_3'              => 'Спецификация 3 (1.Язык) | в базе Spez_3',
    'Spez_4'              => 'Спецификация 4 (1.Язык) | в базе Spez_4',
    'Spez_5'              => 'Спецификация 5 (1.Язык) | в базе Spez_5',
    'Spez_6'              => 'Спецификация 6 (1.Язык) | в базе Spez_6',
    'Spez_7'              => 'Спецификация 7 (1.Язык) | в базе Spez_7',
    'Spez_8'              => 'Спецификация 8 (1.Язык) | в базе Spez_8',
    'Spez_9'              => 'Спецификация 9 (1.Язык) | в базе Spez_9',
    'Spez_10'             => 'Спецификация 10 (1.Язык) | в базе Spez_10',
    'Spez_11'             => 'Спецификация 11 (1.Язык) | в базе Spez_11',
    'Spez_12'             => 'Спецификация 12 (1.Язык) | в базе Spez_12',
    'Spez_13'             => 'Спецификация 13 (1.Язык) | в базе Spez_13',
    'Spez_14'             => 'Спецификация 14 (1.Язык) | в базе Spez_14',
    'Spez_15'             => 'Спецификация 15 (1.Язык) | в базе Spez_15',
    'Spez_1_2'            => 'Спецификация 1 (2.Язык) | в базе Spez_1_2',
    'Spez_2_2'            => 'Спецификация 2 (2.Язык) | в базе Spez_2_2',
    'Spez_3_2'            => 'Спецификация 3 (2.Язык) | в базе Spez_3_2',
    'Spez_4_2'            => 'Спецификация 4 (2.Язык) | в базе Spez_4_2',
    'Spez_5_2'            => 'Спецификация 5 (2.Язык) | в базе Spez_5_2',
    'Spez_6_2'            => 'Спецификация 6 (2.Язык) | в базе Spez_6_2',
    'Spez_7_2'            => 'Спецификация 7 (2.Язык) | в базе Spez_7_2',
    'Spez_8_2'            => 'Спецификация 8 (2.Язык) | в базе Spez_8_2',
    'Spez_9_2'            => 'Спецификация 9 (2.Язык) | в базе Spez_9_2',
    'Spez_10_2'           => 'Спецификация 10 (2.Язык) | в базе Spez_10_2',
    'Spez_11_2'           => 'Спецификация 11 (2.Язык) | в базе Spez_11_2',
    'Spez_12_2'           => 'Спецификация 12 (2.Язык) | в базе Spez_12_2',
    'Spez_13_2'           => 'Спецификация 13 (2.Язык) | в базе Spez_13_2',
    'Spez_14_2'           => 'Спецификация 14 (2.Язык) | в базе Spez_14_2',
    'Spez_15_2'           => 'Спецификация 15 (2.Язык) | в базе Spez_15_2',
    'Spez_1_3'            => 'Спецификация 1 (3.Язык) | в базе Spez_1_3',
    'Spez_2_3'            => 'Спецификация 2 (3.Язык) | в базе Spez_2_3',
    'Spez_3_3'            => 'Спецификация 3 (3.Язык) | в базе Spez_3_3',
    'Spez_4_3'            => 'Спецификация 4 (3.Язык) | в базе Spez_4_3',
    'Spez_5_3'            => 'Спецификация 5 (3.Язык) | в базе Spez_5_3',
    'Spez_6_3'            => 'Спецификация 6 (3.Язык) | в базе Spez_6_3',
    'Spez_7_3'            => 'Спецификация 7 (3.Язык) | в базе Spez_7_3',
    'Spez_8_3'            => 'Спецификация 8 (3.Язык) | в базе Spez_8_3',
    'Spez_9_3'            => 'Спецификация 9 (3.Язык) | в базе Spez_9_3',
    'Spez_10_3'           => 'Спецификация 10 (3.Язык) | в базе Spez_10_3',
    'Spez_11_3'           => 'Спецификация 11 (3.Язык) | в базе Spez_11_3',
    'Spez_12_3'           => 'Спецификация 12 (3.Язык) | в базе Spez_12_3',
    'Spez_13_3'           => 'Спецификация 13 (3.Язык) | в базе Spez_13_3',
    'Spez_14_3'           => 'Спецификация 14 (3.Язык) | в базе Spez_14_3',
    'Spez_15_3'           => 'Спецификация 15 (3.Язык) | в базе Spez_15_3',
    'Fsk18'               => 'Товар для взрослых (0/1) | в базе Fsk18',
    'Frei_1'              => 'Дополнительный аксессуар 1 | в базе Frei_1',
    'Frei_2'              => 'Дополнительный аксессуар 2 | в базе Frei_2',
    'Frei_3'              => 'Дополнительный аксессуар 3 | в базе Frei_3',
    'Frei_1_Pflicht'      => 'Обязательный Дополнительный аксессуар 1 (0/1) | в базе Frei_1_Pflicht',
    'Frei_2_Pflicht'      => 'Обязательный Дополнительный аксессуар 2 (0/1) | в базе Frei_2_Pflicht',
    'Frei_3_Pflicht'      => 'Обязательный Дополнительный аксессуар 3 (0/1) | в базе Frei_3_Pflicht',
    'Gruppen'             => 'Доступность товара группам пользователей | в базе Gruppen',
    'EinheitBezug'        => 'Количество в единице товара | в базе EinheitBezug',
    'EAN_Nr'              => 'EAN номер товара | в базе EAN_Nr',
    'ISBN_Nr'             => 'ISBN номер товара | в базе ISBN_Nr',
    'SeitenTitel'         => 'Название страницы | в базе SeitenTitel',
    'Template'            => 'Шаблон вывода товара | в базе Template',
    'Sektion'             => 'Секция сайта вывода товара | в базе Sektion',
    'PrCountry'           => 'Страна производства товара | в базе PrCountry',
    'Yml'                 => 'Выгрузка в Яндекс.Маркет | в базе Yml',
    'MetaTags'            => 'Метатег keywords | в базе MetaTags',
    'MetaDescription'     => 'Метатег description | в базе MetaDescription');

$CS = View::get();
$CS->assign('form', '?do=shopimport');
$CS->assign('method', 'shop');
$CS->assign('next', 0);

$_REQUEST['action'] = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($_REQUEST['action']) {
    case 'importcsv':
        if (Arr::getRequest('send') == '1') {
            $fileid = importUpload();
            if (empty($fileid)) {
                SX::object('Redir')->redirect('index.php?do=shopimport&action=error&error=upload');
            }

            $types = Tool::extension($fileid);
            if ($types == 'xls' || $types == 'xlsx') {
                require_once (SX_DIR . '/admin/class/class.XLS.php');
                $fp = fopen($TempDir . $fileid, 'rb');
                $csv = new XLSReader($fp, $TempDir . $fileid);
            } else {
                $fp = fopen($TempDir . $fileid, 'r');
                $csv = new AdminCSVReader($fp);
            }

            if ($csv->count() < 1) {
                SX::object('Redir')->redirect('index.php?do=shopimport&action=error&error=empty');
            }
            $fields = $csv->fields();
            $field_table = array();
            foreach ($fields as $csv_field) {
                $my_field = isset($csv_assoc[$csv_field]) ? $csv_assoc[$csv_field] : '';
                $field_table[] = array('id' => md5($csv_field), 'csv_field' => $csv_field, 'my_field' => $my_field);
            }
            $CS->assign('types', $types);
            $CS->assign('fileid', $fileid);
            $CS->assign('field_table', $field_table);
            $CS->assign('available_fields', $array_fields);
            $CS->assign('next', 1);
            $CS->assign('datas', $csv->count());
            $CS->content('/exportimport/import.tpl');
            fclose($fp);
        }
        break;

    case 'importcsv2':
        $fileid = Tool::cleanString(Arr::getRequest('fileid'), '.');

        if (!is_file($TempDir . $fileid)) {
            SX::object('Redir')->redirect('index.php?do=shopimport&action=error&error=cache');
        }

        $existing = Arr::getRequest('existing') == 'ignore' ? 'ignore' : 'replace';
        $types = Arr::getRequest('types');
        if ($types == 'xls' || $types == 'xlsx') {
            $fp = fopen($TempDir . $fileid, 'rb');
            require_once (SX_DIR . '/admin/class/class.XLS.php');
            $csv = new XLSReader($fp, $TempDir . $fileid);
        } else {
            $fp = fopen($TempDir . $fileid, 'r');
            $csv = new AdminCSVReader($fp);
        }

        while ($row = $csv->fetch()) {
            $save_array = array();
            foreach ($row as $key => $value) {
                $field = Arr::getRequest('field_' . md5($key));
                if (isset($array_fields[$field])) {
                    $save_array[$field] = importUmlaut($value);
                }
            }

            if (!empty($save_array['Artikelnummer'])) {
                $save_array['Preis'] = importPrise($save_array['Preis']);
                $save_array['Preis_EK'] = str_replace(',', '.', $save_array['Preis_EK']);
                $save_array['Preis_Liste'] = importPrise($save_array['Preis_Liste']);
                $save_array['Preis_Liste_Gueltig'] = str_replace(',', '.', $save_array['Preis_Liste_Gueltig']);
                $save_array['Gewicht'] = $save_array['Gewicht'] == '' ? 100 : $save_array['Gewicht'];
                $save_array['Lagerbestand'] = $save_array['Lagerbestand'] == '' ? 100 : $save_array['Lagerbestand'];
                $save_array['MinBestellung'] = empty($save_array['MinBestellung']) ? 1 : $save_array['MinBestellung'];
                $save_array['MaxBestellung'] = empty($save_array['MaxBestellung']) ? 10 : $save_array['MaxBestellung'];
                $save_array['Verfuegbar'] = $save_array['Verfuegbar'] == '' ? 1 : $save_array['Verfuegbar'];
                $save_array['Aktiv'] = $save_array['Aktiv'] == '' ? 1 : $save_array['Aktiv'];
                $save_array['Titel_2'] = empty($save_array['Titel_2']) ? $save_array['Titel_1'] : $save_array['Titel_2'];
                $save_array['Titel_3'] = empty($save_array['Titel_2']) ? $save_array['Titel_1'] : $save_array['Titel_3'];
                $save_array['Beschreibung_1'] = str_replace('\n', '', $save_array['Beschreibung_1']);
                $save_array['Beschreibung_2'] = str_replace('\n', '', $save_array['Beschreibung_2']);
                $save_array['Beschreibung_3'] = str_replace('\n', '', $save_array['Beschreibung_3']);
                $save_array['Beschreibung_lang_1'] = str_replace('\n', '', $save_array['Beschreibung_lang_1']);
                $save_array['Beschreibung_lang_2'] = str_replace('\n', '', $save_array['Beschreibung_lang_2']);
                $save_array['Beschreibung_lang_3'] = str_replace('\n', '', $save_array['Beschreibung_lang_3']);
                $save_array['Beschreibung_2'] = empty($save_array['Beschreibung_2']) ? $save_array['Beschreibung_1'] : $save_array['Beschreibung_2'];
                $save_array['Beschreibung_3'] = empty($save_array['Beschreibung_3']) ? $save_array['Beschreibung_1'] : $save_array['Beschreibung_3'];
                $save_array['Beschreibung_lang_2'] = empty($save_array['Beschreibung_lang_2']) ? $save_array['Beschreibung_lang_1'] : $save_array['Beschreibung_lang_2'];
                $save_array['Beschreibung_lang_3'] = empty($save_array['Beschreibung_lang_3']) ? $save_array['Beschreibung_lang_1'] : $save_array['Beschreibung_lang_3'];
                $save_array['Erstellt'] = $save_array['Erstellt'] == '' ? time() : $save_array['Erstellt'];
                $save_array['Sektion'] = empty($save_array['Sektion']) ? 1 : $save_array['Sektion'];

                $db = DB::get();
                if (empty($save_array['Kategorie'])) {
                    $save_array['Kategorie'] = importCateg($db);
                }

                $row = $db->fetch_assoc("SELECT Id FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $db->escape($save_array['Artikelnummer']) . "' LIMIT 1");
                if (!empty($row['Id']) && $existing == 'replace') {
                    $now = $db->update_query('shop_produkte', $save_array, "Artikelnummer='" . $db->escape($save_array['Artikelnummer']) . "'");
                } elseif (empty($row['Id'])) {
                    $now = $db->insert_query('shop_produkte', $save_array);
                }
            }
        }
        fclose($fp);

        File::delete($TempDir . $fileid);
        SX::object('Redir')->redirect('index.php?do=shop&sub=articles');
        break;

    case 'error':
        $array = array(
            'upload' => SX::$lang['UploadFileError'],
            'cache'  => 'Ошибка записи в каталог /temp/cache/ проверьте права доступа...',
            'empty'  => 'В файле нет данных...',
            'error'  => 'Возникла ошибка...',
        );
        $error = Arr::getRequest('error');
        $CS->assign('error', (isset($array[$error]) ? $array[$error] : ''));
        break;
}
$CS->content('/exportimport/import.tpl');
