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

class Cron extends Magic {

    /* Конструктор класса */
    public function __construct() {
        spl_autoload_register(array($this, 'autoload'));
    }

    /* Подключение кроном классов модулей и виджетов */
    public function autoload($class) {
        $result = false;
        if (!class_exists($class, false)) {
            static $array = array();
            if (empty($array)) {
                $files = glob(SX_DIR . '/*/*/class/class.*.php');
                foreach ($files as $file) {
                    $array[basename($file)] = $file;
                }
            }
            $file = 'class.' . $class . '.php';
            if (isset($array[$file])) {
                include $array[$file];
                $result = true;
            }
        }
        return $result;
    }

    /* Запускаем выполнение */
    public static function get($type = 'hits') {
        $time = time();
        $limit = ($type == 'hits') ? ' LIMIT 1' : '';
        $events = DB::get()->fetch_object_all("SELECT * FROM " . PREFIX . "_schedule WHERE Datum <= '" . $time . "' AND Aktiv = '1' ORDER BY Datum ASC" . $limit);
        if (!empty($events)) {
            SX::object('Cron')->execute($events, $time);
        }
    }

    /* Выполнение заданий */
    protected function execute($row, $time) {
        ignore_user_abort(true);
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        foreach ($row as $val) {
            if (empty($val->Func)) {
                switch ($val->Modul) {
                    case 'ping';
                        $this->delete($val->Id);
                        $this->ping($val->Options);
                        break;

                    case 'sitemap';
                        $this->update($val, $time);
                        $this->sitemap($val->Options);
                        break;

                    case 'newsletter';
                        $this->newsletter($val);
                        break;

                    case 'birthday';
                        $this->update($val, $time);
                        if (SX::get('system.birthdays_mail') == '1') {
                            $this->birthday();
                        }
                        break;

                    case 'compile';
                        $this->update($val, $time);
                        $this->compile(TEMP_DIR . '/compiled/' . AREA . '/main/');
                        $this->compile(TEMP_DIR . '/compiled/' . AREA . '/admin/');
                        break;

                    case 'uimages';
                        $this->update($val, $time);
                        $this->uimages();
                        break;

                    case 'search';
                        $this->update($val, $time);
                        Tool::cleanTable('suche_log');
                        break;

                    case 'autorize';
                        $this->update($val, $time);
                        Tool::cleanTable('benutzer_logins');
                        break;

                    case 'syslog';
                        $this->update($val, $time);
                        Tool::cleanTable('log');
                        break;

                    case 'referer';
                        $this->update($val, $time);
                        Tool::cleanTable('counter_referer');
                        break;

                    case 'func';
                        $this->stop($val->Id, $time, 'Ошибка! Не указано имя функции');
                        break;
                }
            } else {
                if ($val->Modul == 'func') {
                    $this->launch($val, $time);
                }
            }
        }
    }

    /* Запускаем произвольную функцию или статическую функцию класса */
    protected function launch($val, $time) {
        $val->Func = Tool::cleanAllow($val->Func, ':.');
        $options = (empty($val->Options)) ? '();' : '(' . str_replace(array(';', '(', ')'), '', $val->Options) . ');';
        if (strpos($val->Func, '::') !== false) {
            $array = explode('::', $val->Func);
            if (is_callable(array($array[0], $array[1]))) {
                $this->update($val, $time);
                eval($val->Func . $options);
            } else {
                $this->stop($val->Id, $time, 'Ошибка! Такой статической функции не существует');
            }
        } else {
            if (function_exists($val->Func)) {
                $this->update($val, $time);
                eval($val->Func . $options);
            } else {
                $this->stop($val->Id, $time, 'Ошибка! Такой функции не существует');
            }
        }
    }

    /* Удаляем задание */
    protected function delete($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_schedule WHERE Id='" . $id . "'");
    }

    /* Деактивируем задание */
    protected function stop($id, $time, $error = '') {
        $this->_db->query("UPDATE " . PREFIX . "_schedule SET Aktiv = '0', PrevTime = '" . $time . "', Error = '" . $this->_db->escape($error) . "' WHERE Id = '" . $id . "'");
    }

    /* Обновляем задание */
    protected function update($val, $time) {
        if ($val->Type != 'one' && $val->NextTime != '0') {
            $start = $this->next($val->NextTime, $val->Datum, $time);
            $this->_db->query("UPDATE " . PREFIX . "_schedule SET Datum = '" . $start . "', PrevTime = '" . $time . "' WHERE Id = '" . $val->Id . "'");
        } else {
            $this->stop($val->Id, $time, 'Ошибка!');
        }
    }

