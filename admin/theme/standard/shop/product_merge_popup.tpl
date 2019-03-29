{if get_active('shop_merge')}
  <div class="popup_header">{#Shop_mergeTitle#}</div>
  <div class="popup_content" style="padding: 5px">
    <div class="popup_box">
      {#Shop_mergeHeadInf#}
      <br />
      {if !$smarty.session.cid}
        <div class="h2">{#Shop_mergeEmpty#}</div>
      {else}
        <strong>{#Shop_mergeList#}</strong>&nbsp;&nbsp;
        <select class="input" style="width: 250px" name="cid" id="cid_s" onchange="eval(this.options[this.selectedIndex].value);">
          {foreach from=$cats item=c}
            {assign var=session_cid value=$c->Id}
            {if $smarty.session.cid[$session_cid]}
              <option value="location.href='{page_link|replace: 'redir=1': 'redir=0'}&categ={$c->Id}'" {if $smarty.request.categ == $c->Id}selected="selected" {/if}>{$c->CatName}</option>
            {/if}
          {/foreach}
        </select>
        &nbsp;
        <input class="button" type="button" onclick="eval(document.getElementById('cid_s').options[document.getElementById('cid_s').selectedIndex].value);" value="{#GlobalShow#}" />
        <br />
        <br />
        {if $merged}
          {foreach from=$cats item=c}
            {if $smarty.request.categ == $c->Id}
              <div class="box_innerhead">{$c->CatName}</div>
            {/if}
          {/foreach}
          <table width="100%" border="0" cellspacing="1" cellpadding="0" class="shop_merge_table">
            <tr>
              <td class="shop_merge_header">&nbsp;</td>
              {foreach from=$merged item=p}
                <td class="shop_merge_header"><table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="10"><a href="#" onclick="window.opener.location.href = '{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&blanc=1{/if}';"><img src="{$p.Bild}" alt="" border="0" /></a></td>
                      <td>{if $p.not_on_store == 1}&nbsp;{else}{$p.VIcon}{/if}</td>
                    </tr>
                  </table>
                </td>
              {/foreach}
            </tr>
            <tr>
              <td class="shop_merge_prodname">&nbsp;</td>
              {foreach from=$merged item=p}
                <td class="shop_merge_prodname">{$p.Titel|sanitize}</td>
              {/foreach} </tr>
            <td class="shop_merge_left">{#Preis_Shop#}</td>
            {foreach from=$merged item=p}
              <td valign="top" nowrap="nowrap" class="{cycle name='fs' values='shop_merge_first,shop_merge_second'}">
                {if $shopsettings->PreiseGaeste == 1 || $loggedin}
                  {if $p.Preis > 0}
                    {if $p.no_vars == 1}
                      {if $p.not_on_store == 1}
                        <div class="shop_lowamount"><strong>{#Shop_notAvailableInf#}</strong></div>
                          {/if}
                          {#Shop_priceNow#}&nbsp;&nbsp;
                      <div class="h2">{$p.Preis|numformat} {$currency_symbol}</div>
                      {if $price_onlynetto != 1}
                        <br />
                        <small>
                          {#Shop_netto#} {$p.netto_price|numformat} {$currency_symbol}
                          <br />
                          ({#Shop_icludes#} {$p.product_ust}% {#Shop_vat#})
                        </small>
                      {/if}
                      {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
                        <br />
                        <small>
                          ({$p.price_ust_ex|numformat} {$currency_symbol})
                          <br />
                          {#Shop_exclVat#} {$p.product_ust}% {#Shop_vat#}
                        </small>
                      {/if}
                    {/if}
                    {if $p.no_vars != 1}
                      {if !empty($smarty.request.first_value)} {#Shop_priceNow#}&nbsp;&nbsp; {else} {#Shop_priceFrom#}&nbsp;&nbsp; {/if}
                      <div class="h2">{$p.Preis|numformat} {$currency_symbol}</div>
                      {if $price_onlynetto != 1}
                        <br />
                        <small>
                          ({#Shop_icludes#} {$p.product_ust}% {#Shop_vat#})
                          <br />
                          {#Shop_netto#} {$p.netto_price|numformat} {$currency_symbol}
                        </small>
                      {/if}
                      {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
                        <br />
                        <small>
                          {#Shop_exclVat#} {$p.product_ust}% {#Shop_vat#}
                          <br />
                          ({$p.price_ust_ex|numformat} {$currency_symbol})
                        </small>
                      {/if}
                    {/if}
                  {else}
                    <br />
                    <div class="h2">{#Zvonite#}</div>
                  {/if}
                {else}
                  <strong>{#Shop_prices_justforUsers#}</strong>
                {/if}
              </td>
            {/foreach}
            </tr>
            {if $det_spez.Spez_1}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_1|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs1' values='shop_merge_first,shop_merge_second'}">{$p.Spez_1|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_2}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_2|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs2' values='shop_merge_first,shop_merge_second'}">{$p.Spez_2|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_3}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_3|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs3' values='shop_merge_first,shop_merge_second'}">{$p.Spez_3|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_4}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_4|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs4' values='shop_merge_first,shop_merge_second'}">{$p.Spez_4|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_5}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_5|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs5' values='shop_merge_first,shop_merge_second'}">{$p.Spez_5|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_6}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_6|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs6' values='shop_merge_first,shop_merge_second'}">{$p.Spez_6|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_7}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_7|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs7' values='shop_merge_first,shop_merge_second'}">{$p.Spez_7|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_8}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_8|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs8' values='shop_merge_first,shop_merge_second'}">{$p.Spez_8|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_9}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_9|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs9' values='shop_merge_first,shop_merge_second'}">{$p.Spez_9|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_10}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_10|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs10' values='shop_merge_first,shop_merge_second'}">{$p.Spez_10|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_11}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_11|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs11' values='shop_merge_first,shop_merge_second'}">{$p.Spez_11|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_12}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_12|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs12' values='shop_merge_first,shop_merge_second'}">{$p.Spez_12|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_13}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_13|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs13' values='shop_merge_first,shop_merge_second'}">{$p.Spez_13|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_14}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_14|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs14' values='shop_merge_first,shop_merge_second'}">{$p.Spez_14|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            {if $det_spez.Spez_15}
              <tr>
                <td class="shop_merge_left">{$det_spez.Spez_15|specialchars}</td>
                {foreach from=$merged item=p}
                  <td class="{cycle name='fs15' values='shop_merge_first,shop_merge_second'}">{$p.Spez_15|specialchars|default:'-'}</td>
                {/foreach}
              </tr>
            {/if}
            <tr>
              <td class="shop_merge_left">&nbsp;</td>
              {foreach from=$merged item=p}
                <td class="{cycle name='fs16' values='shop_merge_first,shop_merge_second'}"><strong><a style="text-decoration: none" href="javascript: void(0)" onclick="location.href = 'index.php?delproduct={$p.Id}&redir=0&p=misc&do=mergeproduct&cid={$smarty.request.categ}&categ={$smarty.request.categ}&red=0';"><img class="absmiddle" src="{$imgpath}/shop/delete.png" border="0" alt="" />&nbsp;{#Shop_delItem#}</a></strong></td>
              {/foreach}
            </tr>
            <tr>
              <td class="shop_merge_left">&nbsp;</td>
              {foreach from=$merged item=p}
                <td class="{cycle name='fs17' values='shop_merge_first,shop_merge_second'}">
                  {if $p.no_vars == 1 && $p.not_on_store != 1 && $p.Preis > 0}
                    {if $smarty.request.reload == 1}
<script type="text/javascript">
<!-- //
window.opener.location.reload();
//-->
</script>
                    {/if}
                    <form method="post" name="merge_to_basket_{$p.Id}" action="index.php?p=shop&amp;area={$area}">
                      <input name="amount" type="hidden" value="1" />
                      <input type="hidden" name="action" value="to_cart" />
                      <input type="hidden" name="redir" value="{page_link}&reload=1" />
                      <input type="hidden" name="product_id" value="{$p.Id}" />
                      <strong><a style="text-decoration: none" href="javascript: void(0);" onclick="javascript: document.forms['merge_to_basket_{$p.Id}'].submit();"><img class="absmiddle" src="{$imgpath}/shop/shoppingcart_plus.png" border="0" alt="" />&nbsp;{#Shop_toBasket#}</a></strong>
                    </form>
                  {elseif $p.no_vars != 1 && $p.not_on_store != 1}
                    <strong><a style="text-decoration: none" href="javascript: void(0);" onclick="window.opener.location.href = '{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}';
                        window.close();"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" border="0" alt="" />&nbsp;{#buttonDetails#}</a></strong>
                      {/if}
                </td>
              {/foreach}
            </tr>
          </table>
        {else}
          <strong>{#Shop_mergeEmptyList#}</strong>
        {/if}
      {/if}
    </div>
  </div>
{/if}
