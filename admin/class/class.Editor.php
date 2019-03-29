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

class Editor {

    /* Инициализация редактора */
    public function load($sys = '', $value = '', $fieldname = '', $height = '', $type = '') {
        switch ($sys) {
            case 'admin':
                $type = empty($type) ? 'Full' : $type;
                return $this->select(SX::get('admin.Type_Redaktor'), $value, $fieldname, $height, $type);
            case 'user':
                $type = empty($type) ? 'User' : $type;
                return $this->select(SX::get('system.SiteEditor'), $value, $fieldname, $height, $type);
            default:
                return $this->textarea($value, $fieldname, $height);
        }
    }

    /* Выбор редактора */
    protected function select($select, $value, $fieldname, $height, $type) {
        if ($select == '1') {
            return $this->cke($value, $fieldname, $height, $type);
        }
        return $this->textarea($value, $fieldname, $height);
    }

    /* Использование textarea */
    protected function textarea($value, $fieldname, $height) {
        $height = empty($height) ? 350 : $height;
        return '<textarea style="width:100%; height:' . $height . 'px" name="' . $fieldname . '" wrap="virtual">' . sanitize($value) . '</textarea>';
    }

    /* Инициализация CKE радактора */
    protected function cke($value, $fieldname, $height, $type) {
        static $CKEditor = NULL;
        $config['toolbar'] = $type;
        $config['height'] = empty($height) ? 450 : $height;
        if (empty($CKEditor)) {
            include_once SX_DIR . '/lib/editor/ckeditor_php5.php';
            $CKEditor = SX::object('CKEditor');
        }
        $CKEditor->returnOutput = true;
        $CKEditor->basePath = BASE_URL . '/lib/editor/';
        return $CKEditor->editor($fieldname, $value, $config);
    }

}
