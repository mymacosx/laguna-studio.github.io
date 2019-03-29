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

class AdminShop extends Magic {

    public $limit = 15;
    public $separator = ";";
    public $enclosed = "\"";
    public $cutter = "\r\n";
    protected $UserId;

    public function __construct() {
        $this->UserId = $_SESSION['benutzer_id'];
    }

    protected function getTracking() {
        $track = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_tracking ORDER BY Id ASC");
        return $track;
    }

    public function showRegion() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Name']) as $id) {
                if (!empty($_POST['Name'][$id])) {
                    $array = array(
                        'VersandFreiAb' => abs(str_replace(',', '.', $_POST['VersandFreiAb'][$id])),
                        'Name'          => $_POST['Name'][$id],
                        'Code'          => strtoupper($_POST['Code'][$id]),
                        'Aktiv'         => $_POST['Aktiv'][$id],
                        'Ust'           => $_POST['Ust'][$id],
                    );
                    $this->_db->update_query('laender', $array, "Id = '" . intval($id) . "'");
                }
            }
            $this->__object('Redir')->redirect('index.php?do=shop&sub=regions&page=' . Arr::getRequest('page', 1));
        }

        $limit = $this->limit;
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_laender ORDER BY Aktiv, Name ASC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=shop&amp;sub=regions&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Settings_countries_title']);
        $this->_view->content('/shop/region.tpl');
    }

    public function addRegion() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1 && !empty($_POST['Name']) && !empty($_POST['Code'])) {
            $code = strtoupper($_POST['Code']);
            $row = $this->_db->fetch_assoc("SELECT Id FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape($code) . "'");
            if (!isset($row['Id'])) {
                $array = array(
                    'Code'          => $code,
                    'Name'          => $_POST['Name'],
                    'Aktiv'         => Arr::getRequest('Aktiv', 1),
                    'Ust'           => Arr::getRequest('Ust', 1),
                    'VersandFreiAb' => Arr::getRequest('VersandFreiAb', 0),
                );
                $this->_db->insert_query('laender', $array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил регион: ' . $_POST['Name'], '1', $this->UserId);
                $this->__object('AdminCore')->script('close');
            } else {
                $this->__object('AdminCore')->script('message', 5000, $this->_lang['RegionCodeUnique']);
            }
        }
        $this->_view->assign('title', $this->_lang['Settings_countries_title']);
        $this->_view->content('/shop/region_new.tpl');
    }

    public function delRegion() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval(Arr::getRequest('id'));
        $row = $this->_db->fetch_assoc("SELECT Name FROM " . PREFIX . "_laender WHERE Id = '" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_laender WHERE Id = '" . $id . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил регион: ' . $row['Name'], '1', $this->UserId);
        $this->__object('AdminCore')->backurl();
    }

    public function codesTracking() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1) {
            foreach ($_POST['Name'] as $sid => $key) {
                if (!empty($_POST['Name'][$sid]) && !empty($_POST['Hyperlink'][$sid])) {
                    $this->_db->insert_query('shop_tracking', array('Name' => $_POST['Name'][$sid], 'Hyperlink' => $_POST['Hyperlink'][$sid]));
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $sid => $key) {
                if (!empty($_POST['Name'][$sid]) && !empty($_POST['Hyperlink'][$sid])) {
                    $this->_db->query("UPDATE " . PREFIX . "_shop_tracking SET Name = '" . $this->_db->escape(trim($_POST['Name'][$sid])) . "', Hyperlink='" . $this->_db->escape(trim($_POST['Hyperlink'][$sid])) . "' WHERE Id = '" . intval($sid) . "'");
                }
                if (isset($_POST['Del'][$sid]) && $_POST['Del'][$sid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_tracking WHERE Id = '" . intval($sid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('Tracker', $this->getTracking());
        $this->_view->assign('title', $this->_lang['Shop_Tracking']);
        $this->_view->content('/shop/tracking.tpl');
    }

    public function showMoney() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getRequest('search') == 1) {
            $ZeitStart = mktime(0, 0, 1, Arr::getRequest('start_Month'), Arr::getRequest('start_Day'), Arr::getRequest('start_Year'));
            $ZeitEnde = mktime(23, 59, 59, Arr::getRequest('end_Month'), Arr::getRequest('end_Day'), Arr::getRequest('end_Year'));
        } else {
            $ZeitStart = mktime(0, 0, 1, date('m'), date('d') - (date('d') - 1), date('Y'));
            $ZeitEnde = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        }

        $ZahlungsId = (isset($_REQUEST['ZahlungsId']) && $_REQUEST['ZahlungsId'] != 'egal') ? "AND ZahlungsId = '" . intval(Arr::getRequest('ZahlungsId')) . "'" : '';
        $VersandId = (isset($_REQUEST['VersandId']) && $_REQUEST['VersandId'] != 'egal') ? "AND VersandId = '" . intval(Arr::getRequest('VersandId')) . "'" : '';
        $Benutzer = (!empty($_REQUEST['Benutzer'])) ? "AND Benutzer = '" . $this->_db->escape(Arr::getRequest('Benutzer')) . "'" : '';

        $query = "SELECT SUM(Betrag) AS GesamtUmsatz FROM " . PREFIX . "_shop_bestellungen WHERE (Status = 'ok' || Status = 'oksend') AND (Datum BETWEEN $ZeitStart AND $ZeitEnde) $ZahlungsId $VersandId $Benutzer ; ";
        $query .= "SELECT SUM(Betrag) AS GesamtUmsatz FROM " . PREFIX . "_shop_bestellungen WHERE Datum BETWEEN $ZeitStart AND $ZeitEnde $ZahlungsId $VersandId $Benutzer ; ";
        $query .= "SELECT SUM(Betrag) AS GesamtUmsatz FROM " . PREFIX . "_shop_bestellungen WHERE (Status = 'wait') AND (Datum BETWEEN $ZeitStart AND $ZeitEnde) $ZahlungsId $VersandId $Benutzer ; ";
        $query .= "SELECT SUM(Betrag) AS GesamtUmsatz FROM " . PREFIX . "_shop_bestellungen WHERE (Status = 'progress') AND (Datum BETWEEN $ZeitStart AND $ZeitEnde) $ZahlungsId $VersandId $Benutzer ; ";
        $query .= "SELECT SUM(Betrag) AS GesamtUmsatz FROM " . PREFIX . "_shop_bestellungen WHERE (Status = 'failed') AND (Datum BETWEEN $ZeitStart AND $ZeitEnde) $ZahlungsId $VersandId $Benutzer";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $row = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row2 = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row3 = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row4 = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row5 = $result->fetch_object();
                $result->close();
            }
        }

        $row->GesamtUmsatz = number_format($row->GesamtUmsatz, '2', ',', '.');
        $row->GesamtUmsatzAlle = number_format($row2->GesamtUmsatz, '2', ',', '.');
        $row->GesamtUmsatzWartend = number_format($row3->GesamtUmsatz, '2', ',', '.');
        $row->GesamtUmsatzBearbeitung = number_format($row4->GesamtUmsatz, '2', ',', '.');
        $row->GesamtFehlgeschlagen = number_format($row5->GesamtUmsatz, '2', ',', '.');
        $this->_view->assign('paymentMethods', $this->listPayments());
        $this->_view->assign('shippingMethods', $this->listShipper());
        $this->_view->assign('row', $row);
        $this->_view->assign('ZeitStart', $ZeitStart);
        $this->_view->assign('ZeitEnde', $ZeitEnde);
        $this->_view->assign('currency', SX::get('shop.WaehrungSymbol_1'));
        $this->_view->assign('title', $this->_lang['Shop_showmoney_title']);
        $this->_view->content('/shop/showmoney.tpl');
    }

    protected function checkAddons($field_id, $id, $field_name, $name_old, $lc) {
        $error = '';
        switch ($field_id) {
            case 1:
                $zubWhere = 'a';
                break;
            case 2:
                $zubWhere = 'b';
                break;
            case 3:
                $zubWhere = 'c';
                break;
        }

        if (empty($_POST['Teile_' . $field_id . '_Name_1'][$id])) {
            $r = $this->_db->cache_fetch_object("SELECT Zub_{$zubWhere} AS DA FROM " . PREFIX . "_shop_produkte WHERE Kategorie = '" . intval($_POST['id'][$id]) . "' LIMIT 1");
            if (is_object($r) && $r->DA && !empty($name_old)) {
                $error = $this->_text->replace($this->_lang['Shop_CategoriesNoEmpty'], array('__FIELD__' => $field_name, '__VAL__' => $name_old));
                $q1 = '';
            } else {
                $q1 = "Teile_" . $field_id . "_Name_1 = '',";
                $q1 .= "Teile_" . $field_id . "_Name_2 = '',";
                $q1 .= "Teile_" . $field_id . "_Name_3 = '',";
            }
        } else {
            $q1 = "Teile_" . $field_id . "_Name_$lc = '" . $this->_db->escape($_POST['Teile_' . $field_id . '_Name_1'][$id]) . "',";
        }
        return $q1 . '|' . $error;
    }

    public function categAddons() {
        if (!perm('shop_addons')) {
            $this->__object('AdminCore')->noAccess();
        }
        $categs_used = $error = array();
        if (Arr::getGet('del') == 1) {
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_kategorie_zubehoer WHERE Id = '" . intval(Arr::getGet('categ')) . "'");
            $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Zub_a = 'NULL',Zub_b = 'NULL',Zub_c = 'NULL' WHERE Kategorie = '" . intval(Arr::getGet('shop_categ')) . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил в магазине категорию', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1) {
            $insert_array = array(
                'Kategorie'      => intval(Arr::getPost('Categ')),
                'Teile_1_Name_1' => Arr::getPost('Teile_1_Name_1'),
                'Teile_1_Name_2' => Arr::getPost('Teile_1_Name_1'),
                'Teile_1_Name_3' => Arr::getPost('Teile_1_Name_1'),
                'Teile_2_Name_1' => Arr::getPost('Teile_2_Name_1'),
                'Teile_2_Name_2' => Arr::getPost('Teile_2_Name_1'),
                'Teile_2_Name_3' => Arr::getPost('Teile_2_Name_1'),
                'Teile_3_Name_1' => Arr::getPost('Teile_3_Name_1'),
                'Teile_3_Name_2' => Arr::getPost('Teile_3_Name_1'),
                'Teile_3_Name_3' => Arr::getPost('Teile_3_Name_1'),
                'Sektion'        => $_SESSION['a_area']);
            $this->_db->insert_query('shop_kategorie_zubehoer', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал в магазине новую категорию', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['id']) as $id) {
                $c1 = explode('|', $this->checkAddons(1, $id, $this->_db->escape($_POST['name'][$id]), $this->_db->escape($_POST['name_old'][$id]), $this->_db->escape(Arr::getRequest('lc'))));
                $q1 = $c1[0];
                if (!empty($c1[1])) {
                    $error[] = $c1[1];
                }
                $c2 = explode('|', $this->checkAddons(2, $id, $this->_db->escape($_POST['name'][$id]), $this->_db->escape($_POST['name_old_2'][$id]), $this->_db->escape(Arr::getRequest('lc'))));
                $q2 = $c2[0];
                if (!empty($c2[1])) {
                    $error[] = $c2[1];
                }
                $c3 = explode('|', $this->checkAddons(3, $id, $this->_db->escape($_POST['name'][$id]), $this->_db->escape($_POST['name_old_3'][$id]), $this->_db->escape(Arr::getRequest('lc'))));
                $q3 = $c3[0];
                if (!empty($c3[1])) {
                    $error[] = $c3[1];
                }

                $this->_db->query("UPDATE " . PREFIX . "_shop_kategorie_zubehoer SET $q1 $q2 $q3 Kategorie = '" . intval($_POST['id'][$id]) . "' WHERE Id = '" . intval($id) . "'");
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил в магазине категорию', '1', $this->UserId);
                $this->__object('AdminCore')->script('save');
            }
        }

        $addons = array();
        $LC = !empty($_REQUEST['lc']) ? intval($_REQUEST['lc']) : 1;
        $sql = $this->_db->query("SELECT
                    Id,
                    Kategorie,
                    Teile_1_name_{$LC} AS Teile_1_Name_1,
                    Teile_2_name_{$LC} AS Teile_2_Name_1,
                    Teile_3_name_{$LC} AS Teile_3_Name_1
             FROM " . PREFIX . "_shop_kategorie_zubehoer WHERE Sektion = '" . $_SESSION['a_area'] . "' ORDER BY Id DESC");
        while ($row = $sql->fetch_object()) {
            $row->CategInfo = $this->infoCateg($row->Kategorie);
            if (isset($row->CategInfo->Id)) {
                $categs_used[] = $row->CategInfo->Id;
            }
            $check = $this->_db->cache_fetch_assoc("SELECT Id FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . $row->Kategorie . "' AND Sektion = '" . $_SESSION['a_area'] . "' LIMIT 1");
            if (is_array($check)) {
                $addons[] = $row;
            }
        }
        $sql->close();

        $scategs = array();
        $this->_view->assign('title', $this->_lang['Shop_CategoriesAddons']);
        $this->_view->assign('error', ((count($error) > 0 && !empty($error)) ? $error : ''));
        $this->_view->assign('categ_addons', $addons);
        $this->_view->assign('categs_used', $categs_used);
        $this->_view->assign('lc', $LC);
        $this->_view->assign('shop_search_small_categs', $this->simpleCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1));
        $this->_view->content('/shop/categories_addons.tpl');
    }

    protected function infoCateg($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name_1 AS Name, Id FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $res;
    }

    protected function ustELements() {
        $items = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_ustzone ORDER BY Id ASC");
        return $items;
    }

    public function showStprices($id) {
        if (!perm('shop_stprices')) {
            $this->__object('AdminCore')->noAccess();
        } else {
            $id = intval($id);
            if (Arr::getRequest('save') == 1) {
                $count = 1;
                if (Arr::getPost('Id') >= 1) {
                    foreach ($_POST['Id'] as $sid => $key) {
                        $sid = intval($sid);
                        $Von = ($_POST['Von'][$sid] == 1 || $_POST['Von'][$sid] < 2 || $_POST['Von'][$sid] == '') ? 2 : $this->_db->escape($_POST['Von'][$sid]);
                        $Bis = ($_POST['Bis'][$sid] <= $Von) ? $Von + 1 : $this->_db->escape($_POST['Bis'][$sid]);
                        $Wert = $this->_db->escape($_POST['Wert'][$sid]);
                        $Operand = $this->_db->escape($_POST['Operand'][$sid]);
                        $this->_db->query("UPDATE " . PREFIX . "_shop_staffelpreise SET Von = '{$Von}', Bis = '{$Bis}', Wert = '{$Wert}', Operand='{$Operand}' WHERE Id = '{$sid}'");
                        $count++;
                    }
                }
                if (!empty($_POST['VonNeu']) && !empty($_POST['BisNeu']) && !empty($_POST['OperandNeu'])) {
                    $von = $this->_db->escape(Arr::getPost('VonNeu'));
                    $bis = $this->_db->escape(Arr::getPost('BisNeu'));
                    $Wert = $this->_db->escape(Arr::getPost('WertNeu'));
                    $Operand = $this->_db->escape(Arr::getPost('OperandNeu'));

                    $insert_array = array(
                        'ArtikelId' => $id,
                        'Von'       => $von,
                        'Bis'       => $bis,
                        'Wert'      => $Wert,
                        'Operand'   => $Operand);
                    $this->_db->insert_query('shop_staffelpreise', $insert_array);
                }
                if (Arr::getPost('Del') >= 1) {
                    foreach ($_POST['Del'] as $did => $key) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_staffelpreise WHERE Id = '" . intval($did) . "'");
                        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил скидку', '1', $this->UserId);
                    }
                }
                SX::syslog($_SESSION['user_name'] . ' обновил скидку', '1', $this->UserId);
                $this->__object('AdminCore')->script('save');
            }

            $stprices = $this->_db->fetch_assoc_all("SELECT * FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '" . $id . "' ORDER BY Von ASC");

            $this->_view->assign('stprices', $stprices);
            $this->_view->assign('title', $this->_lang['Shop_articles_stprices']);
            $this->_view->content('/shop/article_strprices.tpl');
        }
    }

    public function listOrders() {
        $this->_view->assign('orders', $this->getOrders());
        $this->_view->assign('StartDate', mktime(0, 0, 1, date('m') - 1, date('d'), date('Y')));
        $this->_view->assign('title', $this->_lang['Shop_title_orders']);
        $this->_view->content('/shop/orders.tpl');
    }

    protected function countOrder() {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS ResCount FROM " . PREFIX . "_shop_bestellungen");
        return $res->ResCount;
    }

    public function startOrders($limit = 5) {
        $this->_view->assign('count', $this->countOrder());
        $this->_view->assign('orders', $this->getOrders($limit));
        return $this->_view->fetch(THEME . '/shop/start_orders.tpl');
    }

    public function show() {
        $this->_view->assign('articles', $this->load());
        $this->_view->assign('title', $this->_lang['Shop_title_articles']);
        $this->_view->content('/shop/articles.tpl');
    }

    public function startVotes($limit = 5) {
        $votes = array();
        $query = "SELECT
                a.*,
                b.Titel_1 AS Titel
            FROM
                " . PREFIX . "_shop_bewertung AS a,
                " . PREFIX . "_shop_produkte AS b
            WHERE
                a.Offen = '0'
            AND
                b.Id = a.Produkt
            GROUP BY a.Produkt
            ORDER BY a.Datum DESC LIMIT " . intval($limit);

        $result = $this->_db->query($query);
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $this->votes($row['Produkt'], true);
            $votes[] = $row;
        }
        $result->close();
        $this->_view->assign('votes', $votes);
        return $this->_view->fetch(THEME . '/shop/start_votes.tpl');
    }

    public function showVariants($id, $categ) {
        if (!perm('shop_variants')) {
            $this->__object('AdminCore')->noAccess();
        } else {
            if (Arr::getRequest('copy') == 1) {
                $where = explode('|||', $_POST['copyvar']);
                $artid = $where[1];
                $newid = $where[2];

                $nq = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_varianten WHERE ArtId='" . intval($artid) . "'");
                while ($rq = $nq->fetch_object()) {
                    $insert_array = array(
                        'KatId'          => $rq->KatId,
                        'ArtId'          => $newid,
                        'Name_1'         => $rq->Name_1,
                        'Name_2'         => $rq->Name_2,
                        'Name_3'         => $rq->Name_3,
                        'Wert'           => $rq->Wert,
                        'Operant'        => $rq->Operant,
                        'Position'       => $rq->Position,
                        'Vorselektiert'  => $rq->Vorselektiert,
                        'Gewicht'        => $rq->Gewicht,
                        'GewichtOperant' => $rq->GewichtOperant);
                    $this->_db->insert_query('shop_varianten', $insert_array);
                }
                $nq->close();
            }

            if (Arr::getRequest('save') == 1) {
                if (Arr::getPost('Del') >= 1) {
                    foreach ($_POST['Del'] as $del => $key) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_varianten WHERE Id = '" . intval($del) . "'");
                        SX::syslog($_SESSION['user_name'] . ' удалил в магазине вариант товара', '1', $this->UserId);
                    }
                }

                if (isset($_POST['Id'])) {
                    foreach ($_POST['Id'] as $vid => $key) {
                        if (!empty($_POST['Name_1'][$vid])) {
                            $array = array(
                                'Vorselektiert'  => '0',
                                'Name_1'         => sanitize($_POST['Name_1'][$vid]),
                                'Name_2'         => (!empty($_POST['Name_2'][$vid]) ? sanitize($_POST['Name_2'][$vid]) : sanitize($_POST['Name_1'][$vid])),
                                'Name_3'         => (!empty($_POST['Name_3'][$vid]) ? sanitize($_POST['Name_3'][$vid]) : sanitize($_POST['Name_1'][$vid])),
                                'Operant'        => $_POST['Operant'][$vid],
                                'Wert'           => $this->cleanPrice($_POST['Wert'][$vid]),
                                'Position'       => $_POST['Position'][$vid],
                                'Bestand'        => $_POST['Bestand'][$vid],
                                'Gewicht'        => $_POST['Gewicht'][$vid],
                                'GewichtOperant' => $_POST['GewichtOperant'][$vid],
                            );
                            $this->_db->update_query('shop_varianten', $array, "Id = '" . intval($vid) . "'");
                        }
                    }
                }

                if (isset($_POST['Vor'])) {
                    foreach ($_POST['Vor'] as $key) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_varianten SET Vorselektiert = '1' WHERE Id = '" . intval($key) . "'");
                    }
                }

                foreach ($_POST['NewName'] as $neu => $key) {
                    if (!empty($key)) {
                        $key = $this->_db->escape(sanitize($key));
                        $insert_array = array(
                            'KatId'          => $neu,
                            'ArtId'          => intval(Arr::getRequest('id')),
                            'Name_1'         => $key,
                            'Name_2'         => $key,
                            'Name_3'         => $key,
                            'Operant'        => $_POST['NewOperant'][$neu],
                            'Wert'           => $this->cleanPrice($_POST['WertNeu'][$neu]),
                            'Gewicht'        => $_POST['GewichtNeu'][$neu],
                            'GewichtOperant' => $_POST['GewichtOperantNeu'][$neu],
                            'Position'       => $_POST['PositionNeu'][$neu]);
                        $this->_db->insert_query('shop_varianten', $insert_array);
                        SX::syslog($_SESSION['user_name'] . ' добавил в магазине новый вариант товара : ' . $key, '1', $this->UserId);
                    }
                }
                $this->__object('AdminCore')->script('save');
            }

            $vars_for_copy = array();
            $prod_vars = $this->_db->query("SELECT
                    a.Id,
                    a.Kategorie,
                    a.Titel_1,
                    c.Id AS Xid
             FROM
                    " . PREFIX . "_shop_produkte AS a,
                    " . PREFIX . "_shop_varianten_kategorien AS c
            WHERE
                    c.KatId = a.Kategorie AND
                    a.Kategorie = " . intval($categ) . " AND
                    a.Id != " . intval($id) . "
            ORDER BY
                    a.Titel_1 ASC");

            while ($row_vars = $prod_vars->fetch_object()) {
                $cq = $this->_db->cache_fetch_object("SELECT Id AS vId, KatId FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $row_vars->Id . "' LIMIT 1");
                if (is_object($cq)) {
                    $row_vars->Name = $row_vars->Titel_1;
                    $row_vars->arr = $cq->KatId . "|||" . $row_vars->Id . "|||" . $id;
                    $vars_for_copy[] = $row_vars;
                }
            }
            $prod_vars->close();
            $this->_view->assign('vars_for_copy', $vars_for_copy);
            $this->_view->assign('variant_categs', $this->variantCategs($id, $categ));
            $this->_view->assign('title', $this->_lang['Shop_variants']);
            $this->_view->content('/shop/article_variants.tpl');
        }
    }

    protected function variantCategs($id, $categ) {
        $variants = array();
        $sql = $this->_db->query("SELECT
            Id,
                KatId,
                Name_1,
                Name_2,
                Name_3,
                Beschreibung_1 AS Beschreibung
        FROM
                " . PREFIX . "_shop_varianten_kategorien
        WHERE
                KatId = '" . intval($categ) . "'
        ORDER BY Position ASC");
        while ($row = $sql->fetch_assoc()) {
            $row['vars'] = $this->_db->fetch_assoc_all("SELECT
                *
            FROM
                    " . PREFIX . "_shop_varianten
            WHERE
                    ArtId = '" . intval($id) . "'
            AND
                    KatId = '" . $row['Id'] . "'
            ORDER BY Position ASC");
            $variants[] = $row;
        }
        $sql->close();
        return $variants;
    }

    public function delete($id) {
        if (perm('shop_delete')) {
            $id = intval($id);
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_preisalarm WHERE ProdId = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_downloads WHERE ArtId = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_bewertung WHERE Produkt = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_produkte WHERE Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил в магазине товар', '1', $this->UserId);
        }
        $this->__object('AdminCore')->backurl();
    }

    public function copy($id) {
        if (!perm('shop_copyarticle')) {
            $this->__object('AdminCore')->noAccess();
        }
        $error = '';
        $id = intval($id);
        if (Arr::getPost('copy') == 1) {
            if (empty($_POST['Artikelnummer'])) {
                $error[] = $this->_lang['Shop_articles_copyEN'];
            } else {
                $_POST['Artikelnummer'] = Tool::cleanAllow($_POST['Artikelnummer']);
                $row = $this->_db->fetch_object("SELECT Id FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $this->_db->escape($_POST['Artikelnummer']) . "' LIMIT 1");
                if (is_object($row)) {
                    $error[] = $this->_lang['Shop_articles_copyEE'];
                } else {
                    $erg = $this->_db->fetch_assoc("SELECT * FROM " . PREFIX . "_shop_produkte WHERE Id='" . $id . "' LIMIT 1");

                    $insert_array = array();
                    foreach ($erg as $i => $fid) {
                        switch ($i) {
                            case 'Artikelnummer':
                                $fid = Arr::getPost('Artikelnummer');
                                break;
                            case 'Titel_1':
                            case 'Titel_2':
                            case 'Titel_3':
                                $fid = Arr::getPost('Titel_1');
                                break;
                            case 'Erstellt':
                                $fid = time();
                                break;
                            case 'Bild':
                                $fid = $this->renameImage($fid);
                                break;
                            case 'Bilder':
                                $fid = $this->renameImages($fid);
                                break;
                            case 'Klicks':
                            case 'Verkauft':
                                $fid = 0;
                                break;
                        }
                        if ($i != 'Id') {
                            $insert_array[$i] = $fid;
                        }
                    }
                    $this->_db->insert_query('shop_produkte', $insert_array);
                    $Iid = $this->_db->insert_id();
                    SX::syslog($_SESSION['user_name'] . ' скопировал в магазине товар: ' . $erg['Titel_1'], '1', $this->UserId);

                    if (Arr::getPost('edit_new') == 1) {
                        SX::output("<script type=\"text/javascript\">location.href='?do=shop&sub=edit_article&id=" . $Iid . "&noframes=1&langcode=1&closeafter=1&iscopied=1'</script>");
                    } else {
                        $this->__object('AdminCore')->script('close');
                    }
                }
            }
            $this->_view->assign('error', $error);
        }
        $row = $this->_db->fetch_object("SELECT Titel_1, Id FROM " . PREFIX . "_shop_produkte WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('row', $row);
        $this->_view->assign('title', $this->_lang['Shop_articles_copy']);
        $this->_view->content('/shop/article_copy.tpl');
    }

    /* Метод обработки строки изображений с разделителем | */
    protected function renameImages($text) {
        if (!empty($text)) {
            $new_arr = array();
            $array = explode('|', $text);
            foreach ($array as $image) {
                $new_arr[] = $this->renameImage($image);
            }
            return implode('|', $new_arr);
        }
        return $text;
    }

    /* Метод создает копию изображения с новым именем */
    protected function renameImage($file) {
        if (!empty($file) && is_file(UPLOADS_DIR . '/shop/icons/' . $file)) {
            $ext = Tool::extension($file, true);
            $new = Tool::uniqid($file);
            copy(UPLOADS_DIR . '/shop/icons/' . $file, UPLOADS_DIR . '/shop/icons/' . $new . $ext);
            return $new . $ext;
        }
        return $file;
    }

    protected function replace($text) {
        return str_replace(',', '.', $text);
    }

    public function edit($id) {
        if (!perm('shop_articleedit')) {
            $this->__object('AdminCore')->noAccess();
        } else {
            $id = intval($id);
            $Lc = $this->__object('AdminCore')->getLangcode();

            if (Arr::getRequest('save') == 1) {
                $ProdBild = $DB_Spez = $SetAll = '';
                if (isset($_POST['Datei']) && Arr::getPost('pdls_update') == 1) {
                    foreach ($_POST['Datei'] as $did => $key) {
                        if (!empty($_POST['DlName'][$did])) {
                            $array = array(
                                'Datei'        => $_POST['Datei'][$did],
                                'DlName'       => sanitize($_POST['DlName'][$did]),
                                'Beschreibung' => sanitize($_POST['DlBeschreibung'][$did]),
                            );
                            $this->_db->update_query('shop_produkte_downloads', $array, "Id='" . intval($did) . "'");
                        }

                        if (isset($_POST['Dldel'][$did]) && $_POST['Dldel'][$did] == 1) {
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_produkte_downloads WHERE Id='" . intval($did) . "'");
                        }
                    }
                }

                if (!empty($_POST['newFile_2']) || !empty($_POST['DateiDlNeu'])) {
                    $FileDl = !empty($_POST['newFile_2']) ? $_POST['newFile_2'] : $_POST['DateiDlNeu'];
                    $DlName = !empty($_POST['DateiName']) ? strip_tags($_POST['DateiName']) : $FileDl;

                    $insert_array = array(
                        'ProduktId'    => $id,
                        'Datei'        => $FileDl,
                        'Datum'        => time(),
                        'DlName'       => $DlName,
                        'Beschreibung' => strip_tags(Arr::getPost('Dateibeschreibung')));
                    $this->_db->insert_query('shop_produkte_downloads', $insert_array);
                }

                $text = $_REQUEST['Beschreibung'];
                $text2 = $_REQUEST['Beschreibung2'];
                $Preis_Liste = $this->replace(Arr::getPost('Preis_Liste'));
                $Preis_EK = $this->replace(Arr::getPost('Preis_EK'));
                $Preis = $this->replace(Arr::getPost('Preis'));
                $Preis = ($Preis >= $Preis_Liste) ? 0 : $Preis;
                $Preis_Liste = ($Preis_Liste < '0.01') ? '0.00' : $Preis_Liste;
                $plg = !empty($_POST['Preis_Liste_Gueltig']) ? explode('.', $_POST['Preis_Liste_Gueltig']) : explode('.', '01.01.2030');
                $Preis_Liste_Gueltig = ($Preis < '0.01' || $Preis >= $Preis_Liste) ? '0' : mktime(23, 59, 59, $plg[1], $plg[0], $plg[2]);
                $Kategorie_Multi = isset($_POST['Kategorie_Multi']) ? implode(',', $_POST['Kategorie_Multi']) : '';
                $Artikelnummer = (!empty($_POST['Artikelnummer']) && $this->exist($_POST['Artikelnummer'])) ? "Artikelnummer = '" . $this->cleanArtikel($_POST['Artikelnummer']) . "'," : '';
                $Titel = !empty($_POST['Titel']) ? "Titel_{$Lc} = '" . $this->_db->escape(sanitize($_POST['Titel'])) . "'," : '';

                if (perm('shop_images')) {
                    if (!empty($_POST['newImg_1'])) {
                        $ProdBild = "Bild = '" . $this->_db->escape(Arr::getPost('newImg_1')) . "', ";
                    }
                    if (Arr::getPost('Del_Bild_Norm') == '1') {
                        if (empty($_POST['newImg_1'])) {
                            $ProdBild = "Bild = '', ";
                        }
                        $this->delImages($_POST['Bild_Alt']);
                    }
                }

                $BilderMulti = $this->multiUpload($id);
                if (!empty($_POST['Loeschen']) && !empty($BilderMulti)) {
                    $MultiArray = explode('|', $BilderMulti);
                    if (perm('shop_images')) {
                        foreach ($_POST['Loeschen'] as $bid) {
                            if (in_array($bid, $MultiArray)) {
                                $this->delImages($bid);
                            }
                        }
                    }
                    $MultiArray = array_diff($MultiArray, $_POST['Loeschen']);
                    $BilderMulti = implode('|', $MultiArray);
                    $Bilder = !empty($BilderMulti) ? "Bilder = '" . $this->_db->escape($BilderMulti) . "'," : "Bilder = '',";
                } else {
                    $Bilder = !empty($BilderMulti) ? "Bilder = '" . $this->_db->escape($BilderMulti) . "'," : '';
                }

                for ($i = 1; $i <= 15; $i++) {
                    $DB_Spez .= "Spez_{$i} = '" . trim($this->_db->escape(Arr::getPost('Spez_' . $i))) . "',";
                }

                $Free = "
                Frei_1 = '" . $this->_db->escape(Arr::getPost('Frei_1')) . "',
                Frei_2 = '" . $this->_db->escape(Arr::getPost('Frei_2')) . "',
                Frei_3 = '" . $this->_db->escape(Arr::getPost('Frei_3')) . "',
                Frei_1_Pflicht = '" . $this->_db->escape(Arr::getPost('Frei_1_Pflicht')) . "',
                Frei_2_Pflicht = '" . $this->_db->escape(Arr::getPost('Frei_2_Pflicht')) . "',
                Frei_3_Pflicht = '" . $this->_db->escape(Arr::getPost('Frei_3_Pflicht')) . "',";

                $Zub_a = $this->zub('prods', 'a');
                $Zub_b = $this->zub('ersatzteile', 'b');
                $Zub_c = $this->zub('tuningteile', 'c');

                if ($_REQUEST['langcode'] > 1) {
                    $DB_Spez = '';
                    $Lc = intval($_REQUEST['langcode']);
                    for ($i = 1; $i <= 15; $i++) {
                        $DB_Spez .= "Spez_{$i}_{$Lc} = '" . trim($this->_db->escape(Arr::getPost('Spez_' . $i))) . "',";
                    }
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET
                          $DB_Spez
                          $Titel
                          Beschreibung_{$Lc} = '" . $this->_db->escape($text) . "',
                          Beschreibung_lang_{$Lc} = '" . $this->_db->escape($text2) . "'
                    WHERE Id = '" . $id . "'");
                } else {
                    if (Arr::getPost('saveAllLang') == 1) {
                        $DB_Spez2 = $DB_Spez3 = '';
                        for ($i = 1; $i <= 15; $i++) {
                            $DB_Spez2 .= ",Spez_{$i}_2 = '" . trim($this->_db->escape(Arr::getPost('Spez_' . $i))) . "'";
                            $DB_Spez3 .= ",Spez_{$i}_3 = '" . trim($this->_db->escape(Arr::getPost('Spez_' . $i))) . "'";
                        }

                        $SetAll = "
                        {$DB_Spez2}
                        {$DB_Spez3}
                        ,Titel_2 = '" . $this->_db->escape(sanitize($_POST['Titel'])) . "'
                        ,Beschreibung_2 = '" . $this->_db->escape($text) . "'
                        ,Beschreibung_lang_2 = '" . $this->_db->escape($text2) . "'
                        ,Titel_3 = '" . $this->_db->escape(sanitize($_POST['Titel'])) . "'
                        ,Beschreibung_3 = '" . $this->_db->escape($text) . "'
                        ,Beschreibung_lang_3 = '" . $this->_db->escape($text2) . "'";
                    }

                    $Bestellt = (Arr::getPost('Bestellt') == 1) ? 1 : 0;
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET
                            $Free
                            $Zub_a
                            $Zub_b
                            $Zub_c
                            $DB_Spez
                            $Artikelnummer
                            $Titel
                            $ProdBild
                            $Bilder
                            SeitenTitel = '" . $this->_db->escape(Arr::getPost('SeitenTitel')) . "',
                            Template = '" . $this->_db->escape(Arr::getPost('Template')) . "',
                            EinheitBezug = '" . $this->_db->escape($this->replace($_POST['EinheitBezug'])) . "',
                            EAN_Nr = '" . $this->_db->escape(Arr::getPost('EAN_Nr')) . "',
                            ISBN_Nr = '" . $this->_db->escape(Arr::getPost('ISBN_Nr')) . "',
                            PrCountry = '" . $this->_db->escape(Arr::getPost('PrCountry')) . "',
                            Yml = '" . $this->_db->escape(Arr::getPost('Yml')) . "',
                            Gruppen = '" . (($_POST['AlleGruppen'] == 1) ? '' : $this->_db->escape(implode(',', $_POST['Gruppen']))) . "',
                            Lieferzeit = '" . $this->_db->escape(Arr::getPost('Lieferzeit')) . "',
                            Bestellt = '" . $this->_db->escape($Bestellt) . "',
                            Beschreibung_{$Lc} = '" . $this->_db->escape($text) . "',
                            Beschreibung_lang_{$Lc} = '" . $this->_db->escape($text2) . "',
                            Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "',
                            Preis_Liste_Gueltig = '" . $this->_db->escape($Preis_Liste_Gueltig) . "',
                            Preis_Liste = '" . $this->_db->escape($Preis_Liste) . "',
                            Preis = '" . $this->_db->escape($Preis) . "',
                            Preis_EK = '" . $this->_db->escape($Preis_EK) . "',
                            Hersteller = '" . $this->_db->escape(Arr::getRequest('Hersteller')) . "',
                            Kategorie = '" . $this->_db->escape(Arr::getRequest('Kategorie')) . "',
                            Kategorie_Multi = '" . $this->_db->escape($Kategorie_Multi) . "',
                            Gewicht = '" . Tool::cleanDigit($_POST['Gewicht']) . "',
                            Gewicht_Ohne = '" . Tool::cleanDigit($_POST['Gewicht_Ohne']) . "',
                            Abmessungen = '" . $this->_db->escape(Arr::getPost('Abmessungen')) . "',
                            Schlagwoerter = '" . $this->_db->escape(trim(Arr::getPost('Schlagwoerter'))) . "',
                            EinheitCount = '" . $this->_db->escape($this->replace($_POST['EinheitCount'])) . "',
                            EinheitId = '" . $this->_db->escape(Arr::getPost('EinheitId')) . "',
                            Lagerbestand = '" . Tool::cleanDigit($_POST['Lagerbestand']) . "',
                            Verfuegbar = '" . $this->_db->escape(Arr::getPost('Verfuegbar')) . "',
                            EinzelBestellung = '" . $this->_db->escape(Arr::getPost('EinzelBestellung')) . "',
                            MinBestellung = '" . $this->_db->escape(trim(Arr::getPost('MinBestellung'))) . "',
                            MaxBestellung = '" . $this->_db->escape(trim(Arr::getPost('MaxBestellung'))) . "',
                            Startseite = '" . $this->_db->escape(Arr::getPost('Startseite')) . "',
                            Fsk18 = '" . $this->_db->escape(Arr::getPost('Fsk18')) . "',
                            MetaTags = '" . $this->_db->escape(Arr::getPost('MetaTags')) . "',
                            MetaDescription = '" . $this->_db->escape(Arr::getPost('MetaDescription')) . "'
                            {$SetAll}
                    WHERE Id = '" . $id . "'");

                    $Preis_Alt = $this->replace($_POST['Preis_Alt']);
                    $Preis_Neu = $Preis_Liste;
                    $Angebot_Alt = $this->replace($_POST['Angebot_Alt']);
                    $Angebot_Neu = $Preis;

                    if (($Preis_Neu < $Preis_Alt) || ($Angebot_Neu < $Angebot_Alt) || ($Angebot_Alt < '0.01' && $Angebot_Neu > '0.01')) {
                        $p_lim = '';
                        if ($Preis_Neu < $Preis_Alt) {
                            $p_lim = $Preis_Neu;
                        }
                        if ($Angebot_Neu < $Angebot_Alt && $Angebot_Neu > '0.01') {
                            $p_lim = $Angebot_Neu;
                        }
                        if ($Angebot_Alt < '0.01' && $Angebot_Neu > '0.01') {
                            $p_lim = $Angebot_Neu;
                        }

                        if ($p_lim > '0.0.1') {
                            $subject_text = $this->_lang['Shop_Settings_AlertSubject'];
                            $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_preisalarm WHERE ProdId='" . $id . "' AND Preis >= '" . $this->_db->escape($p_lim) . "'");
                            while ($row = $sql->fetch_object()) {
                                $mail_array = array(
                                    '__PRODUCT__'  => sanitize($_POST['Titel']),
                                    '__OLD__'      => number_format($Preis_Alt, 2, '.', ''),
                                    '__NEW__'      => number_format($p_lim, 2, '.', ''),
                                    '__LINK__'     => BASE_URL . 'index.php?p=shop&action=showproduct&id=' . $id . '&cid=' . $_REQUEST['Kategorie'],
                                    '__CURRENCY__' => SX::get('shop.Waehrung_1'));
                                $text = $this->_text->replace($this->_lang['Shop_Settings_AlertText'], $mail_array);
                                SX::setMail(array(
                                    'globs'     => '1',
                                    'to'        => $row->Email,
                                    'to_name'   => '',
                                    'text'      => $text,
                                    'subject'   => $subject_text,
                                    'fromemail' => SX::get('shop.Email_Abs'),
                                    'from'      => SX::get('shop.Name_Abs'),
                                    'type'      => 'text',
                                    'attach'    => '',
                                    'html'      => '',
                                    'prio'      => 3));
                                $this->_db->query("DELETE FROM " . PREFIX . "_shop_preisalarm WHERE ProdId='" . $id . "' AND Email='" . $row->Email . "'");
                            }
                            $sql->close();
                        }
                    }
                }

                SX::syslog($_SESSION['user_name'] . ' сохранил в магазине товар: ' . sanitize($_POST['Titel']), '1', $this->UserId);
                $this->__object('AdminCore')->script('save');
            }

            $db_f = '';
            if ($_REQUEST['langcode'] > 1) {
                $Lc = intval($_REQUEST['langcode']);
                for ($i = 1; $i <= 15; $i++) {
                    $db_f .= "Spez_{$i}_{$Lc} AS Spez_{$i},";
                }
            }
            $row = $this->_db->cache_fetch_assoc("SELECT
                    *,
                    $db_f
                    Titel_{$Lc} AS Titel,
                    Beschreibung_{$Lc} AS Beschreibung,
                    Beschreibung_lang_{$Lc} AS BeschreibungLang
            FROM " . PREFIX . "_shop_produkte WHERE Id = '" . $id . "' LIMIT 1");

            $row['Kategorie_Multi'] = explode(',', $row['Kategorie_Multi']);
            $row['Bild_Norm'] = Tool::thumb('shop', $row['Bild'], SX::get('shop.thumb_width_norm'));
            $row['Bild_Alt'] = $row['Bild'];

            if (!empty($row['Bilder'])) {
                $Bilder_Klein = array();
                $bilder = explode('|', $row['Bilder']);
                foreach ($bilder as $bild) {
                    $arr['BildId'] = $bild;
                    $arr['Bild'] = Tool::thumb('shop', $bild, SX::get('shop.thumb_width_norm'));
                    if (!empty($bild)) {
                        $Bilder_Klein[] = $arr;
                    }
                }
                $this->_view->assign('Bilder', $Bilder_Klein);
            } else {
                $row['Bilder'] = '';
            }

            $spez = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_shop_kategorie_spezifikation WHERE Kategorie = '" . $row['Kategorie'] . "' LIMIT 1");
            if (is_array($spez)) {
                for ($i = 1; $i <= 15; $i++) {
                    $this->_view->assign('Spez_Def_' . $i, $spez['Spez_' . $i]);
                    $this->_view->assign('Spez_' . $i, $row['Spez_' . $i]);
                }
            } else {
                $this->_view->assign('nospez', 1);
            }

            $Tabs = $this->_db->cache_fetch_object("SELECT
                    Teile_1_Name_{$Lc} AS TAB1,
                    Teile_2_Name_{$Lc} AS TAB2,
                    Teile_3_Name_{$Lc} AS TAB3
            FROM " . PREFIX . "_shop_kategorie_zubehoer WHERE Kategorie = '" . $row['Kategorie'] . "' LIMIT 1");

            $scategs = $categs_shop = array();
            $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
            $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
            $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
            $this->_view->assign('can_upload', ((is_writable(UPLOADS_DIR . '/shop/product_downloads/')) ? 1 : 0));
            $this->_view->assign('prodDls', $this->downloads($id));
            $this->_view->assign('prodDlsAll', $this->downloadFiles());
            $this->_view->assign('alternativeTpl', $this->listTpl());
            $this->_view->assign('groupsempty', (empty($row['Gruppen'])) ? 1 : '');
            $this->_view->assign('groups', explode(',', $row['Gruppen']));
            $this->_view->assign('tabs', $Tabs);
            $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
            $this->_view->assign('text', $this->__object('Editor')->load('admin', $row['Beschreibung'], 'Beschreibung', 150, 'Basic'));
            $this->_view->assign('text2', $this->__object('Editor')->load('admin', $row['BeschreibungLang'], 'Beschreibung2', 400, 'Shop'));
            $this->_view->assign('shipping_time', $this->timeShipping());
            $this->_view->assign('row', $row);
            $this->_view->assign('ass', explode(',', $row['Zub_a']));
            $this->_view->assign('units', $this->units());
            $this->_view->assign('categs_ass', $this->getCategs(0, $categs_shop));
            $this->_view->assign('manufaturer', $this->listManufaturer());
            $this->_view->assign('available', $this->getAvailabilities());
            $this->_view->assign('zub', $this->productZub($row['Zub_a']));
            $this->_view->assign('ers', $this->productZub($row['Zub_b']));
            $this->_view->assign('tun', $this->productZub($row['Zub_c']));
            $this->_view->assign('shop_search_small_categs', $this->simpleCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1));
            $this->_view->content('/shop/article_edit.tpl');
        }
    }

    protected function downloads($artid) {
        $dls = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_produkte_downloads WHERE ProduktId = '" . intval($artid) . "' ORDER BY DlName ASC");
        return $dls;
    }

    protected function zub($val, $var) {
        if (!empty($_POST[$val])) {
            $p = $_POST[$val];
            $x = array_unique($p);
            $x = implode(',', $x);
            return "Zub_" . $var . " = '" . $this->_db->escape($x) . "', ";
        }
        return "Zub_" . $var . " = '', ";
    }

    protected function downloadFiles() {
        $verzname = UPLOADS_DIR . '/shop/product_downloads/';
        $handle = opendir($verzname);
        $esds = array();
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && is_file($verzname . $datei)) {
                $esds[] = $datei;
            }
        }
        closedir($handle);
        return $esds;
    }

    public function add() {
        if (!perm('shop_articlenew')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1) {
            $an = $this->_text->lower(trim(Arr::getPost('Artikelnummer')));
            $check = $this->_db->cache_fetch_object("SELECT Artikelnummer FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer = '" . $this->_db->escape($an) . "' LIMIT 1");
            $an = (is_object($check) && $an == $this->_text->lower($check->Artikelnummer)) ? Tool::random(8, 'digit') : $this->cleanArtikel(trim(Arr::getPost('Artikelnummer')));
            $plg = explode('.', $_POST['Preis_Liste_Gueltig']);
            $text = $_REQUEST['Beschreibung'];
            $text2 = $_REQUEST['Beschreibung2'];
            $Preis_EK = $this->replace($_POST['Preis_EK']);
            $Preis_Liste = $this->replace($_POST['Preis_Liste']);
            $Preis = $this->replace($_POST['Preis']);
            $Preis = ($Preis >= $Preis_Liste) ? 0 : $Preis;
            $Preis_Liste = ($Preis_Liste < '0.01') ? '0.99' : $Preis_Liste;
            $Preis_Liste_Gueltig = ($Preis < '0.01' || $Preis >= $Preis_Liste) ? '0' : mktime(23, 59, 59, $plg[1], $plg[0], $plg[2]);
            $Bild = !empty($_POST['newImg_1']) ? $_POST['newImg_1'] : '';
            $Cat_multi = isset($_POST['Kategorie_Multi']) ? implode(',', $_POST['Kategorie_Multi']) : '';
            $titel = sanitize(trim(Arr::getPost('Titel')));

            $insert_array = array(
                'Template'            => Arr::getPost('Template'),
                'SeitenTitel'         => Arr::getPost('SeitenTitel'),
                'Kategorie'           => Arr::getPost('Kategorie'),
                'Kategorie_Multi'     => $Cat_multi,
                'Schlagwoerter'       => Arr::getPost('Schlagwoerter'),
                'Artikelnummer'       => $an,
                'Preis'               => $Preis,
                'Preis_Liste'         => $Preis_Liste,
                'Preis_Liste_Gueltig' => $Preis_Liste_Gueltig,
                'Preis_EK'            => $Preis_EK,
                'Titel_1'             => $titel,
                'Titel_2'             => $titel,
                'Titel_3'             => $titel,
                'Beschreibung_1'      => $text,
                'Beschreibung_2'      => $text,
                'Beschreibung_3'      => $text,
                'Beschreibung_lang_1' => $text2,
                'Beschreibung_lang_2' => $text2,
                'Beschreibung_lang_3' => $text2,
                'Aktiv'               => Arr::getPost('Aktiv'),
                'Erstellt'            => time(),
                'Bild'                => $Bild,
                'Gewicht'             => Arr::getPost('Gewicht'),
                'Hersteller'          => Arr::getPost('Hersteller'),
                'EinheitCount'        => $this->replace(Arr::getPost('EinheitCount')),
                'EinheitId'           => Arr::getPost('EinheitId'),
                'Startseite'          => Arr::getPost('Startseite'),
                'Lagerbestand'        => Arr::getPost('Lagerbestand'),
                'Bestellt'            => Arr::getPost('Bestellt'),
                'Verfuegbar'          => Arr::getPost('Verfuegbar'),
                'EinzelBestellung'    => Arr::getPost('EinzelBestellung'),
                'MaxBestellung'       => Arr::getPost('MaxBestellung'),
                'MinBestellung'       => Arr::getPost('MinBestellung'),
                'Lieferzeit'          => Arr::getPost('Lieferzeit'),
                'Gewicht_Ohne'        => Arr::getPost('Gewicht_Ohne'),
                'Abmessungen'         => Arr::getPost('Abmessungen'),
                'Fsk18'               => Arr::getPost('Fsk18'),
                'Frei_1'              => Arr::getPost('Frei_1'),
                'Frei_2'              => Arr::getPost('Frei_2'),
                'Frei_3'              => Arr::getPost('Frei_3'),
                'Frei_1_Pflicht'      => Arr::getPost('Frei_1_Pflicht'),
                'Frei_2_Pflicht'      => Arr::getPost('Frei_2_Pflicht'),
                'Frei_3_Pflicht'      => Arr::getPost('Frei_3_Pflicht'),
                'Gruppen'             => (Arr::getPost('AlleGruppen') == 1 ? '' : implode(',', Arr::getPost('Gruppen'))),
                'EAN_Nr'              => Arr::getPost('EAN_Nr'),
                'ISBN_Nr'             => Arr::getPost('ISBN_Nr'),
                'EinheitBezug'        => $this->replace(Arr::getPost('EinheitBezug')),
                'Sektion'             => $_SESSION['a_area'],
                'PrCountry'           => Arr::getPost('PrCountry'),
                'Yml'                 => Arr::getPost('Yml'),
                'MetaTags'            => Arr::getPost('MetaTags'),
                'MetaDescription'     => Arr::getPost('MetaDescription'));
            $this->_db->insert_query('shop_produkte', $insert_array);
            $iid = $this->_db->insert_id();

            if (!empty($_POST['newFile_2']) || !empty($_POST['DateiDlNeu'])) {
                $FileDl = !empty($_POST['newFile_2']) ? $_POST['newFile_2'] : $_POST['DateiDlNeu'];
                $DlName = !empty($_POST['DateiName']) ? strip_tags($_POST['DateiName']) : $FileDl;
                $insert_array = array(
                    'ProduktId'    => $iid,
                    'Datei'        => $FileDl,
                    'Datum'        => time(),
                    'DlName'       => $DlName,
                    'Beschreibung' => strip_tags(Arr::getPost('Dateibeschreibung')));
                $this->_db->insert_query('shop_produkte_downloads', $insert_array);
            }

            // Добавляем задание на пинг
            $options = array(
                'name' => $_POST['Titel'],
                'url'  => BASE_URL . '/index.php?p=shop&action=showproduct&id=' . $iid . '&cid=' . $_POST['Kategorie'] . '&pname=' . translit($_POST['Titel']),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $_POST['Titel'],
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog($_SESSION['user_name'] . ' добавил в магазин новый товар: ' . sanitize($_POST['Titel']), '1', $this->UserId);
            $BilderMulti = $this->multiUpload($iid);
            $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Bilder = '" . $BilderMulti . "' WHERE Id = '" . $iid . "'");
            SX::output('<script type="text/javascript">
                Check = confirm("' . $this->_lang['Shop_confirmed_editafter'] . '");
                if (Check == false) {
                        parent.frames.location.href=\'index.php?do=shop&sub=articles\';
                } else {
                        location.href=\'index.php?do=shop&sub=edit_article&id=' . $iid . '&noframes=1&langcode=1\';
                };
                </script>', true);
        }

        $_REQUEST['langcode'] = $this->__object('AdminCore')->getLangcode();
        $scategs = $categs_shop = array();

        $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
        $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
        $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
        $this->_view->assign('prodDlsAll', $this->downloadFiles());
        $this->_view->assign('can_upload', (is_writable(UPLOADS_DIR . '/shop/product_downloads/') ? 1 : 0));
        $this->_view->assign('alternativeTpl', $this->listTpl());
        $this->_view->assign('groupsempty', 1);
        $this->_view->assign('groups', '');
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('artnumber', Tool::random(8, 'digit'));
        $this->_view->assign('text', $this->__object('Editor')->load('admin', '', 'Beschreibung', 200, 'Basic'));
        $this->_view->assign('text2', $this->__object('Editor')->load('admin', '', 'Beschreibung2', 400, 'Shop'));
        $this->_view->assign('units', $this->units());
        $this->_view->assign('shipping_time', $this->timeShipping());
        $this->_view->assign('categs_ass', $this->getCategs(0, $categs_shop));
        $this->_view->assign('manufaturer', $this->listManufaturer());
        $this->_view->assign('available', $this->getAvailabilities());
        $this->_view->assign('shop_search_small_categs', $this->simpleCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1));
        $this->_view->assign('title', $this->_lang['Shop_articles_addnew']);
        $this->_view->content('/shop/article_new.tpl');
    }

    /* Удаление изображения */
    protected function delImages($val) {
        File::delete(UPLOADS_DIR . '/shop/icons/' . $val);
    }

    protected function listTpl() {
        $sec = (isset($_REQUEST['section']) && is_numeric($_REQUEST['section'])) ? intval($_REQUEST['section']) : 1;
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '" . $sec . "' LIMIT 1");
        $d = SX_DIR . '/theme/' . $res->Template . '/shop_product_custom/';
        $handle = opendir($d);
        $alternatives = array();
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_file($d . $file)) {
                $f = new stdClass;
                $f->Name = $file;
                $alternatives[] = $f;
            }
        }
        closedir($handle);
        return $alternatives;
    }

    protected function votes($id, $now = false) {
        $now = $now === true ? " AND Offen = '0'" : '';
        $row = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS count FROM " . PREFIX . "_shop_bewertung WHERE Produkt='" . intval($id) . "'" . $now . " LIMIT 1");
        return $row->count;
    }

    public function prodVotes($prod) {
        if (!perm('shop_votes')) {
            $this->__object('AdminCore')->noAccess();
        }
        $prod = $this->_db->escape($prod);
        if (Arr::getPost('save') == '1') {
            if (isset($_POST['Bewertung'])) {
                foreach (array_keys($_POST['Bewertung']) as $id) {
                    $id = intval($id);
                    $array = array(
                        'Bewertung'        => Tool::cleanTags($_POST['Bewertung'][$id], array('codewidget')),
                        'Bewertung_Punkte' => $_POST['Bewertung_Punkte'][$id],
                        'Offen'            => $_POST['Offen'][$id],
                    );
                    $this->_db->update_query('shop_bewertung', $array, "Id='" . $id . "'");
                    if (isset($_POST['Del'][$id]) && $_POST['Del'][$id] >= 1) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_bewertung WHERE Id = '" . $id . "'");
                    }
                }
                $this->__object('AdminCore')->script('save');
            }
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_shop_bewertung WHERE Produkt='" . $prod . "' ORDER BY Id DESC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $votes = array();
        while ($row = $sql->fetch_object()) {
            $row->BenutzerName = Tool::userName($row->Benutzer);
            $votes[] = $row;
        }
        $sql->close();
        $this->_view->assign('limit', $limit);
        $this->_view->assign('pages', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=shop&amp;sub=prodvotes&amp;id={$prod}&amp;name=$_REQUEST[name]&amp;noframes=1&amp;page={s}\">{t}</a> "));
        $this->_view->assign('votes', $votes);
        $this->_view->assign('title', $this->_lang['Shop_prodvotes']);
        $this->_view->content('/shop/article_votes.tpl');
    }

    public function load() {
        $search_nav = $search_lager = $search_lager_nav = $search_status = $search_status_nav = $search_kategorie = $search_kategorie_nav = '';
        $search_verkauft_nav = $search_verkauft = $search_hersteller_nav = $search_hersteller = $search_preis_nav = $search_preis = $xoutput = '';
        $filename = $this->_lang['Start_ShoArti'];
        $format = 'csv';
        $articles = array();

        if (Arr::getPost('multiaction') == 1) {
            switch ($_POST['Aktion']) {
                case 'updateamount':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Lagerbestand = Lagerbestand + " . $this->_db->escape(Arr::getRequest('updatenewamount')) . " " . $_SESSION['query_user']);
                    break;
                case 'lagernull':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Lagerbestand = '0' " . $_SESSION['query_user']);
                    break;
                case 'setoffline':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv= '0' " . $_SESSION['query_user']);
                    break;
                case 'setonline':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv= '1' " . $_SESSION['query_user']);
                    break;
                case 'move':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Kategorie= '" . $this->_db->escape(Arr::getRequest('Kategorie')) . "' " . $_SESSION['query_user']);
                    break;
                case '5':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verfuegbar = '5' " . $_SESSION['query_user']);
                    break;
                case '4':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verfuegbar = '4' " . $_SESSION['query_user']);
                    break;
                case '3':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verfuegbar = '3', Lagerbestand = '0', Bestellt='0' " . $_SESSION['query_user']);
                    break;
                case '2':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verfuegbar = '2', Lagerbestand = '0', Bestellt='1' " . $_SESSION['query_user']);
                    break;
                case '1':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verfuegbar = '1', Lagerbestand = '99', Bestellt='0' " . $_SESSION['query_user']);
                    break;
                case 'startonline':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Startseite= '1' " . $_SESSION['query_user']);
                    break;
                case 'startoffline':
                    $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Startseite= '0' " . $_SESSION['query_user']);
                    break;
                case 'settoall':
                    if (perm('shop_articleedit')) {
                        $k = '';
                        $sql = $this->_db->query("SELECT Spez_1,Spez_2,Spez_3,Spez_4,Spez_5,Spez_6,Spez_7,Spez_8,Spez_9,Spez_10,Spez_11,Spez_12,Spez_13,Spez_14,Spez_15,Id,Kategorie,Titel_1,Beschreibung_1,Beschreibung_lang_1 FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                        while ($row = $sql->fetch_object()) {
                            $sp1 = $sp2 = '';
                            for ($i = 1; $i <= 15; $i++) {
                                $sp1 .= "Spez_{$i}_2 = Spez_{$i},";
                            }
                            for ($j = 1; $j <= 15; $j++) {
                                $sp2 .= "Spez_{$j}_3 = Spez_{$j},";
                            }

                            $q = "UPDATE " . PREFIX . "_shop_produkte SET
			        $sp1
				$sp2
				Titel_2 = '" . $this->_db->escape($row->Titel_1) . "',
				Titel_3 = '" . $this->_db->escape($row->Titel_1) . "',
				Beschreibung_2 = '" . $this->_db->escape($row->Beschreibung_1) . "',
				Beschreibung_3 = '" . $this->_db->escape($row->Beschreibung_1) . "',
				Beschreibung_lang_2 = '" . $this->_db->escape($row->Beschreibung_lang_1) . "',
				Beschreibung_lang_3 = '" . $this->_db->escape($row->Beschreibung_lang_1) . "'
			    WHERE Id = '" . $row->Id . "'";
                            $this->_db->query($q);
                            $k[] = $row->Kategorie;
                        }
                        $sql->close();

                        $k = array_unique($k);
                        if (count($k) >= 1) {
                            $sf = $sff = $cat_or = '';
                            foreach ($k as $id => $key) {
                                $cat_or .= ($id == '0') ? " WHERE Kategorie = '$key' " : "OR Kategorie = '$key' ";
                            }

                            for ($i = 1; $i <= 15; $i++) {
                                $sf .= "Spez_$i, ";
                            }

                            $g_temp = "SELECT $sf Id FROM " . PREFIX . "_shop_kategorie_spezifikation $cat_or";
                            $sql_s = $this->_db->query($g_temp);
                            while ($row_s = $sql_s->fetch_assoc()) {
                                for ($i = 1; $i <= 15; $i++) {
                                    $sff .= "Spez_{$i}_2 = '" . $row_s['Spez_' . $i] . "',";
                                }

                                for ($i2 = 1; $i2 <= 15; $i2++) {
                                    $sff .= "Spez_{$i2}_3 = '" . $row_s['Spez_' . $i2] . "',";
                                }

                                $g_f = "UPDATE " . PREFIX . "_shop_kategorie_spezifikation SET $sff Id = '" . $row_s[Id] . "' WHERE Id = '" . $row_s['Id'] . "'";
                                $sff = '';
                                $this->_db->query($g_f);
                            }
                            $sql_s->close();
                        }
                    }
                    break;

                case 'shop_prais':
                    if (!empty($_POST['prais_type'])) {
                        $prais_num = Arr::getPost('prais_num', 0) . '.' . Arr::getPost('prais_num2', 0) . Arr::getPost('prais_num3', 0) . Arr::getPost('prais_num4', 0);
                        $prais_num = (float) $prais_num;

                        if ($prais_num > 0) {
                            $prais_num = Arr::getPost('prais_type') == 'minus' ? 100 - $prais_num : 100 + $prais_num;

                            $q = $this->_db->query("SELECT Id, Preis, Preis_Liste FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                            while ($r = $q->fetch_object()) {
                                $this->checkPrice($r, $prais_num);
                                if (Arr::getPost('prais_variants') == 'ok') {
                                    $this->checkVariants($r->Id, $prais_num);
                                }
                            }
                            $q->close();
                        }
                    }
                    break;

                case 'round_prais':
                    $round_num = intval(Arr::getPost('round_num', 0));
                    $q = $this->_db->query("SELECT Id, Preis, Preis_Liste FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                    while ($r = $q->fetch_object()) {
                        $this->roundPrice($r, $round_num);
                        if ($_POST['round_variants'] == 'ok') {
                            $this->roundVariants($r->Id, $round_num);
                        }
                    }
                    $q->close();
                    break;

                case 'cst':
                    $new_cst = $_POST['cst_val'];
                    $q = $this->_db->query("SELECT Id FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                    while ($r = $q->fetch_object()) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Lieferzeit = '" . $this->_db->escape($new_cst) . "' WHERE Id = '" . $r->Id . "'");
                    }
                    $q->close();
                    break;

                case 'delete':
                    if (perm('shop_delete')) {
                        $q = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                        while ($r = $q->fetch_object()) {
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_produkte WHERE Id = '" . $r->Id . "'");
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $r->Id . "'");
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_preisalarm WHERE ProdId = '" . $r->Id . "'");
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_downloads WHERE ArtId = '" . $r->Id . "'");
                            $this->_db->query("DELETE FROM " . PREFIX . "_shop_bewertung WHERE Produkt = '" . $r->Id . "'");
                        }
                        $q->close();
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user']);
                    }
                    break;
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('action') == 'save') {
            foreach ($_POST['Id'] as $id => $key) {
                $update_time = '';
                if (isset($_POST['Erstellt'][$id])) {
                    $update_time = explode('.', $_POST['Erstellt'][$id]);
                    $update_time = mktime(0, 0, 1, $update_time[1], $update_time[0], $update_time[2]);
                    $update_time = "Erstellt = '" . $update_time . "',";
                }

                $update_title = !empty($_POST['Titel'][$id]) ? "Titel_1 = '" . $this->_db->escape(sanitize($_POST['Titel'][$id])) . "'," : '';
                $update_price = (isset($_POST['Preis_Liste'][$id]) && $_POST['Preis_Liste'][$id] > 0) ? "Preis_Liste = '" . $this->_db->escape($this->replace($_POST['Preis_Liste'][$id])) . "'," : '';
                $update_store = isset($_POST['Lagerbestand'][$id]) ? "Lagerbestand = '" . intval($_POST['Lagerbestand'][$id]) . "'," : '';
                $update_selled = isset($_POST['Verkauft'][$id]) ? "Verkauft = '" . intval($_POST['Verkauft'][$id]) . "'," : '';
                $update_hits = isset($_POST['Klicks'][$id]) ? "Klicks = '" . intval($_POST['Klicks'][$id]) . "'," : '';
                $update_categs = isset($_POST['Kategorie'][$id]) ? "Kategorie = '" . intval($_POST['Kategorie'][$id]) . "'," : '';
                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET " . $update_time . $update_title . $update_price . $update_hits . $update_store . $update_selled . $update_categs . " Aktiv = Aktiv WHERE Id = '" . intval($id) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        $db_order = "ORDER BY Erstellt DESC";
        $db_order_nav = $offers_search = '';

        switch (Arr::getRequest('order')) {
            case 'date_asc':
                $db_order = "ORDER BY Erstellt ASC";
                $db_order_nav = '&amp;order=date_asc';
                $this->_view->assign('date_sort', 'date_desc');
                break;
            default:
            case 'date_desc':
                $db_order = "ORDER BY Erstellt DESC";
                $db_order_nav = '&amp;order=date_desc';
                $this->_view->assign('date_sort', 'date_asc');
                break;
            case 'title_asc':
                $db_order = "ORDER BY Titel_1 ASC";
                $db_order_nav = '&amp;order=title_asc';
                $this->_view->assign('title_sort', 'title_desc');
                break;
            case 'title_desc':
                $db_order = "ORDER BY Titel_1 DESC";
                $db_order_nav = '&amp;order=title_desc';
                $this->_view->assign('title_sort', 'title_asc');
                break;
            case 'artnr_asc':
                $db_order = 'ORDER BY Artikelnummer ASC';
                $db_order_nav = '&amp;order=artnr_asc';
                $this->_view->assign('artnr_sort', 'artnr_desc');
                break;
            case 'artnr_desc':
                $db_order = 'ORDER BY Artikelnummer DESC';
                $db_order_nav = '&amp;order=artnr_desc';
                $this->_view->assign('artnr_sort', 'artnr_asc');
                break;
            case 'price_asc':
                $db_order = 'ORDER BY Preis_Liste ASC';
                $db_order_nav = '&amp;order=price_asc';
                $this->_view->assign('price_sort', 'price_desc');
                break;
            case 'price_desc':
                $db_order = 'ORDER BY Preis_Liste DESC';
                $db_order_nav = '&amp;order=price_desc';
                $this->_view->assign('price_sort', 'price_asc');
                break;
            case 'store_asc':
                $db_order = 'ORDER BY Lagerbestand ASC';
                $db_order_nav = '&amp;order=store_asc';
                $this->_view->assign('store_sort', 'store_desc');
                break;
            case 'store_desc':
                $db_order = 'ORDER BY Lagerbestand DESC';
                $db_order_nav = '&amp;order=store_desc';
                $this->_view->assign('store_sort', 'store_asc');
                break;
            case 'ordered_asc':
                $db_order = 'ORDER BY Verkauft ASC';
                $db_order_nav = '&amp;order=ordered_asc';
                $this->_view->assign('ordered_sort', 'ordered_desc');
                break;
            case 'hits_asc':
                $db_order = 'ORDER BY Klicks ASC';
                $db_order_nav = '&amp;order=hits_asc';
                $this->_view->assign('hits_sort', 'hits_desc');
                break;
            case 'hits_desc':
                $db_order = 'ORDER BY Klicks DESC';
                $db_order_nav = '&amp;order=hits_desc';
                $this->_view->assign('hits_sort', 'hits_asc');
                break;
            case 'ordered_desc':
                $db_order = 'ORDER BY Verkauft DESC';
                $db_order_nav = '&amp;order=ordered_desc';
                $this->_view->assign('ordered_sort', 'ordered_asc');
                break;
            case 'categ_asc':
                $db_order = 'ORDER BY Kategorie ASC';
                $db_order_nav = '&amp;order=categ_asc';
                $this->_view->assign('categ_sort', 'categ_desc');
                break;
            case 'categ_desc':
                $db_order = 'ORDER BY Kategorie DESC';
                $db_order_nav = '&amp;order=categ_desc';
                $this->_view->assign('categ_sort', 'categ_asc');
                break;
        }

        if (!empty($_REQUEST['query'])) {
            $q = $this->_db->escape($_REQUEST['query']);
            $t_search = (Arr::getRequest('b_search') == 1) ? " OR Beschreibung_1 LIKE '%$q%' OR Beschreibung_2 LIKE '%$q%' OR Beschreibung_3 LIKE '%$q%' " : '';
            $search = " WHERE (Id = '$q' OR Artikelnummer = '$q' OR Titel_1 LIKE '%$q%' OR Titel_1 LIKE '%$q%' OR Titel_2 LIKE '%$q%' OR Titel_2 LIKE '%$q%' OR Titel_3 LIKE '%$q%' {$t_search} )";
            $search_nav = "&amp;query=$q" . (Arr::getRequest('b_search') == 1 ? "&amp;b_search=" . $_REQUEST['b_search'] : '');
        } else {
            $search = "WHERE Id != 0";
        }

        if (isset($_REQUEST['aktiv']) && $_REQUEST['aktiv'] != '') {
            $search_status = " AND Aktiv = '" . intval($_REQUEST['aktiv']) . "' ";
            $search_status_nav = '&amp;aktiv=' . $_REQUEST['aktiv'];
        }

        if (isset($_REQUEST['b_offers']) && $_REQUEST['b_offers'] == 1) {
            $offers_search = " AND (Preis != '0.00' AND Preis_Liste > Preis AND Preis_Liste_Gueltig >= " . time() . ") ";
        }

        if (isset($_REQUEST['lagerv']) && $_REQUEST['lagerv'] >= 0 && isset($_REQUEST['lagerb']) && $_REQUEST['lagerb'] >= 0 && $_REQUEST['lagerv'] <= $_REQUEST['lagerb']) {
            $search_lager = " AND (Lagerbestand BETWEEN " . intval($_REQUEST['lagerv']) . " AND " . intval($_REQUEST['lagerb']) . ") ";
            $search_lager_nav = '&amp;lagerv=' . $_REQUEST['lagerv'] . '&amp;lagerb=' . $_REQUEST['lagerb'];
        }

        if (isset($_REQUEST['kategorie']) && $_REQUEST['kategorie'] != '') {
            $search_kategorie = " AND (Kategorie = '" . intval($_REQUEST['kategorie']) . "') ";
            $search_kategorie_nav = '&amp;kategorie=' . $_REQUEST['kategorie'];
        } else {
            $sql_cid = $this->_db->query("SELECT Id FROM " . PREFIX . "_shop_kategorie WHERE Sektion = '" . $_SESSION['a_area'] . "'");
            $in = array();
            while ($row_cid = $sql_cid->fetch_assoc()) {
                $in[] = $row_cid['Id'];
            }
            $sql_cid->close();
            $search_kategorie = !empty($in) ? " AND Kategorie IN(" . implode(',', $in) . ") " : " AND Kategorie = '0'";
        }

        if (isset($_REQUEST['hersteller']) && $_REQUEST['hersteller'] != '') {
            $search_hersteller = " AND (Hersteller = '" . intval(Arr::getRequest('hersteller')) . "') ";
            $search_hersteller_nav = "&amp;hersteller=" . $_REQUEST['hersteller'];
        }

        if (isset($_REQUEST['preis_von']) && $_REQUEST['preis_von'] >= 0 && isset($_REQUEST['preis_bis']) && $_REQUEST['preis_bis'] >= 0 && $_REQUEST['preis_von'] <= $_REQUEST['preis_bis']) {
            $v = $this->cleanPrice($_REQUEST['preis_von']);
            $b = $this->cleanPrice($_REQUEST['preis_bis']);
            $search_preis = " AND (Preis_Liste BETWEEN " . $this->_db->escape($v) . " AND " . $this->_db->escape($b) . ") ";
            $search_preis_nav = "&amp;preis_von=$v&amp;preis_bis=$b";
        }

        if (isset($_REQUEST['verkauft_von']) && $_REQUEST['verkauft_von'] >= 0 && isset($_REQUEST['verkauft_bis']) && $_REQUEST['verkauft_bis'] >= 0 && $_REQUEST['verkauft_von'] <= $_REQUEST['verkauft_bis']) {
            $search_verkauft = " AND (Verkauft BETWEEN " . intval($_REQUEST['verkauft_von']) . " AND " . intval($_REQUEST['verkauft_bis']) . ")";
            $search_verkauft_nav = "&amp;verkauft_von=" . $_REQUEST['verkauft_von'] . "&amp;verkauft_bis=" . $_REQUEST['verkauft_bis'];
        }

        $_SESSION['query_user'] = "{$search} AND Sektion = '" . $_SESSION['a_area'] . "' AND Id != '' {$search_status} {$offers_search} {$search_lager} {$search_kategorie} {$search_hersteller} {$search_preis} {$search_verkauft}";

        $cutter = $this->cutter();
        $db_export_lang_sel = (Arr::getRequest('export') == 1) ? '' : ",Titel_1 AS Titel";

        $limit = Arr::getRequest('limit') >= 1 ? intval($_REQUEST['limit']) : $this->limit;
        $a = Tool::getLimit($limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS * {$db_export_lang_sel} FROM " . PREFIX . "_shop_produkte " . $_SESSION['query_user'] . " {$db_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);

        $fieldcount = $sql->field_count();
        for ($i = 0; $i < $fieldcount; $i++) {
            $xoutput .= $this->enclosed . $sql->field_name($i) . $this->enclosed . $this->separator;
        }
        $xoutput .= $cutter;

        while ($row = $sql->fetch_object()) {
            foreach ($row as $key => $val) {
                if (Arr::getRequest('export') == 1) {
                    $val = str_replace("\r\n", "\n", $val);
                    $val = $this->exportReplace($val);
                    $xoutput .= ( $val == '') ? $this->separator : $this->enclosed . $val . $this->enclosed . $this->separator;
                }
            }
            $xoutput .= $cutter;
            $row->IsOffer = ($row->Preis != '0.00' && ($row->Preis_Liste > $row->Preis) && ($row->Preis_Liste_Gueltig >= time())) ? 1 : 0;
            $row->Votes = $this->votes($row->Id);
            $row->CatName = $this->nameCateg($row->Kategorie);
            $row->Bild_Klein = Tool::thumb('shop', $row->Bild, 20);
            $articles[] = $row;
        }
        $sql->close();

        if (Arr::getRequest('export') == 1 && perm('export_articles')) {
            $xoutput = str_replace(array("\";\r\n", "\";\n"), "\"\r\n", $xoutput);
            if (Arr::getRequest('export_format') == 'text') {
                $format = 'txt';
                $header = 'text/plain';
            } else {
                $format = 'csv';
                $header = 'text/csv';
            }
            File::download($xoutput, $filename . '.' . $format, $header);
        }
        $scategs = array();
        $order_page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $nav_string = "index.php?do=shop&amp;sub=articles&amp;b_offers=" . Arr::getRequest('b_offers') . "&amp;b_search=" . Arr::getRequest('b_search') . "&amp;page={s}{$search_lager_nav}{$search_nav}{$search_status_nav}{$search_kategorie_nav}{$search_preis_nav}{$search_verkauft_nav}{$search_hersteller_nav}&amp;limit={$limit}";

        $this->_view->assign('num', $num);
        $this->_view->assign('available', $this->getAvailabilities());
        $this->_view->assign('shippingTime', $this->timeShipping());
        $this->_view->assign('nav_string', "index.php?do=shop&amp;sub=articles&amp;page={$order_page}{$search_lager_nav}{$search_nav}{$search_status_nav}{$search_kategorie_nav}{$search_preis_nav}{$search_verkauft_nav}{$search_hersteller_nav}&amp;limit={$limit}");
        $this->_view->assign('pages', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"{$nav_string}{$db_order_nav}\">{t}</a> "));
        $this->_view->assign('shop_search_small_categs', $this->simpleCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1));
        $this->_view->assign('limit', $limit);
        $this->_view->assign('shop_manufaturer', $this->manufacturer());
        return $articles;
    }

    /* Метод изменения цены товара */
    protected function checkPrice($r, $prais_num) {
        $Preis = $this->replace(($r->Preis / 100) * $prais_num);
        $Preis_Liste = $this->replace(($r->Preis_Liste / 100) * $prais_num);
        $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Preis = '" . $Preis . "', Preis_Liste = '" . $Preis_Liste . "' WHERE Id = '" . $r->Id . "'");
    }

    /* Метод изменения цены вариантов товара */
    protected function checkVariants($id, $prais_num) {
        $query = $this->_db->query("SELECT Id, Wert FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $id . "'");
        while ($item = $query->fetch_object()) {
            $wert = $this->replace(($item->Wert / 100) * $prais_num);
            $this->_db->query("UPDATE " . PREFIX . "_shop_varianten SET Wert = '" . $wert . "' WHERE Id = '" . $item->Id . "'");
        }
    }

    /* Метод округления цены товара */
    protected function roundPrice($r, $round_num = 0) {
        $Preis = $this->replace(round($r->Preis, $round_num));
        $Preis_Liste = $this->replace(round($r->Preis_Liste, $round_num));
        $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Preis = '" . $Preis . "', Preis_Liste = '" . $Preis_Liste . "' WHERE Id = '" . $r->Id . "'");
    }

    /* Метод округления цены вариантов товара */
    protected function roundVariants($id, $round_num = 0) {
        $query = $this->_db->query("SELECT Id, Wert FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $id . "'");
        while ($item = $query->fetch_object()) {
            $wert = $this->replace(round($item->Wert, $round_num));
            $this->_db->query("UPDATE " . PREFIX . "_shop_varianten SET Wert = '" . $wert . "' WHERE Id = '" . $item->Id . "'");
        }
    }

    /* Метод замены символов при экспорте товаров */
    protected function exportReplace($text) {
        $text = str_replace(array('&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;', ';', '"'), array('Ђ', '»', '«', '©', '®', '™', '„', '“', '', '\''), $text);
        return $text;
    }

    public function deleteCateg($id) {
        if (perm('shop_catdeletenew')) {
            $id = intval($id);
            $query = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_kategorie WHERE Parent_Id='" . $id . "'");
            while ($item = $query->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_kategorie WHERE catid='" . $item->Id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv='0' WHERE Kategorie='" . $item->Id . "'");
                $this->deleteCateg($item->Id);
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_kategorie WHERE catid = '" . $item->Id . "'");
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_kategorie WHERE catid = '" . $item->Parent_Id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv='0' WHERE Kategorie='" . $item->Id . "'");
                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv='0' WHERE Kategorie='" . $item->Parent_Id . "'");
            }
            $query->close();
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_kategorie WHERE Id='" . $id . "'");
            $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv='0' WHERE Kategorie='" . $id . "'");
            $this->__object('AdminCore')->backurl();
        } else {
            $this->__object('AdminCore')->noAccess();
        }
    }

    public function addCateg() {
        if (!perm('shop_catdeletenew')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $beschreibung = Arr::getPost('Beschreibung_1');

            $insert_array = array(
                'Parent_Id'      => Arr::getPost('Parent'),
                'Name_1'         => Arr::getPost('Name_1'),
                'Name_2'         => Arr::getPost('Name_1'),
                'Name_3'         => Arr::getPost('Name_1'),
                'Beschreibung_1' => $beschreibung,
                'Beschreibung_2' => $beschreibung,
                'Beschreibung_3' => $beschreibung,
                'posi'           => Arr::getPost('Position'),
                'UstId'          => Arr::getPost('UstId'),
                'Bild_Navi'      => Arr::getPost('newImg_2'),
                'Bild_Kategorie' => Arr::getPost('newImg_1'),
                'Sektion'        => $_SESSION['a_area'],
                'Gruppen'        => (Arr::getPost('AlleGruppen') == 1 ? '' : implode(',', Arr::getPost('Gruppen'))),
            );
            $this->_db->insert_query('shop_kategorie', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новую категорию в магазине ' . $_POST['Name_1'], '1', $this->UserId);
            $this->__object('AdminCore')->script('close');
        }

        $scategs = array();
        $array = array(
            'title'                    => $this->_lang['Global_NewCateg'],
            'UserGroups'               => $this->__object('AdminCore')->groups(),
            'ust_elements'             => $this->ustELements(),
            'shop_search_small_categs' => $this->simpleCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1),
            'Editor'                   => $this->__object('Editor')->load('admin', '', 'Beschreibung_1', 350, 'Shop')
        );
        $this->_view->assign($array);
        $this->_view->content('/shop/categ_new.tpl');
    }

    public function editCateg() {
        if (!perm('shop_catdeletenew')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval(Arr::getRequest('id'));
        $scategs = array();
        if (Arr::getPost('save') == 1) {
            if (!empty($_POST['Name_1'])) {
                $array = array(
                    'Name_1'         => Arr::getPost('Name_1'),
                    'Beschreibung_1' => Arr::getPost('Beschreibung_1'),
                    'UstId'          => Arr::getPost('UstId'),
                    'Gruppen'        => (Arr::getPost('AlleGruppen') == 1 ? '' : implode(',', Arr::getPost('Gruppen'))),
                );
                if (!empty($_POST['newImg_1'])) {
                    $array['Bild_kategorie'] = Arr::getPost('noImg_1') == 1 ? '' : $_POST['newImg_1'];
                }
                if (!empty($_POST['newImg_2'])) {
                    $array['Bild_Navi'] = Arr::getPost('noImg_2') == 1 ? '' : $_POST['newImg_2'];
                }
                if ($_POST['Parent'] != 0) {
                    $up = explode('_', Arr::getPost('Parent'));
                    $array['Parent_Id'] = $up[1];
                    $array['Sektion'] = $up[0];
                    $this->changeParent($id, $up[0]);
                }
                $this->_db->update_query('shop_kategorie', $array, "Id = '" . $id . "'");
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил в магазине категорию ' . Arr::getPost('Name_1'), '1', $this->UserId);
            }
            if (Arr::getPost('noImg_1') == 1) {
                File::delete(UPLOADS_DIR . '/shop/icons_categs/' . Arr::getPost('categ_del_img'));
            }
            if (Arr::getPost('noImg_2') == 1) {
                File::delete(UPLOADS_DIR . '/shop/navi_categs/' . Arr::getPost('navi_del_img'));
            }
            $this->__object('AdminCore')->script('save');
        }

        $row = $this->_db->cache_fetch_object("SELECT *, Name_1 AS Name, Beschreibung_1 AS Beschreibung FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . $id . "' LIMIT 1");
        $row->Bild_Kategorie_Del = $row->Bild_Kategorie;
        $row->Bild_Navi_Del = $row->Bild_Navi;
        $row->Bild_Kategorie = (!empty($row->Bild_Kategorie) && is_file(UPLOADS_DIR . '/shop/icons_categs/' . $row->Bild_Kategorie)) ? '<img src="../uploads/shop/icons_categs/' . $row->Bild_Kategorie . '" alt="" />' : '';
        $row->Bild_Navi = (!empty($row->Bild_Navi) && is_file(UPLOADS_DIR . '/shop/navi_categs/' . $row->Bild_Navi)) ? '<img src="../uploads/shop/navi_categs/' . $row->Bild_Navi . '" alt="" />' : '';
        $array = array(
            'groups'                   => explode(',', $row->Gruppen),
            'UserGroups'               => $this->__object('AdminCore')->groups(),
            'shop_search_small_categs' => $this->multiCategs(0, '', $scategs, $_SESSION['a_area'], 0, 1),
            'title'                    => $this->_lang['Shop_cat_edit'],
            'ust_elements'             => $this->ustELements(),
            'row'                      => $row,
            'Editor'                   => $this->__object('Editor')->load('admin', $row->Beschreibung, 'Beschreibung_1', 400, 'Shop'),
        );
        $this->_view->assign($array);
        $this->_view->content('/shop/categ_edit.tpl');
    }

    public function categName($langcode, $id) {
        if (!perm('shop_catdeletenew')) {
            $this->__object('AdminCore')->noAccess();
        }
        $LC = intval($langcode);

        if (Arr::getPost('save') == 1) {
            $this->_db->query("UPDATE " . PREFIX . "_shop_kategorie SET
                Name_{$LC}='" . $this->_db->escape(Arr::getPost('Name_' . $LC)) . "',
                Beschreibung_{$LC} = '" . $this->_db->escape($_POST['Beschreibung_' . $LC]) . "'
                WHERE Id = '" . intval(Arr::getPost('id')) . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил в магазине категорию ' . $_POST['Name_' . $LC], '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $row = $this->_db->cache_fetch_object("SELECT Name_{$LC} AS Name, Beschreibung_{$LC} AS Beschreibung FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . intval($id) . "' LIMIT 1");
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->assign('row', $row);
        $this->_view->assign('Editor', $this->__object('Editor')->load('admin', $row->Beschreibung, 'Beschreibung_' . $_REQUEST['langcode'], 200, 'Basic'));
        $this->_view->content('/shop/categ_name_edit.tpl');
    }

    public function showCategs() {
        if (Arr::getPost('save') == 1 && perm('shop_catdeletenew')) {
            if ($_POST['name'] >= 1) {
                foreach (array_keys($_POST['name']) as $id) {
                    if (!empty($_POST['name'][$id])) {
                        $array = array(
                            'Aktiv'  => $_POST['Aktiv'][$id],
                            'Search' => $_POST['Search'][$id],
                            'Name_1' => $_POST['name'][$id],
                            'posi'   => $_POST['posi'][$id],
                        );
                        $this->_db->update_query('shop_kategorie', $array, "Id = '" . intval($id) . "'");
                        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил в магазине категорию ' . $_POST['name'][$id], '1', $this->UserId);
                    }
                }
                $this->__object('AdminCore')->script('save');
            }
        }

        $categs_shop = array();
        $this->_view->assign('title', $this->_lang['Global_ShopCategs']);
        $this->_view->assign('categs', $this->getCategs(0, $categs_shop));
        $this->_view->content('/shop/shopcategs.tpl');
    }

    public function productCategs() {
        $categs_shop = array();
        $this->_view->assign('categs_ass', $this->getCategs(0, $categs_shop, Arr::getRequest('c')));
        if (!empty($_REQUEST['c'])) {
            $this->_view->assign('prods_all', $this->productCateg($_REQUEST['c']));
        }
        $out = $this->_view->fetch(THEME . '/shop/browse_categs_products.tpl');
        SX::output($out, true);
    }

    public function productParts() {
        $categs_shop = array();
        $this->_view->assign('categs_ass', $this->getCategs(0, $categs_shop, Arr::getRequest('c')));
        if (!empty($_REQUEST['c'])) {
            $this->_view->assign('prods_all', $this->productCateg($_REQUEST['c']));
        }
        $out = $this->_view->fetch(THEME . '/shop/browse_parts_products.tpl');
        SX::output($out, true);
    }

    public function productTuning() {
        $categs_shop = array();
        $this->_view->assign('categs_ass', $this->getCategs(0, $categs_shop, Arr::getRequest('c')));
        if (!empty($_REQUEST['c'])) {
            $this->_view->assign('prods_all', $this->productCateg($_REQUEST['c']));
        }
        $out = $this->_view->fetch(THEME . '/shop/browse_tuning_products.tpl');
        SX::output($out, true);
    }

    protected function productCateg($cat = 0) {
        $prods = $this->_db->fetch_object_all("SELECT Id, Titel_1 AS Titel FROM " . PREFIX . "_shop_produkte WHERE Kategorie = '" . intval($cat) . "'");
        return $prods;
    }

    protected function productZub($string) {
        if (!empty($string)) {
            $query = $this->_db->fetch_object_all("SELECT Id, Titel_1 AS Titel FROM " . PREFIX . "_shop_produkte WHERE Id IN($string)");
            return $query;
        }
    }

    protected function getCategs($id, &$categs_shop, $categ = '', $prefix = '', $subcount = 0) {
        $query = $this->_db->query("SELECT *,Id as catid, Name_1 as catname, Name_1 as Name FROM " . PREFIX . "_shop_kategorie WHERE Sektion = '" . $_SESSION['a_area'] . "' AND Parent_Id = '" . intval($id) . "' ORDER BY posi ASC");
        while ($item = $query->fetch_object()) {
            $item->Expander = $prefix;
            $item->Subcount = $this->_text->strlen($prefix);
            $item->Bold = ($item->Parent_Id == 0) ? 1 : 0;
            $item->prods = $this->_db->fetch_object_all("SELECT Id, Titel_1 AS Titel FROM " . PREFIX . "_shop_produkte WHERE Kategorie = '" . intval($categ) . "'");
            $categs_shop[] = $item;
            $this->getCategs($item->catid, $categs_shop, $categ, $prefix . '&nbsp;- ', $subcount);
        }
        $query->close();
        return $categs_shop;
    }

    protected function getOrders($lim = '') {
        $orders = array();
        $filename = 'export';
        $format = 'csv';
        $search = $search_nav = $status = $status_nav = $query_date = $date_nav = $zid = $zid_nav = $vid = $vid_nav = $s_search = $s_search_nav = '';

        if (isset($_REQUEST['StartDate'])) {
            $zs = $this->__object('AdminCore')->mktime($_REQUEST['StartDate'], 0, 0, 1);
            $ze = $this->__object('AdminCore')->mktime($_REQUEST['EndDate'], 23, 59, 59);

            if ($zs < 10) {
                $zs = time();
            }
            if ($ze < 10) {
                $ze = time();
            }
            $query_date = " AND (Datum BETWEEN $zs AND $ze)";
            $date_nav = '&StartDate=' . preg_replace('/[^\d.]/u', '', $_REQUEST['StartDate']);
            $date_nav .= '&EndDate=' . preg_replace('/[^\d.]/u', '', $_REQUEST['EndDate']);
            $this->_view->assign('ZeitStart', $zs);
            $this->_view->assign('ZeitEnde', $ze);
        } else {
            $zs = mktime(0, 0, 1, date('m') - 1, date('d'), date('Y'));
            $ze = mktime(0, 0, 1, date('m'), date('d'), date('Y'));
            $this->_view->assign('ZeitStart', $zs);
            $this->_view->assign('ZeitEnde', $ze);
        }

        if (!empty($_REQUEST['query'])) {
            $q = Tool::cleanAllow($_REQUEST['query'], ' ,.;:()!?');
            $qs = $this->_db->escape($q);
            if (!empty($_REQUEST['only_id'])) {
                $search = " WHERE Benutzer = '" . intval($qs) . "' AND Benutzer != '0'";
            } else {
                $search = " WHERE (Id = '$qs' OR TransaktionsNummer = '$qs' OR Rng_Nachname LIKE '$qs%' OR (Benutzer = '" . intval($qs) . "' AND Benutzer != '0'))";
            }
            $search_nav = "&amp;query=" . $q;
        } else {
            $search = "WHERE Id != 0";
        }
        if (!empty($_REQUEST['status'])) {
            $status = " AND (Status = '" . $this->_db->escape(Arr::getRequest('status')) . "')";
            $status_nav = "&amp;status=" . $_REQUEST['status'];
        }
        if (!empty($_REQUEST['ZahlungsId'])) {
            $zid = " AND (ZahlungsId = '" . intval(Arr::getRequest('ZahlungsId')) . "')";
            $zid_nav = "&amp;ZahlungsId=" . $_REQUEST['ZahlungsId'];
        }
        if (!empty($_REQUEST['VersandId'])) {
            $vid = " AND (VersandId = '" . intval(Arr::getRequest('VersandId')) . "')";
            $vid_nav = "&amp;VersandId=" . $_REQUEST['VersandId'];
        }
        if (!empty($_REQUEST['BetragVon']) && !empty($_REQUEST['BetragBis'])) {
            $s_from = $this->_db->escape($this->numClean($_REQUEST['BetragVon']));
            $s_to = $this->_db->escape($this->numClean($_REQUEST['BetragBis']));
            $_REQUEST['BetragVon'] = $s_from;
            $_REQUEST['BetragBis'] = $s_to;
            $s_search = " AND (Betrag BETWEEN $s_from AND $s_to)";
            $s_search_nav = "&amp;BetragVon={$s_from}&amp;BetragBis={$s_to}";
        }

        $db_order = 'ORDER BY Id DESC';
        $db_order_nav = '';

        if (!empty($_REQUEST['order'])) {
            switch ($_REQUEST['order']) {
                case 'ordernum_asc':
                    $db_order = 'ORDER BY TransaktionsNummer ASC';
                    $db_order_nav = '&amp;order=ordernum_asc';
                    $this->_view->assign('ordernum_sort', 'ordernum_desc');
                    break;
                case 'ordernum_asc':
                    $db_order = 'ORDER BY TransaktionsNummer DESC';
                    $db_order_nav = '&amp;order=ordernum_asc';
                    $this->_view->assign('ordernum_sort', 'ordernum_asc');
                    break;
                case 'summ_asc':
                    $db_order = 'ORDER BY Betrag ASC';
                    $db_order_nav = '&amp;order=summ_asc';
                    $this->_view->assign('summ_sort', 'summ_desc');
                    break;
                case 'summ_desc':
                    $db_order = 'ORDER BY Betrag DESC';
                    $db_order_nav = '&amp;order=summ_desc';
                    $this->_view->assign('summ_sort', 'summ_asc');
                    break;
                case 'date_asc':
                    $db_order = 'ORDER BY Datum ASC';
                    $db_order_nav = '&amp;order=date_asc';
                    $this->_view->assign('date_sort', 'date_desc');
                    break;
                case 'date_desc':
                    $db_order = 'ORDER BY Datum DESC';
                    $db_order_nav = '&amp;order=date_desc';
                    $this->_view->assign('date_sort', 'date_asc');
                    break;
                case 'customer_asc':
                    $db_order = 'ORDER BY Rng_Nachname ASC';
                    $db_order_nav = '&amp;order=customer_asc';
                    $this->_view->assign('customer_sort', 'customer_desc');
                    break;
                case 'customer_desc':
                    $db_order = 'ORDER BY Rng_Nachname DESC';
                    $db_order_nav = '&amp;order=customer_desc';
                    $this->_view->assign('customer_sort', 'customer_asc');
                    break;
                case 'customerid_asc':
                    $db_order = 'ORDER BY Benutzer ASC';
                    $db_order_nav = '&amp;order=customerid_asc';
                    $this->_view->assign('customerid_sort', 'customerid_desc');
                    break;
                case 'customerid_desc':
                    $db_order = 'ORDER BY Benutzer DESC';
                    $db_order_nav = '&amp;order=customerid_desc';
                    $this->_view->assign('customerid_sort', 'customerid_asc');
                    break;
                case 'payment_asc':
                    $db_order = 'ORDER BY ZahlungsId ASC';
                    $db_order_nav = '&amp;order=payment_asc';
                    $this->_view->assign('payment_sort', 'payment_desc');
                    break;
                case 'payment_desc':
                    $db_order = 'ORDER BY ZahlungsId DESC';
                    $db_order_nav = '&amp;order=payment_desc';
                    $this->_view->assign('payment_sort', 'payment_asc');
                    break;
                case 'shipper_asc':
                    $db_order = 'ORDER BY VersandId ASC';
                    $db_order_nav = '&amp;order=shipper_asc';
                    $this->_view->assign('shipper_sort', 'shipper_desc');
                    break;
                case 'shipper_desc':
                    $db_order = 'ORDER BY VersandId DESC';
                    $db_order_nav = '&amp;order=shipper_desc';
                    $this->_view->assign('shipper_sort', 'shipper_asc');
                    break;
                case 'status_asc':
                    $db_order = 'ORDER BY Status ASC';
                    $db_order_nav = '&amp;order=status_asc';
                    $this->_view->assign('status_sort', 'status_desc');
                    break;
                case 'status_desc':
                    $db_order = 'ORDER BY Status DESC';
                    $db_order_nav = '&amp;order=status_desc';
                    $this->_view->assign('status_sort', 'status_asc');
                    break;
            }
        }

        $limit = Arr::getRequest('limit') >= 1 ? intval($_REQUEST['limit']) : $this->limit;
        $limit = !empty($lim) ? intval($lim) : $limit;
        $a = Tool::getLimit($limit);
        $cutter = $this->cutter();
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_shop_bestellungen {$search} AND Id != '' {$status} {$query_date} {$zid} {$vid} {$s_search} {$db_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);

        $fieldcount = $sql->field_count();
        $xoutput = '';
        if (Arr::getPost('export_format') == 'html') {

        } else {
            for ($i = 0; $i < $fieldcount; $i++) {
                $xoutput .= $this->enclosed . $sql->field_name($i) . $this->enclosed . $this->separator;
            }
            $xoutput .= $cutter;
        }

        while ($row = $sql->fetch_object()) {
            if (Arr::getRequest('export') == 1) {
                foreach ($row as $key => $val) {
                    if (Arr::getPost('export_format') == 'html') {
                        if ($key == 'Bestellung') {
                            $val = base64_decode($val);
                            $val = str_replace("\r\n", "\n", $val);
                            $xoutput .= "<p>&nbsp;</p><hr noshade=\"noshade\">";
                            $xoutput .= $val;
                        }
                    } else {
                        switch ($key) {
                            case 'Datum':
                                $val = date('d.m.Y H:m', $val);
                                break;
                            case 'Artikel':
                                $val = base64_encode($val);
                                break;
                            case 'Betrag':
                                $val = number_format($val, '2', ',', '.');
                                break;
                            case 'Bestellung':
                                if (Arr::getPost('order_html') == 1) {
                                    $val = base64_encode($val);
                                }
                                break;
                        }
                        $val = str_replace("\r\n", "\n", $val);
                        $xoutput .= ($val == '') ? $this->separator : $this->enclosed . $val . $this->enclosed . $this->separator;
                    }
                }
            }
            $xoutput .= $cutter;
            $items_o = array();
            $pos = '';
            $q_items = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_bestellungen_items WHERE Bestellnummer = '$row->TransaktionsNummer' ORDER BY Id DESC");
            while ($row_q_items = $q_items->fetch_object()) {
                $pos .= '<h3>' . $row_q_items->ArtikelName . '</h3><br />';
                $pos .= '<small>' . $this->_lang['Shop_articles_number'] . ': ' . $row_q_items->Artikelnummer . '</small><br />';
                if (!empty($row_q_items->Varianten)) {
                    $pos .= '<br /><strong>' . $this->_lang['Shop_variants'] . '</strong><br /><small>' . str_replace("\n", '<br />', $row_q_items->Varianten) . '</small>';
                }
                if (!empty($row_q_items->Konfig_Frei)) {
                    $pos .= '<br /><strong>' . $this->_lang['Shop_order_config'] . '</strong><br /><small>' . str_replace("\n", '<br />', $row_q_items->Konfig_Frei) . '</small>';
                }
                $pos .= '<hr noshade size=1 style=color:#ccc />';
                $row_q_items->positions = $pos;
                $items_o[] = $row_q_items;
            }
            $row->positions = $pos;
            $row->payment = $this->namePayment($row->ZahlungsId);
            $row->shipper = $this->nameShipper($row->VersandId);
            $row->downloads = $this->checkDownload($row->Benutzer);
            $orders[] = $row;
        }
        $sql->close();
        $filename = $this->_lang['Shop_ordersS'];

        if (Arr::getRequest('export') == 1 && perm('export_orders')) {
            if (Arr::getPost('export_format') == 'html') {
                $format = 'html';
                $xoutput = str_replace(array("\";\r\n", "\";\n"), "\"\r\n", $xoutput);
                $header = 'text/html';
            } else {
                $xoutput = str_replace(array("\";\r\n", "\";\n"), "\"\r\n", $xoutput);
                if (Arr::getRequest('export_format') == 'text') {
                    $format = 'txt';
                    $header = 'text/plain';
                } else {
                    $format = 'csv';
                    $header = 'text/csv';
                }
            }
            File::download($xoutput, $filename . '.' . $format, $header);
        }

        $this->_view->assign('payments', $this->listPayments());
        $this->_view->assign('shipper', $this->listShipper());
        $order_page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $noframes_page = (Arr::getRequest('noframes') == 1) ? '&amp;noframes=1' : '';
        $nav_string = "index.php?do=shop&amp;sub=orders&amp;page={s}{$search_nav}{$status_nav}{$date_nav}{$zid_nav}{$vid_nav}{$s_search_nav}{$db_order_nav}&amp;limit={$limit}{$noframes_page}";

        $this->_view->assign('nav_string', "index.php?do=shop&amp;sub=orders{$search_nav}{$status_nav}{$date_nav}{$zid_nav}{$vid_nav}{$s_search_nav}&amp;page={$order_page}&amp;limit={$limit}{$noframes_page}");
        $this->_view->assign('pages', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"{$nav_string}\">{t}</a> "));
        $this->_view->assign('limit', $limit);
        return $orders;
    }

    protected function checkDownload($user) {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Numcount FROM " . PREFIX . "_shop_downloads_user WHERE Benutzer = '" . intval($user) . "' LIMIT 1");
        return $res->Numcount >= 1 ? true : false;
    }

    public function cancelOrder($id) {
        if (perm('edit_order')) {
            $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Status = 'failed' WHERE Id = '" . intval($id) . "'");
        }
        $this->__object('AdminCore')->backurl();
    }

    public function editOrder($id) {
        if (!perm('edit_order')) {
            $this->__object('AdminCore')->noAccess();
        } else {
            $id = intval($id);
            if (Arr::getRequest('save') == '1') {
                $array = array(
                    'Payment'         => Arr::getPost('Payment'),
                    'Status'          => Arr::getPost('Status'),
                    'KundenNachricht' => Arr::getPost('KundenNachricht'),
                    'Bemerkung'       => Arr::getPost('Bemerkung'),
                    'Tracking_Id'     => Arr::getPost('Tracking_Id'),
                    'Tracking_Code'   => Arr::getPost('Tracking_Code'),
                    'Verschickt'      => (isset($_POST['Sended']) ? implode(',', $_POST['Sended']) : ''),
                );
                $this->_db->update_query('shop_bestellungen', $array, "Id = '" . intval(Arr::getRequest('id')) . "'");

                $subject_text = $_POST['BetreffKunde'];
                $html_text = $_POST['MailKunde'];
                $histor_text = $html_text;
                $html_text = str_replace("\n", '<br />', $html_text);
                $html_order = $_POST['OHTML'];
                $html_order = str_replace('src="uploads/', 'src="' . BASE_URL . '/uploads/', $html_order);

                if (Arr::getPost('SendEmail') == 1 && !empty($_POST['OEmail'])) {
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $_POST['OEmail'],
                        'to_name'   => '',
                        'text'      => SX::get('system.Mail_Header') . $html_text . '<hr noshade="noshade" size="1" />' . $html_order,
                        'subject'   => $subject_text,
                        'fromemail' => SX::get('shop.Email_Abs'),
                        'from'      => SX::get('shop.Name_Abs'),
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => 1,
                        'prio'      => 3));
                }

                $insert_array = array(
                    'BestellNummer' => Arr::getRequest('id'),
                    'Datum'         => time(),
                    'Subjekt'       => $subject_text,
                    'Kommentar'     => Arr::getPost('Bemerkung'),
                    'StatusText'    => $histor_text);
                $this->_db->insert_query('shop_bestellungen_historie', $insert_array);
                $this->__object('AdminCore')->script('save');
            }

            $history = array();
            $q = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_bestellungen_historie  WHERE BestellNummer = '" . $id . "' ORDER BY Id DESC");
            while ($row = $q->fetch_object()) {
                $row->StatusText = strip_tags($row->StatusText, '<br><br /><strong><b><em>');
                $history[] = $row;
            }
            $this->_view->assign('history', $history);

            $Order = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $id . "' LIMIT 1");

            $Ertrag = 0;
            $BestNr = $Order['TransaktionsNummer'];
            $sql_items = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_bestellungen_items WHERE Bestellnummer='" . $BestNr . "'");
            $items = array();
            while ($row_items = $sql_items->fetch_object()) {
                $Ertrag += $this->ekPreise($row_items->Artikelnummer) * $row_items->Anzahl;
                $row_items->ArtName = $this->getName($row_items->Artikelnummer);
                $items[] = $row_items;
            }

            $Order['Datum'] = date("d-m-Y, H:i", $Order['Datum']);
            $Order['Bestellung'] = base64_decode($Order['Bestellung']);
            $Order['Bestellung'] = str_replace('src="uploads/', 'src="' . BASE_URL . '/uploads/', $Order['Bestellung']);
            $Order['Order_Type'] = base64_decode($Order['Order_Type']);
            $Order['Order_Type'] = str_replace('src="uploads/', 'src="' . BASE_URL . '/uploads/', $Order['Order_Type']);

            switch ($Order['Status']) {
                case 'wait':
                    $OText = $this->_lang['Shop_status_s_wait'];
                    $SText = $this->_lang['Shop_status_t_wait'];
                    break;

                case 'progress':
                    $OText = $this->_lang['Shop_status_s_progress'];
                    $SText = $this->_lang['Shop_status_t_progress'];
                    break;

                case 'ok':
                    $OText = $this->_lang['Shop_status_s_ok'];
                    $SText = $this->_lang['Shop_status_t_ok'];
                    break;

                case 'oksend':
                    $OText = $this->_lang['Shop_status_s_alldone'];
                    $SText = $this->_lang['Shop_status_t_alldone'];
                    break;

                case 'oksendparts':
                    $OText = $this->_lang['Shop_status_s_partsdone'];
                    $SText = $this->_lang['Shop_status_t_partsdone'];
                    break;

                case 'failed':
                    $OText = $this->_lang['Shop_status_s_failed'];
                    $SText = $this->_lang['Shop_status_t_failed'];
                    break;
            }
            $fio = $Order['Rng_Nachname'] . ' ' . $Order['Rng_Vorname'] . ' ' . $Order['Rng_MiddleName'];
            $OText = $this->replaceOrder($OText, $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['oksendparts'] = $this->replaceOrder($this->_lang['Shop_status_s_partsdone'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['failed'] = $this->replaceOrder($this->_lang['Shop_status_s_failed'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['wait'] = $this->replaceOrder($this->_lang['Shop_status_s_wait'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['progress'] = $this->replaceOrder($this->_lang['Shop_status_s_progress'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['ok'] = $this->replaceOrder($this->_lang['Shop_status_s_ok'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);
            $JSText['alldone'] = $this->replaceOrder($this->_lang['Shop_status_s_alldone'], $Order['Datum'], $Order['TransaktionsNummer'], $fio, $id);

            $this->_view->assign('Verschickt', ((empty($Order['Verschickt'])) ? 'leer' : explode(',', $Order['Verschickt'])));
            $this->_view->assign('Ertrag', $Ertrag);
            $this->_view->assign('Oitems', $items);
            $this->_view->assign('Tracking', $this->getTracking());
            $this->_view->assign('JSText', $JSText);
            $this->_view->assign('SText', $SText);
            $this->_view->assign('InfoText', $this->__object('Editor')->load('admin', $OText, 'MailKunde', 220, 'OrderE'));
            $this->_view->assign('order', $Order);
            $this->_view->content('/shop/order_edit.tpl');
        }
    }

    protected function getName($Artikelnummer) {
        $res = $this->_db->cache_fetch_object("SELECT Titel_1 FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $this->_db->escape($Artikelnummer) . "' LIMIT 1");
        return is_object($res) ? $res->Titel_1 : '';
    }

    protected function ekPreise($Artikelnummer) {
        $res = $this->_db->cache_fetch_object("SELECT Preis_EK FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $this->_db->escape($Artikelnummer) . "' LIMIT 1");
        return is_object($res) ? $res->Preis_EK : '';
    }

    protected function replaceOrder($text, $time, $onum, $fio, $id) {
        $array = array(
            '__NAME__'         => $fio,
            '__ORDER_NUMBER__' => $onum,
            '__ORDER_ID__'     => $id,
            '__DATE__'         => $time,
            '\n'               => '<br />');
        return $this->_text->replace($text, $array);
    }

    protected function simpleCategs($id, $prefix, &$categs, &$area, $admin = 0, $select = 0) {
        $Lc = 1;
        $query = $this->_db->query("SELECT
            *,
                Id as catid,
                Name_{$Lc} as catname,
                Name_{$Lc} as Name
        FROM
                " . PREFIX . "_shop_kategorie
        WHERE
                Parent_Id = '" . intval($id) . "'
        AND
                Sektion = '" . $_SESSION['a_area'] . "'
        ORDER BY NAME ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = (($item->Parent_Id == 0) ? '' : ' ') . $prefix . $item->Name;
            $item->bold = ($item->Parent_Id == 0) ? 1 : 0;
            $item->expander = $prefix . ' ';
            $item->sub = ($item->Parent_Id == 0) ? 0 : 1;
            $item->Subcount = $this->_text->strlen($prefix);
            $categs[] = $item;
            if ($admin == 1) {
                $this->simpleCategs($item->catid, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $categs, $area, $admin, 0);
            } else {
                $this->simpleCategs($item->catid, $prefix . (($select == 1) ? '-&nbsp;' : '&nbsp;&nbsp;&nbsp;'), $categs, $area, 0, $select);
            }
        }
        $query->close();
        return $categs;
    }

    protected function multiCategs($id, $prefix, &$categs, &$area, $admin = 0, $select = 0) {
        $Lc = $_SESSION['admin_lang_num'];
        $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_sektionen ORDER BY Id");
        while ($row = $sql->fetch_object()) {
            $push = new stdClass;
            $push->area = $row->Id;
            $categs[] = $push;
            $query = $this->_db->query("SELECT
                        *,
                        Id as catid,
                        Name_{$Lc} as catname,
                        Name_{$Lc} as Name
                FROM
                        " . PREFIX . "_shop_kategorie
                WHERE
                        Parent_Id = '" . intval($id) . "'
                AND
                        Sektion = '" . intval($row->Id) . "'
                ORDER BY NAME ASC");
            while ($item = $query->fetch_object()) {
                $item->visible_title = (($item->Parent_Id == 0) ? '' : ' ') . $prefix . $item->Name;
                $item->bold = ($item->Parent_Id == 0) ? 1 : 0;
                $item->expander = $prefix . ' ';
                $item->sub = ($item->Parent_Id == 0) ? 0 : 1;
                $item->Subcount = $this->_text->strlen($prefix);
                $categs[] = $item;
                if ($admin == 1) {
                    $this->simpleCategs($item->catid, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $categs, $area, $admin, 0);
                } else {
                    $this->simpleCategs($item->catid, $prefix . (($select == 1) ? '-&nbsp;' : '&nbsp;&nbsp;&nbsp;'), $categs, $area, 0, $select);
                }
            }
        }
        $sql->close();
        return $categs;
    }

    protected function changeParent($cat, $area) {
        $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_shop_kategorie WHERE Parent_Id = '" . intval($cat) . "'");
        while ($row = $sql->fetch_object()) {
            $this->_db->query("UPDATE " . PREFIX . "_shop_kategorie SET Sektion = '" . intval($area) . "' WHERE Id = '" . $row->Id . "'");
            $this->changeParent($row->Id, $area);
        }
        $sql->close();
        return;
    }

    protected function multiUpload($rid) {
        $images = '';
        if (!empty($rid)) {
            $options = array(
                'type'   => 'image',
                'result' => 'list',
                'upload' => '/uploads/shop/icons/',
                'input'  => 'files',
            );
            $images = SX::object('Upload')->load($options);

            $images = !empty($images) ? implode('|', $images) : '';
            $row = $this->_db->fetch_object("SELECT Bilder FROM " . PREFIX . "_shop_produkte WHERE Id = '" . intval($rid) . "' LIMIT 1");
            if (!empty($row->Bilder)) {
                $images = $row->Bilder . (!empty($images) ? '|' . $images : '');
            }
        }
        return $images;
    }

    protected function listShipper() {
        $shipper = $this->_db->fetch_assoc_all("SELECT Id,Name_1 AS Name FROM " . PREFIX . "_shop_versandarten ORDER BY Name ASC");
        return $shipper;
    }

    protected function listPayments() {
        $payments = $this->_db->fetch_assoc_all("SELECT Id,Name_1 AS Name FROM " . PREFIX . "_shop_zahlungsmethoden ORDER BY Name ASC");
        return $payments;
    }

    public function htmlOrder($id) {
        $pdf = $this->_db->cache_fetch_object("SELECT Bestellung FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $id . "' LIMIT 1");
        $pdf->Bestellung = base64_decode($pdf->Bestellung);
        $pdf->Bestellung = str_replace('src="uploads/', 'src="' . BASE_URL . '/uploads/', $pdf->Bestellung);
        SX::output($pdf->Bestellung, true);
    }

    public function htmlPay($id) {
        $pdf = $this->_db->cache_fetch_object("SELECT Order_Type FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $id . "' LIMIT 1");
        $pdf->Order_Type = base64_decode($pdf->Order_Type);
        $pdf->Order_Type = str_replace('src="uploads/', 'src="' . BASE_URL . '/uploads/', $pdf->Order_Type);
        SX::output($pdf->Order_Type, true);
    }

    protected function numClean($string) {
        $string = trim($this->replace($string));
        return preg_replace('/[^0-9.]/u', '', $string);
    }

    /* Вывод символа валюты */
    protected function simbolValut($data) {
        for ($i = 1; $i < 15; $i++) {
            if (isset($this->_lang['Valute_' . $i])) {
                $valut = explode('|', $this->_lang['Valute_' . $i]);
                $in[] = $valut[1];
                $out[] = $valut[2];
            } else {
                break;
            }
        }
        return str_replace($in, $out, $data);
    }

    /* Метод вычисления коэффициента */
    protected function setMultiplikator($value) {
        $value = $this->replace($value);
        if ($value < 0.0001) {
            $value = 1;
        }
        return $this->replace(round(1 / $value, 10));
    }

    /* Сохранение настроек магазина */
    public function settingsShop() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $WaehrungSymbol_1 = $this->simbolValut(Arr::getPost('Waehrung_1'));
            $WaehrungSymbol_2 = $this->simbolValut(Arr::getPost('Waehrung_2'));
            $WaehrungSymbol_3 = $this->simbolValut(Arr::getPost('Waehrung_3'));
            $image = !empty($_POST['newImg_1']) ? Arr::getPost('newImg_1') : Arr::getPost('watermark_old');

            $array = array(
                'Wasserzeichen_Bild'     => $image,
                'PreiseGaeste'           => Arr::getPost('PreiseGaeste'),
                'AnfrageForm'            => Arr::getPost('AnfrageForm'),
                'TopNewOffersPos'        => Arr::getPost('TopNewOffersPos'),
                'TopNewOffers'           => Arr::getPost('TopNewOffers'),
                'Waehrung_1'             => Arr::getPost('Waehrung_1'),
                'WaehrungSymbol_1'       => $WaehrungSymbol_1,
                'Waehrung_2'             => Arr::getPost('Waehrung_2'),
                'WaehrungSymbol_2'       => $WaehrungSymbol_2,
                'Waehrung_3'             => Arr::getPost('Waehrung_3'),
                'WaehrungSymbol_3'       => $WaehrungSymbol_3,
                'Multiplikator_2'        => $this->setMultiplikator(Arr::getPost('Multiplikator_2')),
                'Multiplikator_3'        => $this->setMultiplikator(Arr::getPost('Multiplikator_3')),
                'NettoPreise'            => Arr::getPost('NettoPreise'),
                'ShopLand'               => Arr::getPost('ShopLand'),
                'Email_Abs'              => Arr::getPost('Email_Abs'),
                'Email_Bestellung'       => Tool::cleanAllow(Arr::getPost('Email_Bestellung'), '@.;'),
                'Name_Abs'               => Arr::getPost('Name_Abs'),
                'Subjekt_Bestellung'     => Arr::getPost('Subjekt_Bestellung'),
                'Subjekt_Best_Kopie'     => Arr::getPost('Subjekt_Best_Kopie'),
                'Gastbestellung'         => Arr::getPost('Gastbestellung'),
                'RechnungsLogo'          => Arr::getPost('RechnungsLogo'),
                'thumb_width_norm'       => Arr::getPost('thumb_width_norm', 140),
                'thumb_width_middle'     => Arr::getPost('thumb_width_middle', 75),
                'thumb_width_small'      => Arr::getPost('thumb_width_small', 50),
                'thumb_width_big'        => Arr::getPost('thumb_width_big', 450),
                'thumb_quality'          => Arr::getPost('thumb_quality', 90),
                'Wasserzeichen'          => Arr::getPost('Wasserzeichen'),
                'WasserzeichenKomp'      => Arr::getPost('WasserzeichenKomp', 'watermark.png'),
                'Wasserzeichen_Position' => Arr::getPost('Wasserzeichen_Position', 'bottom_right'),
                'Gutscheine'             => Arr::getPost('Gutscheine'),
                'BestMax'                => Arr::getPost('BestMax'),
                'BestMin'                => Arr::getPost('BestMin'),
                'Bestand_Zaehlen'        => Arr::getPost('Bestand_Zaehlen'),
                'Template_Produkte'      => Arr::getPost('Template_Produkte'),
                'Start_Limit'            => Arr::getPost('Start_Limit'),
                'Topseller_Limit'        => Arr::getPost('Topseller_Limit'),
                'Angebote_Limit'         => Arr::getPost('Angebote_Limit'),
                'Spalten_Neueste'        => Arr::getPost('Spalten_Neueste'),
                'Spalten_Topseller'      => Arr::getPost('Spalten_Topseller'),
                'Spalten_Angebote'       => Arr::getPost('Spalten_Angebote'),
                'Produkt_Limit_Seite'    => Arr::getPost('Produkt_Limit_Seite'),
                'Topseller_Navi_Limit'   => Arr::getPost('Topseller_Navi_Limit'),
                'Zubehoer_Limit'         => Arr::getPost('Zubehoer_Limit'),
                'Lager_Gering'           => Arr::getPost('Lager_Gering'),
                'Tab_Limit'              => Arr::getPost('Tab_Limit'),
                'Prodtext_Laenge'        => Arr::getPost('Prodtext_Laenge'),
                'LimitExternNeu'         => Arr::getPost('LimitExternNeu'),
                'Telefon_Pflicht'        => intval(Arr::getPost('Telefon_Pflicht')),
                'GefundenOptionen'       => trim(Arr::getPost('GefundenOptionen')),
                'StartSeite'             => Arr::getPost('StartSeite'),
                'Zeige_Text'             => Arr::getPost('Zeige_Text'),
                'Sortable_Produkte'      => Arr::getPost('Typesort') . '_' . Arr::getPost('Sortable'),
                'Zeige_Verfuegbarkeit'   => Arr::getPost('Zeige_Verfuegbarkeit'),
                'Zeige_Lagerbestand'     => Arr::getPost('Zeige_Lagerbestand'),
                'Zeige_Lieferzeit'       => Arr::getPost('Zeige_Lieferzeit'),
                'Zeige_ArtNr'            => Arr::getPost('Zeige_ArtNr'),
                'Zeige_Hersteller'       => Arr::getPost('Zeige_Hersteller'),
                'Zeige_ErschienAm'       => Arr::getPost('Zeige_ErschienAm'),
                'ArtikelBeiKateg'        => Arr::getPost('ArtikelBeiKateg'),
                'PriceGroup'             => Arr::getPost('PriceGroup'),
                'menu_low_amount'        => Arr::getPost('menu_low_amount'),
                'seen_cat'               => Arr::getPost('seen_cat'),
                'vat_info_cat'           => Arr::getPost('vat_info_cat'),
                'similar_product'        => Arr::getPost('similar_product'),
                'vat_info_product'       => Arr::getPost('vat_info_product'),
                'popup_product'          => Arr::getPost('popup_product'),
                'NettoKlein'             => Arr::getPost('NettoKlein'),
                'AvailType'              => Arr::getPost('AvailType'),
                'OnlyFhrase'             => Arr::getPost('OnlyFhrase'),
                'cheaper'                => Arr::getPost('cheaper'),
                'shipping_info'          => Arr::getPost('shipping_info'),
            );
            SX::save('shop', $array);
            SX::save('system', array('shop_is_startpage' => Arr::getPost('shop_is_startpage')));

            if (Arr::getPost('ThumbRenew') == 1 && !empty($image)) {
                Folder::clean(TEMP_DIR . '/cache/');
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки магазина', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
            SX::load(array('shop', 'system'));
        }
        $row = SX::get('shop');
        list($row['Typesort'], $row['Sortable']) = explode('_', $row['Sortable_Produkte']);
        $row['shop_is_startpage'] = SX::get('system.shop_is_startpage');

        if (empty($row['Wasserzeichen_Bild']) || !is_file(UPLOADS_DIR . '/watermarks/' . $row['Wasserzeichen_Bild'])) {
            $row['Wasserzeichen_Bild'] = 'watermark.png';
        }

        // формируем вывод валют в шаблон
        $valut_out1 = $valut_out2 = $valut_out3 = '';
        for ($i = 1; $i < 15; $i++) {
            if (array_key_exists('Valute_' . $i, $this->_lang)) {
                $valut = explode('|', $this->_lang['Valute_' . $i]);
                $sel1 = ($row['Waehrung_1'] == $valut[1]) ? ' selected="selected"' : '';
                $valut_out1 .= '<option value="' . $valut[1] . '"' . $sel1 . '>' . $valut[0] . '</option>';
                $sel2 = ($row['Waehrung_2'] == $valut[1]) ? ' selected="selected"' : '';
                $valut_out2 .= '<option value="' . $valut[1] . '"' . $sel2 . '>' . $valut[0] . '</option>';
                $sel3 = ($row['Waehrung_3'] == $valut[1]) ? ' selected="selected"' : '';
                $valut_out3 .= '<option value="' . $valut[1] . '"' . $sel3 . '>' . $valut[0] . '</option>';
            } else {
                break;
            }
        }
        $row['Multiplikator_2'] = $this->setMultiplikator($row['Multiplikator_2']);
        $row['Multiplikator_3'] = $this->setMultiplikator($row['Multiplikator_3']);

        $this->_view->assign('valut_out1', $valut_out1);
        $this->_view->assign('valut_out2', $valut_out2);
        $this->_view->assign('valut_out3', $valut_out3);
        $this->_view->assign('row', $row);
        $this->_view->content('/shop/settings.tpl');
    }

    protected function delThumbs($dir) {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php'))) {
                File::delete($dir . $file);
            }
        }
        closedir($handle);
    }

    public function startInfo() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Name_1'             => Arr::getPost('Name_1'),
                'Name_2'             => Arr::getPost('Name_2'),
                'Name_3'             => Arr::getPost('Name_3'),
                'StartText_1'        => Arr::getPost('ShopInfo_1'),
                'StartText_2'        => Arr::getPost('ShopInfo_2'),
                'StartText_3'        => Arr::getPost('ShopInfo_3'),
                'StartText_1_zeigen' => Arr::getPost('StartText_1_zeigen'),
                'StartText_2_zeigen' => Arr::getPost('StartText_2_zeigen'),
                'StartText_3_zeigen' => Arr::getPost('StartText_3_zeigen'),
                'Name_1_zeigen'      => Arr::getPost('Name_1_zeigen'),
                'Name_2_zeigen'      => Arr::getPost('Name_2_zeigen'),
                'Name_3_zeigen'      => Arr::getPost('Name_3_zeigen'),
            );
            $this->_db->update_query('shop_eigenschaften', $array, "Sektion = '" . $_SESSION['a_area'] . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил сообщение на стартовой странице магазина', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_eigenschaften WHERE Sektion='" . $_SESSION['a_area'] . "' LIMIT 1");
        $this->_view->assign('row', $row);
        $this->_view->assign('ShopInfo_1', $this->__object('Editor')->load('admin', $row->StartText_1, 'ShopInfo_1', 300, 'ShopSettings'));
        $this->_view->assign('ShopInfo_2', $this->__object('Editor')->load('admin', $row->StartText_2, 'ShopInfo_2', 300, 'ShopSettings'));
        $this->_view->assign('ShopInfo_3', $this->__object('Editor')->load('admin', $row->StartText_3, 'ShopInfo_3', 300, 'ShopSettings'));
        $this->_view->content('/shop/shopstartinfos.tpl');
    }

    public function shopInfo() {
        if (!perm('shop_settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $error = '';
        if (Arr::getPost('save') == 1) {
            if (empty($_POST['ShopAdresse'])) {
                $error[] = $this->_lang['Shop_error_company'];
            }
            if (empty($_POST['ShopAGB'])) {
                $error[] = $this->_lang['Shop_error_agb'];
            }
            if (empty($_POST['Widerruf'])) {
                $error[] = $this->_lang['Shop_error_cancel'];
            }

            if ($error) {
                $this->_view->assign('error', $error);
            } else {
                $array = array(
                    'Fsk18'              => Arr::getPost('Fsk18'),
                    'ShopAGB'            => Arr::getPost('ShopAGB'),
                    'ShopDatenschutz'    => Arr::getPost('ShopDatenschutz'),
                    'ShopAdresse'        => Arr::getPost('ShopAdresse'),
                    'Widerruf'           => Arr::getPost('Widerruf'),
                    'VersandInfo_Footer' => Arr::getPost('VersandInfo_Footer'));
                SX::save('shop', $array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил информацию в магазине', '1', $this->UserId);
                $this->__object('AdminCore')->script('save');
            }
            SX::load('shop');
        }
        $row = SX::get('shop');
        $this->_view->assign('ShopAdresse', $this->__object('Editor')->load('admin', $row['ShopAdresse'], 'ShopAdresse', 250, 'ShopSettings'));
        $this->_view->assign('ShopAGB', $this->__object('Editor')->load('admin', $row['ShopAGB'], 'ShopAGB', 250, 'ShopSettings'));
        $this->_view->assign('ShopDatenschutz', $this->__object('Editor')->load('admin', $row['ShopDatenschutz'], 'ShopDatenschutz', 250, 'ShopSettings'));
        $this->_view->assign('Widerruf', $this->__object('Editor')->load('admin', $row['Widerruf'], 'Widerruf', 250, 'ShopSettings'));
        $this->_view->assign('Fsk18', $this->__object('Editor')->load('admin', $row['Fsk18'], 'Fsk18', 250, 'ShopSettings'));
        $this->_view->assign('VersandInfo_Footer', $this->__object('Editor')->load('admin', $row['VersandInfo_Footer'], 'VersandInfo_Footer', 250, 'ShopSettings'));
        $this->_view->content('/shop/shopinfos.tpl');
    }

    public function personalDownloads($order, $user) {
        if (!perm('shop_downloads')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::hasGet('getfile')) {
            $dl_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_kundendownloads WHERE Id = '" . intval(Arr::getGet('getfile')) . "' LIMIT 1");
            File::filerange(UPLOADS_DIR . '/shop/customerfiles/' . $dl_c->Datei, 'application/octet-stream');
        }

        if (Arr::getRequest('save') == 1) {
            foreach (array_keys($_POST['Datei']) as $uf) {
                $Datei = trim($_POST['Datei'][$uf]);
                $Beschreibung = trim($_POST['Beschreibung'][$uf]);
                if (!empty($Datei) && !empty($Beschreibung)) {
                    $this->_db->query("UPDATE " . PREFIX . "_shop_kundendownloads SET
                        Datei = '" . $this->_db->escape($Datei) . "',
                        Beschreibung = '" . $this->_db->escape($Beschreibung) . "'
                        WHERE Id = '" . intval($uf) . "'");
                }
                if (isset($_POST['Del'][$uf]) && $_POST['Del'][$uf] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_kundendownloads WHERE Id = '" . intval($uf) . "'");
                    File::delete(UPLOADS_DIR . '/shop/customerfiles/' . $Datei);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getRequest('new') == 1) {
            $insert_array = array(
                'Bestellung'   => $order,
                'Kunde'        => $user,
                'Datum'        => time(),
                'Datei'        => Arr::getPost('newFile_1'),
                'Titel'        => '',
                'Beschreibung' => Arr::getPost('Beschreibung'),
                'Downloads'    => '');
            $this->_db->insert_query('shop_kundendownloads', $insert_array);
            $this->__object('AdminCore')->script('save');
        }

        $downloads = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_kundendownloads WHERE Bestellung = '" . intval($order) . "' ORDER BY Datum DESC");

        $this->_view->assign('userfiles', $downloads);
        $this->_view->content('/shop/userfiles_personal.tpl');
    }

    public function userDownloads($user) {
        if (!perm('shop_downloads')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getRequest('save') == 1) {
            $user = intval($_REQUEST['user']);
            foreach (array_keys($_POST['Userfile']) as $uf) {
                $uf = intval($uf);
                $array = array(
                    'DownloadBis'       => $this->__object('AdminCore')->mktime($_POST['DownloadBis'][$uf], 23, 59, 59),
                    'UrlLizenz'         => $_POST['UrlLizenz'][$uf],
                    'KommentarBenutzer' => $_POST['KommentarBenutzer'][$uf],
                    'KommentarAdmin'    => $_POST['KommentarAdmin'][$uf],
                    'Gesperrt'          => $_POST['Gesperrt'][$uf],
                    'GesperrtGrund'     => $_POST['GesperrtGrund'][$uf],
                    'UrlLizenz_Pflicht' => $_POST['UrlLizenz_Pflicht'][$uf],
                );
                $this->_db->update_query('shop_downloads_user', $array, "Benutzer = '" . $user . "' AND Id = '" . $uf . "'");
                if (isset($_POST['del'][$uf]) && $_POST['del'][$uf] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_downloads_user WHERE Id = '" . $uf . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('newfile') == 1) {
            $user = intval($_REQUEST['user']);
            $art_arr = explode('||', Arr::getRequest('ArtikelId'));
            $artid = $art_arr[0];
            $dltill = $this->__object('AdminCore')->mktime($_REQUEST['DownloadBis'], 23, 59, 59);
            $pname = isset($art_arr[1]) ? $art_arr[1] : '';
            $license = $this->newKey();

            $insert_array = array(
                'Benutzer'    => $user,
                'Lizenz'      => $license,
                'Datum'       => time(),
                'Datum_Stamp' => date('Y-m-d H:i:s'),
                'Produktname' => $pname,
                'GueltigBis'  => $dltill,
                'ProuktId'    => $artid);
            $this->_db->insert_query('shop_lizenzen', $insert_array);

            $insert_array = array(
                'Benutzer'    => $user,
                'PName'       => $pname,
                'ArtikelId'   => $artid,
                'DownloadBis' => $dltill,
                'Lizenz'      => $license);
            $this->_db->insert_query('shop_downloads_user', $insert_array);
            $this->__object('AdminCore')->script('save');
        }

        $userfiles = array();
        $res_c = $this->_db->fetch_object("SELECT Id FROM " . PREFIX . "_shop_downloads_user LIMIT 1");
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_downloads_user WHERE Benutzer = '" . $user . "' ORDER BY PName ASC");
        if (is_object($res_c)) {
            while ($row = $sql->fetch_object()) {
                $row_2 = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_produkte WHERE Id='" . $row->ArtikelId . "' LIMIT 1");
                $row->article = (isset($row_2->Titel_1)) ? $row_2->Titel_1 : '';
                $row->date_day = date('d', $row->DownloadBis);
                $row->date_month = date('m', $row->DownloadBis);
                $row->date_year = date('Y', $row->DownloadBis);
                $userfiles[] = $row;
            }
            $this->_view->assign('userfiles', $userfiles);
            $this->_view->assign('row', $row);
        }

        $produkte = array();
        $sql = $this->_db->query("SELECT Titel_1, Id FROM " . PREFIX . "_shop_produkte WHERE Hat_ESD = '1'");
        while ($row = $sql->fetch_object()) {
            $produkte[] = $row;
        }
        $sql->close();

        $ld = $this->licence();
        $this->_view->assign('lizenz', $ld);
        $this->_view->assign('produkte', $produkte);
        $this->_view->assign('selmon', date('m'));
        $this->_view->assign('selday', date('d'));
        $this->_view->assign('selyear', date('Y'));
        $this->_view->content('/shop/userfiles.tpl');
    }

    protected function licence() {
        $licdata = mt_rand(100000000, 199999999);
        $data = '';
        for ($i = 0; $i < strlen($licdata); $i++) {
            $data .= substr($licdata, $i, 1);
            if ((($i + 1) % 3) == 0 && $i != 0) {
                $data .= '-';
            }
        }
        $lic = substr($data, 0, 11);
        $ld_c = $this->_db->cache_fetch_object("SELECT Lizenz FROM " . PREFIX . "_shop_downloads_user WHERE Lizenz = '" . $lic . "' LIMIT 1");
        if (is_object($ld_c) && $ld_c->Lizenz == $lic) {
            $this->licdata();
        } else {
            return $lic;
        }
        return '';
    }

    public function esdDownloads($aid) {
        if (!perm('shop_downloads')) {
            $this->__object('AdminCore')->noAccess();
        }
        $aid = intval($aid);
        if (Arr::getRequest('subaction') == 'new') {
            $insert_array = array(
                'ArtId'        => $aid,
                'Datei'        => (!empty($_POST['newFile_1']) ? $_POST['newFile_1'] : $_POST['Datei']),
                'DateiTyp'     => Arr::getPost('DateiTyp'),
                'TageNachKauf' => Arr::getPost('TageNachKauf'),
                'Bild'         => Arr::getPost('Bild'),
                'Titel'        => Arr::getPost('Titel'),
                'Beschreibung' => sanitize($_POST['Beschreibung']),
                'Position'     => intval(Arr::getPost('Position')));
            $this->_db->insert_query('shop_downloads', $insert_array);
            $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Hat_ESD = '1' WHERE Id = '" . $aid . "'");
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getRequest('subaction') == 'save') {
            if (Arr::getPost('Del') >= 1) {
                foreach ($_POST['Del'] as $id => $Datei) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_downloads WHERE Id = '" . intval($id) . "'");
                }
            }

            if (isset($_POST['Datei'])) {
                foreach ($_POST['Datei'] as $id => $Datei) {
                    $array = array(
                        'Datei'        => $_POST['Datei'][$id],
                        'DateiTyp'     => $_POST['DateiTyp'][$id],
                        'TageNachKauf' => (isset($_POST['TageNachKauf'][$id]) ? $_POST['TageNachKauf'][$id] : ''),
                        'Bild'         => (isset($_POST['Bild'][$id]) ? $_POST['Bild'][$id] : ''),
                        'Titel'        => $_POST['Titel'][$id],
                        'Beschreibung' => sanitize($_POST['Beschreibung'][$id]),
                        'Position'     => $_POST['Position'][$id],
                    );
                    $this->_db->update_query('shop_downloads', $array, "Id = '" . intval($id) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $downloads_full = $downloads_updates = $downloads_bugfixes = $downloads_other = array();
        $query = "SELECT * FROM " . PREFIX . "_shop_downloads WHERE DateiTyp='full' AND ArtId='" . $aid . "' ORDER BY Position ASC ; ";
        $query .= "SELECT * FROM " . PREFIX . "_shop_downloads WHERE DateiTyp='update' AND ArtId='" . $aid . "' ORDER BY Position ASC ; ";
        $query .= "SELECT * FROM " . PREFIX . "_shop_downloads WHERE DateiTyp='bugfix' AND ArtId='" . $aid . "' ORDER BY Position ASC ; ";
        $query .= "SELECT * FROM " . PREFIX . "_shop_downloads WHERE DateiTyp='other' AND ArtId='" . $aid . "' ORDER BY Position ASC";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row_full = $result->fetch_object()) {
                    $downloads_full[] = $row_full;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_updates = $result->fetch_object()) {
                    $downloads_updates[] = $row_updates;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_bugfixes = $result->fetch_object()) {
                    $downloads_bugfixes[] = $row_bugfixes;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_other = $result->fetch_object()) {
                    $downloads_other[] = $row_other;
                }
                $result->close();
            }
        }

        $this->_view->assign('can_upload', ((is_writable(UPLOADS_DIR . '/shop/files/')) ? 1 : 0));
        $this->_view->assign('downloads_full', $downloads_full);
        $this->_view->assign('downloads_updates', $downloads_updates);
        $this->_view->assign('downloads_bugfixes', $downloads_bugfixes);
        $this->_view->assign('downloads_other', $downloads_other);
        $this->_view->assign('esds', $this->esdFiles());
        $this->_view->content('/shop/article_downloads.tpl');
    }

    public function volumesShipping($id) {
        if (!perm('shop_shipper')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('new') == 1 && !empty($_POST['Bis']) && !empty($_POST['Gebuehr'])) {
            $_POST['Gebuehr'] = trim($this->replace($_POST['Gebuehr']));
            $_POST['Bis'] = trim($this->replace($_POST['Bis']));
            $_POST['Bis'] = ($_POST['Bis'] <= $_POST['Von']) ? ($_POST['Von'] + 10.00) : $_POST['Bis'];
            $Gebuehr = ($_POST['Gebuehr'] < $_POST['GebuehrCheck']) ? ($_POST['GebuehrCheck'] + 10.00) : $_POST['Gebuehr'];

            $insert_array = array(
                'VersandId' => $id,
                'Von'       => Arr::getPost('Von'),
                'Bis'       => Arr::getPost('Bis'),
                'Gebuehr'   => $Gebuehr);
            $this->_db->insert_query('shop_versandarten_volumen', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый объем для доставки', '1', $this->UserId);
            $this->__object('Redir')->redirect('index.php?do=shop&sub=editshippingvolumes&Id=' . $id . '&noframes=1');
        }

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Von']) as $vid) {
                $_POST['Bis'][$vid] = ($_POST['Bis'][$vid] <= $_POST['Von'][$vid]) ? $_POST['Von'][$vid] + 1 : $_POST['Bis'][$vid];
                $array = array(
                    'Von'     => $_POST['Von'][$vid],
                    'Bis'     => $_POST['Bis'][$vid],
                    'Gebuehr' => $_POST['Gebuehr'][$vid],
                );
                $this->_db->update_query('shop_versandarten_volumen', $array, "Id = '" . intval($vid) . "'");
                if (isset($_POST['Del'][$vid]) && $_POST['Del'][$vid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_versandarten_volumen WHERE Id='" . intval($vid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $volumes = array();

        $query = "SELECT * FROM " . PREFIX . "_shop_versandarten_volumen WHERE VersandId = '" . $id . "' ORDER BY Von ASC ; ";
        $query .= "SELECT * FROM " . PREFIX . "_shop_versandarten_volumen WHERE VersandId = '" . $id . "' ORDER BY Von DESC ; ";
        $query .= "SELECT Name_1 AS Name FROM " . PREFIX . "_shop_versandarten WHERE Id = '" . intval(Arr::getRequest('Id')) . "'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row = $result->fetch_object()) {
                    $volumes[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $last = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row = $result->fetch_object();
                $result->close();
            }
        }

        $this->_view->assign('new_f', ($last->Bis + '0.01'));
        $this->_view->assign('new_t', ($last->Bis + '10.00'));
        $this->_view->assign('new_g', ($last->Gebuehr + '5.00'));
        $this->_view->assign('volumes', $volumes);
        $this->_view->assign('title', $this->_lang['Shop_shipper_w_edit'] . ': ' . sanitize($row->Name));
        $this->_view->assign('row', $row);
        $this->_view->content('/shop/shipper_volumes.tpl');
    }

    public function editShipping($id, $lc = 1) {
        if (!perm('shop_shipper')) {
            $this->__object('AdminCore')->noAccess();
        }
        $lc = intval($lc);
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            if ($_REQUEST['lc'] < 2) {
                $array = array(
                    'Name_' . $lc         => Arr::getPost('Name'),
                    'Beschreibung_' . $lc => Arr::getPost('Beschreibung_' . $lc),
                    'Laender'             => implode(',', $_POST['Laender']),
                    'Gruppen'             => implode(',', $_POST['Gruppen']),
                    'GewichtNull'         => Arr::getPost('GewichtNull'),
                    'Versanddauer'        => Arr::getPost('Versanddauer'),
                    'Gebuehr_Pauschal'    => $this->replace($_POST['Gebuehr_Pauschal']),
                );
                if (Arr::getPost('IconDel') == 1 && !empty($_POST['IconDelOld'])) {
                    $array['Icon'] = '';
                    File::delete(UPLOADS_DIR . '/shop/shipper_icons/' . $_POST['IconDelOld']);
                }
                if (!empty($_POST['newImg_1'])) {
                    $array['Icon'] = Arr::getPost('newImg_1');
                }
                if (Arr::getPost('saveAllLang') == 1) {
                    $array['Name_2'] = Arr::getPost('Name');
                    $array['Beschreibung_2'] = Arr::getPost('Beschreibung_1');
                    $array['Name_3'] = Arr::getPost('Name');
                    $array['Beschreibung_3'] = Arr::getPost('Beschreibung_1');
                }
            } else {
                $array = array(
                    'Name_' . $lc         => Arr::getPost('Name'),
                    'Beschreibung_' . $lc => Arr::getPost('Beschreibung_' . $lc),
                );
            }
            $this->_db->update_query('shop_versandarten', $array, "Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' сохранил способ доставки (' . Arr::getPost('Name') . ')', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }
        $row = $this->_db->cache_fetch_object("SELECT
                    *,
                    Name_{$lc} AS Name,
                    Beschreibung_{$lc} AS Beschreibung
            FROM " . PREFIX . "_shop_versandarten WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('row', $row);
        $this->_view->assign('writable', ((is_writable(UPLOADS_DIR . '/shop/shipper_icons/')) ? 1 : 0));
        $this->_view->assign('countries_in', (explode(',', $row->Laender)));
        $this->_view->assign('groups_in', (explode(',', $row->Gruppen)));
        $this->_view->assign('countries', Tool::countries());
        $this->_view->assign('groups', $this->groups());
        $this->_view->assign('intro', $this->__object('Editor')->load('admin', $row->Beschreibung, "Beschreibung_{$lc}", 150, 'Basic'));
        $this->_view->assign('title', $this->_lang['Shop_shipper_edit']);
        $this->_view->content('/shop/shipper_edit.tpl');
    }

    public function showShipping() {
        if (!perm('shop_shipper')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $id) {
                $array = array(
                    'Aktiv'    => $_POST['Aktiv'][$id],
                    'Position' => $_POST['Position'][$id],
                );
                $this->_db->update_query('shop_versandarten', $array, "Id='" . intval($id) . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' изменил позицию способа доставки', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $methods = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_versandarten ORDER BY Position ASC");

        $this->_view->assign('methods', $methods);
        $this->_view->assign('title', $this->_lang['Shop_shipper_title']);
        $this->_view->content('/shop/shippingmethods.tpl');
    }

    public function editPayment($id, $lc = 1, $IconDel = '') {
        if (!perm('shop_paymentmethods')) {
            $this->__object('AdminCore')->noAccess();
        }
        $lc = intval($lc);
        $id = intval($id);
        $SetAll = '';
        if (Arr::getPost('save') == 1) {
            if (Arr::getPost('IconDel') == 1 && !empty($_POST['IconDelOld'])) {
                $IconDel = "Icon = '',";
                File::delete(UPLOADS_DIR . '/shop/payment_icons/' . $_POST['IconDelOld']);
            }

            if ($_REQUEST['lc'] < 2) {
                $Betreff = isset($_POST['Betreff']) ? "Betreff = '" . $this->_db->escape(trim(Arr::getPost('Betreff'))) . "'," : '';
                $Testmodus = isset($_POST['Testmodus']) ? "Testmodus = '" . $this->_db->escape(trim(Arr::getPost('Testmodus'))) . "'," : '';
                $Install_Id = isset($_POST['Install_Id']) ? "Install_Id = '" . $this->_db->escape(trim(Arr::getPost('Install_Id'))) . "'," : '';
                $Kosten = $this->replace($_POST['Kosten']);
                $Kosten = ($_POST['KostenTyp'] == 'pro' && $Kosten > 100) ? 100 : $Kosten;

                switch ($id) {
                    case '15':
                    case '20':
                        $Install_Id = "Install_Id = '" . $this->_db->escape(trim(Arr::getPost('IdSeller'))) . ',' . $this->_db->escape(trim(Arr::getPost('IdPayer'))) . "',";
                        break;
                    case '18':
                        $Betreff = "Betreff = '" . $this->_db->escape(trim(Arr::getPost('IdSeller'))) . ',' . $this->_db->escape(trim(Arr::getPost('IdPayer'))) . "',";
                        break;
                }

                if (Arr::getPost('saveAllLang') == 1) {
                    $SetAll = "
                        ,Name_2 = '" . $this->_db->escape(Arr::getPost('Name')) . "'
                        ,Beschreibung_2 = '" . $this->_db->escape(Arr::getPost('Beschreibung_1')) . "'
                        ,BeschreibungLang_2 = '" . $this->_db->escape(Arr::getPost('BeschreibungLang_1')) . "'
                        ,Name_3 = '" . $this->_db->escape(Arr::getPost('Name')) . "'
                        ,Beschreibung_3 = '" . $this->_db->escape(Arr::getPost('Beschreibung_1')) . "'
                        ,BeschreibungLang_3 = '" . $this->_db->escape(Arr::getPost('BeschreibungLang_1')) . "'
                        ";
                }

                $this->_db->query("UPDATE " . PREFIX . "_shop_zahlungsmethoden SET
                            Name_{$lc} = '" . $this->_db->escape(Arr::getPost('Name')) . "',
                            $IconDel
                            $Betreff
                            $Testmodus
                            $Install_Id
                            " . ((!empty($_POST['newImg_1'])) ? "Icon = '" . $this->_db->escape(Arr::getPost('newImg_1')) . "'," : "") . "
                            Beschreibung_{$lc} = '" . $this->_db->escape(Arr::getPost('Beschreibung_' . $lc)) . "',
                            BeschreibungLang_{$lc} = '" . $this->_db->escape(Arr::getPost('BeschreibungLang_' . $lc)) . "',
                            Laender = '" . $this->_db->escape(implode(',', $_POST['Laender'])) . "',
                            Gruppen = '" . $this->_db->escape(implode(',', $_POST['Gruppen'])) . "',
                            Versandarten = '" . $this->_db->escape(implode(',', $_POST['Versandarten'])) . "',
                            KostenOperant = '" . $this->_db->escape(Arr::getPost('KostenOperant')) . "',
                            Kosten = '" . $this->_db->escape($Kosten) . "',
                            KostenTyp = '" . $this->_db->escape(Arr::getPost('KostenTyp')) . "',
                            MaxWert = '" . $this->_db->escape(Arr::getPost('MaxWert')) . "',
                            DetailInfo = '" . $this->_db->escape(Arr::getPost('DetailInfo')) . "'
                            {$SetAll}
                    WHERE Id='" . $id . "'");
            } else {
                $this->_db->query("UPDATE " . PREFIX . "_shop_zahlungsmethoden SET
                            Name_{$lc} = '" . $this->_db->escape(Arr::getPost('Name')) . "',
                            Beschreibung_{$lc} = '" . $this->_db->escape(Arr::getPost('Beschreibung_' . $lc)) . "',
                            BeschreibungLang_{$lc} = '" . $this->_db->escape(Arr::getPost('BeschreibungLang_' . $lc)) . "'
                    WHERE Id='" . $id . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал способ оплаты (' . Arr::getPost('Name') . ')', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $row = $this->_db->cache_fetch_object("SELECT
                    *,
                    Name_{$lc} AS Name,
                    Beschreibung_{$lc} AS Beschreibung,
                    BeschreibungLang_{$lc} AS BeschreibungLang
            FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . $id . "' LIMIT 1");

        switch ($id) {
            case '15':
            case '20':
                list($IdSeller, $IdPayer) = explode(',', $row->Install_Id);
                $this->_view->assign('IdSeller', $IdSeller);
                $this->_view->assign('IdPayer', $IdPayer);
                break;
            case '18':
                list($IdSeller, $IdPayer) = explode(',', $row->Betreff);
                $this->_view->assign('IdSeller', $IdSeller);
                $this->_view->assign('IdPayer', $IdPayer);
                break;
        }

        $this->_view->assign('row', $row);
        $this->_view->assign('writable', ((is_writable(UPLOADS_DIR . '/shop/payment_icons/')) ? 1 : 0));
        $this->_view->assign('countries_in', (explode(',', $row->Laender)));
        $this->_view->assign('groups_in', (explode(',', $row->Gruppen)));
        $this->_view->assign('shipper_in', (explode(',', $row->Versandarten)));
        $this->_view->assign('countries', Tool::countries());
        $this->_view->assign('groups', $this->groups());
        $this->_view->assign('shipper', $this->shipper());
        $this->_view->assign('intro', $this->__object('Editor')->load('admin', $row->Beschreibung, "Beschreibung_{$lc}", 250, 'ShopSettings'));
        $this->_view->assign('text', $this->__object('Editor')->load('admin', $row->BeschreibungLang, "BeschreibungLang_{$lc}", 300, 'ShopSettings'));
        $this->_view->assign('payinf', $this->__object('Editor')->load('admin', $row->DetailInfo, "DetailInfo", 300, 'ShopSettings'));
        $this->_view->assign('title', $this->_lang['Shop_payment_edit']);
        $this->_view->content('/shop/paymentmethod_edit.tpl');
    }

    public function showPayment() {
        if (!perm('shop_paymentmethods')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $id) {
                $array = array(
                    'Aktiv'    => $_POST['Aktiv'][$id],
                    'Position' => $_POST['Position'][$id],
                    'MaxWert'  => $_POST['MaxWert'][$id],
                );
                $this->_db->update_query('shop_zahlungsmethoden', $array, "Id='" . intval($id) . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' сохранил способ оплаты', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $methods = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_zahlungsmethoden ORDER BY Position ASC");

        $this->_view->assign('methods', $methods);
        $this->_view->assign('title', $this->_lang['Shop_payment_title']);
        $this->_view->content('/shop/paymentmethods.tpl');
    }

    public function couponCodes() {
        if (!perm('shop_couponcodes')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1 && $_POST['Code'] >= 1) {
            foreach ($_POST['Code'] as $id => $couponcode) {
                if (!empty($_POST['Code'][$id]) && !empty($_POST['Wert'][$id])) {
                    $ex = explode('.', $_POST['GueltigBis'][$id]);
                    $min_amount = $this->replace($_POST['MinBestellwert'][$id]);
                    $hersteller = !empty($_POST['Hersteller'][$id]) ? implode(',', $_POST['Hersteller'][$id]) : '';
                    $expires_on = (!empty($ex[0]) && !empty($ex[1]) && !empty($ex[2])) ? mktime(23, 23, 59, $ex[1], $ex[0], $ex[2]) : mktime(23, 23, 59, date('m'), date('d'), date('Y') + 1);
                    $amount = ($_POST['Typ'][$id] == 'pro') ? (($_POST['Wert'][$id] > 99) ? 99 : $this->replace($_POST['Wert'][$id])) : ($this->replace($_POST['Wert'][$id]));
                    $insert_array = array(
                        'Code'           => $_POST['Code'][$id],
                        'Wert'           => $amount,
                        'Erstellt'       => time(),
                        'Typ'            => $_POST['Typ'][$id],
                        'Endlos'         => $_POST['Endlos'][$id],
                        'Hersteller'     => $hersteller,
                        'GueltigBis'     => $expires_on,
                        'MinBestellwert' => $min_amount,
                        'Gastbestellung' => $_POST['Gastbestellung'][$id],
                        'CommentCupon'   => $_POST['CommentCupon'][$id]);
                    $this->_db->insert_query('shop_gutscheine', $insert_array);
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Code'] as $id => $couponcode) {
                if (!empty($_POST['Code'][$id]) && !empty($_POST['Wert'][$id])) {
                    $ex = explode('.', $_POST['GueltigBis'][$id]);
                    $min_amount = $this->replace($_POST['MinBestellwert'][$id]);
                    $hersteller = !empty($_POST['Hersteller'][$id]) ? implode(',', $_POST['Hersteller'][$id]) : '';
                    $expires_on = (!empty($ex[0]) && !empty($ex[1]) && !empty($ex[2])) ? mktime(23, 23, 59, $ex[1], $ex[0], $ex[2]) : mktime(23, 23, 59, date('m'), date('d'), date('Y') + 1);
                    $amount = ($_POST['Typ'][$id] == 'pro') ? (($_POST['Wert'][$id] > 99) ? 99 : $this->replace($_POST['Wert'][$id])) : ($this->replace($_POST['Wert'][$id]));
                    $array = array(
                        'Code'           => $_POST['Code'][$id],
                        'Wert'           => $amount,
                        'Typ'            => $_POST['Typ'][$id],
                        'Endlos'         => $_POST['Endlos'][$id],
                        'Hersteller'     => $hersteller,
                        'GueltigBis'     => $expires_on,
                        'MinBestellwert' => $min_amount,
                        'Gastbestellung' => $_POST['Gastbestellung'][$id],
                        'CommentCupon'   => $_POST['CommentCupon'][$id],
                    );
                    $this->_db->update_query('shop_gutscheine', $array, "Id = '" . intval($id) . "'");
                }

                if (!empty($_POST['Del'][$id]) && $_POST['Del'][$id] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_gutscheine WHERE Id = '" . intval($id) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $_REQUEST['show'] = !empty($_REQUEST['show']) ? $_REQUEST['show'] : 'all';
        switch ($_REQUEST['show']) {
            case 'all':
                $dbsort = "ORDER BY Erstellt DESC";
                break;
            case 'red':
                $dbsort = " WHERE Eingeloest > 1 ORDER BY Erstellt DESC";
                break;
            case 'free':
                $dbsort = " WHERE Eingeloest < 1 ORDER BY Erstellt DESC";
                break;
        }

        $limit = !empty($_REQUEST['limit']) ? intval($_REQUEST['limit']) : $this->limit;
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_shop_gutscheine $dbsort LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $codes = array();
        while ($row = $sql->fetch_object()) {
            $multi = array();
            if (!empty($row->Bestellnummern)) {
                $on = explode(',', $row->Bestellnummern);
                foreach ($on as $id => $order) {
                    $r_status = $this->_db->cache_fetch_object("SELECT Status, Id FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $order . "' LIMIT 1");
                    if (is_object($r_status)) {
                        $multi[] = $r_status;
                    }
                }
            } else {
                $r_status = $this->_db->cache_fetch_object("SELECT Status, Id FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $row->Bestellnummer . "' LIMIT 1");
                if (is_object($r_status)) {
                    $multi[] = $r_status;
                }
            }
            $row->Multi = $multi;
            $row->Hersteller = explode(',', $row->Hersteller);
            $this->_view->assign('row', $row);
            $row->InfT = $this->_view->fetch(THEME . '/shop/couponcodes_inf.tpl');
            $codes[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=shop&sub=couponcodes&amp;show=$_REQUEST[show]&amp;limit=$limit&amp;page={s}\">{t}</a> "));
        }
        $new = array();
        for ($i = 1; $i < 4; $i++) {
            $new[] = Tool::random();
        }
        $this->_view->assign('hersteller', $this->manufacturer());
        $this->_view->assign('new_till', mktime(0, 0, 0, date('m') + 3, date('d'), date('Y')));
        $this->_view->assign('nc', $new);
        $this->_view->assign('codes', $codes);
        $this->_view->assign('title', $this->_lang['Shop_couponcodes_title']);
        $this->_view->content('/shop/couponcodes.tpl');
    }

    public function showSpecifications($id) {
        if (!perm('shop_specifications')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $q = '';
            for ($i = 1; $i <= 15; $i++) {
                $q .= "
                    Spez_{$i} = '" . trim($this->_db->escape(Arr::getPost('Spez_' . $i))) . "',
                    Spez_{$i}_2 = '" . (empty($_POST['Spez_' . $i . '_2']) ? trim($this->_db->escape(Arr::getPost('Spez_' . $i))) : trim($this->_db->escape(Arr::getPost('Spez_' . $i . '_2')))) . "',
                    Spez_{$i}_3 = '" . (empty($_POST['Spez_' . $i . '_3']) ? trim($this->_db->escape(Arr::getPost('Spez_' . $i))) : trim($this->_db->escape(Arr::getPost('Spez_' . $i . '_3')))) . "',
                    ";
            }
            $this->_db->query("UPDATE " . PREFIX . "_shop_kategorie_spezifikation SET $q Kategorie = '" . $id . "' WHERE Kategorie = '" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_shop_kategorie_spezifikation WHERE Kategorie = '" . $id . "' LIMIT 1");
        if (!is_array($res)) {
            $this->_db->insert_query('shop_kategorie_spezifikation', array('Kategorie' => $id));
        }
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Shop_articles_spez']);
        $this->_view->content('/shop/specifications.tpl');
    }

    public function categVariants($id) {
        if (!perm('shop_variants')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            if (isset($_POST['Name_1'])) {
                foreach (array_keys($_POST['Name_1']) as $cid) {
                    $cid = intval($cid);
                    $n1 = $_POST['Name_1'][$cid];
                    $n2 = (empty($_POST['Name_2'][$cid])) ? $n1 : $_POST['Name_2'][$cid];
                    $n3 = (empty($_POST['Name_3'][$cid])) ? $n1 : $_POST['Name_3'][$cid];
                    $b1 = $_POST['Beschreibung_1'][$cid];
                    $b2 = (empty($_POST['Beschreibung_2'][$cid])) ? $b1 : $_POST['Beschreibung_2'][$cid];
                    $b3 = (empty($_POST['Beschreibung_3'][$cid])) ? $b1 : $_POST['Beschreibung_3'][$cid];

                    if (!empty($n1)) {
                        $array = array(
                            'Name_1'         => $n1,
                            'Name_2'         => $n2,
                            'Name_3'         => $n3,
                            'Beschreibung_1' => $b1,
                            'Beschreibung_2' => $b2,
                            'Beschreibung_3' => $b3,
                            'Position'       => $_POST['Position'][$cid],
                            'Aktiv'          => $_POST['Aktiv'][$cid],
                        );
                        $this->_db->update_query('shop_varianten_kategorien', $array, "Id = '" . $cid . "'");
                    }

                    if (!empty($_POST['Del'][$cid])) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_varianten_kategorien WHERE Id = '" . $cid . "'");
                        $this->_db->query("DELETE FROM " . PREFIX . "_shop_varianten WHERE KatId = '" . $cid . "'");
                    }
                }
                $this->__object('AdminCore')->script('save');
            }

            if (!empty($_POST['Name_1_n'])) {
                $n1 = Arr::getPost('Name_1_n');
                $n2 = empty($_POST['Name_2_n']) ? $n1 : $_POST['Name_2_n'];
                $n3 = empty($_POST['Name_3_n']) ? $n1 : $_POST['Name_3_n'];
                $b1 = Arr::getPost('Beschreibung_1_n');
                $b2 = empty($_POST['Beschreibung_2_n']) ? $b1 : $_POST['Beschreibung_2_n'];
                $b3 = empty($_POST['Beschreibung_3_n']) ? $b1 : $_POST['Beschreibung_3_n'];

                $insert_array = array(
                    'KatId'          => $id,
                    'Name_1'         => $n1,
                    'Name_2'         => $n2,
                    'Name_3'         => $n3,
                    'Beschreibung_1' => $b1,
                    'Beschreibung_2' => $b2,
                    'Beschreibung_3' => $b3,
                    'Aktiv'          => 1,
                    'Position'       => intval(Arr::getPost('Position_n')));
                $this->_db->insert_query('shop_varianten_kategorien', $insert_array);
            }
        }

        $vars = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_varianten_kategorien WHERE KatId = '" . intval($id) . "' ORDER BY Position ASC");

        $this->_view->assign('vars', $vars);
        $this->_view->assign('title', $this->_lang['Global_Categories']);
        $this->_view->content('/shop/categ_variants.tpl');
    }

    public function showUnits() {
        if (!perm('shop_units')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1 && !empty($_POST['Titel_1']) && !empty($_POST['Mz_1'])) {
            $t1 = Arr::getPost('Titel_1');
            $t2 = !empty($_POST['Titel_2']) ? $_POST['Titel_2'] : $t1;
            $t3 = !empty($_POST['Titel_3']) ? $_POST['Titel_3'] : $t1;
            $m1 = Arr::getPost('Mz_1');
            $m2 = !empty($_POST['Mz_2']) ? $_POST['Mz_2'] : $m1;
            $m3 = !empty($_POST['Mz_3']) ? $_POST['Mz_3'] : $m1;

            $insert_array = array(
                'Titel_1' => $t1,
                'Titel_2' => $t2,
                'Titel_3' => $t3,
                'Mz_1'    => $m1,
                'Mz_2'    => $m2,
                'Mz_3'    => $m3);
            $this->_db->insert_query('shop_einheiten', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новую единицу измерения ($t1)', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Titel_1']) as $id) {
                $id = intval($id);
                $array = array(
                    'Titel_1' => $_POST['Titel_1'][$id],
                    'Titel_2' => $_POST['Titel_2'][$id],
                    'Titel_3' => $_POST['Titel_3'][$id],
                    'Mz_1'    => $_POST['Mz_1'][$id],
                    'Mz_2'    => $_POST['Mz_2'][$id],
                    'Mz_3'    => $_POST['Mz_3'][$id],
                );
                $this->_db->update_query('shop_einheiten', $array, "Id='" . $id . "'");

                if (!empty($_POST['Del'][$id]) && $_POST['Del'][$id] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_einheiten WHERE Id = '" . $id . "'");
                }
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' сохранил единицу измерения', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $units = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_einheiten ORDER BY Titel_1 ASC");

        $this->_view->assign('units', $units);
        $this->_view->assign('title', $this->_lang['Shop_units_title']);
        $this->_view->content('/shop/units.tpl');
    }

    protected function newKey() {
        return Tool::random(3) . '-' . Tool::random(3) . date('y') . '-' . Tool::random(3) . date('s');
    }

    public function showTaxes() {
        if (!perm('shop_taxes')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Name']) as $id) {
                $this->_db->query("UPDATE " . PREFIX . "_shop_ustzone SET Name = '" . $this->_db->escape($_POST['Name'][$id]) . "', Wert = '" . $this->replace($this->_db->escape($_POST['Wert'][$id])) . "' WHERE Id='" . intval($id) . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' сохранил налог', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        $taxes = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_shop_ustzone ORDER BY Id ASC");

        $this->_view->assign('taxes', $taxes);
        $this->_view->assign('title', $this->_lang['Shop_taxes_title']);
        $this->_view->content('/shop/taxes.tpl');
    }

    protected function timeShipping($s = 'Lieferzeit_1 ASC') {
        $items = $this->_db->fetch_object_all("SELECT *, Lieferzeit_1 AS Name FROM " . PREFIX . "_shop_packzeiten ORDER BY " . $this->_db->escape($s));
        return $items;
    }

    public function showAvailabilities() {
        if (!perm('shop_availability')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Titel_1']) as $id) {
                if (!empty($_POST['Titel_1'][$id])) {
                    $array = array(
                        'Titel_1' => $_POST['Titel_1'][$id],
                        'Titel_2' => $_POST['Titel_2'][$id],
                        'Titel_3' => $_POST['Titel_3'][$id],
                        'Text_1'  => $_POST['Text_1'][$id],
                        'Text_2'  => $_POST['Text_2'][$id],
                        'Text_3'  => $_POST['Text_3'][$id],
                    );
                    $this->_db->update_query('shop_verfuegbarkeit', $array, "Id='" . intval($id) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('items', $this->getAvailabilities());
        $this->_view->assign('title', $this->_lang['Shop_availabilities_title']);
        $this->_view->content('/shop/availabilities.tpl');
    }

    public function showShippingready() {
        if (!perm('shop_shippingready')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1 && !empty($_POST['Lieferzeit_1'])) {
            $lz1 = $_POST['Lieferzeit_1'];
            $lz2 = !empty($_POST['Lieferzeit_2']) ? $_POST['Lieferzeit_2'] : $lz1;
            $lz3 = !empty($_POST['Lieferzeit_3']) ? $_POST['Lieferzeit_3'] : $lz1;

            $insert_array = array(
                'Lieferzeit_1' => $lz1,
                'Lieferzeit_2' => $lz2,
                'Lieferzeit_3' => $lz3);
            $this->_db->insert_query('shop_packzeiten', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый срок доставки', '1', $this->UserId);
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Lieferzeit_1']) as $id) {
                if (!empty($_POST['Lieferzeit_1'][$id])) {
                    $array = array(
                        'Lieferzeit_1' => $_POST['Lieferzeit_1'][$id],
                        'Lieferzeit_2' => $_POST['Lieferzeit_2'][$id],
                        'Lieferzeit_3' => $_POST['Lieferzeit_3'][$id],
                    );
                    $this->_db->update_query('shop_packzeiten', $array, "Id='" . intval($id) . "'");
                }
                if (!empty($_POST['Del'][$id]) && $_POST['Del'][$id] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_shop_packzeiten WHERE Id = '" . intval($id) . "'");
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил срок доставки', '1', $this->UserId);
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('items', $this->timeShipping('Id ASC'));
        $this->_view->assign('title', $this->_lang['Shop_shippingready_title']);
        $this->_view->content('/shop/shippingready.tpl');
    }

    public function settingsGroups() {
        if (!perm('shop_groupsettings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Gruppe']) as $id) {
                $this->_db->query("UPDATE " . PREFIX . "_benutzer_gruppen SET Rabatt = '" . $this->_db->escape($this->replace($_POST['Rabatt'][$id])) . "', ShopAnzeige = '" . $this->_db->escape($_POST['ShopAnzeige'][$id]) . "' WHERE Id = '" . intval($id) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('groups', $this->groups(2));
        $this->_view->assign('title', $this->_lang['Shop_groupsettings']);
        $this->_view->content('/shop/groupsettings.tpl');
    }

    public function active($a, $id) {
        $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Aktiv='" . $this->_db->escape($a) . "' WHERE Id = '" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    protected function esdFiles() {
        $verzname = UPLOADS_DIR . '/shop/files/';
        $handle = opendir($verzname);
        $esds = array();
        while (false !== ($datei = readdir($handle))) {
            if (!in_array($datei, array('.', '..', '.htaccess', 'index.php')) && is_file($verzname . $datei)) {
                $esds[] = $datei;
            }
        }
        closedir($handle);
        return $esds;
    }

    protected function nameCateg($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name_1 AS Name FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $res->Name;
    }

    protected function shipper() {
        $shipper = $this->_db->fetch_object_all("SELECT Id, Name_1 AS Name FROM " . PREFIX . "_shop_versandarten ORDER BY Position ASC");
        return $shipper;
    }

    protected function groups($not = '') {
        $db_not = (!empty($not)) ? "WHERE Id != " . $this->_db->escape($not) : '';
        $groups = $this->_db->fetch_object_all("SELECT Rabatt, ShopAnzeige, Name_Intern, Name, Id FROM " . PREFIX . "_benutzer_gruppen {$db_not} ORDER BY Name ASC");
        return $groups;
    }

    protected function nameShipper($id) {
        $rl = $this->_db->cache_fetch_object("SELECT Name_1 AS Name FROM " . PREFIX . "_shop_versandarten WHERE Id = '" . intval($id) . "' LIMIT 1");
        return (is_object($rl)) ? $rl->Name : '&nbsp;';
    }

    protected function namePayment($id) {
        $rl = $this->_db->cache_fetch_object("SELECT Name_1 AS Name FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $rl->Name;
    }

    protected function listManufaturer() {
        $manufaturer = $this->_db->fetch_object_all("SELECT Name, Id FROM " . PREFIX . "_hersteller  ORDER BY Name ASC");
        return $manufaturer;
    }

    protected function cutter() {
        $cutter = str_replace('\\r', "\015", $this->cutter);
        $cutter = str_replace('\\n', "\012", $cutter);
        $cutter = str_replace('\\t', "\011", $cutter);
        return $cutter;
    }

    protected function getAvailabilities() {
        $avail = $this->_db->fetch_assoc_all("SELECT *, Id, Titel_1 AS Name FROM " . PREFIX . "_shop_verfuegbarkeit ORDER BY Id ASC");
        return $avail;
    }

    protected function units() {
        $units = $this->_db->fetch_assoc_all("SELECT Id, Mz_1 AS Mz, Titel_1 AS Name FROM " . PREFIX . "_shop_einheiten ORDER BY Name ASC");
        return $units;
    }

    protected function manufacturer() {
        $m = $this->_db->fetch_assoc_all("SELECT Id, Name FROM " . PREFIX . "_hersteller ORDER BY Name ASC");
        return $m;
    }

    protected function cleanArtikel($var) {
        return Tool::cleanAllow($var, '.');
    }

    protected function cleanPrice($var) {
        $var = trim($var);
        $var = $this->replace($var);
        return $var;
    }

    protected function exist($art) {
        $row = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer = '" . $this->_db->escape($art) . "' LIMIT 1");
        return (!is_object($row)) ? true : false;
    }

    /* Удаляем один заказ */
    public function deleteOrder($id) {
        if (perm('del_order')) {
            $id = intval($id);
            $row = $this->_db->cache_fetch_object("SELECT TransaktionsNummer FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $id . "' LIMIT 1");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_bestellungen_items WHERE Bestellnummer = '" . $row->TransaktionsNummer . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_bestellungen_historie WHERE BestellNummer = '" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил заказ ' . $row->TransaktionsNummer . ' в магазине', '1', $this->UserId);
        }
        $this->__object('Redir')->redirect('index.php?do=shop&sub=orders');
    }

    /* Удаляем все заказы */
    public function cleanOrders() {
        if (perm('del_order')) {
            Tool::cleanTable('shop_bestellungen');
            Tool::cleanTable('shop_bestellungen_items');
            Tool::cleanTable('shop_bestellungen_historie');
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил все заказы в магазине', '1', $this->UserId);
        }
        $this->__object('Redir')->redirect('index.php?do=shop&sub=orders');
    }

}
