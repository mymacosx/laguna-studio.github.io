<form method="post" action="index.php">
  <input type="hidden" name="p" value="shop" />
  <input type="hidden" name="area" value="{$area}" />
  <input type="hidden" name="action" value="shoporder" />
  <input type="hidden" name="subaction" value="final" />
  {if !$loggedin}
    <input type="hidden" name="order" value="guest" />
  {/if}
  <div class="shop_order_final_div">
    <table  cellspacing="0" cellpadding="1">
      <tr>
        <td valign="top"><input name="agb_ok[]" type="checkbox" value="1" {if $agb_accept_checked == 1}checked="checked"{/if} /></td>
        <td valign="top"><span class="{if $agb_error == 1}error_font{else}{/if}">{$INF_MSG}</span></td>
      </tr>
    </table>
  </div>
  {if $GefundenDurch}
    <br />
    <div class="shop_order_final_div">
      <div style="float: left"><strong>{#Shop_referedby#}</strong></div>
      <div style="float: right">
        <select name="GefundenDurch" style="width: 200px">
          {foreach from=$GefundenDurch item=gd}
            <option value="{$gd|sanitize}">{$gd|sanitize}</option>
          {/foreach}
        </select>
      </div>
      <div class="clear"></div>
    </div>
  {/if}
  <div style="text-align: right; padding: 5px">
    <input class="shop_order_send_button" type="submit" value="{#Shop_sendorder_button#}" />
  </div>
</form>
<br />
