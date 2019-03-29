<style type="text/css">
  html, td, body, div { font: 11px arial,verdana,'bitstream vera sans'; color: #000 }
  .row_first          { background-color: #FDFDFD; color: #666666; border-bottom: 1px solid #81858c; padding: 3px }
  .row_second         { background-color: #f5f5f5; border-bottom: 1px solid #81858c; padding: 3px }
  .border             { background-color: #ccc; }
  .basket_header      { font-weight: bold }
  .header             { margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #ccc }
  .forms_headers      { font-weight: bold; font-size: 12px }
  .shop_summ_netto    { border-bottom: 1px solid #dedede }
  .shop_summ_final    { border-top: 1px solid #dedede }
  h1                  { font-size: 19px }
  h2                  { font-size: 17px }
  h3                  { font-size: 15px }
  h4                  { font-size: 14px }
  a:link, a:visited { color: #000; text-decoration: underline }
  a:hover            { color: #000; text-decoration: none }
</style>
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <div class="h2">{#Shop_f_confirmtitle#}</div>
      {#Shop_f_confirmheader#}
    </td>
    <td width="50%" align="right" valign="top">
      {$billing_logo}
      <br />
      {$shop_adress_html}
    </td>
  </tr>
</table>
<div class="header">&nbsp;</div>
<div class="shop_data_forms">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%" valign="top"><div class="forms_headers">{#Shop_f_billingadress#}</div></td>
      <td valign="top">&nbsp;&nbsp;</td>
      <td width="50%" valign="top">
        {if $smarty.session.ship_ok == 1}
          <div class="forms_headers">{#Shop_f_shippingadress#}</div>
        {/if}
      </td>
    </tr>
    <tr>
      <td valign="top">
        {if !empty($smarty.session.r_nachname) || !empty($smarty.session.r_vorname)}
          <strong>{#Profile_Buyer#}: </strong> {$smarty.session.r_nachname} {$smarty.session.r_vorname}
          {if !empty($smarty.session.r_middlename)} {$smarty.session.r_middlename}{/if}
          <br />
          <br />
        {/if}
        {if !empty($smarty.session.r_email)}
          <strong>{#Email#}: </strong> {$smarty.session.r_email}
          <br />
        {/if}
        {if !empty($smarty.session.r_telefon)}
          <strong>{#Phone#}: </strong> {$smarty.session.r_telefon}
          <br />
        {/if}
        {if !empty($smarty.session.r_fax)}
          <strong>{#Fax#}: </strong> {$smarty.session.r_fax}
          <br />
        {/if}
        {if !empty($smarty.session.r_land_lang)}
          <strong>{#Country#}: </strong> {$smarty.session.r_land_lang}
          <br />
        {/if}
        {if !empty($smarty.session.r_plz)}
          <strong>{#Profile_Zip#}: </strong> {$smarty.session.r_plz}
          <br />
        {/if}
        {if !empty($smarty.session.r_ort)}
          <strong>{#Town#}: </strong> {$smarty.session.r_ort}
          <br />
        {/if}
        {if !empty($smarty.session.r_strasse)}
          <strong>{#Profile_Street#}: </strong> {$smarty.session.r_strasse}
          <br />
          <br />
        {/if}
        {if !empty($smarty.session.r_firma)}
          <strong>{#Profile_company#}: </strong> {$smarty.session.r_firma}
          <br />
        {/if}
        {if !empty($smarty.session.r_ustid)}
          <strong>{#Profile_vatnum#}: </strong> {$smarty.session.r_ustid}
          <br />
        {/if}
        {if $settings.Reg_Bank == 1 && !empty($smarty.session.r_ustid)}
          <strong>{#Profile_Bank#}: </strong>
          <br />
          {$smarty.session.r_bankname}
          <br />
        {/if}
      </td>
      <td valign="top">&nbsp;&nbsp; </td>
      <td valign="top">
        {if $smarty.session.ship_ok == 1}
          {if $smarty.session.diff_rl == 'liefer_gleich'}
            {#Shop_f_same_sa#}
            <br />
          {else}
            {if !empty($smarty.session.l_nachname) || !empty($smarty.session.l_vorname)}
              <strong>{#Recipient#}: </strong> {$smarty.session.l_nachname} {$smarty.session.l_vorname}
              {if !empty($smarty.session.l_middlename)} {$smarty.session.l_middlename}{/if}
              <br />
            {/if}
            {if !empty($smarty.session.l_firma)}
              <strong>{#Profile_company#}: </strong> {$smarty.session.l_firma}
              <br />
              <br />
            {/if}
            {if !empty($smarty.session.l_telefon)}
              <strong>{#Phone#}: </strong> {#Phone#}: {$smarty.session.l_telefon}
              <br />
            {/if}
            {if !empty($smarty.session.l_fax)}
              <strong>{#Fax#}: </strong> {$smarty.session.l_fax}
              <br />
              <br />
            {/if}
            {if !empty($smarty.session.l_land_lang)}
              <strong>{#Country#}: </strong> {$smarty.session.l_land_lang}
              <br />
            {/if}
            {if !empty($smarty.session.l_plz)}
              <strong>{#Profile_Zip#}: </strong> {$smarty.session.l_plz}
              <br />
            {/if}
            {if !empty($smarty.session.l_ort)}
              <strong>{#Town#}: </strong> {$smarty.session.l_ort}
              <br />
            {/if}
            {if !empty($smarty.session.l_strasse)}
              <strong>{#Profile_Street#}: </strong> {$smarty.session.l_strasse}
              <br />
            {/if}
          {/if}
        {/if}
      </td>
    </tr>
  </table>
  <br />
</div>
<br />
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="forms_headers" valign="top">{#Shop_f_ordernumber#}: </td>
    <td valign="top">{$smarty.session.id_num_order}</td>
  </tr>
  <tr>
    <td class="forms_headers" valign="top">{#Date#}: </td>
    <td valign="top">{$order_time}</td>
  </tr>
  <tr>
    <td class="forms_headers" valign="top">{#Shop_f_orderTrans#}: </td>
    <td valign="top">{$order_num}</td>
  </tr>
  <tr>
    <td class="forms_headers" width="50%" valign="top"> {#Shop_f_shipping_method#}: </td>
    <td width="50%" valign="top"> {$smarty.session.shipper_name}</td>
  </tr>
  <tr>
    <td class="forms_headers" valign="top">{#Shop_payment_methods#}: </td>
    <td valign="top">{$smarty.session.payment_name}</td>
  </tr>
  {if !empty($smarty.post.GefundenDurch)}
    <tr>
      <td class="forms_headers">{#Shop_referedby#}</td>
      <td>{$smarty.post.GefundenDurch|sanitize|truncate: 500}</td>
    </tr>
  {/if}
</table>
<br />
<table width="100%" cellpadding="2" cellspacing="1" class="border">
  <tr>
    <td class="basket_header">{#Shop_title2#}</td>
    <td align="center" class="basket_header">{#Shop_amount#}</td>
    <td align="center" class="basket_header">{#Shop_f_price_s#}</td>
    <td align="center" class="basket_header">{#Shop_f_ovall#}</td>
    {if $show_vat_table == 1}
      <td align="center" class="basket_header">{#Shop_vat#}</td>
    {/if}
  </tr>
  {foreach from=$product_array item=p}
    <tr class="{cycle name='d' values='row_first,row_second'}">
      <td>
        <strong><a href="{$link_prefix}{$p->ProdLink}">{$p->Titel|sanitize}</a></strong>
        <br />
        {#Shop_f_artNr#}: {$p->Artikelnummer}
        <br />
        {foreach from=$p->Vars item=v} <strong>{$v->KatName}</strong>: {$v->Name} ({$v->Operant|replace: '--': '-'}{$v->Wert|numformat} {$currency_symbol})
          <br />
        {/foreach}
        {if $p->FreeFields}
          <br />
          <strong>{#Konfiguration#}</strong>
          <br />
          {$p->FreeFields}
        {/if}
        <strong>{$lang.Shop_shipping_timeinf|replace: ': ': ''}</strong>
        <br />
        <small>{$p->Lieferzeit}</small>
      </td>
      <td nowrap="nowrap" align="center">{$p->Anzahl}</td>
      <td align="center" nowrap="nowrap">
        {if $show_vat_table == 1}
          <strong>{$p->Preis_b|numformat} {$currency_symbol}</strong>
          <br />
        {/if}
        <small>{$p->Preis|numformat} {$currency_symbol}</small>
      </td>
      <td align="center" nowrap="nowrap">
        {if $show_vat_table == 1}
          <strong>{$p->Preis_bs|numformat} {$currency_symbol}</strong>
          <br />
        {/if}
        <small>{$p->Endpreis|numformat} {$currency_symbol}</small>
      </td>
      {if $show_vat_table == 1}
        <td align="center">{$p->UstZone}%</td>
      {/if}
    </tr>
  {/foreach}
</table>
<br />
{include file="$incpath/shop/basket_summ.tpl"}
<br />
<div style="clear: both"></div>
{if !empty($smarty.session.r_nachricht)}
  <div class="forms_headers">{#Shop_f_ordermessage#}</div>
  {$smarty.session.r_nachricht}
  <br />
  <br />
{/if}
<br />
<div class="forms_headers">{#Shop_f_inf_payment#}</div>
{if !empty($smarty.session.DetailInfo)}
  {$smarty.session.DetailInfo}
  <hr noshade="noshade" size="1" />
{/if}
{$smarty.session.payment_details}
<br />
<br />
<div class="forms_headers">{#Shop_f_inf_billing#}</div>
{$smarty.session.shipping_details}
