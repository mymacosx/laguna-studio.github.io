<style type="text/css">
  p { margin-top: 7px;margin-bottom: 6px }
  td { font-size: 12pt }
  small { font-size: 7pt }
</style>
<div align="center">
  <br />
  <table width="900" border="0" cellpadding="3" cellspacing="0">
    <tr>
      <td align="center"><table border="0" width="800" cellspacing="1">
          <tr>
            <td align="center">
              <small>
                Внимание! Оплата данного счета означает согласие с условиями поставки товара. Уведомление об оплате
                <br />
                обязательно, в противном случае не гарантируется наличие товара на складе. Товар отпускается по факту
                <br />
                прихода денег на р/с Поставщика, способом доставки указанном в заказе, при наличии доверенности и паспорта.
              </small>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center"><font style="font-size: 12pt"><strong>Образец заполнения платежного поручения</strong></font></td>
    </tr>
    <tr>
      <td align="left">
        <table border="1" width="100%" cellpadding="2" bordercolor="#000000" cellspacing="0">
          <tr>
            <td colspan="2" style="border-bottom-style: none; border-bottom-width: medium">{$settings.Bank|sanitize}</td>
            <td width="60" valign="top">БИК </td>
            <td valign="top" style="border-bottom-style: none; border-bottom-width: medium">{$settings.Bik|sanitize}</td>
          </tr>
          <tr>
            <td colspan="2" valign="bottom" style="border-top-style: none; border-top-width: medium"><small>Банк получателя </small></td>
            <td width="60">Сч. № </td>
            <td style="border-top-style: none; border-top-width: medium">{$settings.Kschet|sanitize}</td>
          </tr>
          <tr>
            <td width="172">ИНН {$settings.Inn|sanitize}</td>
            <td width="172">КПП {$settings.Kpp|sanitize}</td>
            <td width="60" rowspan="3" valign="top">Сч. № </td>
            <td width="195" rowspan="3" valign="top">{$settings.Rschet|sanitize}</td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom-style: none; border-bottom-width: medium">{$settings.Firma|sanitize}</td>
          </tr>
          <tr>
            <td colspan="2" valign="bottom" style="border-top-style: none; border-top-width: medium"><small>Получатель </small></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center"><strong> <font style="font-size: 16pt">Счет № {$smarty.session.id_num_order} от {$schet_time}</font></strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="left">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80" valign="top">Поставщик: </td>
            <td valign="top">
              {if !empty($settings.Firma)}{$settings.Firma|sanitize}, {/if}
              {if !empty($settings.Zip)}{$settings.Zip|sanitize}, {/if}
              {if !empty($settings.Stadt)}{$settings.Stadt|sanitize}, {/if}
              {if !empty($settings.Strasse)}{$settings.Strasse|sanitize}, {/if}
              {$settings.Telefon|sanitize}
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="80" valign="top">Покупатель: </td>
            <td valign="top">
              {if !empty($smarty.session.r_firma)}{$smarty.session.r_firma|sanitize}, {/if}
              {if !empty($smarty.session.r_plz)}{$smarty.session.r_plz|sanitize}, {/if}
              {if !empty($smarty.session.r_ort)}{$smarty.session.r_ort|sanitize}, {/if}
              {if !empty($smarty.session.r_strasse)}{$smarty.session.r_strasse|sanitize}, {/if}
              {$smarty.session.r_telefon|sanitize}
            </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="left">
        <table border="1" width="100%" bordercolor="#000000" cellpadding="2" cellspacing="0">
          <tr>
            <td width="26" height="28" align="center"><strong>№</strong></td>
            <td height="28" align="center"><strong>Наименование</strong></td>
            <td width="60" height="28" align="center"><strong>Кол-во</strong></td>
            <td width="38" height="28" align="center"><strong>Ед.</strong></td>
            <td width="70" height="28" align="center"><strong>Цена</strong></td>
            <td width="90" height="28" align="center"><strong>Сумма</strong></td>
          </tr>
          {assign var='count' value=0}
          {foreach from=$product_array item=bn}
            {assign var='count' value=$count+1}
            <tr>
              <td align="center">{$count}</td>
              <td>{$bn->Titel|sanitize} ({$bn->Artikelnummer})</td>
              <td align="center">{$bn->Anzahl}</td>
              <td align="center">шт</td>
              <td align="right">{$bn->Preis_b|numf}</td>
              <td align="right">{$bn->Preis_bs|numf}</td>
            </tr>
          {/foreach}
          {if $smarty.session.shipping_summ && $smarty.session.shipping_summ > 0}
            {assign var='count' value=$count+1}
            <tr>
              <td align="center">{$count}</td>
              <td>{#Shop_shipping_cost#}</td>
              <td align="center">-</td>
              <td align="center">-</td>
              <td align="right">{$smarty.session.shipping_summ|numf}</td>
              <td align="right">{$smarty.session.shipping_summ|numf}</td>
            </tr>
          {/if}
          {if $smarty.session.payment_summ_extra}
            {assign var='count' value=$count+1}
            <tr>
              <td align="center">{$count}</td>
              <td>
                {if $smarty.session.payment_summ_mipu == 'zzgl'}
                  {#Shop_f_excl_pm#}
                {else}
                  {#Shop_f_icl_pm#}
                {/if}
              </td>
              <td align="center">-</td>
              <td align="center">-</td>
              <td align="right">{$smarty.session.payment_summ_symbol} {$smarty.session.payment_summ_extra|numf}</td>
              <td align="right">{$smarty.session.payment_summ_symbol} {$smarty.session.payment_summ_extra|numf}</td>
            </tr>
          {/if}
        </table>
      </td>
    </tr>
    <tr>
      <td align="left">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right"><strong>Итого: </strong></td>
            <td width="100" height="26" align="right"><strong>{$smarty.session.price_final|numf}&nbsp;</strong></td>
          </tr>
          {if $show_vat_table == 1}
            {foreach from=$ust_vals item=ust}
              {assign var=ust_code value=$ust->Wert}
              {if $smarty.session.$ust_code}
                <tr>
                  <td align="right"><strong>{#Shop_f_exclVat#} {$ust->Wert}%: </strong></td>
                  <td width="100" height="26" align="right"><strong>{$smarty.session.$ust_code|numf}&nbsp;</strong></td>
                </tr>
              {/if}
            {/foreach}
          {/if}
        </table>
      </td>
    </tr>
    <tr>
      <td align="left">Всего наименований {$count}, на сумму {$smarty.session.price_final|numf} {$currency_symbol}</td>
    </tr>
    <tr>
      <td style="border-style: none none solid none;border-color: rgb(0, 0, 0) rgb(0, 0, 0) -moz-use-text-color;border-width: medium medium 1pt medium" align="left"><strong>{$price_string}</strong></td>
    </tr>
    <tr>
      <td><strong>&nbsp;</strong></td>
    </tr>
    <tr>
      <td align="left"><strong>Счет выписал: __________________________</strong></td>
    </tr>
    <tr>
      <td><strong>&nbsp;</strong></td>
    </tr>
    {*
    <tr>
    <td align="left" valign="top"><img border="0" src="uploads/shop/payment_icons/beznal_footer.jpg" alt="" /></td>
    </tr>
    *}
  </table>
  <br />
  <br />
</div>
