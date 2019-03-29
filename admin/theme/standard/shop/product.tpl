{if $p.Fsk18 == 1 && $fsk_user != 1}
{assign var="not_possible_to_buy" value=1}
{/if}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_price_alert', 'toggler', 30, '{$basepath}');
togglePanel('navpanel_prod_votes', 'toggler', 30, '{$basepath}');
togglePanel('navpanel_prod_seen', 'toggler', 30, '{$basepath}');

$(document).ready(function() {
    $('#container-options ul.rounded').tabs();
});

//-->
</script>

{if isset($notfound) && $notfound == 1}
  {#Shop_errorProduct#}
  <br />
  <br />
{else}

{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('.product_images a').colorbox({
        photo: true,
        maxHeight: "98%",
        maxWidth: "98%",
        slideshow: true,
        slideshowAuto: false,
        slideshowSpeed: 2500,
        current: "{#GlobalImage#} {ldelim}current{rdelim} {#PageNavi_From#} {ldelim}total{rdelim}",
        slideshowStart: "{#GlobalStart#}",
        slideshowStop: "{#GlobalStop#}",
        previous: "{#GlobalBack#}",
        next: "{#GlobalNext#}",
        close: "{#GlobalGlose#}"
    });
    $('.moreImgs a').colorbox({ height: "620px", width: "980px", iframe: true });
    $('#tobasket').validate({
        rules: {
            {if !empty($p.Frei_1) && $p.Frei_1_Pflicht == 1}
            free_1: { required: true },
            {/if}
            {if !empty($p.Frei_2) && $p.Frei_2_Pflicht == 1}
            free_2: { required: true },
            {/if}
            {if !empty($p.Frei_3) && $p.Frei_3_Pflicht == 1}
            free_3: { required: true },
            {/if}
            amount: { required: true, number: true }
        },
        messages: { },
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                {if empty($smarty.request.blanc)}
                target: '#ajaxbasket',
                {/if}
                timeout: 6000,
                success: showResponse,
                clearForm: false,
                resetForm: false
            });
        },
        success: function(label) { }
    });
});
function showResponse() {
    if ($('#to_mylist').val() == 1) {
        showNotice('<br /><p class="h3">{#Shop_ProdAddedToList#}</p><br />', 2000);
     } else {
        showNotice($('#prodmessage'), 10000);
        $('#yes_click').on('click', function() {
            {if isset($smarty.request.blanc) && $smarty.request.blanc == 1}parent.{/if}document.location = 'index.php?action=showbasket&p=shop';
            $.unblockUI();
            return false;
        });
        $('#no_click').on('click', function() {
            $.unblockUI();
            return false;
        });
    }
    $('#to_mylist').val(0)
}
//-->
</script>

