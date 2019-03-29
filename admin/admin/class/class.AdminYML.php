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

class AdminYML extends Magic {

    protected $area;
    protected $categ;
    protected $product;
    protected $manufacturer;

    public function __construct() {
        $this->area = $_SESSION['a_area'];
        $this->categ = $this->categs();
        $this->product = $this->products();
        $this->manufacturer = $this->manufacturers();
    }

    public function get() {
        if (Arr::getRequest('generate') == '1') {
            $output = $this->generate();
            $link = 'uploads/shop/yml/ym_shop_' . $this->area . '.xml';
            $file = SX_DIR . '/' . $link;
            File::set($file, $output);
            $this->compress($file, $file . '.gz');
            $output = highlight_string($output, true);
            $this->_view->assign('output', $output);
            $this->_view->assign('link_yml', BASE_URL . '/' . $link);
            $this->_view->assign('aktive', 1);
        } else {
            $this->_view->assign('aktive', 0);
        }
        $this->_view->assign('title', $this->_lang['YaMarket']);
        $this->_view->content('/shop/yml.tpl');
    }

    protected function categs() {
        $categs = $this->_db->fetch_object_all("SELECT Id, Parent_Id, Name_1 as Name FROM " . PREFIX . "_shop_kategorie WHERE Sektion = '" . $this->area . "' AND Aktiv = '1'");
        return $categs;
    }

    protected function products() {
        $products = array();
        $sql = $this->_db->query("SELECT Id, Kategorie, Artikelnummer, Hersteller, Bild, Preis, Preis_Liste, Lagerbestand, PrCountry, Titel_1 as Titel, Beschreibung_1 as Beschreibung, Beschreibung_lang_1 as Beschreibung_lang FROM " . PREFIX . "_shop_produkte WHERE Sektion = '" . $this->area . "' AND Aktiv = '1' AND Yml = '1'");
        while ($row = $sql->fetch_object()) {
            $row->url_product = BASE_URL . '/shop/show-product/' . $row->Id . '/' . $row->Kategorie . '/' . translit($row->Titel) . '/';
            $row->Preis_Liste = ($row->Preis > 0 && $row->Preis < $row->Preis_Liste) ? $row->Preis : $row->Preis_Liste;
            $row->available = ($row->Lagerbestand > 0) ? 'true' : 'false';
            $row->Text = $this->cut(strip_tags(!empty($row->Beschreibung) ? $row->Beschreibung : $row->Beschreibung_lang), 512);
            $products[] = $row;
        }
        $sql->close();
        return $products;
    }

    protected function manufacturers() {
        $manufacturers = $this->_db->fetch_object_all("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Sektion = '" . $this->area . "'");
        return $manufacturers;
    }

    protected function name($id) {
        foreach ($this->manufacturer as $manufacturer) {
            if ($id == $manufacturer->Id) {
                return $manufacturer->Name;
            }
        }
        return '';
    }

    protected function categ($Parent_Id = 0) {
        $out = '';
        foreach ($this->categ as $category) {
            if ($category->Parent_Id == $Parent_Id) {
                $out .= '      <category id="' . $category->Id . '"';
                if ($Parent_Id != 0) {
                    $out .= ' parentId="' . $Parent_Id . '"';
                }
                $out .= '>' . $this->ymlsanitize($category->Name) . '</category>' . "\n";
                if (($var = $this->categ($category->Id))) {
                    $out .= $var;
                }
            }
        }
        return $out;
    }

