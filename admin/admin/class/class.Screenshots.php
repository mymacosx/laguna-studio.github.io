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

class Screenshots extends Magic {

    /* Метод конструктор класса */
    public function __construct() {
        if (!perm('screenshots')) {
            SX::object('AdminCore')->noAccess();
        }
    }

    /* Метод добавления */
    public function add() {
        $options = array(
            'type'   => 'image',
            'result' => 'data',
            'upload' => '/uploads/screenshots/',
            'input'  => 'shot',
        );
        $id = SX::object('Upload')->load($options);
        if (!empty($id)) {
            $array = unserialize(base64_decode(Arr::getRequest('code')));
            $array[] = array('titel' => Arr::getRequest('titel'), 'text' => Arr::getRequest('text'), 'id' => $id);
            $_REQUEST['code'] = base64_encode(serialize($array));
        }
        $this->load();
    }

    /* Метод внесения изменений */
    public function choice() {
        $array = unserialize(base64_decode(Arr::getRequest('code')));
        $id = intval(Arr::getRequest('id'));
        if (Arr::getRequest('submit') == 'delete') {
            $entry = $array[$id];
            unset($array[$id]);
            File::delete(UPLOADS_DIR . '/screenshots/' . $entry['id']);
        }
        if (Arr::getRequest('submit') == 'edit') {
            $array[$id]['text'] = Arr::getRequest('text');
            $array[$id]['titel'] = Arr::getRequest('titel');
            $array[$id]['id'] = Arr::getRequest('image');
        }
        $_REQUEST['code'] = base64_encode(serialize($array));
        $this->load();
    }

    /* Метод вывода скриншотов */
    public function load() {
        $code = !isset($_REQUEST['code']) ? serialize(array()) : base64_decode(Arr::getRequest('code'));
        if (!empty($_REQUEST['is'])) {
            $array = array(
                'faq'      => 'faq',
                'news'     => 'news',
                'article'  => 'artikel',
                'content'  => 'content',
                'products' => 'produkte');
            $table = Arr::getRequest('table');
            if (isset($array[$table])) {
                $row = DB::get()->cache_fetch_object("SELECT " . Tool::cleanAllow(Arr::getRequest('fieldname'), '.') . " AS Textbilder FROM " . PREFIX . "_" . $array[$table] . " WHERE Id='" . intval(Arr::getRequest('id')) . "' LIMIT 1");
                $_REQUEST['is'] = base64_encode(serialize($row->Textbilder));
            }
        }

        $_REQUEST['is'] = !empty($_REQUEST['is']) ? $_REQUEST['is'] : '';
        if (!empty($_REQUEST['is'])) {
            $code = unserialize(base64_decode($_REQUEST['is']));
        } else {
            $code .= unserialize(base64_decode($_REQUEST['is']));
        }

        $array = unserialize($code);

        $i = 0;
        $hiddenvalue = array();
        foreach ($array as $key => $val) {
            $hiddenvalue[$i]['hiddencode'] = base64_encode(serialize($array));
            $hiddenvalue[$i]['hiddenid'] = $key;
            $hiddenvalue[$i]['image'] = $val['id'];
            $hiddenvalue[$i]['titel'] = sanitize($val['titel']);
            $hiddenvalue[$i]['text'] = sanitize($val['text']);
            $i++;
        }
        $this->_view->assign('hiddenvalue', $hiddenvalue);
        $this->_view->assign('thearray', base64_encode(serialize($array)));
        $this->_view->content('/screenshots/frame.tpl');
    }

}
