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

$array_fields = array(
    'Id'                     => 'ID пользователя | в базе Id',
    'Gruppe'                 => 'Основная группа пользователя | в базе Gruppe',
    'Team'                   => 'Относится к Администрации сайта| в базе Team',
    'Regdatum'               => 'Дата регистрации| в базе Regdatum',
    'RegCode'                => 'Регистрационный код| в базе RegCode',
    'Email'                  => 'E-mail | в базе Email',
    'Kennwort'               => 'Пароль | в базе Kennwort',
    'KennwortTemp'           => 'Временный пароль при востановлении | в базе KennwortTemp',
    'Benutzername'           => 'Имя пользователя (логин)| в базе Benutzername',
    'Vorname'                => 'Имя | в базе Vorname',
    'Nachname'               => 'Фамилия | в базе Nachname',
    'Strasse_Nr'             => 'Улица, дом, офис | в базе Strasse_Nr',
    'Postleitzahl'           => 'Почтовый индекс| в базе Postleitzahl',
    'Ort'                    => 'Город | в базе Ort',
    'Firma'                  => 'Организация | в базе Firma',
    'UStId'                  => 'UStId | в базе UStId',
    'Telefon'                => 'Телефон | в базе Telefon',
    'Telefax'                => 'Факс | в базе Telefax',
    'Geburtstag'             => 'День Рождения | в базе Geburtstag',
    'Land'                   => 'Страна | в базе Land',
    'LandCode'               => 'Код страны| в базе LandCode',
    'Aktiv'                  => 'Блокировка пользователя | в базе Aktiv',
    'Logins'                 => 'Количество авторизаций на сайте | в базе Logins',
    'Profil_public'          => 'Опубликовать профиль | в базе Profil_public',
    'Profil_Alle'            => 'Ограничение на просмотр профиля гостями| в базе Profil_Alle',
    'Geburtstag_public'      => 'Показывать День Рождения пользователя | в базе Geburtstag_public',
    'Ort_Public'             => 'Отображать город пользователя| в базе Ort_Public',
    'Unsichtbar'             => 'Скрывать присутствие пользователя | в базе Unsichtbar',
    'Newsletter'             => 'Получение сообщений Администрации | в базе Newsletter',
    'Emailempfang'           => 'Получать письма на email от пользователей | в базе Emailempfang',
    'Pnempfang'              => 'Получать Л.С. от пользователей | в базе Pnempfang',
    'PnEmail'                => 'Уведомление по почте о новом Л.С. | в базе PnEmail',
    'PnPopup'                => 'Всплывающее окно при новом Л.С. | в базе PnPopup',
    'Gaestebuch'             => 'Гостевая книга пользователя | в базе Gaestebuch',
    'Gaestebuch_KeineGaeste' => 'Включение Гостевой книги пользователя | в базе Gaestebuch_KeineGaeste',
    'Gaestebuch_Moderiert'   => 'Модерация Гостевой книги пользователем | в базе Gaestebuch_Moderiert',
    'Gaestebuch_Zeichen'     => 'Количество символов в Гостевой пользователя | в базе Gaestebuch_Zeichen',
    'Gaestebuch_imgcode'     => 'Разрешить IMG-Код в Гостевой пользователя | в базе Gaestebuch_imgcode',
    'Gaestebuch_smilies'     => 'Разрешить смайлы в Гостевой пользователя | в базе Gaestebuch_smilies',
    'Gaestebuch_bbcode'      => 'Разрешить BB-Код в Гостевой пользователя | в базе Gaestebuch_bbcode',
    'msn'                    => 'MSN пользователя | в базе msn',
    'aim'                    => 'AIM пользователя | в базе aim',
    'icq'                    => 'ICQ пользователя | в базе icq',
    'skype'                  => 'Скайп пользователя | в базе skype',
    'Webseite'               => 'Адрес сайта пользователя | в базе Webseite',
    'Signatur'               => 'Подпись пользователя | в базе Signatur',
    'Interessen'             => 'Интересы пользователя | в базе Interessen',
    'Avatar_Default'         => 'Стандартный аватар группы | в базе Avatar_Default',
    'Avatar'                 => 'Аватар пользователя | в базе Avatar',
    'Beitraege'              => 'Количество сообщений пользователя на форуме| в базе Beitraege',
    'Profil_Hits'            => 'Просмотры профиля пользователя | в базе Profil_Hits',
    'Zuletzt_Aktiv'          => 'Последнее посещение сайта пользователем | в базе Zuletzt_Aktiv',
    'Geschlecht'             => 'Пол пользователя | в базе Geschlecht',
    'Beruf'                  => 'Профессия пользователя | в базе Beruf',
    'Hobbys'                 => 'Увлечения пользователя | в базе Hobbys',
    'Essen'                  => 'Любимые блюда пользователя | в базе Essen',
    'Musik'                  => 'Любимая музыка пользователя | в базе Musik',
    'Forum_Beitraege_Limit'  => 'Количество сообщений на странице форума | в базе Forum_Beitraege_Limit',
    'Forum_Themen_Limit'     => 'Количество тем на странице форума | в базе Forum_Themen_Limit',
    'Geloescht'              => 'Запрет востанавливать пароль по почте | в базе Geloescht',
    'Fsk18'                  => 'Доступ к товарам для взрослых | в базе Fsk18',
    'MiddleName'             => 'Отчество пользователя | в базе MiddleName',
    'BankName'               => 'Банковские реквизиты | в базе BankName',
    'Films'                  => 'Любимые фильмы | в базе Films',
    'Tele'                   => 'Любимые телешоу | в базе Tele',
    'Book'                   => 'Любимые книги | в базе Book',
    'Game'                   => 'Любимые игры | в базе Game',
    'Citat'                  => 'Любимые цитаты | в базе Citat',
    'Other'                  => 'О себе | в базе Other',
    'Status'                 => 'Cтатус-сообщение | в базе Status',
    'Gravatar'               => 'Использовать аватар с сервиса  Gravatar | в базе Gravatar',
    'Vkontakte'              => 'Адрес акккаунта в социальной сети Вконтакте | в базе Vkontakte',
    'Odnoklassniki'          => 'Адрес акккаунта в социальной сети Одноклассники | в базе Odnoklassniki',
    'Facebook'               => 'Адрес акккаунта в социальной сети Фейсбук | в базе Facebook',
    'Twitter'                => 'Адрес акккаунта в социальной сети Твиттер | в базе Twitter',
    'Google'                 => 'Адрес акккаунта в социальной Гугл Плюс | в базе Google',
    'Mymail'                 => 'Адрес акккаунта в социальной Мой Мир | в базе Mymail',
    'UloginId'               => 'Регистрация через систему Ulogin | в базе UloginId',
);

