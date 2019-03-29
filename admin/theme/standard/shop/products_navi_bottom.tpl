{assign var=categ value=$smarty.request.cid|default:'0'}
<div class="shop_tips round">
  {if get_active('shop_newinshop_navi')}
    <a class="page_navigation" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;cid={$categ}&amp;limit={$smarty.request.limit|default:$plim}">{#Shop_newProducts#}</a>
  {/if}
  {if get_active('shop_angebote')}
    <a class="page_navigation" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;offers=1&amp;cid={$categ}&amp;limit={$smarty.request.limit|default:$plim}">{#Shop_Offers#}</a>
  {/if}
  {if get_active('shop_topseller')}
    <a class="page_navigation" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;topseller=1&amp;cid={$categ}&amp;limit={$smarty.request.limit|default:$plim}">{#Shop_Topseller#}</a>
  {/if}
  {if $shopsettings->menu_low_amount == 1}
    <a class="page_navigation" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;lowamount=1&amp;cid={$categ}&amp;limit={$smarty.request.limit|default:$plim}">{#Shop_lowProducts#}</a>
  {/if}
</div>