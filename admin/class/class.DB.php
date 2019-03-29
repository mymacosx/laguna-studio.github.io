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

final class Result {

    protected $_result;

    public function __construct($result) {
        $this->_result = $result;
    }

    /* Метод получает ряд из набора данных в виде ассоциативного массива, нумерованного массива, или обоих */
    public function fetch_array() {
        return $this->_result ? $this->_result->fetch_array() : false;
    }

    /* Метод получает ряд из набора данных в виде объекта */
    public function fetch_object() {
        return $this->_result ? $this->_result->fetch_object() : false;
    }

    /* Метод получает ряд из набора данных в виде нумерованного массива */
    public function fetch_row() {
        return $this->_result ? $this->_result->fetch_row() : false;
    }

    /* Метод получает ряд из набора данных в виде ассоциативного массива */
    public function fetch_assoc() {
        return $this->_result ? $this->_result->fetch_assoc() : false;
    }

    /* Метод возвращает количество рядов в наборе данных */
    public function num_rows() {
        return $this->_result ? $this->_result->num_rows : false;
    }

    /* Метод возвращает количество полей в наборе данных */
    public function field_count() {
        return $this->_result ? $this->_result->field_count : false;
    }

    /* Метод устанавливает указатель на результат смещенным на определенное количество полей */
    public function data_seek() {
        return $this->_result ? $this->_result->data_seek() : false;
    }

    /* Метод возвращает названия колонок таблицы */
    public function field_name($i) {
        if (!$this->_result) {
            return false;
        }
        $this->_result->field_seek($i);
        $field = $this->_result->fetch_field();
        return $field->name;
    }

    /* Метод освобождает память, занимаемую результатом запроса */
    public function close() {
        if (!$this->_result) {
            return false;
        }
        $result = $this->_result->close();
        return $result;
    }

    /* Метод сопостовления объекту метода */
    public function __get($key) {
        if (in_array($key, array('lengths', 'field_count', 'num_rows'))) {
            return $this->$key();
        }
    }

}

class DB {

    protected $_mysqli;
    protected $_cache;
    protected $_prefix;

    protected function __clone() {

    }

    /* Инициализация класса и установка соединения с базой */
    protected function __construct($config) {
        $port = !empty($config['dbport']) ? $config['dbport'] : 3306;
        $this->_prefix = $config['dbprefix'];
        $this->_mysqli = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'], $port);
        SX::set('database.load', $this->connect_base($config['dbcharset']));
    }

    /* Метод возвращает по умолчанию единый экземпляр класса для системы */
    public static function get() {
        static $object = NULL;
        if ($object === NULL) {
            $object = new self(SX::get('database'));
        }
        return $object;
    }

    /* Метод для использования альтернативных баз */
    public static function create($config = NULL) {
        static $object = array();
        if (empty($config)) {
            return self::get();
        }
        $name = $config['dbname'] . $config['dbprefix'];
        if (!isset($object[$name])) {
            $object[$name] = new self($config);
        }
        return $object[$name];
    }

    /* Подключение к базе данных */
    protected function connect_base($charset = 'utf8') {
        if (!$this->_mysqli->connect_errno) {
            if ($this->server_info() && substr($this->server_info(), 0, 1) > 4) {
                $this->_mysqli->query("SET SESSION SQL_MODE = ''");
            }
            if (!$this->_mysqli->set_charset($charset)) {
                $this->_mysqli->query("SET NAMES " . $charset);
            }
            return true;
        }
        $this->error_connect();
        return false;
    }

    /* Метод возвращает полное количество строк в запросе с SQL_CALC_FOUND_ROWS */
    public function found_rows() {
        $sql = $this->fetch_object("SELECT FOUND_ROWS() AS total");
        return $sql->total;
    }

    /* Запрос multi_query */
    public function multi_query($query) {
        if ($this->_mysqli->multi_query($query)) {
            return true;
        }
        $this->error_query($query);
        return false;
    }

