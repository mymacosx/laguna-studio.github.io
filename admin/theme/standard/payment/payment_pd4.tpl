<style type="text/css">
  p { margin-top: 6px;margin-bottom: 6px }
  td { font-size: 9pt }
  small { font-size: 7pt }
</style>
<div align="center">
  <br />
  <table style="width: 180mm;height: 145mm" border="0" cellpadding="0" cellspacing="0">
    <tr valign="top">
      <td style="border-style: solid none none solid;border-color: rgb(0, 0, 0) -moz-use-text-color -moz-use-text-color rgb(0, 0, 0);border-width: 1pt medium medium 1pt; width: 50mm;height: 70mm" align="center">
        <b>Извещение</b>
        <br />
        <font style="font-size: 53mm">&nbsp;
        <br />
        </font>
        <b>Кассир</b>
      </td>
      <td style="border-style: solid none none solid;border-color: rgb(0, 0, 0) -moz-use-text-color rgb(0, 0, 0) rgb(0, 0, 0);border-width: 1pt medium medium 1pt">&nbsp;&nbsp;&nbsp;</td>
      <td style="border-style: solid solid none none;border-color: rgb(0, 0, 0) rgb(0, 0, 0) -moz-use-text-color;border-width: 1pt 1pt medium medium" align="left">
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><small><i>Форма № ПД-4</i></small></td>
          </tr>
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">&nbsp;{$settings.Firma|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(наименование получателя платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 37mm">&nbsp;{$settings.Inn|sanitize}</td>
            <td style="width: 9mm">&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">&nbsp;{$settings.Rschet|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(ИНН получателя платежа)</small></td>
            <td><small>&nbsp;</small></td>
            <td align="center"><small>(номер счета получателя платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>в&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 73mm">&nbsp;{$settings.Bank|sanitize}</td>
            <td align="right">БИК&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 33mm">&nbsp;{$settings.Bik|sanitize}</td>
          </tr>
          <tr>
            <td></td>
            <td align="center"><small>(наименование банка получателя платежа)</small></td>
            <td></td>
            <td></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">&nbsp;{$settings.Kschet|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">{$inf_payment|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(наименование платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Ф.И.О. плательщика&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">{$smarty.session.r_nachname|sanitize} {$smarty.session.r_vorname|sanitize} {$smarty.session.r_middlename|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Адрес плательщика&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">{$smarty.session.r_plz|sanitize}, {$smarty.session.r_ort|sanitize}, {$smarty.session.r_strasse|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>Сумма платежа&nbsp;{$smarty.session.price_final|numf|replace: '.': '&nbsp;руб.&nbsp;'}&nbsp;коп.</td>
            <td align="right">&nbsp;Сумма платы за услуги&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп.</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
            <td align="right">&nbsp;&nbsp;«______»________________ 201____ г.</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><small>С условиями приема указанной в платежном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><b>Подпись плательщика _____________________</b></td>
          </tr>
        </table></td>
    </tr>
    <tr valign="top">
      <td style="border-style: solid none solid solid;border-color: rgb(0, 0, 0) -moz-use-text-color rgb(0, 0, 0) rgb(0, 0, 0);border-width: 1pt medium 1pt 1pt;width: 50mm; height: 70mm" align="center">
        <font style="font-size: 50mm">&nbsp;
        <br />
        </font>
        <b>Квитанция</b>
        <br />
        <font style="font-size: 8pt">&nbsp;
        <br />
        </font>
        <b>Кассир</b>
      </td>
      <td style="border-style: solid none solid solid;border-color: rgb(0, 0, 0) -moz-use-text-color rgb(0, 0, 0) rgb(0, 0, 0);border-width: 1pt medium 1pt 1pt">&nbsp;&nbsp;&nbsp;</td>
      <td style="border-style: solid solid solid none;border-color: rgb(0, 0, 0) rgb(0, 0, 0) -moz-use-text-color;border-width: 1pt 1pt 1pt medium" align="left">
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><small>&nbsp;</small></td>
          </tr>
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">&nbsp;{$settings.Firma|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(наименование получателя платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 37mm">&nbsp;{$settings.Inn|sanitize}</td>
            <td style="width: 9mm">&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">&nbsp;{$settings.Rschet|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(ИНН получателя платежа)</small></td>
            <td><small>&nbsp;</small></td>
            <td align="center"><small>(номер счета получателя платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>в&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 73mm">&nbsp;{$settings.Bank|sanitize}</td>
            <td align="right">БИК&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0);width: 33mm">&nbsp;{$settings.Bik|sanitize}</td>
          </tr>
          <tr>
            <td></td>
            <td align="center"><small>(наименование банка получателя платежа)</small></td>
            <td></td>
            <td></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">&nbsp;{$settings.Kschet|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)">{$inf_payment|sanitize}</td>
          </tr>
          <tr>
            <td align="center"><small>(наименование платежа)</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Ф.И.О. плательщика&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">{$smarty.session.r_nachname|sanitize} {$smarty.session.r_vorname|sanitize} {$smarty.session.r_middlename|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap="nowrap" width="1%">Адрес плательщика&nbsp;&nbsp;</td>
            <td style="border-bottom: 1pt solid rgb(0, 0, 0)" width="100%">{$smarty.session.r_plz|sanitize}, {$smarty.session.r_ort|sanitize}, {$smarty.session.r_strasse|sanitize}</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>Сумма платежа&nbsp;{$smarty.session.price_final|numf|replace: '.': '&nbsp;руб.&nbsp;'}&nbsp;коп.</td>
            <td align="right">&nbsp;Сумма платы за услуги&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп.</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
            <td align="right">&nbsp;&nbsp;«______»________________ 201____ г.</td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><small>С условиями приема указанной в платежном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
          </tr>
        </table>
        <table style="width: 122mm;margin-top: 3pt" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><b>Подпись плательщика _____________________</b></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  <br />
</div>
