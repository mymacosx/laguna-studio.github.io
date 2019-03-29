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

class Avatar extends Magic {

    /* Метод вывода аватара по параметрам */
    public function load($gravatar, $email, $group, $avatar, $default, $width = 0) {
        $array = $this->_db->cache_fetch_assoc("SELECT
                a.Avatar_B,
                a.Avatar AS Group_Avatar,
                a.Avatar_Default AS Group_Avatar_Default,
                b.Rechte
            FROM
                    " . PREFIX . "_benutzer_gruppen AS a,
                    " . PREFIX . "_berechtigungen AS b
            WHERE
                a.Id = '" . intval($group) . "'
            AND
                b.Gruppe = a.Id
            AND
                b.Sektion = '" . AREA . "' LIMIT 1");

        $array['Email'] = $email;
        $array['Gruppe'] = $group;
        $array['Avatar'] = $avatar;
        $array['Gravatar'] = $gravatar;
        $array['Avatar_Default'] = $default;

        return $this->choice($array, $width);
    }

    /* Метод вывода аватара */
    public function get($id, $width = 0) {
        $row = $this->_db->cache_fetch_assoc("SELECT
                a.Gruppe,
                a.Avatar,
                a.Avatar_Default,
                a.Email,
                a.Gravatar,
                b.Avatar_B,
                b.Avatar AS Group_Avatar,
                b.Avatar_Default AS Group_Avatar_Default,
                g.Rechte
        FROM
                " . PREFIX . "_benutzer AS a,
                " . PREFIX . "_benutzer_gruppen AS b,
                " . PREFIX . "_berechtigungen AS g
        WHERE
            a.Id = '" . intval($id) . "'
        AND
            b.Id = a.Gruppe
        AND
            g.Gruppe = a.Gruppe
        AND
            g.Sektion = '" . AREA . "' LIMIT 1");

        return $this->choice($row, $width);
    }

    /* Метод формирования ссылки аватара */
    protected function link($image, $width = 80) {
        return '<img class="comment_avatar" src="' . Tool::thumb('avatar', $image, $width) . '" alt="" border="0" />';
    }

    /* Метод вывода аватара c Gravatar */
    protected function gravatar($email, $width = 80) {
        $email = md5(strtolower($email));
        return '<img src="http://www.gravatar.com/avatar.php?gravatar_id=' . $email . '&size=' . $width . '" alt="" />';
    }

    /* Метод проверки разрешений */
    protected function permission($array) {
        if (!empty($array)) {
            if ($array['Rechte'] == 'all' || $array['Gruppe'] == 1) {
                return true;
            }
            $permission = explode(',', $array['Rechte']);
            if (in_array('own_avatar', $permission)) {
                return true;
            }
        }
        return false;
    }

    /* Метод выбора аватара */
    protected function choice($array, $width = 0) {
        if (!empty($array) && is_array($array)) {
            if (empty($width)) {
                $width = $array['Avatar_B'];
            }
            $check = $this->permission($array);
            if ($check && $array['Gravatar'] == '1') {
                return $this->gravatar($array['Email'], $width);
            }
            if ($array['Avatar_Default'] == 1 || empty($array['Avatar'])) {
                $check = false;
            }
            if ($check && is_file(UPLOADS_DIR . '/avatars/' . $array['Avatar'])) {
                return $this->link($array['Avatar'], $width);
            }
            if (!empty($array['Group_Avatar']) && $array['Group_Avatar_Default'] == 1) {
                return $this->link($array['Group_Avatar'], $width);
            }
        }
        return $this->link('no_avatar.png', $width);
    }

}
