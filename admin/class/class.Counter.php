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

class Counter extends Magic {

    public $nobann;
    protected $uri;
    protected $expire;
    protected $user_bot;
    protected $user_page;
    protected $user_agent;
    protected $user_logged;
    protected $counter_ablauf;
    protected $params = array();
    protected $_date = array();
    protected $ignore = false;

    public function __construct() {
        $this->_date = array('date' => date('Y-m-d H:i:s'), 'time' => time(), 'd' => date('d'), 'm' => date('m'), 'z' => date('z'), 'n' => date('n'), 'Y' => date('Y'), 'W' => date('W'));
        $this->user_agent = SX::object('Agent');
        $this->expire = $this->_date['time'] + (60 * 10);
        $this->counter_ablauf = (60 * 60) * 6;
        $this->result();
        $this->delete();
        $this->load();
        $this->change();
        $this->ignore = $this->user_agent->is_robot;
        if ($this->ignore === false) {
            $this->save();
        }
        if ($this->ignore === false) {
            $this->stats();
        }
        $this->refferer();
    }

    /* Метод вывода информации кто сейчас на сайте */
    public function online() {
        $online = $bot_online = array();
        $UserOnline = $GuestOnline = $BotOnline = '0';
        $link = '<a href="%s">' . SX::$lang['User_Url'] . '</a>';
        $sql = $this->_db->cache_fetch_assoc_all("SELECT * FROM " . PREFIX . "_benutzer_online WHERE Type = 'site'");
        foreach ($sql as $row) {
            if (($row['Benutzername'] != 'UNAME' && $row['Bots'] == '0') && ($row['Unsichtbar'] == '0' || $_SESSION['user_group'] == '1')) {
                if (!isset($online[$row['Benutzername']])) {
                    $UserOnline++;
                }
                $row['Link'] = sprintf($link, $row['Link']);
                $online[$row['Benutzername']] = $row;
            }
            if ($row['Bots'] == '1') {
                $BotOnline++;
                $row['BotsId'] = $BotOnline;
                if (isset($bot_online[$row['Benutzername']])) {
                    $row['CountBotName'] = $bot_online[$row['Benutzername']]['CountBotName'] + 1;
                    $row['Link'] = $bot_online[$row['Benutzername']]['Link'] . '<br />' . sprintf($link, $row['Link']);
                    $bot_online[$row['Benutzername']] = $row;
                } else {
                    $row['CountBotName'] = 1;
                    $row['Link'] = sprintf($link, $row['Link']);
                    $bot_online[$row['Benutzername']] = $row;
                }
            }
            if ($row['Benutzername'] == 'UNAME' && $row['Bots'] == 0) {
                $GuestOnline++;
            }
        }
        $tpl_array = array(
            'UserOnline'      => $UserOnline,
            'GuestsOnline'    => $GuestOnline,
            'BotOnline'       => $BotOnline,
            'userOnlineLinks' => $online,
            'botOnlineLinks'  => $bot_online);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/counter/counter_online.tpl');
    }

    protected function result() {
        $this->nobann = 0;
        if ($_SESSION['loggedin'] == 1) {
            $this->user_logged = $_SESSION['user_name'];
            $this->user_page = $this->user_agent->uri;
            $this->user_bot = 0;
        } else {
            if ($this->user_agent->robot) {
                $this->user_logged = $this->user_agent->robot;
                $this->user_page = $this->user_agent->uri;
                $this->user_bot = $this->nobann = 1;
            } else {
                $this->user_logged = 'UNAME';
                $this->user_page = '';
                $this->user_bot = 0;
            }
        }
    }

    /* Выполняем запрос на удаление истекших по времени */
    protected function delete() {
        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_online WHERE Expire <= '" . $this->_date['time'] . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_counter_ips WHERE UNIX_TIMESTAMP('" . $this->_date['date'] . "')-UNIX_TIMESTAMP(visit) > '" . $this->counter_ablauf . "'");
    }

