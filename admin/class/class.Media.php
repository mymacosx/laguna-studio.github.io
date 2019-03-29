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

class Media extends Magic {

    /* Метод вывода медиа */
    public function get($text) {
        $text = preg_replace_callback('!\[VIDEO:([\d]*)\]!iu', array($this, 'video'), $text);
        $text = preg_replace_callback('!\[AUDIO:([\d]*)\]!iu', array($this, 'audio'), $text);
        return $text;
    }

    /* Вспомогательная функция вывода видео */
    public function video($match) {
        if (!empty($match[1])) {
            $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_videos WHERE Id='" . intval($match[1]) . "' LIMIT 1");
            if (is_object($res)) {
                $tpl_array = array(
                    'video_id'     => Tool::random(10, 'alfa'),
                    'video_Video'  => $res->Video,
                    'video_Bild'   => $res->Bild,
                    'video_Breite' => $res->Breite,
                    'video_Hoehe'  => $res->Hoehe);
                $this->_view->assign($tpl_array);
                return $this->_view->fetch(THEME . '/media/video.tpl');
            }
        }
        return '';
    }

    /* Вспомогательная функция вывода аудио */
    public function audio($match, $url = '', $width = '340') {
        $opt = array();
        $opt['initialvolume'] = '75';     // Уровень громкости по умолчанию
        $opt['transparentbg'] = 'yes';    // Прозрачность фона
        $opt['bg'] = 'CCCCCC'; // Цвет фона
        $opt['leftbg'] = '999999'; // Цвет фона регулятора громкости
        $opt['lefticon'] = '000000'; // Цвет регулятора громкости
        $opt['voltrack'] = 'FFFFFF'; // Цвет трека
        $opt['volslider'] = '666666'; // Цвет слайдера
        $opt['rightbg'] = '999999'; // Цвет фона вокруг кнопки Воспроизведение / Пауза
        $opt['rightbghover'] = '666666'; // Цвет фона вокруг кнопки Воспроизведение / Пауза (при наведении)
        $opt['righticon'] = '000000'; // Цвет фона кнопки Воспроизведение / Пауза
        $opt['righticonhover'] = '000000'; // Цвет фона кнопки Воспроизведение / Пауза (при наведении)
        $opt['loader'] = '009933'; // Цвет бара загрузки
        $opt['track'] = 'FFFFFF'; // Цвет фона полосы прокрутки
        $opt['tracker'] = 'CCCCCC'; // Цвет прогресс трека
        $opt['border'] = '999999'; // Прогресс бар границы
        $opt['skip'] = 'FFFFFF'; // Цвет кнопок Предыдущая / Следующая

        if (!empty($match[1])) {
            $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_audios WHERE Id='" . intval($match[1]) . "' LIMIT 1");
            if (is_object($res)) {
                $url = '/uploads/audios/' . $res->Audio;
                $width = $res->Width;
            }
        }
        if (!empty($url)) {
            $url = preg_replace(array('/\?/su', '/(&amp;|&)/su'), array('%3F', '%26'), $url);
            $rand = mt_rand(0, 9999999);
            $player = '<object type="application/x-shockwave-flash" data="' . BASE_URL . '/lib/player.swf" width="' . $width . '" height="24" id="audio' . $rand . '">' . "\n";
            $player .= '<param name="movie" value="' . BASE_URL . '/lib/player.swf" />' . "\n";
            $player .= '<param name="FlashVars" value="initialvolume=' . $opt['initialvolume'] . '&transparentbg=' . $opt['transparentbg'] . '&bg=' . $opt['bg'] . '&leftbg=' . $opt['leftbg'] . '&lefticon=' . $opt['lefticon'] . '&voltrack=' . $opt['voltrack'] . '&volslider=' . $opt['volslider'] . '&rightbg=' . $opt['rightbg'] . '&rightbghover=' . $opt['rightbghover'] . '&righticon=' . $opt['righticon'] . '&righticonhover=' . $opt['righticonhover'] . '&loader=' . $opt['loader'] . '&tracker=' . $opt['tracker'] . '&track=' . $opt['track'] . '&border=' . $opt['border'] . '&skip=' . $opt['skip'] . '&playerID=' . $rand . '&soundFile=' . BASE_URL . $url . '">' . "\n";
            $player .= '<param name="quality" value="high" />' . "\n";
            $player .= '<param name="menu" value="false" />' . "\n";
            $player .= '<param name="wmode" value="transparent" />' . "\n";
            $player .= '</object>' . "\n";
            return $player;
        }
        return '';
    }

}
