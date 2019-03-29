{if !empty($ShopInfo)}
  {$ShopInfo}
{/if}
{$new_in_shop}
{$topseller_in_shop}
{$angebote_in_shop}
{if $shopsettings->seen_cat == 1}
  {$small_seen_products}
{/if}
{if $shopsettings->vat_info_cat == 1}
  {include file="$incpath/shop/vat_info.tpl"}
{/if}