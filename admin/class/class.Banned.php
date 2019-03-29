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

class Banned extends Magic {

    public function bann() {
        if (!get_active('banned') || $this->__object('Counter')->nobann == 1 || $_SESSION['user_group'] == 1) {
            $this->__object('Redir')->redirect();
        } else {
            $arr = $array = array();
            $stime = time();
            $period = 3600 * 24;
            $Reson = 'Система безопасности выявила попытку проведения атаки на сервер';
            $TimeEnd = $stime + $period;
            $array = Arr::getSession(array('user_name' => '', 'login_email' => '', 'benutzer_id' => 0));
            $array['ip_user'] = IP_USER != '127.0.0.1' ? IP_USER : '';
            $array['benutzer_id'] = $array['benutzer_id'] != 0 ? $array['benutzer_id'] : '';

            $sql = $this->_db->query("SELECT User_id, Name, Email, Ip FROM " . PREFIX . "_banned WHERE Type = 'autobann'");
            while ($row = $sql->fetch_object()) {
                if (!empty($row->User_id)) {
                    $arr['user'][] = $row->User_id;
                }
                if (!empty($row->Name)) {
                    $arr['name'][] = $row->Name;
                }
                if (!empty($row->Email)) {
                    $arr['mail'][] = $row->Email;
                }
                if (!empty($row->Ip)) {
                    $arr['ip'][] = $row->Ip;
                }
            }
            $sql->close();

            if (in_array($array['benutzer_id'], $arr['user'])) {
                $where = "User_id = '" . $array['benutzer_id'] . "'";
            } elseif (in_array($array['user_name'], $arr['name'])) {
                $where = "Name = '" . $array['user_name'] . "'";
            } elseif (in_array($array['login_email'], $arr['mail'])) {
                $where = "Email = '" . $array['login_email'] . "'";
            } elseif (in_array($array['ip_user'], $arr['ip'])) {
                $where = "Ip = '" . $array['ip_user'] . "'";
            } else {
                $where = '';
            }

            if (!empty($where)) {
                $array = array(
                    'User_id'   => $array['benutzer_id'],
                    'TimeStart' => $stime,
                    'TimeEnd'   => $TimeEnd,
                    'Name'      => $array['user_name'],
                    'Email'     => $array['login_email'],
                    'Ip'        => $array['ip_user'],
                    'Aktiv'     => '1',
                );
                $this->_db->update_query('banned', $array, $where);
                SX::syslog('Автоматическое продление системой безопасности нахождения пользователя в бан-листе на 24 часа', '3', $array['benutzer_id']);
                Arr::setCookie('welcome', $array['ip_user'], $period);
            } else {
                if (!empty($array['benutzer_id']) || !empty($array['user_name']) || !empty($array['login_email']) || !empty($array['ip_user'])) {
                    $insert_array = array(
                        'User_id'   => $array['benutzer_id'],
                        'Reson'     => $Reson,
                        'Type'      => 'autobann',
                        'TimeStart' => $stime,
                        'TimeEnd'   => $TimeEnd,
                        'Name'      => $array['user_name'],
                        'Email'     => $array['login_email'],
                        'Ip'        => $array['ip_user'],
                        'Aktiv'     => '1');
                    $this->_db->insert_query('banned', $insert_array);
                    SX::syslog('Автоматическое добавление системой безопасности пользователя в бан-лист на 24 часа', '3', $array['benutzer_id']);
                    Arr::setCookie('welcome', $array['ip_user'], $period);
                }
            }
        }
        $this->__object('Redir')->redirect('index.php?p=banned');
    }

    public function get() {
        if ($_SESSION['banned'] == 1 && Arr::getSession('user_group') != 1) {
            $check = false;
            $where = array();
            $period = time() + 10;
            $array = Arr::getSession(array('user_name' => '', 'login_email' => '', 'benutzer_id' => 0));
            if (!empty($array['benutzer_id'])) {
                $where[] = "User_id = '" . $array['benutzer_id'] . "'";
            }
            if (!empty($array['user_name'])) {
                $where[] = "Name = '" . $array['user_name'] . "'";
            }
            if (Tool::isMail($array['login_email'])) {
                $domain = explode('@', $array['login_email']);
                $where[] = "Email = '" . $array['login_email'] . "'";
                $where[] = "Email = '*@" . $domain[1] . "'";
            }
            $ip = explode('.', IP_USER);
            $where[] = "Ip = '" . IP_USER . "'";
            $where[] = "Ip = '" . $ip[0] . "." . $ip[1] . "." . $ip[2] . ".*'";
            $where[] = "Ip = '" . $ip[0] . "." . $ip[1] . ".*.*'";
            $where[] = "Ip = '" . $ip[0] . "*.*.*'";
            $array['cookie_ip'] = preg_replace('/[^\d.]/u', '', Arr::getCookie('welcome'));
            if (!empty($array['cookie_ip'])) {
                $ip = explode('.', $array['cookie_ip']);
                $where[] = "Ip = '" . $array['cookie_ip'] . "'";
                $where[] = "Ip = '" . $ip[0] . "." . $ip[1] . "." . $ip[2] . ".*'";
                $where[] = "Ip = '" . $ip[0] . "." . $ip[1] . ".*.*'";
                $where[] = "Ip = '" . $ip[0] . "*.*.*'";
            }
            $sql = $this->_db->query("SELECT SQL_CACHE * FROM " . PREFIX . "_banned WHERE (" . implode(' OR ', $where) . ") AND Aktiv = '1' LIMIT 10");
            $banned = array();
            while ($row = $sql->fetch_object()) {
                $period = max($row->TimeEnd, $period);
                if ($row->Ip == IP_USER) {
                    $check = true;
                }
                $banned[] = $row;
            }
            $sql->close();
            $this->add($array['cookie_ip'], $period, $check);
            $this->_view->assign('banned', $banned);

            $seo_array = array(
                'headernav' => $this->_lang['NoPerm'],
                'pagetitle' => $this->_lang['NoPerm'],
                'content'   => $this->_view->fetch(THEME . '/banned/banned.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $this->__object('Redir')->redirect();
        }
    }

    protected function add($cookie_ip, $period, $check) {
        if ($check === false && $cookie_ip != IP_USER) {
            $insert_array = array(
                'Reson'   => 'Система распознала Вас как сменившего IP адрес после блокировки предыдущего IP адреса',
                'Type'    => 'autobann',
                'Ip'      => IP_USER,
                'TimeEnd' => $period);
            $this->_db->insert_query('banned', $insert_array);
            SX::syslog('Автоматическое добавление в бан нового IP адреса: ' . IP_USER, '3', $_SESSION['benutzer_id']);
        }
        Arr::setCookie('welcome', IP_USER, $period - time());
    }

}
