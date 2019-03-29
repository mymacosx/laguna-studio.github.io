{if perm('shop') && admin_active('shop') && $count > 0}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('shop_navi', 'shop_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="shop_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/shoppingcart.png" alt="" /> {#StartNewOrders#} - <a href="index.php?do=shop&amp;sub=orders">{#Global_ShowAll#} ({$count})</a>
</div>
<div id="shop_open" class="sysinfos">
  <div class="maintable">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      {foreach from=$orders item=o name=orders}
        <tr class="{cycle values='second,first'}">
          <td width="100">{$o->Betrag|numformat}</td>
          <td width="60" align="center" class="stip" title="{$o->Datum|date_format: "%d/%m/%Y - %H:%M"}">{$o->Datum|date_format: "%d/%m/%y"}</td>
          <td>
            {if $o->Benutzer != '0'}
              <a title="{#User_edit#}" class="colorbox stip" href="index.php?do=user&amp;sub=edituser&amp;user={$o->Benutzer}&amp;noframes=1">{$o->Rng_Nachname|sanitize} {$o->Rng_Vorname|truncate: 2: "."}</a>
            {else}
              {$o->Rng_Nachname|sanitize} {$o->Rng_Vorname|truncate: 2: "."} (g)
            {/if}
          </td>
          <td width="60" nowrap="nowrap">
            <a class="colorbox stip" title="{$lang.Shop_edit_order|sanitize}" href="?do=shop&amp;sub=edit_order&amp;id={$o->Id}&amp;noframes=1&amp;status={$o->Status}"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
              {if $o->Benutzer >= 1}
              <a class="colorbox stip" title="{$lang.Shop_edit_orderDownloads|sanitize}" href="?do=shop&amp;sub=user_downloads&amp;user={$o->Benutzer}&amp;name={$o->Rng_Nachname|sanitize}&amp;noframes=1"><img src="{$imgpath}/download{if !$o->downloads}_none{/if}.png" alt="" border="0" /></a>
              {/if}
              {if $o->Benutzer < 1}
              <a class="colorbox stip" title="{$lang.Shop_convertGuestToUser|sanitize}" href="index.php?do=user&amp;sub=convertguesttouser&amp;order={$o->Id}&amp;noframes=1"><img src="{$imgpath}/convert_touser.png" alt="" border="0" /></a>
              {else}
              <img class="stip" title="{$lang.Shop_convertGuestToUserN|sanitize}" src="{$imgpath}/convert_touser_no.png" alt="" border="0" />
              {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
</div>
{/if}