    /* Получаем первый результат запроса multi_query */
    public function store_result() {
        if (($result = $this->_mysqli->store_result())) {
            return new Result($result);
        }
        return false;
    }

    /* Проверка на существование следующего результата запроса multi_query */
    public function more_results() {
        return $this->_mysqli->more_results();
    }

    /* Переход к следующему результату запроса multi_query */
    public function next_result() {
        return $this->_mysqli->next_result();
    }

    /* Микс предыдущих трех функций, для получения второго и последующих результатов запроса multi_query */
    public function store_next_result() {
        if ($this->more_results() !== false) {
            if ($this->next_result() !== false) {
                return $this->store_result();
            }
        }
        return false;
    }

    /* Метод выполняет запрос */
    public function query($query) {
        $result = $this->_mysqli->query($query);
        if ($result) {
            return new Result($result);
        }
        $this->error_query($query);
        return false;
    }

    /* Метод записи в таблицу, в качестве параметров ассоциативный массив */
    public function insert_query($table, $array) {
        $arr = array();
        foreach ($array as $key => $val) {
            $arr['key'][] = '`' . $key . '`';
            $arr['val'][] = '\'' . $this->escape(trim($val)) . '\'';
        }
        $arr['key'] = implode(',', $arr['key']);
        $arr['val'] = implode(',', $arr['val']);
        $query = 'INSERT INTO `' . $this->_prefix . '_' . $table . '` (' . $arr['key'] . ') VALUES (' . $arr['val'] . ')';
        return !empty($arr['key']) ? $this->query($query) : false;
    }

    /* Метод обновления таблицы, в качестве параметров ассоциативный массив */
    public function update_query($table, $array, $where = NULL) {
        $arr = array();
        foreach ($array as $key => $val) {
            $arr[] = '`' . $key . '` = \'' . $this->escape(trim($val)) . '\'';
        }
        if (!empty($where)) {
            $where = ' WHERE ' . $where;
        }
        $query = 'UPDATE `' . $this->_prefix . '_' . $table . '` SET ' . implode(', ', $arr) . $where;
        return !empty($arr) ? $this->query($query) : false;
    }

    /* Возвращаем количество рядов результата запроса */
    public function num_rows($query) {
        return $this->query_type($query, 'num_rows');
    }

    /* Обрабатываем ряд результата запроса и возвращаем объект */
    public function fetch_object($query) {
        return $this->query_type($query, 'fetch_object');
    }

    /* Метод возвращает количество рядов в наборе данных */
    public function fetch_assoc($query) {
        return $this->query_type($query, 'fetch_assoc');
    }

    /* Обрабатываем все ряды результата запроса и возвращаем объект */
    public function fetch_object_all($query) {
        return $this->query_all($query, 'fetch_object');
    }

    /* Метод возвращает количество рядов в наборе данных */
    public function fetch_assoc_all($query) {
        return $this->query_all($query, 'fetch_assoc');
    }

    /* Обрабатываем все ряды результата запроса и возвращаем объект с кешированием */
    public function cache_fetch_object_all($query) {
        return $this->cache_query_all($query, 'fetch_object');
    }

    /* Метод возвращает количество весь наборе данных с кешированием */
    public function cache_fetch_assoc_all($query) {
        return $this->cache_query_all($query, 'fetch_assoc');
    }

    /* Возвращаем количество рядов результата запроса, функция с кешированием */
    public function cache_num_rows($query) {
        return $this->cache_query($query, 'num_rows');
    }

    /* Обрабатываем ряд результата запроса и возвращаем объект, функция с кешированием */
    public function cache_fetch_object($query) {
        return $this->cache_query($query, 'fetch_object');
    }

    /* Обрабатываем ряд результата запроса, возвращая ассоциативный массив, функция с кешированием */
    public function cache_fetch_assoc($query) {
        return $this->cache_query($query, 'fetch_assoc');
    }

    /* Получаем ID последнего выполненного запроса INSERT */
    public function insert_id() {
        return $this->_mysqli->insert_id;
    }

