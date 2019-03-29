<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#order').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
});
function update_message() {
    if(document.getElementById('s_ok').selected == true) {
        var Inner = '{$JSText.ok}';
        var Subject = '{$lang.Shop_status_t_ok|default:''|unspecialchars}';
    } else if(document.getElementById('s_oksendparts').selected == true) {
        var Inner = '{$JSText.oksendparts}';
        var Subject = '{$lang.Shop_status_t_partsdone|default:''|unspecialchars}';
    } else if(document.getElementById('s_wait').selected == true) {
        var Inner = '{$JSText.wait}';
        var Subject = '{$lang.Shop_status_t_wait|default:''|unspecialchars}';
    } else if(document.getElementById('s_ok_send').selected == true) {
        var Inner = '{$JSText.alldone}';
        var Subject = '{$lang.Shop_status_t_alldone|default:''|unspecialchars}';
    } else if(document.getElementById('s_progress').selected == true) {
        var Inner = '{$JSText.progress}';
        var Subject = '{$lang.Shop_status_t_progress|default:''|unspecialchars}';
    } else if(document.getElementById('s_failed').selected == true) {
        var Inner = '{$JSText.failed}';
        var Subject = '{$lang.Shop_status_t_failed|default:''|unspecialchars}';
    } else if(document.getElementById('s_n').selected == true) {
        var Inner = '';
        var Subject = '';
    }
    document.getElementById('MSubject').value = Subject;
    CKEDITOR.instances.MailKunde.setData(Inner);
}
//-->
</script>

