{if $smarty.request.s != 1 && ($sub_categs || $newin_shop || $offers_shop  || $topseller_shop)}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#container-options ul.rounded').tabs();
});
//-->
</script>

<div id="container-options">
  <ul class="rounded">
    {if $sub_categs}
      <li><a href="#opt-1"><span>{#AllCategs#}</span></a></li>
    {/if}
    {if $newin_shop}
      <li><a href="#opt-2"><span>{#Shop_NewProducts#}</span></a></li>
    {/if}
    {if $offers_shop}
      <li><a href="#opt-3"><span>{#Shop_Offers#}</span></a></li>
    {/if}
    {if $topseller_shop}
      <li><a href="#opt-4"><span>{#Shop_Topseller#}</span></a></li>
    {/if}
  </ul>
  {if $sub_categs}
    <div id="opt-1" class="ui-tabs-panel-content">
      {include file="$incpath/shop/shop_browse_tab_categs.tpl"}
    </div>
  {/if}
  {if $newin_shop}
    <div id="opt-2" class="ui-tabs-panel-content">{$newin_shop}</div>
  {/if}
  {if $offers_shop}
    <div id="opt-3" class="ui-tabs-panel-content">{$offers_shop}</div>
  {/if}
  {if $topseller_shop}
    <div id="opt-4" class="ui-tabs-panel-content">{$topseller_shop}</div>
  {/if}
</div>
<div class="clear"></div>
<br />
{/if}
