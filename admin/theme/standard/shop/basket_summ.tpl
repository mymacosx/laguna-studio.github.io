<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      {if !$smarty.session.shopstep && $product_array}
        <div style="margin-bottom: 5px"><a style="text-decoration: none" href="javascript: void(0);" onclick="if (confirm('{#Shop_del_basketc#}')) location.href = 'index.php?d=1&amp;p=shop&amp;action=delbasket';"><img src="{$imgpath}/shop/delete.png" alt="" class="absmiddle" /> {#Shop_del_basket#}</a></div>
      {/if}
    </td>
    <td width="320" valign="top">
      <table width="100%" align="right" cellpadding="2" cellspacing="0">
        {if $show_vat_table == 1 && $shopsettings->NettoPreise != 1}
          <tr>
            <td nowrap="nowrap">{#Shop_f_summ_brutto#}: </td>
            <td align="right" nowrap="nowrap">{$basket_products_brutto|numformat} {$currency_symbol}</td>
          </tr>
        {/if}
        <tr>
          <td nowrap="nowrap">
            {if $show_vat_table == 1}
              {#Shop_f_summ_netto#}:
            {else}
              {#Shop_f_summ_brutto#}:
            {/if}
          </td>
          <td align="right" nowrap="nowrap">{$basket_products_price_netto|numformat} {$currency_symbol}</td>
        </tr>
        {if $smarty.session.print_coupon_price == 1 && $smarty.session.coupon_val>0}
          <tr>
            {if $smarty.session.coupon_typ == 'pro'}
              <td nowrap="nowrap"> {#Shop_o_coupon#}: <strong>{$smarty.session.coupon_code}</strong> (- {$smarty.session.coupon_val|numformat}%)</td>
              <td align="right" nowrap="nowrap"> - {$smarty.session.price_netto_coupon|numformat} {$currency_symbol}</td>
            {elseif $smarty.session.coupon_typ == 'wert'}
              <td nowrap="nowrap"> {#Shop_o_coupon#}: <strong>{$smarty.session.coupon_code}</strong></td>
              <td align="right" nowrap="nowrap"> - {$smarty.session.price_netto_coupon|numformat} {$currency_symbol}</td>
            {/if}
          </tr>
          <tr>
            <td nowrap="nowrap">{#Shop_f_summ_current#}: </td>
            <td align="right" nowrap="nowrap">{$smarty.session.price_netto_zwi|numformat} {$currency_symbol}</td>
          </tr>
        {/if}
        {if $show_vat_table == 1}
          {foreach from=$ust_vals item=ust}
            {assign var=ust_code value=$ust->Wert}
            {if $smarty.session.$ust_code}
              <tr>
                <td class="shop_basket_summ_small" nowrap="nowrap">{#Shop_f_exclVat#} {$ust->Wert}%: </td>
                <td align="right" class="shop_basket_summ_small" nowrap="nowrap">{$smarty.session.$ust_code|numformat} {$currency_symbol}</td>
              </tr>
            {/if}
          {/foreach}
        {/if}
        {if $smarty.session.shipping_summ}
          <tr>
            <td class="shop_basket_summ_small" nowrap="nowrap">{#Shop_shipping_cost#}: </td>
            <td class="shop_basket_summ_small" align="right" nowrap="nowrap"> {$smarty.session.shipping_summ|numformat} {$currency_symbol} </td>
          </tr>
        {/if}
        {if $smarty.session.payment_summ_extra}
          <tr>
            <td class="shop_basket_summ_small" nowrap="nowrap">
              {if $smarty.session.payment_summ_mipu == 'zzgl'}
                {#Shop_f_excl_pm#}:
              {else}
                {#Shop_f_icl_pm#}:
              {/if}
            </td>
            <td class="shop_basket_summ_small" align="right" nowrap="nowrap">
              {$smarty.session.payment_summ_symbol} {$smarty.session.payment_summ_extra|numformat} {$currency_symbol}
            </td>
          </tr>
        {/if}
        <tr>
          <td class="shop_summ_final" nowrap="nowrap"><strong style="font-size: 120%">{#Shop_f_summ_ovall#}: </strong></td>
          <td align="right" class="shop_summ_final" nowrap="nowrap"><strong style="font-size: 120%">{$basket_products_price|numformat} {$currency_symbol}</strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr>
          <td nowrap="nowrap">{#Shop_parts_weight#}: </td>
          <td align="right" nowrap="nowrap">{#Shop_ca#} {$smarty.session.gewicht_detail|numformat} {#Shop_weights_unit#} </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
