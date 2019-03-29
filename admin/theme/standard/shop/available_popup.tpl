<div class="shop_merge_body">
  <div class="shop_product_title_detail">
    <h1>{#Shop_Availablility#}</h1>
  </div>
  <table width="100%" cellspacing="0" cellpadding="4">
    {foreach from=$icons item=icon}
      <tr>
        <td class="{if $smarty.request.id == $icon.Id}shop_available_selected{/if}" width="10"><img src="{$imgpath}/shop/{$icon.Bild}" alt="" /></td>
        <td class="{if $smarty.request.id == $icon.Id}shop_available_selected{/if}">{$icon.Titel}</td>
      </tr>
    {/foreach}
  </table>
  <br />
  <div style="padding: 10px; text-align: center">
    <input class="button" onclick="window.close()" type="button" value="{#WinClose#}" />
  </div>
</div>
