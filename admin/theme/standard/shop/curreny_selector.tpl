{if !isset($smarty.request.action) || isset($smarty.request.action) && ($smarty.request.action != 'shoporder' || $smarty.request.action != 'showbasket')}
{if $curr_change == 1 && ($cu_array.Waehrung_2 || $cu_array.Waehrung_3)}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_currency', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_currency" title="{#Shop_changeCurrency#}">
    <div class="boxes_body">
      {if $cu_array.Waehrung_1}
        {if (isset($smarty.request.currency) && $smarty.request.currency == 1) || (isset($smarty.session.currency) && $smarty.session.currency == 1) || empty($smarty.session.currency)}
          <strong>{$cu_array.Waehrung_1}</strong>
        {else}
          <a href="index.php?p=shop&amp;currency=1">{$cu_array.Waehrung_1}</a>
        {/if}
      {/if}
      {if $cu_array.Waehrung_2}
        &nbsp;|&nbsp;
        {if (isset($smarty.request.currency) && $smarty.request.currency == 2) || (isset($smarty.session.currency) && $smarty.session.currency == 2)}
          <strong>{$cu_array.Waehrung_2}</strong>
        {else}
          <a href="index.php?p=shop&amp;currency=2">{$cu_array.Waehrung_2}</a>
        {/if}
      {/if}
      {if $cu_array.Waehrung_3}
        &nbsp;|&nbsp;
        {if (isset($smarty.request.currency) && $smarty.request.currency == 3) || (isset($smarty.session.currency) && $smarty.session.currency == 3)}
          <strong>{$cu_array.Waehrung_3}</strong>
        {else}
          <a href="index.php?p=shop&amp;currency=3">{$cu_array.Waehrung_3}</a>
        {/if}
      {/if}
      {assign var='foo' value='1'}
      {if $cu_array.Waehrung_2}
        <br />
        <br />
        <strong>1 {$cu_array.WaehrungSymbol_2} = {($foo/$cu_array.Multiplikator_2)|numformat} {$cu_array.WaehrungSymbol_1}</strong>
      {/if}
      {if $cu_array.Waehrung_3}
        <br />
        <strong>1 {$cu_array.WaehrungSymbol_3} = {($foo/$cu_array.Multiplikator_3)|numformat} {$cu_array.WaehrungSymbol_1}</strong>
      {/if}
    </div>
  </div>
</div>
{/if}
{/if}
