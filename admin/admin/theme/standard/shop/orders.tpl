<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#StartDate, #EndDate').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});
//-->
</script>

{if !isset($smarty.request.noframes) || $smarty.request.noframes != 1}
  <div class="header">{#Shop_title_orders#}</div>
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <div class="subheaders">
    <form method="get" action="">
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td width="100"><label for="lquery">{#Search#}</label></td>
          <td width="150" nowrap="nowrap"><input type="text" class="input" name="query" id="lquery" value="{$smarty.request.query|default:''|escape: html}" style="width: 130px" /></td>
          <td width="80"><label for="SSelect">{#Global_Status#}</label></td>
          <td width="150"><select id="SSelect" name="status" class="input" style="width: 134px">
              <option id="s_n" value="0"></option>
              <option id="s_wait" value="wait" {if isset($smarty.request.status) && $smarty.request.status == 'wait'}selected="selected" {/if}>{#Shop_order_status_wait#}</option>
              <option id="s_ok" value="ok" {if isset($smarty.request.status) && $smarty.request.status == 'ok'}selected="selected" {/if}>{#Shop_order_status_ok#}</option>
              <option id="s_ok_send" value="oksend" {if isset($smarty.request.status) && $smarty.request.status == 'oksend'}selected="selected" {/if}>{#Shop_order_status_oksend#}</option>
              <option id="s_ok_sendparts" value="oksendparts" {if isset($smarty.request.status) && $smarty.request.status == 'oksendparts'}selected="selected" {/if}>{#Shop_order_status_oksendparts#}</option>
              <option id="s_progress" value="progress" {if isset($smarty.request.status) && $smarty.request.status == 'progress'}selected="selected" {/if}>{#Shop_order_status_progress#}</option>
              <option id="s_failed" value="failed" {if isset($smarty.request.status) && $smarty.request.status == 'failed'}selected="selected" {/if}>{#Shop_order_status_failed#}</option>
            </select>
          </td>
          <td width="80"><label for="label">{#Shop_showmoney_payment#}</label></td>
          <td>
            <select name="ZahlungsId" class="input" style="width: 134px">
              <option value=""></option>
              {foreach from=$payments item=p}
                <option value="{$p.Id}" {if isset($smarty.request.ZahlungsI) && $smarty.request.ZahlungsI == $p.Id}selected="selected"{/if}>{$p.Name}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td width="100"><label for="label">{#Shop_order_osummftill#}</label></td>
          <td width="150" nowrap="nowrap">
            <input name="BetragVon" id="BetragVon" type="text" class="input" value="{$smarty.request.BetragVon|default:'0'}" maxlength="10" style="width: 59px" /> -
            <input name="BetragBis" type="text" class="input" id="BetragBis" style="width: 59px" value="{$smarty.request.BetragBis|default:'10000'}" maxlength="10" />
          </td>
          <td width="80"><label for="StartDate">{#Date_since#}</label></td>
          <td width="150"><input readonly="readonly" style="width: 130px" class="input" name="StartDate" id="StartDate" value="{if empty($smarty.request.StartDate)}{$StartDate|date_format: "%d.%m.%y"}{else}{$smarty.request.StartDate}{/if}" /></td>
          <td width="80">{#Date_till#}</td>
          <td><input readonly="readonly" style="width: 130px" class="input" name="EndDate" id="EndDate" value="{if empty($smarty.request.EndDate)}{$smarty.now|date_format: "%d.%m.%y"}{else}{$smarty.request.EndDate}{/if}" /></td>
        </tr>
        <tr>
          <td width="100"><label for="lVersandId">{#Shop_order_oshipper_l#}</label></td>
          <td width="150" nowrap="nowrap">
            <select name="VersandId" id="lVersandId" class="input" style="width: 134px">
              <option value=""></option>
              {foreach from=$shipper item=v}
                <option value="{$v.Id}" {if isset($smarty.request.VersandId) && $smarty.request.VersandId == $v.Id}selected="selected"{/if}>{$v.Name}</option>
              {/foreach}
            </select>
          </td>
          <td width="80"><label for="llimit">{#DataRecords#}</label></td>
          <td width="150"><input type="text" class="input" name="limit" id="llimit" value="{$limit}" style="width: 30px" /></td>
          <td width="80">&nbsp;</td>
          <td><input type="submit" class="button" value="{#Shop_order_osearch_s#}" /></td>
        </tr>
      </table>
      <input type="hidden" name="do" value="shop" />
      <input type="hidden" name="sub" value="orders" />
      <input type="hidden" name="search" value="1" />
      <input type="hidden" name="page" value="1" />
    </form>
  </div>
{/if}
<div class="maintable">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="20" align="center" nowrap="nowrap" class="headers"><a href="{$nav_string}&amp;order={$date_sort|default:'date_asc'}">{#Shop_order_onum#}</a></td>
      <td class="headers" nowrap="nowrap"><a href="{$nav_string}&amp;order={$ordernum_sort|default:'ordernum_asc'}">{#Shop_order_otransid#}</a></td>
      <td align="center" class="headers" width="80" nowrap="nowrap"><a href="{$nav_string}&amp;order={$summ_sort|default:'summ_desc'}">{#Shop_order_osumm#}</a></td>
      <td align="center" class="headers" width="70" nowrap="nowrap"><a href="{$nav_string}&amp;order={$date_sort|default:'date_asc'}">{#Global_Date#}</a></td>
      <td align="center" class="headers" width="90" nowrap="nowrap"><a href="{$nav_string}&amp;order={$customer_sort|default:'customer_asc'}">{#Shop_order_ocustomer#}</a></td>
      <td align="center" width="60" class="headers" nowrap="nowrap"><a href="{$nav_string}&amp;order={$customerid_sort|default:'customerid_asc'}">{#Shop_order_ocnumber#}</a></td>
      <td align="center" colspan="2" class="headers" nowrap="nowrap"><a href="{$nav_string}&amp;order={$payment_sort|default:'payment_asc'}">{#Shop_showmoney_payment#}</a></td>
      <td align="center" width="90" class="headers" nowrap="nowrap"><a href="{$nav_string}&amp;order={$shipper_sort|default:'shipper_asc'}">{#Shop_order_oshipper#}</a></td>
      <td align="center" width="10" valign="middle" class="headers" nowrap="nowrap"><a href="{$nav_string}&amp;order={$status_sort|default:'status_asc'}">{#Global_Status#}</a></td>
      <td align="center" width="120" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$orders item=o name=orders}
      <tr class="{cycle values='second,first'}">
        <td align="center" width="20">{$o->Id}</td>
        <td><a  class="colorbox stip" title="{$o->positions|sanitize}" href="?do=shop&amp;sub=edit_order&amp;id={$o->Id}&amp;noframes=1&amp;status={$o->Status}">{$o->TransaktionsNummer}</a></td>
        <td align="center" width="80">{$o->Betrag|numformat}</td>
        <td align="center" width="70" class="stip" title="{$o->Datum|date_format: "%d/%m/%Y - %H:%M"}">{$o->Datum|date_format: "%d/%m/%y"}</td>
        <td align="center" width="90">
          {if $o->Benutzer != '0'}
            <a title="{#User_edit#}" class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$o->Benutzer}&amp;noframes=1">{$o->Rng_Nachname|sanitize} {$o->Rng_Vorname|truncate: 2: "."}</a>
          {else}
            {$o->Rng_Nachname|sanitize} {$o->Rng_Vorname|truncate: 2: "."} (g)
          {/if}
        </td>
        <td align="center" width="50">
          {if $o->Benutzer == 0}
            &nbsp;
          {else}
            <a class="colorbox stip" title="{$lang.User_edit}" href="index.php?do=user&amp;sub=edituser&amp;user={$o->Benutzer}&amp;noframes=1">{$o->Benutzer}</a>
          {/if}
        </td>
        <td align="center" width="10">
            {if $o->Payment == 1}
            <img class="stip" title="{$lang.Shop_articles_selled}" src="{$imgpath}/ok.gif" alt="" border="0" />
            {/if}
        </td>
        <td align="center" width="150" class="stip" title="{$o->payment|sanitize}">
            {$o->payment|slice:25:'...'}
        </td>
        <td align="center" width="90" class="stip" title="{$o->shipper|sanitize}">{$o->shipper|truncate: 12: ''}</td>
        <td style="width: 12px; text-align: center"><div class="shop_status_{$o->Status}">&nbsp;</div></td>
        <td width="120" nowrap="nowrap">
          <a class="colorbox stip" title="{$lang.Shop_edit_order|sanitize}" href="?do=shop&amp;sub=edit_order&amp;id={$o->Id}&amp;noframes=1&amp;status={$o->Status}"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
            {if $o->Benutzer>=1}
            <a class="colorbox stip" title="{$lang.Shop_edit_orderDownloads|sanitize}" href="?do=shop&amp;sub=user_downloads&amp;user={$o->Benutzer}&amp;name={$o->Rng_Nachname|sanitize}&amp;noframes=1"><img src="{$imgpath}/download{if !$o->downloads}_none{/if}.png" alt="" border="0" /></a>
            {/if}
            {if $o->Benutzer>=1}
            <a class="colorbox stip" title="{$lang.Shop_PersonalDownloads|sanitize}" href="?do=shop&amp;sub=user_downloads_personal&amp;order={$o->Id}&amp;user={$o->Benutzer}&amp;name={$o->Rng_Nachname|sanitize}&amp;noframes=1"><img src="{$imgpath}/download_personal.png" alt="" border="0" /></a>
            {/if}
            {if $o->Benutzer<1}
            <a class="colorbox stip" title="{$lang.Shop_convertGuestToUser|sanitize}" href="index.php?do=user&amp;sub=convertguesttouser&amp;order={$o->Id}&amp;noframes=1"><img src="{$imgpath}/convert_touser.png" alt="" border="0" /></a>
            {else}
            <img class="stip" title="{$lang.Shop_convertGuestToUserN|sanitize}" src="{$imgpath}/convert_touser_no.png" alt="" border="0" />
          {/if}
          <a class="stip" title="{$lang.Shop_cancel_order|sanitize}" href="?do=shop&amp;sub=cancel_order&amp;id={$o->Id}&amp;backurl={$backurl}"><img src="{$imgpath}/stop.png" alt="" border="0" /></a>
            {if perm('del_order')}
            <a onclick="return confirm('{#ConfirmGlobal#}{#Shop_order_onum#} {$o->Id} - {#Shop_order_otransid#} {$o->TransaktionsNummer}');" class="stip" title="{$lang.Global_Delete|sanitize}" href="?do=shop&amp;sub=delzakaz&amp;id={$o->Id}&amp;backurl={$backurl}"><img src="{$imgpath}/delete.png" alt="" border="0" /></a>
            {/if}
        </td>
      </tr>
    {/foreach}
  </table>
</div>
<br />
<div class="navi_div"><strong>{#GoPagesSimple#}</strong>
  <form method="get" action="index.php">
    <input type="text" class="input" style="width: 25px; text-align: center" name="page" value="{$smarty.request.page|default:'1'}" />
    <input type="hidden" name="do" value="shop" />
    <input type="hidden" name="sub" value="orders" />
    <input type="hidden" name="query" value="{$smarty.request.query|default:''}" />
    <input type="hidden" name="status" value="{$smarty.request.status|default:''}" />
    <input type="hidden" name="ZahlungsId" value="{$smarty.request.ZahlungsId|default:''}" />
    <input type="hidden" name="StartDate" value="{if empty($smarty.request.StartDate)}{$StartDate|date_format: "%d.%m.%Y"}{else}{$smarty.request.StartDate}{/if}" />
    <input type="hidden" name="EndDate" value="{if empty($smarty.request.EndDate)}{$smarty.now|date_format: "%d.%m.%Y"}{else}{$smarty.request.EndDate}{/if}" />
    <input type="hidden" name="BetragVon" value="{$smarty.request.BetragVon|default:'0'}" />
    <input type="hidden" name="BetragBis" value="{$smarty.request.BetragBis|default:'10000'}" />
    <input type="hidden" name="VersandId" value="{$smarty.request.VersandId|default:''}" />
    <input type="hidden" name="search" value="1" />
    <input type="hidden" name="limit" value="{$smarty.request.limit|default:'15'}" />
    {if isset($smarty.request.noframes) && $smarty.request.noframes == 1}
      <input type="hidden" name="noframes" value="1" />
    {/if}
    <input type="submit" class="button" value="{#GoPagesButton#}" />
  </form>
  &nbsp;&nbsp;
  {if !empty($pages)}
    <strong>{#GoPages#}</strong>
    {$pages}
  {/if}
</div>
<br />
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="160"><table border="0" cellpadding="1" cellspacing="0">
        <tr>
          <td width="20"><div class="shop_status_wait">&nbsp;</div></td>
          <td>{#Shop_order_status_wait#}</td>
        </tr>
        <tr>
          <td><div class="shop_status_ok">&nbsp;</div></td>
          <td>{#Shop_order_status_ok#}</td>
        </tr>
        <tr>
          <td><div class="shop_status_oksendparts">&nbsp;</div></td>
          <td>{#Shop_order_status_oksendparts#}</td>
        </tr>
        <tr>
          <td><div class="shop_status_oksend">&nbsp;</div></td>
          <td>{#Shop_order_status_oksend#}</td>
        </tr>
        <tr>
          <td><div class="shop_status_progress">&nbsp;</div></td>
          <td>{#Shop_order_status_progress#}</td>
        </tr>
        <tr>
          <td><div class="shop_status_failed">&nbsp;</div></td>
          <td>{#Shop_order_status_failed#}</td>
        </tr>
      </table>
    </td>
    <td width="170" valign="top">
      <img src="{$imgpath}/ok.gif" alt="" border="0" /> {#Shop_articles_selled#}
      <br />
      <br />
      {#Shop_order_olegend#}
      <br />
      <br />
    </td>
    <td width="420" valign="top" style="padding: 0px">
      {if $orders}
        {if !isset($smarty.request.noframes) || $smarty.request.noframes != 1}
          <fieldset>
            <legend>{#Shop_order_oexport#}</legend>
            <form method="post" action="{page_link}&amp;export=1">
              <label><input name="export_format" type="radio" value="csv" />CSV&nbsp;</label>
              <label><input name="export_format" type="radio" value="text" />Text&nbsp;</label>
              <label><input name="export_format" type="radio" value="html" checked="checked" />HTML&nbsp;</label>
                {if perm('export_orders')}
                <input type="submit" class="button" style="margin-bottom: 0px" value="{#Shop_order_oexport_button#}" />
              {else}
                <input type="button" onclick="alert('{#NoPerm#}');" class="button" style="margin-bottom: 0px" value="{#Shop_order_oexport_button#}" />
              {/if}
              {if perm('del_order')}
                <input type="button" class="button" onclick="if (confirm('{#DelZakazInf#}')) location.href = 'index.php?do=shop&amp;sub=delallzakaz';" value="{#DelZakaz#}" />
              {/if}
            </form>
          </fieldset>
        {/if}
      {/if}
    </td>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<br />
