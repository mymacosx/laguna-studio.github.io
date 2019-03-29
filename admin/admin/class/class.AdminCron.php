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

class AdminCron extends Magic {

    /* Выбор действия */
    public function get() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }

        $_REQUEST['type'] = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 'show';
        switch ($_REQUEST['type']) {
            case 'aktiv_cron':
                $this->aktive(Arr::getRequest('id'));
                break;

            case 'del_cron':
                $this->delete(Arr::getRequest('id'));
                break;

            case 'add_cron':
                $this->add();
                break;

            case 'edit_cron':
                $this->edit(Arr::getRequest('id'));
                break;

            case 'def_cron':
                $this->load();
                break;

            default:
            case 'show_cron':
                $this->show();
                break;
        }
    }

    /* Получаем список заданий */
    protected function show() {
        $cron = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_schedule ORDER BY Id ASC");
        while ($row = $sql->fetch_object()) {
            switch ($row->Type) {
                case 'sys':
                    $row->Typel = $this->_lang['GlobalSystem'];
                    break;

                case 'one':
                    $row->Typel = $this->_lang['CronTypeOne'];
                    break;

                case 'more':
                    $row->Typel = $this->_lang['CronTypeMore'];
                    break;
            }
            $cron[] = $row;
        }
        $this->_view->assign('cron', $cron);
        $this->_view->content('/cron/cron.tpl');
    }

    /* Добавление задания */
    protected function add() {
        if (Arr::getPost('save') == 1) {
            $this->save(Arr::getPost('NextTime'), Arr::getPost('Datum'));
        }
        $this->_view->content('/cron/cron_add.tpl');
    }

    /* Редактирование задания */
    protected function edit($id) {
        if (!empty($id)) {
            if (Arr::getPost('save') == 1) {
                $this->update($id, Arr::getPost('NextTime'));
            }
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_schedule WHERE Id='" . intval($id) . "' LIMIT 1");
            $this->_view->assign('row', $row);
            $this->_view->content('/cron/cron_edit.tpl');
        } else {
            SX::object('Redir')->redirect('index.php?do=settings&sub=cron');
        }
    }

    /* Обновление задания в базе */
    protected function update($id, $time) {
        $time = (Arr::getPost('Type') == 'more' && empty($time)) ? 86400 : intval($time);
        $Datum = $this->__object('AdminCore')->mktime(Arr::getPost('Datum'));
        $s_hour = Arr::getPost('s_hour') * 3600;
        $s_minut = Arr::getPost('s_minut') * 60;
        $Datum = $Datum + $s_hour + $s_minut;

        $array = array(
            'Datum'    => $Datum,
            'NextTime' => $time,
            'Type'     => Arr::getPost('Type'),
            'Modul'    => Arr::getPost('Modul'),
            'Title'    => Arr::getPost('Title'),
            'Func'     => Arr::getPost('Func'),
            'Options'  => $this->check(),
            'Aktiv'    => Arr::getPost('Aktiv'),
            'Error'    => '',
        );
        $this->_db->update_query('schedule', $array, "Id='" . intval($id) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал задание (' . Arr::getPost('Title') . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->script('save');
    }

    /* Запись в базу нового задания */
    protected function save($time, $datum) {
        $time = (Arr::getPost('Type') == 'more' && empty($time)) ? 86400 : intval($time);
        $datum = !empty($datum) ? $this->__object('AdminCore')->mktime($datum) : time();
        $s_hour = Arr::getPost('s_hour') * 3600;
        $s_minut = Arr::getPost('s_minut') * 60;
        $datum = $datum + $s_hour + $s_minut;

        $insert_array = array(
            'Datum'    => $datum,
            'NextTime' => $time,
            'Type'     => Arr::getPost('Type'),
            'Modul'    => Arr::getPost('Modul'),
            'Title'    => Arr::getPost('Title'),
            'Func'     => Arr::getPost('Func'),
            'Options'  => $this->check(),
            'Aktiv'    => Arr::getPost('Aktiv'));
        $this->_db->insert_query('schedule', $insert_array);
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новое задание (' . Arr::getPost('Title') . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->script('close');
    }

    /* Метод возвращает умолчание для типов sitemap */
    protected function check() {
        $type = Arr::getPost('Modul');
        return ($type == 'sitemap') ? BASE_URL : Arr::getPost('Options');
    }

    /* Загрузка дефолтных заданий */
    protected function load() {
        Tool::cleanTable('schedule');
        $time = mktime(1, 0, 0, date('m'), date('d'), date('Y')) + 86400;
        $this->insert($time, '86400', 'sitemap', 'Генерация карты сайта xml', '1', BASE_URL);
        $time += 1800;
        $this->insert($time, '86400', 'birthday', 'Поздравление пользователей с ДР по почте', '1');
        $time += 1800;
        $this->insert($time, '604800', 'compile', 'Удаление скомпилированных шаблонов смарти', '1');
        $time += 1800;
        $this->insert($time, '604800', 'uimages', 'Чистка пользовательской директории от ненужных файлов', '0');
        $time += 1800;
        $this->insert($time, '604800', 'search', 'Чистка статистики поиска', '1');
        $time += 1800;
        $this->insert($time, '604800', 'autorize', 'Чистка статистики авторизаций', '1');
        $time += 1800;
        $this->insert($time, '604800', 'referer', 'Чистка статистики рефереров', '1');
        $time += 1800;
        $this->insert($time, '604800', 'syslog', 'Чистка системных сообщений', '1');
        SX::object('Redir')->redirect('index.php?do=settings&sub=cron');
    }

    /* Записывает задание в базу */
    protected function insert($time, $interval, $sysname, $name, $aktiv, $func = '') {
        $insert_array = array(
            'Datum'    => $time,
            'PrevTime' => 0,
            'NextTime' => $interval,
            'Type'     => 'more',
            'Modul'    => $sysname,
            'Title'    => $name,
            'Func'     => '',
            'Options'  => $func,
            'Aktiv'    => $aktiv);
        $this->_db->insert_query('schedule', $insert_array);
    }

    /* Устанавливаем активно ли задание */
    protected function aktive($id) {
        if (!empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_schedule SET Aktiv = '" . intval(Arr::getRequest('aktiv')) . "' WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    /* Удаляем задание */
    protected function delete($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_schedule WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

}
