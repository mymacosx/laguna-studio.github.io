{if $popup == 1}
  <div class="popup_header">
    <h2>{$title_html}</h2>
  </div>
  <div class="popup_content" style="padding: 5px">
    <div class="popup_box">
    {/if}
    {#Shop_shipping_cost_inf1#}
    <br />
    {if $freeshipping > 0}
      {$freeshipping_inf2}
      <br />
    {/if}
    <br />
    <table width="100%" cellpadding="5" cellspacing="0" class="shop_shipping_table">
      <tr>
        <td width="300" class="shop_headers" nowrap="nowrap">{#Shop_shipping_method#}&nbsp;&nbsp;</td>
        <td class="shop_headers" nowrap="nowrap">{#Shop_shipping_countries#}&nbsp;&nbsp;</td>
        <td class="shop_headers" nowrap="nowrap">{#Shop_shipping_during#}&nbsp;&nbsp;</td>
        <td align="right" class="shop_headers" nowrap="nowrap">{#Shop_shipping_cost#}</td>
      </tr>
      {foreach from=$shipper item=s}
        <tr class="{cycle name='1' values='shop_shipping_row_first,shop_shipping_row_second'}">
          <td>
            <strong>{$s->Name|sanitize}</strong>
            <br />
            {if $s->Icon}
              <img src="uploads/shop/shipper_icons/{$s->Icon}" alt="{$s->Name|sanitize}" />
              <br />
            {/if}
            {$s->Beschreibung}
          </td>
          <td>{$s->Laenders}</td>
          <td>
            {if empty($s->Versanddauer)}
              &nbsp;
            {else}
              {$s->Versanddauer} {#Shop_shipping_days#}
            {/if}
          </td>
          <td class="{cycle name='4' values='shop_shipping_row_first,shop_shipping_row_second'}" style="text-align: right; white-space: nowrap">
            {if $s->Gebuehr_Pauschal>0}
              {#Shop_shipping_pausch#} <strong>{$s->Gebuehr_Pauschal|numformat} {$currency_symbol}</strong>*
            {else}
              <table width="100%" cellpadding="1" cellspacing="0">
                {foreach from=$s->Volumes item=volumes}
                  <tr>
                    <td>
                      {if $volumes->Von>0}
                        {$volumes->Von}
                      {else}
                        &nbsp;
                      {/if}
                    </td>
                    <td>{if $volumes->Von>0}-{/if}</td>
                    <td align="left" nowrap="nowrap">{$volumes->Bis} кг</td>
                    <td align="right" nowrap="nowrap">&nbsp;&nbsp;<strong>{$volumes->Gebuehr|numformat} {$currency_symbol}</strong>*</td>
                  </tr>
                {/foreach}
              </table>
            {/if}
          </td>
        </tr>
      {/foreach}
    </table>
    <div class="infobox">{$Inf_Footer}</div>
    {if $popup == 1}
      <div style="padding: 10px; text-align: center">
        <input class="button" onclick="closeWindow();" type="button" value="{#WinClose#}" />&nbsp;
        <input class="button" onclick="window.print();" type="button" value="{#PrintNow#}" />
      </div>
    </div>
  </div>
{/if}