    /* Получаем данные для работы класса */
    protected function load() {
        $query = "SELECT ip FROM " . PREFIX . "_counter_ips WHERE ip = INET_ATON('" . IP_USER . "') ; ";
        $query .= "SELECT Rekord_Wert, Rekord_Datum FROM " . PREFIX . "_counter_werte ORDER BY Rekord_Wert DESC LIMIT 1 ; ";
        $query .= "SELECT * FROM " . PREFIX . "_counter_werte WHERE Tag_Id = '" . $this->_date['z'] . "' AND Jahr='" . $this->_date['Y'] . "' LIMIT 1 ; ";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $this->params['getip'] = $result->fetch_assoc();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $this->params['rekord'] = $result->fetch_assoc();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $this->params['werte'] = $result->fetch_assoc();
                $result->close();
            }

        }
    }

    protected function change() {
        $unsichtbar = Arr::getSession('unsichtbar') == 1 ? 'INVISIBLE' : '0';
        $this->_db->query("INSERT IGNORE INTO " . PREFIX . "_benutzer_online (
			Ip,
			Uid,
			Expire,
			Benutzername,
			Unsichtbar,
			Link,
			Bots,
			Type
		) VALUES (
			INET_ATON('" . IP_USER . "'),
			'" . $_SESSION['benutzer_id'] . "',
			'" . $this->expire . "',
			'" . $this->_db->escape($this->user_logged) . "',
			'" . $unsichtbar . "',
			'" . $this->_db->escape($this->user_page) . "',
			'" . $this->_db->escape($this->user_bot) . "',
			'site'
		) ON DUPLICATE KEY UPDATE
		        Expire = '" . $this->expire . "',
			Unsichtbar = '" . $this->_db->escape($unsichtbar) . "',
			Benutzername = '" . $this->_db->escape($this->user_logged) . "',
			Uid = '" . $_SESSION['benutzer_id'] . "',
			Link = '" . $this->_db->escape($this->user_page) . "',
			Bots = '" . $this->_db->escape($this->user_bot) . "',
			Type = 'site'");

        if (empty($this->params['werte'])) {
            $insert_array = array(
                'Tag'          => $this->_date['d'],
                'Jahr'         => $this->_date['Y'],
                'Monat'        => $this->_date['m'],
                'Tag_Id'       => $this->_date['z'],
                'Tag_Wert'     => 0,
                'Wochen_Id'    => $this->_date['W'],
                'Wochen_Wert'  => 0,
                'Monat_Id'     => $this->_date['n'],
                'Monat_Wert'   => 0,
                'Jahr_Id'      => $this->_date['Y'],
                'Jahr_Wert'    => 0,
                'Gesamt_Wert'  => 0,
                'Rekord_Datum' => $this->_date['date'],
                'Rekord_Wert'  => 0,
                'Hits'         => 1);
            $this->_db->insert_query('counter_werte', $insert_array);

            $this->ignore = true;
        } else {
            $this->_db->query("UPDATE " . PREFIX . "_counter_werte SET Hits=Hits+1 WHERE Tag_Id = '" . $this->_date['z'] . "' AND Jahr = '" . $this->_date['Y'] . "'");
        }
    }

    /* Записываем или обновляем время последнего посещения пользователем сайта */
    protected function save() {
        if (!empty($this->params['getip']['ip'])) {
            $this->ignore = true;
        }
        $this->_db->query("INSERT IGNORE INTO " . PREFIX . "_counter_ips (
	        ip,
	        visit
	    ) VALUES (
	        INET_ATON('" . IP_USER . "'),
	        '" . $this->_date['date'] . "'
	    ) ON DUPLICATE KEY UPDATE
	        visit = '" . $this->_date['date'] . "'");
    }

    /* Записываем новую строку в таблице статистики */
    protected function stats() {
        $array = $this->params['werte'];
        if (isset($array['Tag_Id']) && $array['Tag_Id'] == $this->_date['z']) {
            $array['Tag_Wert'] ++;
        } else {
            $array['Tag_Wert'] = 1;
            $array['Tag_Id'] = $this->_date['z'];
        }
        if (isset($array['Wochen_Id']) && $array['Wochen_Id'] == $this->_date['W']) {
            $array['Wochen_Wert'] ++;
        } else {
            $array['Wochen_Wert'] = 1;
            $array['Wochen_Id'] = $this->_date['W'];
        }
        if (isset($array['Monat_Id']) && $array['Monat_Id'] == $this->_date['n']) {
            $array['Monat_Wert'] ++;
        } else {
            $array['Monat_Wert'] = 1;
            $array['Monat_Id'] = $this->_date['n'];
        }
        if (isset($array['Jahr_Id']) && $array['Jahr_Id'] == $this->_date['Y']) {
            $array['Jahr_Wert'] ++;
        } else {
            $array['Jahr_Wert'] = 1;
            $array['Jahr_Id'] = $this->_date['Y'];
        }
        $array['Gesamt_Wert'] ++;

        if ($array['Tag_Wert'] > $array['Rekord_Wert']) {
            $array['Rekord_Wert'] = $array['Tag_Wert'];
            $array['Rekord_Datum'] = $this->_date['date'];
        }

        $array = array(
            'Tag_Id'       => $array['Tag_Id'],
            'Tag_Wert'     => $array['Tag_Wert'],
            'Wochen_Id'    => $array['Wochen_Id'],
            'Wochen_Wert'  => $array['Wochen_Wert'],
            'Monat_Id'     => $array['Monat_Id'],
            'Monat_Wert'   => $array['Monat_Wert'],
            'Jahr_Id'      => $array['Jahr_Id'],
            'Jahr_Wert'    => $array['Jahr_Wert'],
            'Gesamt_Wert'  => $array['Gesamt_Wert'],
            'Rekord_Datum' => $array['Rekord_Datum'],
            'Rekord_Wert'  => $array['Rekord_Wert'],
        );
        $this->_db->update_query('counter_werte', $array, "Tag_Id = '" . $this->_date['z'] . "' AND Jahr = '" . $this->_date['Y'] . "'");
    }

    /* Вывод в шаблон данных о статистике сайта */
    public function show() {
        $counter = $this->_db->fetch_assoc_all("SELECT COUNT(Ip) AS counter FROM " . PREFIX . "_benutzer_online WHERE Type = 'site'
		  UNION ALL
		SELECT SUM(Gesamt_Wert) AS counter FROM " . PREFIX . "_counter_werte
		  UNION ALL
		SELECT SUM(Gesamt_Wert) AS counter FROM " . PREFIX . "_counter_werte WHERE Tag_Id='" . $this->_date['z'] . "' AND Jahr='" . $this->_date['Y'] . "'
		  UNION ALL
		SELECT SUM(Gesamt_Wert) AS counter FROM " . PREFIX . "_counter_werte WHERE Jahr='" . $this->_date['Y'] . "'
		  UNION ALL
		SELECT SUM(Gesamt_Wert) AS counter FROM " . PREFIX . "_counter_werte WHERE Monat='" . $this->_date['m'] . "' AND Jahr='" . $this->_date['Y'] . "'
		  UNION ALL
		SELECT SUM(Gesamt_Wert) AS counter FROM " . PREFIX . "_counter_werte WHERE Wochen_Id='" . $this->_date['W'] . "' AND Jahr='" . $this->_date['Y'] . "'");

        $tpl_array = array(
            'Counter_Online'   => $counter[0]['counter'],
            'Counter_Gesamt'   => $counter[1]['counter'],
            'Counter_Heute'    => $counter[2]['counter'],
            'Counter_Jahr'     => $counter[3]['counter'],
            'Counter_Monat'    => $counter[4]['counter'],
            'Counter_Woche'    => $counter[5]['counter'],
            'Counter_RekordAm' => date('d.m.Y', strtotime($this->params['rekord']['Rekord_Datum'])),
            'Counter_Rekord'   => $this->params['rekord']['Rekord_Wert']);
        $this->_view->assign($tpl_array);
        $this->_view->assign('CounterDisplay', $this->_view->fetch(THEME . '/counter/counter_small.tpl'));
    }

    /* Запись данных о пользователе в базу */
    protected function refferer() {
        $browser = !empty($this->user_agent->browser) ? $this->user_agent->browser : $this->user_agent->mobile;
        if (empty($browser)) {
            $browser = 'unknown';
        }
        if ($this->user_agent->robot) {
            $platform = $this->user_agent->robot;
        } else {
            $platform = !empty($this->user_agent->platform) ? $this->user_agent->platform : 'unknown';
        }
        $insert_array = array(
            'Os'        => $platform,
            'IPAdresse' => IP_USER,
            'Ua'        => $browser . ' ' . $this->user_agent->version,
            'Referer'   => $this->user_agent->referer,
            'Details'   => $this->user_agent->agent,
            'Datum'     => $this->_date['date'],
            'Datum_Int' => $this->_date['time'],
            'Words'     => $this->user_agent->search,
            'UserId'    => $_SESSION['benutzer_id'],
            'UserName'  => $this->user_logged,
            'Url'       => BASE_URL . $this->user_agent->uri);
        $this->_db->insert_query('counter_referer', $insert_array);
    }

}
