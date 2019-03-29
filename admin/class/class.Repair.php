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

class Repair {

    protected $config;

    public function __construct() {
        $this->config = SX::get('configs');
        if (!empty($this->config['site']['ip'])) {
            $array = explode(',', $this->config['site']['ip']);
            if (!in_array(IP_USER, $array)) {
                $this->closed();
            }
        } else {
            $this->closed();
        }
    }

    /* Закрываем сайт по настройке в конфигсис */
    protected function closed() {
        $link = SHEME_URL . $_SERVER['HTTP_HOST'] . Tool::getPatch();
        SX::output('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<html>
		<body>
		<title>' . $this->config['site']['time'] . '</title>
		<p><br /><br /><br /><br /></p>
		<div align="center"><h2>' . $this->config['site']['time'] . '</h2></div>
		<div align="center"><img src="' . $link . 'uploads/other/repair.jpg" /></div>
		</body>
		</html>', true);
    }

}
