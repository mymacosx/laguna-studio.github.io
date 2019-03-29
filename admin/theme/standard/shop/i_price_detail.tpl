{if isset($smarty.request.action) && $smarty.request.action == 'showproduct'}
  <input type="hidden" name="h_vat" id="hidden_vat" value="{$p.product_ust_js}" />
  <input type="hidden" name="h_count" id="hidden_count" value="{$p.EinheitCount}" />
  <span id="netto_display" style="display: none"></span> <span id="prodonce_display_netto" style="display: none"></span> <span id="prodonce_display" style="display: none"></span>
{/if}
{if !empty($p.EinheitBezug)}
  {assign var=Mth value=$p.EinheitBezug / $p.EinheitCount}
  {if $hide_vat_details != 1}
    {if $p.EinheitOut}
      {#Shop_icludesB#} {$p.EinheitCount|decimal3} {$p.EinheitOut|specialchars} / {$p.EinheitBezug|decimal3} {$p.Einheit|specialchars} = <span id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.Preis*$Mth|numformat}</span> {$currency_symbol}
    {/if}
  {/if}
{else}
  {if $hide_vat_details != 1}
    {if $p.EinheitOut}
      <br />
      {#Shop_icludesB#} {$p.EinheitCount|decimal3} {$p.EinheitOut|specialchars}, 1 {$p.Einheit|specialchars} = <span id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.EinheitPreisEinzel|numformat}</span> {$currency_symbol}
    {/if}
  {/if}
{/if}
