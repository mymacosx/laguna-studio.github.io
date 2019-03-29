<br />
{if $no_nettodisplay  != 1}
  {if $price_onlynetto != 1}
    <div align="center"><span class="sup">*</span> {#Shop_info_incl_vat#} <a href="index.php?{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}blanc=1&amp;{/if}p=shop&amp;action=shippingcost">{#Shop_info_shipping#}</a></div>
    {/if}
    {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
    <div align="center"><span class="sup">*</span> {#Shop_info_excl_vat#} <a href="index.php?{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}blanc=1&amp;{/if}p=shop&amp;action=shippingcost">{#Shop_info_shipping#}</a></div>
  {/if}
{/if}
