<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form  method="post" action="">
  <table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr>
      <td width="250" class="row_left"><label for="pq">{#Shop_showmoney_from#}</label></td>
      <td class="row_right"> {html_select_date time=$ZeitStart prefix="start_" end_year="+0" start_year="-10" display_days=true  month_format="%m" reverse_years=false day_size=1 field_order=DMY all_extra=""} </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_showmoney_to#}</td>
      <td class="row_right"> {html_select_date time=$ZeitEnde prefix="end_" end_year="+0" start_year="-10" display_days=true  month_format="%m" reverse_years=false day_size=1 field_order=DMY all_extra=""} </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_showmoney_payment#}</td>
      <td class="row_right">
        <select class="input" style="width: 260px" name="ZahlungsId">
          <option value="egal">{#Global_All#}</option>
          {foreach from=$paymentMethods item=pm}
            <option value="{$pm.Id}" {if isset($smarty.request.ZahlungsId) && $smarty.request.ZahlungsId == $pm.Id}selected="selected"{/if}>{$pm.Name}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_showmoney_shipper#}</td>
      <td class="row_right">
        <select class="input" style="width: 260px" name="VersandId">
          <option value="egal">{#Global_All#}</option>
          {foreach from=$shippingMethods item=zm}
            <option value="{$zm.Id}" {if isset($smarty.request.VersandId) && $smarty.request.VersandId == $zm.Id}selected="selected"{/if}>{$zm.Name}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_payment_ID#}</td>
      <td class="row_right"><input style="width: 160px" class="input" name="Benutzer" type="text" value="{$smarty.request.Benutzer|default:''}" /></td>
    </tr>
    <tr>
      <td width="250" class="row_left">&nbsp;</td>
      <td class="row_right">
        <input type="submit" class="button" value="{#Search#}" />
        <input name="search" type="hidden" id="search" value="1" />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="headers">{#Shop_ordersS#}</td>
    </tr>
    <tr>
      <td class="row_left">{#Shop_showmoney_okall#}</td>
      <td class="row_right shop_status_ok_text">{$row->GesamtUmsatz} {$currency}</td>
    </tr>
    <tr>
      <td class="row_left">{#Shop_order_status_wait#}</td>
      <td class="row_right shop_status_wait_text">{$row->GesamtUmsatzWartend} {$currency}</td>
    </tr>
    <tr>
      <td class="row_left">{#Shop_order_status_progress#}</td>
      <td class="row_right shop_status_progress_text">{$row->GesamtUmsatzBearbeitung} {$currency}</td>
    </tr>
    <tr>
      <td class="row_left">{#Shop_order_status_failed#}</td>
      <td class="row_right shop_status_failed_text">{$row->GesamtFehlgeschlagen} {$currency}</td>
    </tr>
    <tr>
      <td class="row_left">{#Shop_showmoney_possible#}</td>
      <td class="row_right shop_ovall_text"> {$row->GesamtUmsatzAlle} {$currency}</td>
    </tr>
  </table>
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
</form>