    /* Возвращаем количество рядов, затронутых последним INSERT, UPDATE, DELETE */
    public function affected_rows() {
        return $this->_mysqli->affected_rows;
    }

    /* Метод получения информации о версии базы */
    public function server_info() {
        return $this->_mysqli->server_info;
    }

    /* Экранируем специальные символы в строке, используемой в SQL-запросе, принимая во внимание кодировку соединения */
    public function escape($query) {
        return $this->_mysqli->real_escape_string($query);
    }

    /* Метод возвращает префикс текущей базы */
    public function prefix() {
        return $this->_prefix;
    }

    /* Закрываем соединение с базой */
    public function close() {
        $this->_mysqli->close();
    }

    /* Метод отмены автоматической фиксации изменений транзакции */
    public function begin() {
        $this->_mysqli->autocommit(false);
    }

    /* Метод фиксирует изменения текущей транзакции */
    public function commit() {
        $this->_mysqli->commit();
        $this->_mysqli->autocommit(true);
    }

    /* Метод отменяет изменения текущей транзакции */
    public function rollback() {
        $this->_mysqli->rollback();
        $this->_mysqli->autocommit(true);
    }

    protected function query_type($query, $type) {
        $result = $this->query($query);
        if ($result) {
            $return = $result->$type();
            $result->close();
            return $return;
        }
        return false;
    }

    protected function query_all($query, $type) {
        $array = array();
        $result = $this->query($query);
        if ($result) {
            while ($row = $result->$type()) {
                $array[] = $row;
            }
            $result->close();
        }
        return $array;
    }

    protected function cache_query_all($query, $type) {
        $array = array();
        $key = md5($query, $type);
        if (isset($this->_cache['all'][$key])) {
            $array = unserialize($this->_cache['all'][$key]);
        } else {
            $array = $this->query_all($query, $type);
            $this->_cache['all'][$key] = serialize($array);
        }
        return $array;
    }

    /* Метод кеширования повторных запросов SELECT */
    protected function cache_query($query, $array) {
        $data = md5($query);
        if (isset($this->_cache[$array][$data])) {
            return unserialize($this->_cache[$array][$data]);
        } else {
            return $this->cache_result($query, $array, $data);
        }
    }

    /* Метод выборки из базы и записи в кеш */
    protected function cache_result($query, $array, $data) {
        $result = $this->query($query);
        if ($result) {
            $result = $this->query($query);
            $res = $result->$array();
            $result->close();
            $this->_cache[$array][$data] = serialize($res);
            return $res;
        }
        return false;
    }

    /* Метод вывода сообщения об ошибке соединения с базой */
    protected function error_connect() {
        $text = 'Ошибка подключения к базе!' . PE . 'Ошибка №:' . $this->_mysqli->connect_errno . PE . 'Пояснение: ' . $this->_mysqli->connect_error;
        SX::syslog($text, '5', 0);
        SX::output('<li> Сайт временно недоступен', true);
    }

    /* Метод обработки ошибочных запросов к базе */
    protected function error_query($query = '') {
        $my_error = $this->_mysqli->error;
        $my_errno = $this->_mysqli->errno;
        $query = preg_replace('/\s+/u', ' ', $query);
        if (!defined('NOLOGGED')) {
            SX::syslog('Ошибка в MySQL!' . PE . 'Ошибка №:' . $my_errno . PE . 'Запрос: ' . $query . PE . 'Ошибка: ' . $my_error . PE . 'Страница: ' . SHEME_URL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 4);
        }
        if (defined('SQLERROR_WIDTH')) {
            SX::output('<div class="info_red" style="width:' . SQLERROR_WIDTH . '%;height:100px"><div style="margin-bottom:5px; width:98%">Ошибка №: ' . $my_errno . '<br />Ошибка: ' . $my_error . '<br />Запрос: <br /><em>' . $query . '</em></div></div>');
        }
    }

}