    /* Вычисляем время следующего запуска при пропуске выполнений */
    protected function next($ntime, $datum, $time) {
        $val = $time - $datum;
        if ($val < $ntime) {
            return $datum + $ntime;
        } else {
            $var = floor($val / $ntime) + 1;
            return $datum + ($var * $ntime);
        }
    }

    /* Записываем задание в базу
     *
     * 	$array['datum']     Дата следующего выполнения
     *  $array['prevtime']  Дата предыдущего выполнения
     *  $array['nexttime']  Интервал для последующих выполнений
     *  $array['type']      Тип задания
     *  $array['modul']     Модуль задания
     *  $array['title']     Название задания
     *  $array['func']      Название запускаемой функции
     *  $array['options']   Дополнительные параметры
     *  $array['aktiv']     Активно ли задание */
    public function add($array) {
        if (!empty($array['datum']) && !empty($array['title'])) {
            $insert_array = array(
                'Datum'    => $array['datum'],
                'PrevTime' => (!empty($array['prevtime']) ? $array['prevtime'] : '0'),
                'NextTime' => (!empty($array['nexttime']) ? $array['nexttime'] : '0'),
                'Type'     => (!empty($array['type']) ? $array['type'] : 'sys'),
                'Modul'    => (!empty($array['modul']) ? $array['modul'] : 'system'),
                'Title'    => $array['title'],
                'Func'     => (!empty($array['func']) ? $array['func'] : ''),
                'Options'  => (!empty($array['options']) ? $array['options'] : ''),
                'Aktiv'    => (!empty($array['aktiv']) ? $array['aktiv'] : '0'));
            $this->_db->insert_query('schedule', $insert_array);
        }
    }

    /* Запускаем пинг */
    protected function ping($val) {
        if (get_active('ping')) {
            $val = unserialize($val);
            $this->__object('RPC')->get($val['name'], $val['url'], $val['lang']);
        }
    }

    /* Работаем с рассылками */
    protected function newsletter($val) {
        include_once SX_DIR . '/admin/class/class.AdminNewsletter.php';
        SX::object('AdminNewsletter')->send($val);
    }

    /* Генерируем карту сайта */
    protected function sitemap($link) {
        include_once SX_DIR . '/admin/class/class.AdminSeo.php';
        SX::object('AdminSeo')->startSitemap('0', 'cron', $link);
    }

    /* Поздравляем пользователей с днем рождения */
    protected function birthday() {
        $query = "SELECT
            Email,
            Geburtstag,
            Benutzername,
            " . date('Y') . " - RIGHT(Geburtstag, 4) AS Age
        FROM
            " . PREFIX . "_benutzer
        WHERE
            Geburtstag != ''
        AND
            Aktiv = '1'
        AND
            Geburtstag_public = '1'
        HAVING
            Age > 0
        AND
            LEFT(Geburtstag, 2) = " . date('d') . "
        AND
            LEFT(RIGHT(Geburtstag, 7), 2) = " . date('m') . "
        ORDER BY Age DESC";
        $sql = $this->_db->query($query);
        while ($row = $sql->fetch_object()) {
            $body = str_replace('__USER__', $row->Benutzername, $this->_lang['Birthdays_Mail']);
            $body = str_replace("\n", "\r\n", $body);
            SX::object('Mail')->send(1, $row->Email, $row->Benutzername, $body, $this->_lang['Birthdays_Subject'], SX::get('system.Mail_Absender'), SX::get('system.Mail_Name'), 'text', '', '', 1);
        }
        $sql->close();
    }

    /* Чистим папку с скомпилированными файлами */
    protected function compile($verzname) {
        $handle = opendir($verzname);
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php'))) {
                File::delete($verzname . $datei);
            }
        }
        closedir($handle);
    }

    /* Чистим папку пользовательских изображений, если нет упоминания о файле в базе */
    protected function uimages() {
        $sql_u = $this->_db->query("SELECT Datei FROM " . PREFIX . "_user_images");
        while ($row_u = $sql_u->fetch_assoc()) {
            $img[] = $row_u['Datei'];
        }
        $sql_u->close();
        $verzname = UPLOADS_DIR . '/user/gallery/';
        $handle = opendir($verzname);
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && !in_array($datei, $img)) {
                File::delete($verzname . $datei);
            }
        }
        closedir($handle);
    }

}