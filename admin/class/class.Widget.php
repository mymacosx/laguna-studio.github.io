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

class Widget extends Magic {

    protected $_cache = array();
    protected $_object = array();

    /* Метод получения настроек виджета */
    public function settings($widget, $default = array()) {
        $array = array();
        if (!empty($widget)) {
            $settings = unserialize(SX::get('widgets.' . $widget));
            if ($settings !== false) {
                $array += (array) $settings;
            }
            $array += (array) $default;
        }
        return $array;
    }

    /* Метод подключения виджета */
    public function get($params = array()) {
        if (get_active($params['name'])) {
            $key = $this->key($params);
            if (isset($this->_cache[$key])) {
                return $this->_cache[$key];
            }
            $class = $this->name($params['name']);
            if ($this->load($params['name'], $class) && is_callable(array($class, 'get'))) {
                return $this->_cache[$key] = $this->object($class)->get($params);
            }
        }
        return NULL;
    }

    /* Метод получения ключа по содержимому массива */
    protected function key($params) {
        array_multisort($params);
        return md5(serialize($params));
    }

    /* Метод получения имени класса */
    protected function name($value) {
        return 'Widget' . ucfirst($value);
    }

    /* Метод получения имени класса */
    protected function load($widget, $class) {
        $file = WIDGET_DIR . '/' . $widget . '/class/class.' . $class . '.php';
        if (false === include_once $file) {
            return false;
        }
        return true;
    }

    /* Метод получения объекта */
    protected function object($class) {
        if (!isset($this->_object[$class])) {
            $this->_object[$class] = $this->__object($class);
        }
        return $this->_object[$class];
    }

}