    protected function ymlsanitize($text) {
        return str_replace(array('&', '<', '>', '"', "'"), array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;'), $text);
    }

    protected function valut($val) {
        return str_replace(array('РУБ', 'ГРН'), array('RUR', 'UAH'), SX::get('shop.Waehrung_' . $val));
    }

    protected function kurs($val) {
        switch ($val) {
            default:
            case 1: return ' rate="1"';
            case 2: return ' rate="' . numf(1 / SX::get('shop.Multiplikator_2')) . '"';
            case 3: return ' rate="' . numf(1 / SX::get('shop.Multiplikator_3')) . '"';
        }
    }

    protected function generate() {
        set_time_limit(600);
        $bid = !empty($_REQUEST['bid']) ? 'bid="' . Arr::getRequest('bid') . '" ' : '';
        $cbid = !empty($_REQUEST['cbid']) ? 'cbid="' . Arr::getRequest('cbid') . '" ' : '';
        $delivery = Arr::getRequest('delivery');
        $local_delivery_cost = Arr::getRequest('local_delivery_cost');
        $output = '<?xml version="1.0" encoding="' . CHARSET . '" ?>' . "\n";
        $output .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
        $output .= '<yml_catalog date="' . date('Y-m-d H:m') . '">' . "\n";
        $output .= '  <shop>' . "\n";
        $output .= '    <name>' . $this->cut($this->ymlsanitize(SX::get('system.Seitenname')), 20) . '</name>' . "\n";
        $output .= '    <company>' . $this->ymlsanitize(SX::get('system.Firma')) . '</company>' . "\n";
        $output .= '    <url>' . BASE_URL . '/shop/' . $this->area . '/</url>' . "\n";
        $output .= '    <platform>SX CMS</platform>' . "\n";
        $output .= '    <version>1.06</version>' . "\n";
        $output .= '    <email>info@sx-cms.ru</email>' . "\n";
        $output .= '    <currencies>' . "\n";
        $output .= '      <currency id="' . $this->valut(1) . '"' . $this->kurs(1) . '/>' . "\n";
        if (SX::get('shop.Waehrung_2') != '') {
            $output .= '      <currency id="' . $this->valut(2) . '"' . $this->kurs(2) . '/>' . "\n";
        }
        if (SX::get('shop.Waehrung_3') != '') {
            $output .= '      <currency id="' . $this->valut(3) . '"' . $this->kurs(3) . '/>' . "\n";
        }
        $output .= '    </currencies>' . "\n";
        $output .= '    <categories>' . "\n";
        $output .= $this->categ();
        $output .= '    </categories>' . "\n";
        $output .= '    <offers>' . "\n";
        $products = $this->product;
        foreach ($products as $product) {
            if (!empty($product->Text) && $product->Preis_Liste > 0) {
                $output .= '      <offer id="' . $this->cut($product->Id, 20) . '" type="vendor.model" ' . $bid . $cbid . 'available="' . $product->available . '">' . "\n";
                $output .= '        <url>' . $this->cut($product->url_product, 512) . '</url>' . "\n";
                $output .= '        <price>' . $product->Preis_Liste . '</price>' . "\n";
                $output .= '        <currencyId>' . $this->valut('1') . '</currencyId>' . "\n";
                $output .= '        <categoryId>' . $product->Kategorie . '</categoryId>' . "\n";
                if (!empty($product->Bild)) {
                    $output .= '        <picture>' . $this->cut(BASE_URL . '/uploads/shop/icons/' . $product->Bild, 512) . '</picture>' . "\n";
                }
                $output .= '        <delivery>' . $delivery . '</delivery>' . "\n";
                $output .= '        <local_delivery_cost>' . $local_delivery_cost . '</local_delivery_cost>' . "\n";
                $vendor = $this->name($product->Hersteller);
                if (!empty($vendor)) {
                    $output .= '        <vendor>' . $this->ymlsanitize($vendor) . '</vendor>' . "\n";
                }
                if (!empty($product->Artikelnummer)) {
                    $output .= '        <vendorCode>' . $this->ymlsanitize($product->Artikelnummer) . '</vendorCode>' . "\n";
                }
                $output .= '        <model>' . $this->ymlsanitize($product->Titel) . '</model>' . "\n";
                if (!empty($product->Text)) {
                    $output .= '        <description>' . $this->ymlsanitize($product->Text) . '</description>' . "\n";
                }
                $output .= '        <manufacturer_warranty>true</manufacturer_warranty>' . "\n";
                if (!empty($product->PrCountry)) {
                    $output .= '        <country_of_origin>' . $this->ymlsanitize($product->PrCountry) . '</country_of_origin>' . "\n";
                }
                $output .= '      </offer>' . "\n";
            }
        }
        $output .= '    </offers>' . "\n";
        $output .= '  </shop>' . "\n";
        $output .= '</yml_catalog>' . "\n";
        return $output;
    }

    protected function compress($src, $dst) {
        $data = File::get($src);
        $zp = gzopen($dst, 'w9');
        gzwrite($zp, $data);
        gzclose($zp);
    }

    protected function cut($text, $num) {
        if (!empty($text) && $this->_text->strlen($text) > $num) {
            $text = $this->_text->substr($text, 0, $num);
        }
        return $text;
    }

}