$CS = View::get();
$TempDir = TEMP_DIR . '/cache/';

switch (Arr::getRequest('action')) {
    case 'userexp':
        new AdminDataExport('База_пользователей_' . date('d_m_y'), Arr::getRequest('format'), Arr::getRequest('groups'));
        break;

    case 'importcsv':
        if (Arr::getRequest('send') == '1') {
            $fileid = importUpload();
            if (empty($fileid)) {
                SX::object('Redir')->redirect('index.php?do=expimp&action=error&error=upload');
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
            $fields = $csv->fields();
            $count = $csv->count();
            fclose($fp);

            if ($count < 1) {
                SX::object('Redir')->redirect('index.php?do=expimp&action=error&error=empty');
            }

            $field_table = array();
            foreach ($fields as $csv_field) {
                $my_field = isset($csv_assoc[$csv_field]) ? $csv_assoc[$csv_field] : '';
                $field_table[] = array('id' => md5($csv_field), 'csv_field' => $csv_field, 'my_field' => $my_field);
            }

            $CS->assign('types', $types);
            $CS->assign('fileid', $fileid);
            $CS->assign('field_table', $field_table);
            $CS->assign('available_fields', $array_fields);
            $CS->assign('datas', $count);
        }
        $CS->assign('action', 'importcsv');
        break;

    case 'importcsv2':
        $fileid = Tool::cleanString(Arr::getRequest('fileid'), '.');

        if (!is_file($TempDir . $fileid)) {
            SX::object('Redir')->redirect('index.php?do=expimp&action=error&error=cache');
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
                    $save_array[$field] = $value;
                }
            }

            if (!empty($save_array['Email'])) {
                $db = DB::get();
                $row = $db->fetch_assoc("SELECT Id FROM " . PREFIX . "_benutzer WHERE Id='" . intval($save_array['Id']) . "' AND Email='" . $db->escape($save_array['Email']) . "' LIMIT 1");

                $save_array['Geburtstag'] = Tool::formatDate($save_array['Geburtstag']);
                if (!empty($row['Id']) && $existing == 'replace') {
                    $now = $db->update_query('benutzer', $save_array, "Id='" . $db->escape($save_array['Id']) . "' AND Email='" . $db->escape($save_array['Email']) . "'");
                } elseif (empty($row['Id'])) {
                    $now = $db->insert_query('benutzer', $save_array);
                }
            }
        }
        fclose($fp);

        File::delete($TempDir . $fileid);
        SX::object('Redir')->redirect('index.php?do=user&sub=showusers');
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
        $CS->assign('action', 'error');
        break;

    default:
        $groups = DB::get()->fetch_assoc_all("SELECT * FROM " . PREFIX . "_benutzer_gruppen WHERE Id != 2");
        $CS->assign('groups', $groups);
        $CS->assign('db_fields', SX::object('AdminMain')->load(PREFIX));
        $CS->assign('action', 'start');
        break;
}
$CS->content('/exportimport/expimp.tpl');
