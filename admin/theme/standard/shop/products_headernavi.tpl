{if empty($no_colums)}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#avail_click_{$position}').on('click', function(e) {
        $('#popup_{$position}').slideToggle(300);
        e.stopPropagation();
    });
    $(document).on('click', function() {
        $('#popup_{$position}').slideUp(300);
    });
});
//-->
</script>

{assign var=avail value=''}
{if !empty($smarty.request.avail)}
  {assign var=avail value='&amp;avail='|cat: $smarty.request.avail}
{/if}
<div class="shop_header_inf">
  <table width="100%" cellspacing="0" cellpadding="0">
    {if $pages_inf}
      <tr>
        <td class="shop_header_inf_pages">
          {#Goto#}
          <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="shop_q" value="{$smarty.request.shop_q|default:'empty'}" />
            <input type="hidden" name="man" value="{$smarty.request.man|default:'0'}" />
            <input type="hidden" name="p" value="shop" />
            <input type="hidden" name="action" value="showproducts" />
            <input type="hidden" name="cid" value="{$smarty.request.cid|default:'0'}" />
            <input type="text" name="page" class="input" style="width: 25px" value="{$smarty.request.page|default:'1'}" />
            <input type="hidden" name="limit" value="{$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}" />
            <input type="hidden" name="pf" value="{$smarty.request.pf|default:'0.00'}" />
            <input type="hidden" name="pt" value="{$smarty.request.pt|default:'10000.00'}" />
            <input type="hidden" name="list" value="{$smarty.request.list|default:$shopsettings->Sortable_Produkte}" />
            <input type="hidden" name="s" value="{$smarty.request.s|default:'0'}" />
            <input type="hidden" name="avail" value="{$smarty.request.avail|default:'0'}" />
            <input type="submit" class="button" value="{#GotoButton#}" />
          </form>
        </td>
        <td colspan="2" class="shop_header_inf_pages" align="right" nowrap="nowrap">
          {if !empty($pages)}
            {$pages}
          {/if}
        </td>
      </tr>
    {/if}
    {assign var=shop_q value=$smarty.request.shop_q|urlencode|default:'empty'}
    <tr>
      <td class="shop_header_inf_pages">
        {#DataRecords#}:
        <a class="{if $smarty.request.limit == 6}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=6&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">6</a>
        <a class="{if $smarty.request.limit == 10}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=10&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">10</a>
        <a class="{if $smarty.request.limit == 20 || empty($smarty.request.limit)}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=20&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">20</a>
        <a class="{if $smarty.request.limit == 50}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=50&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">50</a>
        <a class="{if $smarty.request.limit == 100}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=100&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">100</a>
        <a class="{if $smarty.request.limit == 200}page_active{else}page_navigation{/if}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit=200&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}{$avail}">200</a>
      </td>
      <td align="right" class="shop_header_inf_pages">
        {if isset($smarty.request.action) && $smarty.request.action == 'start'}
          &nbsp;
        {else}
          {#SortBy#}:
          <a class="page_navigation" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$title_sort}&amp;s={$smarty.request.s|default:'0'}{$avail}">{#SortName#} <img src="{$imgpath_page}{$img_title_sort|default:"sorter_none.png"}" alt="" /></a>
          <a class="page_navigation" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$art_sort}&amp;s={$smarty.request.s|default:'0'}{$avail}">{#SortArtikel#} <img src="{$imgpath_page}{$img_art_sort|default:"sorter_none.png"}" alt="" /></a>
          <a class="page_navigation" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$price_sort}&amp;s={$smarty.request.s|default:'0'}{$avail}">{#Shop_sortPrice#} <img src="{$imgpath_page}{$img_price_sort|default:"sorter_none.png"}" alt="" /></a>
          <a class="page_navigation" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$date_sort}&amp;s={$smarty.request.s|default:'0'}{$avail}">{#SortDate#} <img src="{$imgpath_page}{$img_date_sort|default:"sorter_none.png"}" alt="" /></a>
          <a class="page_navigation" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$klick_sort}&amp;s={$smarty.request.s|default:'0'}{$avail}">{#SortKlicks#} <img src="{$imgpath_page}{$img_klick_sort|default:"sorter_none.png"}" alt="" /></a>
          {/if}
      </td>
      <td align="right" class="shop_header_inf_pages avail_cont">
        <img id="avail_click_{$position}" src="{$imgpath}/shop/avail-{$smarty.request.avail|default:'0'}.png" alt="" class="absmiddle stip" title="{#ShopShowLegend#}" />
        <div class="avail_popup" id="popup_{$position}">
          <a class="stip" title="{#ShopAllProducts#}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=0"><img src="{$imgpath}/shop/avail-0.png" alt="" class="absmiddle" /></a>
          <a class="stip" title="{$available_array.0->Name|tooltip}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=1"><img src="{$imgpath}/shop/avail-1.png" alt="" class="absmiddle" /></a>
            {if $shopsettings->AvailType == 1}
            <a class="stip" title="{$available_array.4->Name|tooltip}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=5"><img src="{$imgpath}/shop/avail-5.png" alt="" class="absmiddle" /></a>
            <a class="stip" title="{$available_array.1->Name|tooltip}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=2"><img src="{$imgpath}/shop/avail-2.png" alt="" class="absmiddle" /></a>
            <a class="stip" title="{$available_array.2->Name|tooltip}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=3"><img src="{$imgpath}/shop/avail-3.png" alt="" class="absmiddle" /></a>
            <a class="stip" title="{$available_array.3->Name|tooltip}" href="index.php?shop_q={$smarty.request.shop_q|urlencode|default:'empty'}&amp;man={$smarty.request.man|default:'0'}&amp;p=shop&amp;action=showproducts&amp;cid={$smarty.request.cid|default:'0'}&amp;page={$smarty.request.page|default:'1'}&amp;limit={$smarty.request.limit|default:'20'}&amp;pf={$smarty.request.pf}&amp;pt={$smarty.request.pt}&amp;list={$smarty.request.list|default:$shopsettings->Sortable_Produkte}&amp;s={$smarty.request.s|default:'0'}&amp;avail=4"><img src="{$imgpath}/shop/avail-4.png" alt="" class="absmiddle" /></a>
            {/if}
        </div>
      </td>
    </tr>
  </table>
</div>
{/if}