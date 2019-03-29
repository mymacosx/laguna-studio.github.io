<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['shopsettings'].submit();
    }
});

$(document).ready(function() {
    $('#commentForm').validate( {
	rules: {
            ShopLand: { required: true, minlength: 2 },
            Email_Abs: { required: true, email: true },
            Name_Abs: { required: true },
            Email_Bestellung: { required: true },
            Subjekt_Bestellung: { required: true },
            Subjekt_Best_Kopie: { required: true },
            RechnungsLogo: { url: true },
            thumb_width_small: { required: true, range: [20, 200] },
            thumb_width_norm: { required: true, range: [60, 400] },
            thumb_width_middle: { required: true, range: [60, 600] },
            thumb_width_big: { required: true, range: [300, 1000] },
            BestMin: { required: true, number: true },
            BestMax: { required: true, number: true },
            Start_Limit: { required: true, range: [3, 20] },
            Spalten_Neueste: { required: true, range: [2, 4] },
            Topseller_Limit: { required: true, range: [3, 20] },
            Spalten_Topseller: { required: true, range: [2, 4] },
            Angebote_Limit: { required: true, range: [3, 20] },
            Spalten_Angebote: { required: true, range: [2, 4] },
            Topseller_Navi_Limit: { required: true, range: [2, 25] },
            Zubehoer_Limit: { required: true, range: [2, 35] },
            LimitExternNeu: { required: true, range: [2, 15] },
            Lager_Gering: { required: true, range: [1, 20] },
            Tab_Limit: { required: true, range: [2, 8] },
            Prodtext_Laenge: { required: true, range: [100, 450] },
            WasserzeichenKomp: { required: true, range: [10, 100] }
        },
        messages: {
            ShopLand: {
                required: '{#SettingsCountry#}',
                minlength: '{#SettingsCountryLength#}'
            }
        }
    });
});

function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    var resize = document.getElementById('resizeUpload_' + divid).value;
    $.ajaxFileUpload({
	url: 'index.php?do=shop&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
	secureuri: false,
	fileElementId: 'fileToUpload_' + divid,
	dataType: 'json',
	success: function (data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                }
	    }
	},
	error: function (data, status, e) {
	    document.getElementById('UpInf_' + divid).innerHTML = e;
	}
    });
    return false;
}
//-->
</script>

