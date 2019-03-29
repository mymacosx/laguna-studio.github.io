<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$articles item=a name=art}
    $('#Erstellt_{$a->Id}').datepicker({
        duration: '',
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    {/foreach}
});
function buttonPopup() {
    $.colorbox({
        width: '90%',
        height: '90%',
        iframe: true,
        href: 'index.php?do=shop&sub=new&noframes=1'
    });
}
function checkmulti() {
    if (document.getElementById('deltrue').selected == true) {
        return confirm('{#Shop_articles_multidel_c#}') ? true : false;
    }
    if (document.getElementById('settoall').selected == true) {
        return confirm('{#Shop_articles_multi_setalllang#}') ? true : false;
    }
}
function getformelem() {
    $('#cstopt, #mover, #amountval, #prais, #round').hide();
    if (document.getElementById('cst').selected == true) {
        $('#cstopt').show();
    } else if (document.getElementById('shop_prais').selected == true) {
        $('#prais').show();
    } else if (document.getElementById('round_prais').selected == true) {
        $('#round').show();
    } else if (document.getElementById('move').selected == true) {
        $('#mover').show();
    } else if (document.getElementById('updateamount').selected == true) {
        $('#amountval').show();
    }
}
//-->
</script>

<div class="header">{#Shop_articles_articles#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$shop_search_small_categs}
  <div class="info_red"> {#ShopNoCateg#} </div>
{else}
  <div id="dialog">
    <div class="subheaders">
      <form method="get" action="index.php">
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width="100"><label for="lq">{#Search#}: </label></td>
            <td width="150"><input type="text" class="input" id="lq" name="query" value="{$smarty.request.query|default:''|sanitize}" style="width: 130px" /></td>
            <td width="100"><label for="lcateg">{#Global_Categ#}: </label></td>
            <td width="150">
              <select id="lcateg" name="kategorie" class="input" style="width: 134px;">
                <option value=""></option>
                {foreach from=$shop_search_small_categs item=scs}
                  <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if isset($smarty.request.kategorie) && $smarty.request.kategorie == $scs->catid}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
                {/foreach}
              </select>
            </td>
            <td width="70"><label for="laktiv">{#Global_Status#}: </label></td>
            <td>
              <select name="aktiv" id="laktiv" class="input" style="width: 134px">
                <option value=""></option>
                <option value="1" {if isset($smarty.request.aktiv) && $smarty.request.aktiv == 1}selected="selected" {/if}>{#Global_Active#}</option>
                <option value="0" {if isset($smarty.request.aktiv) && $smarty.request.aktiv == 0}selected="selected" {/if}>{#Shop_order_astatus_off#}</option>
              </select>
            </td>
          </tr>
          <tr>
            <td width="100"><label for="lpreis_von">{#Shop_order_asearch_price#}: </label></td>
            <td width="150"><input class="input" style="width: 59px" name="preis_von" id="lpreis_von" type="text" value="{$smarty.request.preis_von|default:'0.00'}" />
              -
              <input class="input" style="width: 59px" name="preis_bis" type="text" id="preis_bis" value="{$smarty.request.preis_bis|default:'30000.00'}" /></td>
            <td width="100"><label for="lverkauft_von">{#Shop_order_asearch_selled#}: </label></td>
            <td width="150"><input class="input" style="width: 59px" name="verkauft_von" id="lverkauft_von" type="text" value="{$smarty.request.verkauft_von|default:'0'}" />
              -
              <input class="input" style="width: 59px" name="verkauft_bis" type="text" id="preis_bis" value="{$smarty.request.verkauft_bis|default:'999'}" /></td>
            <td width="70"><label for="lhersteller">{#Manufacturer#}: </label></td>
            <td>
              <select name="hersteller" id="lhersteller" class="input" style="width: 134px">
                <option value=""></option>
                {foreach from=$shop_manufaturer item=man}
                  <option value="{$man.Id}" {if isset($smarty.request.hersteller) && $smarty.request.hersteller == $man.Id}selected="selected" {/if}>{$man.Name|sanitize}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="100"><label for="llagerv">{#Shop_articles_avbl#}: </label></td>
            <td width="150"><input class="input" style="width: 59px" name="lagerv" id="llagerv" type="text" value="{$smarty.request.lagerv|default:'0'}" />
              -
              <input class="input" style="width: 59px" name="lagerb" type="text" value="{$smarty.request.lagerb|default:'99999'}" /></td>
            <td width="100"><label for="lb_search">{#Shop_order_asearch_extrab#}: </label></td>
            <td width="150">
              <select name="b_search" class="input" id="lb_search" style="width: 134px">
                <option value="0" {if isset($smarty.request.b_search) && $smarty.request.b_search == 0}selected="selected" {/if}>{#No#}</option>
                <option value="1" {if isset($smarty.request.b_search) && $smarty.request.b_search == 1}selected="selected" {/if}>{#Yes#}</option>
              </select>
            </td>
            <td width="70"><label for="lb_offers">{#Shop_order_asearch_offers#}: </label></td>
            <td>
              <select name="b_offers" id="lb_offers" class="input" style="width: 134px;">
                <option value="0" {if isset($smarty.request.b_offers) && $smarty.request.b_offers == 0}selected="selected" {/if}>{#Global_All#}</option>
                <option value="1" {if isset($smarty.request.b_offers) && $smarty.request.b_offers == 1}selected="selected" {/if}>{#Shop_order_asearch_offersJust#}</option>
              </select>
            </td>
          </tr>
          <tr>
            <td width="100"><label for="llimit">{#DataRecords#}: </label></td>
            <td width="150"><input type="text" class="input" name="limit" id="limit" value="{$limit}" style="width: 30px" /></td>
            <td colspan="2" class="search_info" nowrap="nowrap">{#SearchResult#} {$num}</td>
            <td width="70">&nbsp;</td>
            <td><input type="submit" class="button" value="{#Shop_order_asearch_button#}" />&nbsp;&nbsp;<input type="button" class="button" onclick="location.href = 'index.php?do=shop&amp;sub=articles';" value="{#ButtonReset#}" /></td>
          </tr>
        </table>
        <input type="hidden" name="do" value="shop" />
        <input type="hidden" name="sub" value="articles" />
        <input type="hidden" name="search" value="1" />
        <input type="hidden" name="page" value="1" />
      </form>
    </div>
  </div>
  {if isset($smarty.request.search) && $smarty.request.search == 1}
    <fieldset>
      <legend>{#Shop_articles_multidel_t#}</legend>
      {if $smarty.post.Aktion}
        <strong>{if $emsg}{$emsg}{else}{#Shop_articles_multi_ok#}{/if}</strong>
        <br />
      {/if}
      <form onsubmit="return checkmulti();" method="post" action="">
        {strip}
          <select onchange="return getformelem();" class="input" name="Aktion" id="Aktion">
            <option>{#Shop_articles_multi_sel#}</option>
            <optgroup label="{#Shop_articles_multidel_setsta#}">
              <option value="setoffline" {if $smarty.request.Aktion == 'setoffline'}selected="selected"{/if}>{#Shop_setinactive#}</option>
              <option value="setonline" {if $smarty.request.Aktion == 'setonline'}selected="selected"{/if}>{#Shop_setactive#}</option>
            </optgroup>
            <optgroup label="{#ShopStartProducts#}">
              <option value="startoffline" {if $smarty.request.Aktion == 'startoffline'}selected="selected"{/if}>{#Shop_setinactive#}</option>
              <option value="startonline" {if $smarty.request.Aktion == 'startonline'}selected="selected"{/if}>{#Shop_setactive#}</option>
            </optgroup>
            <optgroup label="{#Shop_articles_multidel_setst#}">
              <option id="updateamount" value="updateamount">{#Shop_articles_multidel_setnewam#}</option>
              <option value="lagernull" {if $smarty.request.Aktion == 'lagernull'}selected="selected"{/if}>{#Shop_articles_multidel_setnull#}</option>
            </optgroup>
            <optgroup label="{#SMAShipping#}">
              <option id="cst" value="cst">{#Shop_articles_edit_readyforshipping#}</option>
            </optgroup>
            <optgroup label="{#Shop_articles_multidel_setav#}">
              {foreach from=$available item=av}
                <option id="s_{$av.Id}" value="{$av.Id}" {if $smarty.request.Aktion == $av.Id}selected="selected"{/if} > {$av.Name|sanitize}</option>
              {/foreach}
            </optgroup>
            <optgroup label="{#Shop_articles_multidel_setoth#}">
              <option id="shop_prais" value="shop_prais">{#ShopPrais#}</option>
              <option id="round_prais" value="round_prais">{#RoundPrais#}</option>
              <option id="move" value="move">{#Shop_articles_multidel_movethis#}</option>
              <option id="settoall" value="settoall" {if $smarty.request.Aktion == 'settoall'}selected="selected"{/if}>{#Shop_articles_multidel_settoalllang#}</option>
              <option id="deltrue" value="delete" {if $smarty.request.Aktion == 'delete'}selected="selected"{/if}>{#Shop_articles_del#}</option>
            </optgroup>
          </select>
        {/strip}
        <span id="prais" style="display: none">
          <select name="prais_type">
            <option value="plus" selected="selected">{#Shop_payment_ZZ#}</option>
            <option value="minus">{#Shop_payment_AZ#}</option>
          </select>
          <select name="prais_num">
            {section name=nax loop=101 step=1}
              <option value="{$smarty.section.nax.index}">{$smarty.section.nax.index}</option>
            {/section}
          </select>
          <strong>.</strong>
          <select name="prais_num2">
            {section name=nax2 loop=10 step=1}
              <option value="{$smarty.section.nax2.index}">{$smarty.section.nax2.index}</option>
            {/section}
          </select>
          <select name="prais_num3">
            {section name=nax3 loop=10 step=1}
              <option value="{$smarty.section.nax3.index}">{$smarty.section.nax3.index}</option>
            {/section}
          </select>
          <select name="prais_num4">
            {section name=nax4 loop=10 step=1}
              <option value="{$smarty.section.nax4.index}">{$smarty.section.nax4.index}</option>
            {/section}
          </select>
          <strong>%</strong>
          <select name="prais_variants">
            <option value="no">{#PraisVariantsNo#}</option>
            <option value="ok">{#PraisVariantsYes#}</option>
          </select>
        </span>
        <span id="round" style="display: none">
          <select name="round_num">
            <option value="0">{#RoundPraisNum#}</option>
            <option value="1">{#RoundPraisNum10#}</option>
          </select>
          <select name="round_variants">
            <option value="no">{#PraisVariantsNo#}</option>
            <option value="ok">{#PraisVariantsYes#}</option>
          </select>
        </span>
        <span id="cstopt"style="display: none">
          <select name="cst_val">
            {foreach from=$shippingTime item=st}
              <option value="{$st->Id}">{#Shop_articles_edit_readyforshipping#}: {$st->Name}</option>
            {/foreach}
          </select>
        </span>
        <span id="mover" style="display: none">
          <select name="Kategorie" class="input" style="width: 200px; padding: 0px">
            {foreach from=$shop_search_small_categs item=scs}
              <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if isset($smarty.request.kategorie) && $smarty.request.kategorie == $scs->catid}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
            {/foreach}
          </select>
        </span>
        <span id="amountval" style="display: none">
          <select class="input" name="updatenewamount">
            {section name=na loop=250 step=25}
              <option value="{$smarty.section.na.index+25}">{$smarty.section.na.index+25}</option>
            {/section}
            {section name=na2 loop=1000 step=250 start=250}
              <option value="{$smarty.section.na2.index+250}">{$smarty.section.na2.index+250}</option>
            {/section}
          </select>
        </span>
        <input type="submit" class="button" value="{#Shop_articles_multi_doaction#}" />
        <input name="multiaction" type="hidden" id="multiaction" value="1" />
      </form>
    </fieldset>
    <br />
  {/if}
  <form method="post" action="index.php?do=shop&amp;sub=articles">
    <input type="hidden" name="action" value="save" />
    <input name="aktiv" type="hidden" id="aktiv" value="{$smarty.request.aktiv|default:''}" />
    <input name="kategorie" type="hidden" id="kategorie" value="{$smarty.request.kategorie|default:''}" />
    <input name="hersteller" type="hidden" id="hersteller" value="{$smarty.request.hersteller|default:''}" />
    <input name="query" type="hidden" id="query" value="{$smarty.request.query|default:''}" />
    <input name="limit" type="hidden" id="limit" value="{$smarty.request.limit|default:''}" />
    <div class="maintable">
      <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
        <tr>
          <td width="22" class="headers">&nbsp;</td>
          <td width="185" class="headers"><a href="{$nav_string}&amp;order={$title_sort|default:'title_desc'}">{#Shop_articles_name#}</a></td>
          <td class="headers"><a href="{$nav_string}&amp;order={$date_sort|default:'date_asc'}">{#Global_Created#}</a></td>
          <td width="100" class="headers"><a href="{$nav_string}&amp;order={$artnr_sort|default:'artnr_asc'}">{#Shop_articles_number#}</a></td>
          <td width="20" class="headers">&nbsp;</td>
          <td class="headers" width="55"><a href="{$nav_string}&amp;order={$price_sort|default:'price_desc'}">{#Products_price#}</a></td>
          <td class="headers" width="30">{#Shop_PriceEk#}</td>
          <td class="headers" width="30"><a href="{$nav_string}&amp;order={$store_sort|default:'store_desc'}">{#Shop_articles_avbl#}</a></td>
          <td class="headers" width="40"><a href="{$nav_string}&amp;order={$hits_sort|default:'hits_desc'}">{#Global_Hits#}</a></td>
          <td class="headers" width="40"><a href="{$nav_string}&amp;order={$ordered_sort|default:'ordered_desc'}">{#Shop_articles_selled#}</a></td>
          <td width="50" class="headers"><a href="{$nav_string}&amp;order={$categ_sort|default:'categ_asc'}">{#Global_Categ#}</a></td>
          <td class="headers">&nbsp;</td>
        </tr>
        {foreach from=$articles item=a name=art}
          <tr class="{cycle values='second,first'}">
            <td width="22">
              <a title="{#Shop_articles_edit#}" class="colorbox" href="?do=shop&amp;sub=edit_article&amp;id={$a->Id}&amp;noframes=1&amp;langcode=1"><img src="{$a->Bild_Klein}" alt="" border="0" /></a>
            </td>
            <td width="180">
              <input type="hidden" name="Id[{$a->Id}]" value="{$a->Id}" />
              <input class="input" style="width: 170px; padding: 2px; " type="text" name="Titel[{$a->Id}]" value="{$a->Titel|sanitize}" />
            </td>
            <td width="55"><input class="input" style="width: 57px; padding: 2px; " type="text" name="Erstellt[{$a->Id}]" id="Erstellt_{$a->Id}" value="{$a->Erstellt|date_format: '%d.%m.%Y'}" /></td>
            <td width="100"><a href="../index.php?p=shop&amp;action=showproduct&amp;id={$a->Id}&amp;cid={$a->Kategorie}" target="_blank" style="border: 0px">{$a->Artikelnummer}</a></td>
            <td width="1" align="center"><img class="stip" title={if $a->IsOffer}"{$lang.Shop_order_asearch_offersIs|sanitize}"{else}"{$lang.Shop_order_asearch_offersIsNot|sanitize}"{/if} src="{$imgpath}/{if $a->IsOffer}offer{else}offer_no{/if}.png" alt="" /></td>
            <td width="55">{$a->Preis_Liste|numformat}</td>
            <td width="30">{$a->Preis_EK|numformat}</td>
            <td width="30"><input name="Lagerbestand[{$a->Id}]" type="text" class="input" style="width: 30px" value="{$a->Lagerbestand}" /></td>
            <td width="40"><input name="Klicks[{$a->Id}]" type="text" class="input" style="width: 30px" value="{$a->Klicks}" /></td>
            <td width="40"><input name="Verkauft[{$a->Id}]" type="text" class="input" style="width: 40px" value="{$a->Verkauft}" /></td>
            <td width="50">
              <select name="Kategorie[{$a->Id}]" class="input" style="width: 100px;">
                {foreach from=$shop_search_small_categs item=scs}
                  <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if $a->Kategorie == $scs->catid}selected="selected" {assign var=catname value=$scs->visible_title}{/if}>{$scs->visible_title|specialchars}</option>
                {/foreach}
              </select>
            </td>
            <td nowrap="nowrap">&nbsp;&nbsp;
              {if perm('shop_articleedit')}
                <a class="colorbox stip" title="{$lang.Shop_articles_edit|sanitize}" href="?do=shop&amp;sub=edit_article&amp;id={$a->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
                {/if}
                {if perm('shop_variants')}
                <a class="colorbox stip" title="{$lang.Shop_variants|sanitize}" href="?do=shop&amp;sub=article_variants&amp;id={$a->Id}&amp;cat={$a->Kategorie}&amp;noframes=1&amp;name={$a->CatName}"><img class="absmiddle" src="{$imgpath}/various.png" alt="" border="0" /></a>
                {/if}
                {if perm('shop_stprices')}
                <a class="colorbox stip" title="{$lang.Shop_articles_stprices|sanitize}" href="?do=shop&amp;sub=article_stprices&amp;id={$a->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/prices_multi.png" alt="" border="0" /></a>
                {/if}
                {if perm('shop_copyarticle')}
                <a class="colorbox stip" title="{$lang.Shop_articles_copy|sanitize}" href="?do=shop&amp;sub=copy_article&amp;id={$a->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/copy.png" alt="" border="0" /></a>
                {/if}
                {if perm('shop_downloads')}
                <a class="colorbox stip" title="{$lang.Shop_downloads_name|sanitize}" href="?do=shop&amp;sub=esd_downloads&amp;id={$a->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/download.png" alt="" border="0" /></a>
                {/if}
                {if $a->Aktiv == 1}
                <a class="stip" title="{$lang.Shop_setinactive|sanitize}" href="?do=shop&amp;sub=openclose&amp;id={$a->Id}&amp;status=0&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>
                {else}
                <a class="stip" title="{$lang.Shop_setactive|sanitize}" href="?do=shop&amp;sub=openclose&amp;id={$a->Id}&amp;status=1&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>
                {/if}
                {if $a->Votes>=1}
                <a class="colorbox stip" title="{$lang.Shop_prodvotes|sanitize}" href="?do=shop&amp;sub=prodvotes&amp;id={$a->Id}&amp;name={$a->Titel|sanitize}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/prod_votes.png" alt="" border="0" /></a>
                {else}
                <img class="absmiddle stip" title="{$lang.Shop_prodvotes_no|sanitize}" src="{$imgpath}/no_prod_votes.png" alt="" border="0" />
              {/if}
              {if perm('shop_delete')}
                <a class="stip" title="{$lang.Shop_articles_del|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$a->Titel|jsspecialchars}');" href="?do=shop&amp;sub=delete_article&amp;id={$a->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                {/if}
            </td>
          </tr>
        {/foreach}
      </table>
    </div>
    <div class="button_save_div">
      <input type="submit" class="button" value="{#Save#}" />
      <input title="{#Shop_articles_addnew#}" onclick="buttonPopup();" class="button_second" type="button" value="{#Shop_articles_addnew#}" />
    </div>
  </form>
  <div class="navi_div">
    <strong>{#GoPagesSimple#}</strong>
    <form method="get" action="index.php">
      <input type="text" class="input" style="width: 25px; text-align: center" name="page" value="{$smarty.request.page|default:'1'}" />
      <input type="hidden" name="do" value="shop" />
      <input type="hidden" name="sub" value="articles" />
      <input type="hidden" name="b_offers" value="{$smarty.request.b_offers|default:'0'}" />
      <input type="hidden" name="b_search" value="{$smarty.request.b_offers|default:'0'}" />
      <input type="hidden" name="lagerv" value="{$smarty.request.lagerv|default:'0'}" />
      <input type="hidden" name="lagerb" value="{$smarty.request.lagerb|default:'9999'}" />
      <input type="hidden" name="query" value="{$smarty.request.query|default:''}" />
      <input type="hidden" name="verkauft_von" value="{$smarty.request.verkauft_von|default:'0'}" />
      <input type="hidden" name="verkauft_bis" value="{$smarty.request.verkauft_bis|default:'9999'}" />
      <input type="hidden" name="limit" value="{$smarty.request.limit|default:'15'}" />
      <input type="submit" class="button" value="{#GoPagesButton#}" />
    </form>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    {if !empty($pages)}
      <strong>{#GoPages#}</strong>
      {$pages}
    {/if}
    <br />
    <br />
    <strong>{#Shop_articles_export#}</strong>
    <form method="post" action="{page_link}&amp;export=1">
      <label><input name="export_format" type="radio" value="csv" checked="checked" /> CSV</label>
      <label><input name="export_format" type="radio" value="text" /> Текст</label>&nbsp;
        {if perm('export_articles')}
        <input type="submit" class="button" value="{#Shop_order_oexport_button#}" />
      {else}
        <input type="button" onclick="alert('{#NoPerm#}');" class="button" value="{#Shop_order_oexport_button#}" />
      {/if}
    </form>
  </div>
{/if}
