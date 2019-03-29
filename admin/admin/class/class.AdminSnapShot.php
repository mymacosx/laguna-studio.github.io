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

class AdminSnapShot {

    public function get($data, $width = 100) {
        $image = $this->load($data);
        if ($image !== false) {
            $name = Tool::uniqid() . '.jpg';
            if (File::set(SX_DIR . '/temp/cache/' . $name, $image)) {
                $this->resise($name, $width);
                $result = '<img id="image" src="../uploads/links/' . $name . '" alt="' . $name . '"/>';
            } else {
                $result = SX::$lang['Error'];
            }
        } else {
            $result = SX::$lang['SnapShotError'];
        }
        SX::output($result, true);
    }

    protected function load($data) {
        return file_get_contents('http://mini.s-shot.ru/?' . $data);
    }

    protected function resise($name, $width) {
        if (!empty($name)) {
            $object = SX::object('Image');
            $file = TEMP_DIR . '/cache/' . $name;

            if ($object->open($file)) {
                $object->resize($width, 'width');
                $object->save(SX_DIR . '/uploads/links/' . $name);
                $object->close();
            }
            unlink(SX_DIR . '/temp/cache/' . $name);
        }
    }

}