<div class="header">{#Global_Shop#} - {#Global_Settings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form id="commentForm" name="shopsettings" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_curr1#}</td>
      <td class="row_right">
        <select name="Waehrung_1" id="Waehrung_1" class="input">
          {$valut_out1}
        </select>
        <a class="colorbox" href="http://www.cbr.ru/">{#Shop_Settings_currWeb#}</a>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_curr2#}</td>
      <td class="row_right">
        <select name="Waehrung_2" class="input">
          <option value=""></option>
          {$valut_out2}
        </select>
        <input name="Multiplikator_2" type="text" class="input" id="Multiplikator_2" value="{if !empty($row.Waehrung_2)}{$row.Multiplikator_2}{/if}" size="16" maxlength="16" />
        <img class="absmiddle stip" title="{$lang.Shop_Settings_MultiInf|sanitize}" src="{$imgpath}/help.png" alt="" />
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_curr3#}</td>
      <td class="row_right">
        <select name="Waehrung_3" class="input">
          <option value=""></option>
          {$valut_out3}
        </select>
        <input name="Multiplikator_3" type="text" class="input" id="Multiplikator_3" value="{if !empty($row.Waehrung_3)}{$row.Multiplikator_3}{/if}" size="16" maxlength="16" />
        <img class="absmiddle stip" title="{$lang.Shop_Settings_MultiInf|sanitize}" src="{$imgpath}/help.png" alt="" />
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_CountryInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_shopCountry#}</td>
      <td class="row_right"><label><input class="input { required: true }" name="ShopLand" type="text" id="ShopLand" size="4" maxlength="2" value="{$row.ShopLand|upper}" /></label></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Setting_shopstart_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_shopasstartpage#} </td>
      <td class="row_right">
        <label><input type="radio" name="shop_is_startpage" value="1" {if $row.shop_is_startpage == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="shop_is_startpage" value="0" {if $row.shop_is_startpage == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
     <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.ShopStartInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#ShopStartT#} </td>
      <td class="row_right">
        <select class="input" name="StartSeite" id="StartSeite">
          <option value="artikel" {if $row.StartSeite == 'artikel'}selected="selected"{/if}>{#ShopStartAll#}</option>
          <option value="shopstart" {if $row.StartSeite == 'shopstart'}selected="selected"{/if}>{#ShopStartNewOff#}</option>
          <option value="startartikel" {if $row.StartSeite == 'startartikel'}selected="selected"{/if}>{#ShopStartArtikel#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_settings_ancategsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_settings_ancategs#}</td>
      <td class="row_right">
        <label><input type="radio" name="ArtikelBeiKateg" value="1" {if $row.ArtikelBeiKateg == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="ArtikelBeiKateg" value="0" {if $row.ArtikelBeiKateg == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_PriceInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_priceDisplay#}</td>
      <td class="row_right">
        <label><input type="radio" name="NettoPreise" value="0" {if $row.NettoPreise == 0}checked="checked"{/if} />{#Shop_Settings_priceB#}</label>
        <label><input type="radio" name="NettoPreise" value="1" {if $row.NettoPreise == 1}checked="checked"{/if} />{#Shop_Settings_priceN#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_NettoKleinInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_NettoKlein#}</td>
      <td class="row_right">
        <label><input type="radio" name="NettoKlein" value="1" {if $row.NettoKlein == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="NettoKlein" value="0" {if $row.NettoKlein == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_GuestInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_guestorder#}</td>
      <td class="row_right">
        <label><input type="radio" name="Gastbestellung" value="1" {if $row.Gastbestellung == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Gastbestellung" value="0" {if $row.Gastbestellung == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_settings_nguestsInf}" src="{$imgpath}/help.png" alt="" /> {#Shop_settings_nguests#}</td>
      <td class="row_right">
        <label><input type="radio" name="PreiseGaeste" id="radio" value="1" {if $row.PreiseGaeste == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="PreiseGaeste" id="radio" value="0" {if $row.PreiseGaeste == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_phone_MustInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_phone_Must#}</td>
      <td class="row_right">
        <label><input type="radio" name="Telefon_Pflicht" value="1" {if $row.Telefon_Pflicht == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Telefon_Pflicht" value="0" {if $row.Telefon_Pflicht == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>

    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.ShopCheaperInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#ShopCheaper#}</td>
      <td class="row_right">
        <label><input type="radio" name="cheaper" value="1" {if $row.cheaper == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="cheaper" value="0" {if $row.cheaper == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>

    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.TopNavTabsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#TopNavTabs#}</td>
      <td class="row_right">
        <label><input type="radio" name="TopNewOffers" value="1" {if $row.TopNewOffers == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="TopNewOffers" value="0" {if $row.TopNewOffers == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.TopNavTabsPosInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#TopNavTabsPos#}</td>
      <td class="row_right">
        <label><input type="radio" name="TopNewOffersPos" value="top" {if $row.TopNewOffersPos == 'top'}checked="checked"{/if} />{#TopNavTabsTop#}</label>
        <label><input type="radio" name="TopNewOffersPos" value="bottom" {if $row.TopNewOffersPos == 'bottom'}checked="checked"{/if} />{#TopNavTabsBottom#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_TopTabsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountInTabs#} </td>
      <td class="row_right"><input name="Tab_Limit" type="text" class="input" id="Tab_Limit" value="{$row.Tab_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_CouponsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_Coupons#}</td>
      <td class="row_right">
        <label><input name="Gutscheine" type="radio" value="1" {if $row.Gutscheine == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Gutscheine" type="radio" value="0" {if $row.Gutscheine == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"> <img class="absmiddle stip" title="{$lang.Shop_product_requestainf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_product_requesta#}</td>
      <td class="row_right">
        <label><input name="AnfrageForm" type="radio" value="1" {if $row.AnfrageForm == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="AnfrageForm" type="radio" value="0" {if $row.AnfrageForm == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_OMinInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_Ordermin#}</td>
      <td class="row_right"><input name="BestMin" type="text" class="input" id="BestMin" value="{$row.BestMin}" size="10" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_LimMaxInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_Ordermax#}</td>
      <td class="row_right"><input name="BestMax" type="text" class="input" id="BestMax" value="{$row.BestMax}" size="10" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ShippFreeInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ShippingFree#}</td>
      <td class="row_right"> {#Settings_countries_text#} - <a href="index.php?do=shop&amp;sub=regions">{#Settings_countries_title#}</a></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ReductInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_Storereduct#}</td>
      <td class="row_right">
        <label><input type="radio" name="Bestand_Zaehlen" id="radio3" value="1" {if $row.Bestand_Zaehlen == 1}checked="checked"{/if}/>{#Shop_Settings_Storereduct1#}</label>
        <label><input type="radio" name="Bestand_Zaehlen" id="radio4" value="0" {if $row.Bestand_Zaehlen == 0}checked="checked"{/if}/>{#Shop_Settings_Storereduct2#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartNewCountInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountNew#}</td>
      <td class="row_right"><input name="Start_Limit" type="text" class="input" id="Start_Limit" value="{$row.Start_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartNewCountRowInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountNewRow#}</td>
      <td class="row_right"><input name="Spalten_Neueste" type="text" class="input" id="Spalten_Neueste" value="{$row.Spalten_Neueste}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartTsCountInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountTopseller#}</td>
      <td class="row_right"><input name="Topseller_Limit" type="text" class="input" id="Topseller_Limit" value="{$row.Topseller_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartNewCountRowInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountTopsellerRow#}</td>
      <td class="row_right"><input name="Spalten_Topseller" type="text" class="input" id="Spalten_Topseller" value="{$row.Spalten_Topseller}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartOffersCountInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountOffers#}</td>
      <td class="row_right"><input name="Angebote_Limit" type="text" class="input" id="Angebote_Limit" value="{$row.Angebote_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_StartNewCountRowInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountOffersRow#}</td>
      <td class="row_right"><input name="Spalten_Angebote" type="text" class="input" id="Spalten_Angebote" value="{$row.Spalten_Angebote}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_CountExternInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_CountExtern#}</td>
      <td class="row_right"><input name="LimitExternNeu" type="text" class="input" value="{$row.LimitExternNeu}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_LowAmountInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_LowAmmountWarn#}</td>
      <td class="row_right"><input name="Lager_Gering" type="text" class="input" id="Lager_Gering" value="{$row.Lager_Gering}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ProdTextInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ProdTextLength#}</td>
      <td class="row_right"><input name="Prodtext_Laenge" type="text" class="input" id="Prodtext_Laenge" value="{$row.Prodtext_Laenge}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_CountProductsPage#}</td>
      <td class="row_right">
        <select class="input" name="Produkt_Limit_Seite" id="Produkt_Limit_Seite">
          <option value="5" {if $row.Produkt_Limit_Seite == 5}selected="selected"{/if}>5</option>
          <option value="10" {if $row.Produkt_Limit_Seite == '10'}selected="selected"{/if}>10</option>
          <option value="20" {if $row.Produkt_Limit_Seite == '20'}selected="selected"{/if}>20</option>
          <option value="50" {if $row.Produkt_Limit_Seite == '50'}selected="selected"{/if}>50</option>
          <option value="100" {if $row.Produkt_Limit_Seite == '100'}selected="selected"{/if}>100</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_CountTopSNavi#}</td>
      <td class="row_right"><input name="Topseller_Navi_Limit" type="text" class="input" id="Topseller_Navi_Limit" value="{$row.Topseller_Navi_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_TplProducts#}</td>
      <td class="row_right">
        <select style="width: 250px" name="Template_Produkte" id="Template_Produkte" class="input">
          <option value="products" {if $row.Template_Produkte == 'products'}selected="selected"{/if}>{#Shop_Settings_TplProducts1#} (products.tpl)</option>
          <option value="products_2colums" {if $row.Template_Produkte == 'products_2colums'}selected="selected"{/if}>{#Shop_Settings_TplProducts_2#} (products_2colums.tpl)</option>
          <option value="products_3colums" {if $row.Template_Produkte == 'products_3colums'}selected="selected"{/if}>{#Shop_Settings_TplProducts_3#} (products_3colums.tpl)</option>
          <option value="products_table" {if $row.Template_Produkte == 'products_table'}selected="selected"{/if}>{#Shop_Settings_TplTable#} (products_table.tpl)</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopSortable#}</td>
      <td class="row_right">
        <select name="Typesort" id="Typesort" class="input">
          <option value="date" {if $row.Typesort == 'date'}selected="selected"{/if}>{#ShopSortableTDate#}</option>
          <option value="title" {if $row.Typesort == 'title'}selected="selected"{/if}>{#ShopSortableTitle#}</option>
          <option value="price" {if $row.Typesort == 'price'}selected="selected"{/if}>{#ShopSortablePrice#}</option>
          <option value="klick" {if $row.Typesort == 'klick'}selected="selected"{/if}>{#ShopSortableKlicks#}</option>
          <option value="art" {if $row.Typesort == 'art'}selected="selected"{/if}>{#ShopSortableArtikel#}</option>
        </select>
        <select style="width: 140px" name="Sortable" id="Sortable" class="input">
          <option value="asc" {if $row.Sortable == 'asc'}selected="selected"{/if}>{#asc_t#}</option>
          <option value="desc" {if $row.Sortable == 'desc'}selected="selected"{/if}>{#desc_t#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopSimilarProduct#}</td>
      <td class="row_right">
        <label><input name="similar_product" type="radio" value="1" {if $row.similar_product == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="similar_product" type="radio" value="0" {if $row.similar_product == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_CountZuProducts#}</td>
      <td class="row_right"><input name="Zubehoer_Limit" type="text" class="input" id="Zubehoer_Limit" value="{$row.Zubehoer_Limit}" size="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_Text#}</td>
      <td class="row_right">
        <label><input name="Zeige_Text" type="radio" value="1" {if $row.Zeige_Text == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_Text" type="radio" value="0" {if $row.Zeige_Text == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_Verfuegbarkeit#}</td>
      <td class="row_right">
        <label><input name="Zeige_Verfuegbarkeit" type="radio" value="1" {if $row.Zeige_Verfuegbarkeit == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_Verfuegbarkeit" type="radio" value="0" {if $row.Zeige_Verfuegbarkeit == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_Lagerbestand#}</td>
      <td class="row_right">
        <label><input name="Zeige_Lagerbestand" type="radio" value="1" {if $row.Zeige_Lagerbestand == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_Lagerbestand" type="radio" value="0" {if $row.Zeige_Lagerbestand == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_Lieferzeit#}</td>
      <td class="row_right">
        <label><input name="Zeige_Lieferzeit" type="radio" value="1" {if $row.Zeige_Lieferzeit == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_Lieferzeit" type="radio" value="0" {if $row.Zeige_Lieferzeit == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_ArtNr#}</td>
      <td class="row_right">
        <label><input name="Zeige_ArtNr" type="radio" value="1" {if $row.Zeige_ArtNr == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_ArtNr" type="radio" value="0" {if $row.Zeige_ArtNr == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_Hersteller#}</td>
      <td class="row_right">
        <label><input name="Zeige_Hersteller" type="radio" value="1" {if $row.Zeige_Hersteller == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_Hersteller" type="radio" value="0" {if $row.Zeige_Hersteller == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopZeige_ErschienAm#}</td>
      <td class="row_right">
        <label><input name="Zeige_ErschienAm" type="radio" value="1" {if $row.Zeige_ErschienAm == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Zeige_ErschienAm" type="radio" value="0" {if $row.Zeige_ErschienAm == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopMenuLowAmount#}</td>
      <td class="row_right">
        <label><input name="menu_low_amount" type="radio" value="1" {if $row.menu_low_amount == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="menu_low_amount" type="radio" value="0" {if $row.menu_low_amount == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopSeenCat#}</td>
      <td class="row_right">
        <label><input name="seen_cat" type="radio" value="1" {if $row.seen_cat == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="seen_cat" type="radio" value="0" {if $row.seen_cat == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopVatInfoCat#}</td>
      <td class="row_right">
        <label><input name="vat_info_cat" type="radio" value="1" {if $row.vat_info_cat == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="vat_info_cat" type="radio" value="0" {if $row.vat_info_cat == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopShippingInfo#}</td>
      <td class="row_right">
        <label><input name="shipping_info" type="radio" value="1" {if $row.shipping_info == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="shipping_info" type="radio" value="0" {if $row.shipping_info == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#PriceGroup#}</td>
      <td class="row_right">
        <label><input name="PriceGroup" type="radio" value="1" {if $row.PriceGroup == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="PriceGroup" type="radio" value="0" {if $row.PriceGroup == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopVatInfoProduct#}</td>
      <td class="row_right">
        <label><input name="vat_info_product" type="radio" value="1" {if $row.vat_info_product == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="vat_info_product" type="radio" value="0" {if $row.vat_info_product == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#ShopPopupProduct#}</td>
      <td class="row_right">
        <label><input name="popup_product" type="radio" value="1" {if $row.popup_product == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="popup_product" type="radio" value="0" {if $row.popup_product == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#AvailType#}</td>
      <td class="row_right">
        <label><input name="AvailType" type="radio" value="0" {if $row.AvailType == 0}checked="checked"{/if} />{#AvailTypeJust#}</label>
        <label><input name="AvailType" type="radio" value="1" {if $row.AvailType == 1}checked="checked"{/if} />{#AvailTypeDiff#}</label>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#OnlyFhrase#}</td>
      <td class="row_right">
        <label><input name="OnlyFhrase" type="radio" value="1" {if $row.OnlyFhrase == 1}checked="checked"{/if} />{#OnlyFhraseWord#}</label>
        <label><input name="OnlyFhrase" type="radio" value="0" {if $row.OnlyFhrase == 0}checked="checked"{/if} />{#OnlyFhraseNotWord#}</label>
      </td>
    </tr>
  </table>
  <div class="subheaders">{#Settings_mailsettings#}</div>
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_EmailStdInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_EmailStandard#}</td>
      <td class="row_right"><label><input name="Email_Abs" type="text" class="input" id="Email_Abs" value="{$row.Email_Abs}" size="30" /></label></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_EmailStdFInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_EmailStandardFrom#}</td>
      <td class="row_right"><input name="Name_Abs" type="text" id="Name_Abs" class="input" value="{$row.Name_Abs}" size="30" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_EmailCopyInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_EmailOrders#}</td>
      <td class="row_right"><input name="Email_Bestellung" type="text" class="input" id="Email_Bestellung" value="{$row.Email_Bestellung}" size="30" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_SubjectOrder#}</td>
      <td class="row_right"><input name="Subjekt_Bestellung" id="Subjekt_Bestellung" type="text" class="input { required: true }" value="{$row.Subjekt_Bestellung}" size="30" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_SubjectOrder2#}</td>
      <td class="row_right"><input name="Subjekt_Best_Kopie" id="Subjekt_Best_Kopie" type="text" class="input { required: true }" value="{$row.Subjekt_Best_Kopie}" size="30" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"> {#Shop_Settings_OrderLogo#}</td>
      <td class="row_right"><input name="RechnungsLogo" id="RechnungsLogo" type="text" class="input { url: true }" value="{$row.RechnungsLogo}" size="30" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_settings_referedoptions|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_settings_refered#} </td>
      <td class="row_right"><textarea cols="" rows="" name="GefundenOptionen" class="input" style="width: 400px; height: 120px">{$row.GefundenOptionen}</textarea></td>
    </tr>
  </table>
  <div class="subheaders">
    {#Shop_Settings_Images#}
    <br />
    {#Shop_Settings_ImagesInf#}
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ThumbSmall|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ImagesSmall#}</td>
      <td class="row_right"><input onchange="document.getElementById('ThumbRenew_Yes').checked='true';" name="thumb_width_small" type="text" class="input" id="thumb_width_small" value="{$row.thumb_width_small}" size="10" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ThumbNorm|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ImagesNormal#} </td>
      <td class="row_right"><input onchange="document.getElementById('ThumbRenew_Yes').checked='true';" name="thumb_width_norm" type="text" class="input" id="thumb_width_norm" value="{$row.thumb_width_norm}" size="10" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ThumbMiddle|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ImagesMiddle#} </td>
      <td class="row_right"><input onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" name="thumb_width_middle" type="text" class="input" id="thumb_width_middle" value="{$row.thumb_width_middle}" size="10" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_ThumbBig|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_ImagesBig#}</td>
      <td class="row_right"><input onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" name="thumb_width_big" type="text" class="input" id="thumb_width_big" value="{$row.thumb_width_big}" size="10" maxlength="4" /></td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_ImagesQuality#}</td>
      <td class="row_right">
        <select style="width: 250px" onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" name="thumb_quality" id="thumb_quality">
          <option value="99" {if $row.thumb_quality == '99'}selected="selected"{/if}>{#Shop_Settings_Q1#}</option>
          <option value="90" {if $row.thumb_quality == '90'}selected="selected"{/if}>{#Shop_Settings_Q2#}</option>
          <option value="60" {if $row.thumb_quality == '60'}selected="selected"{/if}>{#Shop_Settings_Q3#}</option>
          <option value="35" {if $row.thumb_quality == '35'}selected="selected"{/if}>{#Shop_Settings_Q4#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_Watermark#}</td>
      <td class="row_right">
        <label><input onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" type="radio" name="Wasserzeichen" value="1" {if $row.Wasserzeichen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" type="radio" name="Wasserzeichen" value="0" {if $row.Wasserzeichen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    {if !empty($row.Wasserzeichen_Bild)}
      <tr>
        <td width="450" class="row_left">{#Shop_Settings_WatermarkCurrent#}</td>
        <td class="row_right">
          <img src="../uploads/watermarks/{$row.Wasserzeichen_Bild}?{$time}" alt="" border="" />
          <input type="hidden" name="watermark_old" value="{$row.Wasserzeichen_Bild}" />
        </td>
      </tr>
    {/if}
    <tr>
      <td width="450" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_WatermarkInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_WatermarkUp#}</td>
      <td class="row_right"><div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="300" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('watermark', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'watermarks', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#WatermarkPosistion#}</td>
      <td class="row_right">
        <select style="width: 250px" class="input" onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" name="Wasserzeichen_Position" id="Wasserzeichen_Position">
          <option value="bottom_right" {if $row.Wasserzeichen_Position == 'bottom_right'}selected="selected"{/if}>{#BottomRight#}</option>
          <option value="bottom_left" {if $row.Wasserzeichen_Position == 'bottom_left'}selected="selected"{/if}>{#BottomLeft#}</option>
          <option value="bottom_center" {if $row.Wasserzeichen_Position == 'bottom_center'}selected="selected"{/if}>{#BottomCenter#}</option>
          <option value="top_right" {if $row.Wasserzeichen_Position == 'top_right'}selected="selected"{/if}>{#TopRight#}</option>
          <option value="top_left" {if $row.Wasserzeichen_Position == 'top_left'}selected="selected"{/if}>{#TopLeft#}</option>
          <option value="top_center" {if $row.Wasserzeichen_Position == 'top_center'}selected="selected"{/if}>{#TopCenter#}</option>
          <option value="center_right" {if $row.Wasserzeichen_Position == 'center_right'}selected="selected"{/if}>{#CenterRight#}</option>
          <option value="center_left" {if $row.Wasserzeichen_Position == 'center_left'}selected="selected"{/if}>{#CenterLeft#}</option>
          <option value="center" {if $row.Wasserzeichen_Position == 'center'}selected="selected"{/if}>{#Center#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="450" class="row_left"> {#Settings_WmTrans#} </td>
      <td class="row_right"><input onchange="document.getElementById('ThumbRenew_Yes').checked = 'true';" name="WasserzeichenKomp" type="text" class="input" id="WasserzeichenKomp" value="{$row.WasserzeichenKomp}" size="10" maxlength="3" /> % </td>
    </tr>
    <tr>
      <td width="450" class="row_left">{#Shop_Settings_RenewImages#}</td>
      <td class="row_right">
        <label><input type="radio" name="ThumbRenew" id="ThumbRenew_Yes" value="1" />{#Yes#}</label>
        <label><input name="ThumbRenew" type="radio" id="radio2" value="0" checked="checked" />{#No#}</label>
      </td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Global_savesettings#}" />
</form>