<form method="post" action="?do=shop&amp;sub=edit_order&amp;id={$smarty.request.id}&amp;save=1&amp;noframes=1">
  <div id="order">
    <ul>
      <li><a href="#ord">{#Shop_order_confirmation#}</a></li>
        {if !empty($order.Order_Type)}
        <li><a href="#pay">{#Schet#}</a></li>
        {/if}
      <li><a href="#det">{#GlobalDetails#}</a></li>
      <li><a href="#otr">{#Shop_Tracking#}</a></li>
      <li><a href="#sended_refounded">{#Shop_orderedit_remains#}</a></li>
      <li><a href="#ordh">{#Shop_orderedit_history#}</a></li>
    </ul>
    <div id="ord">
      <iframe src="index.php?do=shop&amp;sub=getHtmlOrder&amp;id={$smarty.request.id}" class="iframe_order"></iframe>
    </div>
    {if !empty($order.Order_Type)}
      <div id="pay">
        <iframe src="index.php?do=shop&amp;sub=getHtmlPay&amp;id={$smarty.request.id}" class="iframe_order"></iframe>
      </div>
    {/if}
    <div id="det">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td width="200" class="row_left">{#Shop_articles_selled#}</td>
          <td class="row_right">
            <select name="Payment" class="input" style="width: 250px">
              <option value="0"{if $order.Payment == 0} selected="selected"{/if}>{#No#}</option>
              <option value="1"{if $order.Payment == 1} selected="selected"{/if}>{#Yes#}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#Shop_order_status#}</td>
          <td class="row_right">
            <select id="SSelect" name="Status" class="input" style="width: 250px" onchange="update_message();">
              <option id="s_wait" value="wait" {if $order.Status == 'wait'}selected="selected" {/if}>{#Shop_order_status_wait#}</option>
              <option id="s_progress" value="progress" {if $order.Status == 'progress'}selected="selected" {/if}>{#Shop_order_status_progress#}</option>
              <option id="s_ok" value="ok" {if $order.Status == 'ok'}selected="selected" {/if}>{#Shop_order_status_ok#}</option>
              <option id="s_oksendparts" value="oksendparts" {if $order.Status == 'oksendparts'}selected="selected" {/if}>{#Shop_order_status_oksendparts#}</option>
              <option id="s_ok_send" value="oksend" {if $order.Status == 'oksend'}selected="selected" {/if}>{#Shop_order_status_oksend#}</option>
              <option id="s_failed" value="failed" {if $order.Status == 'failed'}selected="selected" {/if}>{#Shop_order_status_failed#}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#Shop_order_email_subject#}</td>
          <td class="row_right"><input name="BetreffKunde" type="text" class="input" id="MSubject" style="width: 99%;" value="{$SText|default:''|unspecialchars}" /></td>
        </tr>
        <tr>
          <td width="200" class="row_left">
            {#Shop_order_email_text#}
            <br />
            <small>{#Shop_order_email_text_inf#}</small>
          </td>
          <td class="row_right">{$InfoText} </td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#Shop_order_customer_message#}</td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 99%; height: 80px" name="KundenNachricht" id="KundenNachricht">{$order.KundenNachricht|escape: html}</textarea></td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#Shop_order_admin_message#}</td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 99%; height: 80px" name="Bemerkung" id="Bemerkung">{$order.Bemerkung|escape: html}</textarea></td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#Shop_customer_ip#}</td>
          <td class="row_right"><a href="http://www.status-x.ru/webtools/whois/{$order.Ip}/">{$order.Ip}</a></td>
        </tr>
      </table>
    </div>
    <div id="otr">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td width="200" class="row_left">{#Shop_TrackingWith#}</td>
          <td class="row_right">
            <select name="Tracking_Id">
              <option value="0">-</option>
              {foreach from=$Tracking item=Tr}
                <option value="{$Tr->Id}" {if $Tr->Id == $order.Tracking_Id}selected="selected"{/if}>{$Tr->Name}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Shop_TrackingCode#}</td>
          <td class="row_right"><input type="text" class="input" name="Tracking_Code" value="{$order.Tracking_Code}" /></td>
        </tr>
      </table>
    </div>
    <div id="sended_refounded" style="height: 500px; overflow: auto">
      <div class="subheaders">{#Shop_orderedit_remainsinfo#}</div>
      {foreach from=$Oitems item=i}
        <div class="{cycle values='second,first'}">
          <table width="100%" border="0" cellpadding="4" cellspacing="0">
            <tr>
              <td width="10"><input type="checkbox" {if $Verschickt == 'leer' || in_array($i->Artikelnummer, $Verschickt)}checked="checked"{/if} name="Sended[{$i->Id}]" id="s{$i->Id}" value="{$i->Artikelnummer}" /></td>
              <td width="120"><strong><label style="cursor: pointer" for="s{$i->Id}">{$i->Artikelnummer}</label></strong></td>
              <td><label style="cursor: pointer" for="s{$i->Id}">{$i->ArtName}</label></td>
            </tr>
          </table>
        </div>
      {/foreach}
    </div>
    <div id="ordh" style="height: 500px; overflow: auto">
      <div class="maintable">
        <table class="tableborder" width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td width="100" class="headers"><strong>{#Global_Date#}</strong></td>
            <td class="headers" nowrap="nowrap"><strong>{#Shop_order_email_subject#}</strong></td>
            <td class="headers" nowrap="nowrap"><strong>{#Shop_order_email_text#}</strong></td>
            <td class="headers" nowrap="nowrap"><strong>{#Shop_order_admin_message#}</strong></td>
          </tr>
          {foreach from=$history item=orditem}
            <tr class="{cycle values='second,first'}">
              <td width="100" valign="top">{$orditem->Datum|date_format: "%d.%m.%y, %H:%M"}</td>
              <td valign="top">{$orditem->Subjekt|sanitize|default:'-'}</td>
              <td valign="top"><div style="height: 140px; border: 1px solid #ccc; background: #fff; padding: 5px; overflow: auto;">{$orditem->StatusText}</div></td>
              <td valign="top"><div style="height: 140px; border: 1px solid #ccc; background: #fff; padding: 5px; overflow: auto;">{$orditem->Kommentar|sanitize|default:'-'}</div></td>
            </tr>
          {/foreach}
        </table>
      </div>
    </div>
  </div>
  <br />
  <label><input name="SendEmail" type="checkbox" id="SendEmail" value="1" checked="checked" />{#Shop_send_emailorder#}</label>&nbsp;&nbsp;
  <input type="hidden" name="OEmail" value="{$order.Email}" />
  <textarea cols="" rows="" style="display: none" id="ohtml" name="ohtml">{$order.Bestellung}</textarea>
  {if !empty($order.Order_Type)}
    <textarea cols="" rows="" style="display: none" id="othtml" name="othtml">{$order.Order_Type}</textarea>
  {/if}
  <input type="submit" class="button" value="{#Shop_order_editbutton#}" />
  <input type="button" class="button" value="{#Shop_print_order#}" onclick="printWindow('ohtml');" />
  {if !empty($order.Order_Type)}
    <input type="button" class="button" value="{#PrintSchet#}" onclick="printWindow('othtml');" />
  {/if}
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="button" class="button" value="{#Close#}" onclick="closeWindow(true);" />
</form>