<div id="prodmessage" style="display: none">
  <br />
  <p class="h3">{#Shop_ProdAddedToBasket#}</p>
  <p>{#LoginExternActions#}</p>
  <input class="shop_buttons_big" type="button" id="yes_click" value="{#Shop_go_basket#}" />
  <input class="shop_buttons_big_second" type="button" id="no_click" value="{#WinClose#}" />
  <br />
  <br />
</div>
<form method="post" name="product_request_form" action="{page_link}#product_request">
  <input type="hidden" name="subaction" value="product_request" />
</form>
<form method="post" id="tobasket" action="index.php?p=shop&amp;area={$area}">
  <div class="shop_product_once_body">
    <div class="box_innerhead">
      <div style="float: left">
        <h2>{$p.Titel|sanitize}</h2>
        </div>
        {if $shipping_free == 1}
          <div class="product_important_noshipping round">{#Shop_freeshipping#}</div>
        {/if}
        {if $p.diffpro > 0}
          <div class="product_important_cheaper round">{#Shop_Billiger#}{$p.diffpro|numformat}%</div>
        {/if}
        <div class="clear"></div>
      </div>
      {assign var=alert_t value=$p.Titel}
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top" width="150">
            <div class="product_images">
              <div class="product_images_box round">
                {if empty($p.NoBild)}
                <a rel="poppim" href="{$p.BildPopLink}"><img src="{$p.Bild}" alt="{$p.Titel|sanitize}" border="0" class="shop_productimage_left" /></a>
                {else}
                <img src="{$p.Bild}" alt="{$p.Titel|sanitize}" border="0" class="shop_productimage_left" />
                {/if}
              </div>
              {if $images}
                <div class="product_images_boxmore round">
                  {foreach from=$images item=im}
                    {assign var=icount value=$icount+1}
                    {if $icount < 3}
                      <div class="product_images_box_small"><a rel="poppim" href="{$im.Bild_GrossLink}"><img src="{$im.Bild}" alt="" border="0" /></a></div>
                        {else}
                          {assign var=ShowLink value=1}
                      <div style="display: none"><a rel="poppim" href="{$im.Bild_GrossLink}"><img src="{$im.Bild}" alt="" border="0" /></a></div>
                        {/if}
                      {/foreach}
                  <div class="clear"></div>
                  {if $ShowLink == 1}
                    <div class="moreImgs" style="text-align: center"><a href="index.php?p=misc&do=shopimgages&prodid={$p.Id}">{#Shop_moreImages#}</a></div>
                    {/if}
                </div>
              {/if}
            </div>
            <div class="clear"></div>
            {if $p.Fsk18 == 1}
              <p align="center"><img src="{$imgpath_page}usk_small.gif" alt="{#Shop_isFSKWarning#}" />
                <br />
                <strong>{#Shop_isFSKWarning#}</strong>
              </p>
            {/if}
          </td>
          <td valign="top">
            <div class="product_details_right round">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td valign="top">
                    {if $not_possible_to_buy == 1}
                      <img src="{$imgpath_page}usk.gif" alt="{#Shop_isFSKWarning#}" hspace="5" vspace="5" align="right" />
                      {$shopsettings->Fsk18}
                      <div class="clear"></div>
                      <br />
                    {/if}
                    {#Shop_ArticleNumber#}: <strong>{$p.Artikelnummer}</strong>
                    <br />
                    <br />
                    {include file="$incpath/shop/product_price.tpl"}
                    <br />
                    <input type="hidden" value="{$p.Preis|jsnum}" id="price_hidden" name="price_h" />
                    <table width="100%" cellspacing="0" cellpadding="1">
                      <tr>
                        <td width="50" valign="top">{$p.VIcon}</td>
                        <td>
                          {if $not_on_store == 1}
                            {$p.VMsg|sanitize}
                          {else}
                            <strong>{#Shop_shipping_timeinf#}</strong>&nbsp;&nbsp;
                            {if $order_for_you == 1}
                              {$available_array.3->Name|sanitize}
                            {else}
                              {$p.Lieferzeit|sanitize}
                            {/if}
                            {if $low_amount == 1 && $p.Lagerbestand > 0}
                              <div class="shop_lowamount">
                                {#Shop_lowAmount#}
                                <br />
                                {$lang.ShopLowWarnInf|replace: '__COUNT__': $p.Lagerbestand}
                              </div>
                            {else}
                              {if $p.Lagerbestand > 0 && $shopsettings->Zeige_Lagerbestand == 1} ({#Shop_av_storeAv#} {$p.Lagerbestand}){/if}
                            {/if}
                          {/if}
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td valign="top" width="120">
                          {if get_active('shop_merge')}
                        <div class="product_extern_actions round"><a href="" onclick="mergeProduct('{$p.Id}', '{$p.Kategorie}', '{$baseurl}/', '{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}1{/if}'); return false;"><img alt="" border="0" class="absmiddle" src="{$imgpath}/shop/p_merge.png" />&nbsp;&nbsp;{#Merge#}</a></div>
                          {/if}
                        <div class="product_extern_actions round"><a href="#product_request" onclick="document.forms['product_request_form'].submit(); return false;"><img alt="" border="0" class="absmiddle" src="{$imgpath}/shop/p_question.png" />&nbsp;&nbsp;{#Shop_prod_request_link#}</a></div>
                          {if get_active('shop_preisalarm')}
                            {if $shopsettings->PreiseGaeste == 1 || $loggedin}
                          <div class="product_extern_actions round"><a href="#pricealert"><img alt="" border="0" class="absmiddle" src="{$imgpath}/shop/p_alert.png" />&nbsp;&nbsp;{#Shop_priceAlert#}</a></div>
                            {/if}
                          {/if}
                          {if $shop_bewertung == 1}
                        <div class="product_extern_actions round"><a href="#vote"><img alt="" border="0" class="absmiddle" src="{$imgpath}/shop/p_vote.png" />&nbsp;&nbsp;{#Shop_prod_votes_link#}</a></div>
                          {/if}
                          {if isset($shop_cheaper)}
                        <div class="product_extern_actions round"><a id="cheaper_link" href="#" title="{#cheaper_name#}"><img alt="" border="0" class="absmiddle" src="{$imgpath}/shop/cheaper.png" />&nbsp;&nbsp;{#cheaper_name#}</a></div>
                          {/if}
                  </td>
                </tr>
              </table>
            </div>
            {if $shopsettings->PreiseGaeste == 1 || $loggedin}
              {include file="$incpath/shop/product_vars.tpl"}
              {include file="$incpath/shop/product_config.tpl"}
              {include file="$incpath/shop/product_amount_submit.tpl"}
            {/if}
          </td>
        </tr>
      </table>
      <div class="clear"></div>
    </div>
  </form>
  <br />
  {$shop_cheaper}
{/if}

  <div id="container-options">
    <ul class="rounded">
      <li><a href="#opt-1"><span>{#buttonDetails#}</span></a></li>
        {if $Zub_a_products_array}
        <li><a href="#opt-2"><span>{$tabs->TAB1|sanitize}</span></a></li>
        {/if}
        {if $Zub_b_products_array}
        <li><a href="#opt-3"><span>{$tabs->TAB2|sanitize}</span></a></li>
        {/if}
        {if $Zub_c_products_array}
        <li><a href="#opt-4"><span>{$tabs->TAB3|sanitize}</span></a></li>
        {/if}
        {if $shopsettings->similar_product == 1 && $Zub_d_products_array}
        <li><a href="#opt-5"><span>{#Shop_detailSimilar#}</span></a></li>
        {/if}
        {if $prod_downloads}
        <li><a href="#opt-downloads"><span>{#Shop_Downloads#}</span></a></li>
        {/if}
    </ul>
    <div id="opt-1" class="ui-tabs-panel-content">
      {include file="$incpath/shop/products_details.tpl"}
    </div>
    {if $Zub_a_products_array}
      <div id="opt-2" class="ui-tabs-panel-content"> {$Zub_a_products} </div>
    {/if}
    {if $Zub_b_products_array}
      <div id="opt-3" class="ui-tabs-panel-content"> {$Zub_b_products} </div>
    {/if}
    {if $Zub_c_products_array}
      <div id="opt-4" class="ui-tabs-panel-content"> {$Zub_c_products} </div>
    {/if}
    {if $shopsettings->similar_product == 1 && $Zub_d_products_array}
      <div id="opt-5" class="ui-tabs-panel-content"> {$Zub_d_products} </div>
    {/if}
    {if $prod_downloads}
      <div id="opt-downloads" class="ui-tabs-panel-content">
        <table width="100%" border="0" cellpadding="4" cellspacing="0">
          {foreach from=$prod_downloads item=pdd}
            <tr>
              <td class="{cycle name='pdls1' values='shop_shipping_row_first,shop_shipping_row_second'}" width="25"><a href="{$baseurl}/uploads/shop/product_downloads/{$pdd->Datei}"><img src="{$imgpath}/filetypes/{$pdd->Icon}" alt="" border="0" /></a></td>
              <td class="{cycle name='pdls2' values='shop_shipping_row_first,shop_shipping_row_second'}" width="300">
                <strong><a href="{$baseurl}/uploads/shop/product_downloads/{$pdd->Datei}">{$pdd->DlName}</a></strong>
                <br />
                {$pdd->Beschreibung}
              </td>
              <td class="{cycle name='pdls3' values='shop_shipping_row_first,shop_shipping_row_second'}" >{$pdd->Size}</td>
            </tr>
          {/foreach}
        </table>
      </div>
    {/if}
  </div>
  <div class="clear">&nbsp;</div>
  {if $smarty.request.subaction == 'product_request' || $shopsettings->AnfrageForm == 1}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#product_request').validate({
        rules: {
            product_request_email: { required: true, email: true },
            product_request_name: { required: true },
            product_request_text: { required: true, minlength: 10 }
        },
        submitHandler: function() {
            document.forms['product_request'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>
    <a name="product_request"></a>
    <br />
    <br />
    <div class="box_innerhead">{#Shop_prod_request#}</div>
    <div class="infobox">
      {if isset($msg_send) && $msg_send == 1}
        {#Shop_prod_request_thankyou#}
      {else}
        {if !empty($error)}
          <div class="error_box">
            <ul>
              {foreach from=$error item=err}
                <li>{$err}</li>
                {/foreach}
            </ul>
          </div>
        {/if}
        <form name="product_request" id="product_request" method="post" action="{page_link}#product_request">
          <input type="hidden" name="subaction" value="product_request" />
          <input type="hidden" name="id" value="{$smarty.request.id}" />
          <input type="hidden" name="cid" value="{$smarty.request.cid}" />
          <input type="hidden" name="red" value="{$red}" />
          <input type="hidden" name="sub" value="product_request" />
          <input type="hidden" name="prod_name" value="{$p.Titel|sanitize}" />
          <label><input type="text" class="input" style="width: 250px" name="product_request_email" value="{$smarty.request.product_request_email|default:$smarty.session.login_email|sanitize}" /> {#SendEmail_Email#}</label>
          <br />
          <label><input type="text" class="input" style="width: 250px" name="product_request_name" value="{$smarty.request.product_request_name|default:$whole_name|sanitize}" /> {#Contact_myName#}</label>
          <br />
          <textarea class="input" name="product_request_text" cols="45" rows="5" style="width: 90%">{$smarty.request.product_request_text|sanitize}</textarea>
          {include file="$incpath/other/captcha.tpl"}
          <br />
          <input class="button" type="submit" value="{#ButtonSend#}" onclick="" />
        </form>
      {/if}
    </div>
  {/if}
  {if get_active('shop_preisalarm')}
    {if $shopsettings->PreiseGaeste == 1 || $loggedin}
      <a name="pricealert"></a>
      <br />
      <br />
      <div class="opened" id="navpanel_price_alert" title="{#Shop_priceAlert#}">
        <div class="shop_contents_box_other">
          {$price_alert}
        </div>
      </div>
    {/if}
  {/if}
  {if $shop_bewertung == 1}
    {script file="$jspath/jrating.js" position='head'}
    <a name="vote"></a>
    <div class="opened" id="navpanel_prod_votes" title="{#Shop_prod_vote_votesall#}">
      {if $votes}
        <br />
        {foreach from=$votes item=v}
          <div class="{cycle name=sv values='comment_box,comment_box_second'}">
            <table width="100%" cellspacing="0" cellpadding="3">
              <tr>
                <td width="120">{#Date#}: </td>
                <td>{$v->Datum|date_format: $lang_settings.Zeitformat}</td>
              </tr>
              <tr>
                <td width="120">{#GlobalAutor#}: </td>
                <td>{$v->Benutzer}</td>
              </tr>
              <tr>
                <td width="120" valign="top">{#Shop_prod_vote_auttext#}</td>
                <td>{$v->Bewertung}</td>
              </tr>
              <tr>
                <td width="120">{#Shop_prod_vote_points#}</td>
                <td>
                  <input name="starrate{$v->Id}" type="radio" value="1" class="star" disabled="disabled"{if $v->Bewertung_Punkte == 1} checked="checked"{/if} />
                  <input name="starrate{$v->Id}" type="radio" value="2" class="star" disabled="disabled"{if $v->Bewertung_Punkte == 2} checked="checked"{/if} />
                  <input name="starrate{$v->Id}" type="radio" value="3" class="star" disabled="disabled"{if $v->Bewertung_Punkte == 3} checked="checked"{/if} />
                  <input name="starrate{$v->Id}" type="radio" value="4" class="star" disabled="disabled"{if $v->Bewertung_Punkte == 4} checked="checked"{/if} />
                  <input name="starrate{$v->Id}" type="radio" value="5" class="star" disabled="disabled"{if $v->Bewertung_Punkte == 5} checked="checked"{/if} />
                  <br style="clear: both" />
                </td>
              </tr>
            </table>
          </div>
        {/foreach}
      {else}
        {#Shop_prod_vote_novotes#}
      {/if}
      <br />
      <br />
      <fieldset>
        <legend>{#Shop_prod_vote_now#}</legend>
        {if !permission('shop_vote')}
          {#Shop_prod_vote_login#}
        {else}
        {script file="$jspath/jvalidate.js" position='head'}
        <script type="text/javascript">
        <!-- //
        {include file="$incpath/other/jsvalidate.tpl"}
        $(document).ready(function() {
            $('#prod_vote_form').validate({
                rules: {
                    prod_vote_text: { required: true, minlength: 10 }
                },
                submitHandler: function() {
                    document.forms['prod_vote_form'].submit();
                },
                success: function(label) {
                    label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
                }
            });
        });
        //-->
        </script>

        {assign var=secure_uniqid value="two"}
          <a name="vote_form"></a>
          {if !empty($error{$secure_uniqid})}
            <div class="error_box">
              <ul>
                {foreach from=$error{$secure_uniqid} item=err}
                  <li>{$err}</li>
                  {/foreach}
              </ul>
            </div>
          {/if}
          <form name="prod_vote_form" id="prod_vote_form" method="post" action="{page_link}#vote_form">
            <input type="hidden" name="id" value="{$smarty.request.id}" />
            <input type="hidden" name="red" value="{$red}" />
            <input type="hidden" name="sub" value="prod_vote" />
            <input type="hidden" name="prod_name" value="{$p.Titel|sanitize}" />
            <table width="100%" cellpadding="2" cellspacing="0">
              <tr>
                <td colspan="3"><textarea class="input" name="prod_vote_text" cols="45" rows="5" style="width: 90%">{$smarty.request.prod_vote_text|sanitize}</textarea></td>
              </tr>
              <td colspan="3">{include file="$incpath/other/captcha.tpl"}</td>
              <tr>
                <td width="120">{#Shop_prod_vote_points#}</td>
                <td width="120">
                  <input name="prod_vote_points" type="radio" value="1" class="star" />
                  <input name="prod_vote_points" type="radio" value="2" class="star" />
                  <input name="prod_vote_points" type="radio" value="3" class="star" />
                  <input name="prod_vote_points" type="radio" value="4" class="star" checked="checked" />
                  <input name="prod_vote_points" type="radio" value="5" class="star" />
                </td>
                <td><input class="button" type="submit" value="{#RateThis#}" /></td>
              </tr>
            </table>
          </form>
        {/if}
      </fieldset>
      <br />
    </div>
  {/if}
  <div class="opened" id="navpanel_prod_seen" title="{#Shop_detailLastSeen#}"> {$small_seen_products} </div>
{if $shopsettings->vat_info_product == 1}
  {include file="$incpath/shop/vat_info.tpl"}
{/if}
