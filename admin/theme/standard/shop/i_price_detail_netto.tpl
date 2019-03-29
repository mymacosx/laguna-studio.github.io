{if !empty($p.EinheitBezug)}
  {assign var=Mth value=$p.EinheitBezug/$p.EinheitCount}
  {if isset($smarty.request.action) && $smarty.request.action == 'showproduct'}
    <input type="hidden" name="h_vat" id="hidden_vat" value="{$p.product_ust_js}" />
    <input type="hidden" name="h_count" id="hidden_count" value="{$p.EinheitCount}" />
  {/if}
  <span style="display: none" id="netto_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.netto_price|numformat}</span> {if $p.EinheitOut}
  <br />
  {#Shop_icludesB#} {$p.EinheitCount|decimal3} {$p.EinheitOut|specialchars} / {$p.EinheitBezug|decimal3} {$p.Einheit|specialchars} =
  <span id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.Preis*$Mth|numformat}</span> {$currency_symbol}&nbsp;({#Shop_netto#}
  <span id="prodonce_display_netto{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.netto_price*$Mth|numformat}</span> {$currency_symbol})
{else}
  <span style="display: none" id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}"></span><span style="display: none" id="prodonce_display_netto{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}"></span>
  {/if}
  {else}
    {if isset($smarty.request.action) && $smarty.request.action == 'showproduct'}
    <input type="hidden" name="h_vat" id="hidden_vat" value="{$p.product_ust_js}" />
    <input type="hidden" name="h_count" id="hidden_count" value="{$p.EinheitCount}" />
  {/if}
  <span style="display: none" id="netto_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.netto_price|numformat}</span> {if $p.EinheitOut}
  <br />
  {#Shop_icludesB#} {$p.EinheitCount|decimal3} {$p.EinheitOut|specialchars} / 1 {$p.Einheit|specialchars} =
  <span id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.EinheitPreisEinzel|numformat}</span> {$currency_symbol}&nbsp;({#Shop_netto#}
  <span id="prodonce_display_netto{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}">{$p.EinheitPreisEinzelNetto|numformat}</span> {$currency_symbol})
  {else}
    <span style="display: none" id="prodonce_display{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}"></span><span style="display: none" id="prodonce_display_netto{if $smarty.request.action != 'showproduct'}_{$p.Id}{/if}"></span>
  {/if}
{/if}
