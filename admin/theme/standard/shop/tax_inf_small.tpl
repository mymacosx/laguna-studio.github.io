{if $shopsettings->NettoKlein == 1}
  {include file="$incpath/shop/vat_info_small.tpl"}
{/if}
{if $shopsettings->shipping_info == 1}
{if (isset($shipping_free) && $shipping_free == 1) || $p.shipping_free == 1}
  <br />
  {#Shop_freeshipping#}
{else}
  <br />
  {#Shop_shipping_extra#}
  <a class="colorbox_small" href="index.php?p=misc&amp;do=shippingcost">{#Shop_showshipping_cost#}</a>
{/if}
{/if}
