<div class="box_innerhead">{#Shop_go_myorders#}</div>
{if !$loggedin}
{#Shop_myListError#}
{else}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('.mydownloads a').colorbox({ height: '95%', width: '80%', iframe: true });
    $.validator.setDefaults({
        submitHandler: function() {
            document.forms['cf'].submit();
        }
    });
    $('#requestform').validate({
	rules: {
            subject: { required: true,minlength: 5 },
	    text: { required: true,minlength: 50 }
        },
	messages: { }
    });
    {if !empty($error)}
    $('#request').show();
    {/if}
});
function orderRequest(id) {
    var text = '{#order_text1#}';
    text = text.replace(/__ORDER__/gi, id);
    text = text.replace(/__USER__/gi, '{$whole_name}');
    $('#request_text').val(text);
    $('#requestsubject').val('{#order_text2#} ' + id);
    $('#request').show();
}
//-->
</script>

{#Shop_myorders_inf#}
<br />
<br />
<a name="request"></a>
<div id="request" style="display: none">
  <div class="shop_headers">{#Shop_zapros#}</div>
  {if !empty($error)}
    <div class="error_box">
      <ul>
        {foreach from=$error item=err}
          <li>{$err}</li>
          {/foreach}
      </ul>
    </div>
  {/if}
  <div id="request_form">
    <form id="requestform" name="cf" method="post" action="#request">
      <input type="hidden" name="sub" value="sendrequest" />
      <fieldset>
        <legend>{#GlobalTheme#}</legend>
        <input class="input" id="requestsubject" name="subject" style="width: 97%;" type="text" value="{$smarty.post.subject}" />
      </fieldset>
      <br />
      <fieldset>
        <legend>{#GlobalMessage#}</legend>
        <textarea class="input" id="request_text" name="text" rows="5" cols="5" style="width: 97%;">{$smarty.post.text}</textarea>
        <br />
        <input class="button" value="{#ButtonSend#}" type="submit" />
      </fieldset>
    </form>
  </div>
</div>
{foreach from=$orders_array item=o}
  <div class="infobox">
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td width="120" class="iter_head" nowrap="nowrap"><strong>{#Date#}</strong></td>
        <td width="120" class="iter_head" nowrap="nowrap"><strong>{#Shop_f_ovall#}</strong></td>
        <td width="70" class="iter_head" align="center" nowrap="nowrap"><strong>{#GlobalStatus#}</strong></td>
        <td width="70" class="iter_head" align="center" nowrap="nowrap"><strong>{#Paid#}</strong></td>
        <td class="iter_head" nowrap="nowrap"><strong>{#Global_Action#}</strong></td>
        <td class="iter_head" nowrap="nowrap" align="center"><strong>{#Shop_tracking#}</strong></td>
      </tr>
      <tr class="iter_first">
        <td width="120" nowrap="nowrap">{$o->Datum|date_format: $lang.DateFormat}</td>
        <td width="120" nowrap="nowrap">{$o->Betrag|numformat} {$currency_symbol}</td>
        <td width="70" align="center" nowrap="nowrap" class="stip" title="{$o->SText|tooltip}"><div class="shop_status_{$o->Status}">&nbsp;</div></td>
        <td width="70" align="center" nowrap="nowrap" class="stip" title="{$lang.Paid|tooltip}">
          {if $o->Payment == 1}
          <div title="{$lang.Yes|tooltip}" class="shop_status_ok stip">&nbsp;</div>
          {else}
          <div title="{$lang.No|tooltip}" class="shop_status_failed stip">&nbsp;</div>
          {/if}
        </td>
        <td nowrap="nowrap">
          <span class="mydownloads">
            <a style="text-decoration: none" href="index.php?p=misc&amp;do=viewmyorder&amp;oid={$o->Id}">
              <img class="absmiddle stip" title="{$lang.Shop_myorders_show|tooltip}" src="{$imgpath}/shop/search_small.png" alt="" border="0" />
            </a>
          </span>
          <a style="text-decoration: none" href="javascript: void(0);" onclick="orderRequest({$o->Id});">
            <img class="absmiddle stip" title="{$lang.Shop_zapros|tooltip}" src="{$imgpath}/shop/odrerrequest.png" alt="" border="0" />
          </a>
          {if $o->Viewpayorder == 1}
            <span class="mydownloads">
              <a style="text-decoration: none" href="index.php?p=misc&amp;do=viewpayorder&amp;oid={$o->Id}">
                <img class="absmiddle stip" title="{$lang.Print|tooltip}" src="{$imgpath}/shop/product_print.png" alt="" border="0" />
              </a>
            </span>
          {/if}
          {if $o->DownloadsCustom == 1}
            <span class="mydownloads">
              <a style="text-decoration: none" href="index.php?p=misc&amp;do=mypersonaldownloads&amp;oid={$o->Id}">
                <img class="absmiddle stip" title="{$lang.Shop_personalDownloads|tooltip}" src="{$imgpath}/shop/download_small.png" alt="" border="0" />
              </a>
            </span>
          {/if}
        </td>
        <td nowrap="nowrap" align="center">
          {if $o->TrackingLink}
            <a style="text-decoration: none" target="_blank" href="{$o->TrackingLink}">{#Shop_trackingClick#}</a>
            <br />
            {#Shop_status_tracking#}: {$o->Tracking_Code}
          {else}
            -
          {/if}
        </td>
      </tr>
      {if $o->Status != 'failed'}
        <tr>
          <td colspan="5">
            <div class="iter_head">
              <strong>{#Shop_status_sendeda#}</strong>
              {if !empty($o->TrackingName)}
                - {#Shop_sendedBy#} <strong>{$o->TrackingName}</strong>
              {/if}
            </div>
            {foreach from=$o->Items item=i}
              {if in_array($i->Artikelnummer, $o->Verschickt)}
                {assign var=count value=$count+1}
                <div class="iter_first">
                  <table width="100%" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                      <td width="120">{$i->Artikelnummer}</td>
                      <td>
                        {$i->Anzahl}
                        {if !empty($i->ArtName)}
                          x
                          <a href="index.php?p=shop&amp;action=showproduct&amp;id={$i->ArtId}&amp;cid={$i->CatId}&amp;pname={$i->ArtlName}">{$i->ArtName}</a>
                        {/if}
                      </td>
                    </tr>
                  </table>
                </div>
              {/if}
            {/foreach}
            {if $count == 0}
              <div class="iter_first">&nbsp;&nbsp; - - - - - </div>
            {/if}
            <br />
            {assign var=count value=0}
            <div class="iter_head"><strong>{#Shop_status_sendetopen#}</strong></div>
                {foreach from=$o->Items item=i}
                  {if !in_array($i->Artikelnummer, $o->Verschickt)}
                    {assign var=count value=$count+1}
                <div class="iter_first">
                  <table width="100%" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                      <td width="120">{$i->Artikelnummer}</td>
                      <td>{$i->Anzahl} x <a href="index.php?p=shop&amp;action=showproduct&amp;id={$i->ArtId}&amp;cid={$i->CatId}&amp;pname={$i->ArtlName}">{$i->ArtName}</a></td>
                    </tr>
                  </table>
                </div>
              {/if}
            {/foreach}
            {if $count == 0}
              <div class="iter_first">&nbsp;&nbsp; - - - - - </div>
            {/if}
            {assign var=count value=0}
          </td>
        </tr>
      {/if}
    </table>
  </div>
{/foreach}
<br />
{if !empty($pages)}
  {$pages}
{/if}
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td><span class="shop_status_ok" style="background: transparent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=all">{#Shop_status_all#}</a></td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><span class="shop_status_ok">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=ok">{#Shop_status_ok#}</a></td>
    <td align="right"><strong style="font-size: 120%">{$orders_summ.ok|numformat} {$currency_symbol}</strong></td>
  </tr>
  <tr>
    <td><span class="shop_status_oksend">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=oksend">{#Shop_status_oksend#}</a></td>
    <td align="right"><strong>{$orders_summ.oksend|numformat}</strong> {$currency_symbol}</td>
  </tr>
  <tr>
    <td><span class="shop_status_oksendparts">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=oksendparts">{#Shop_status_oksendparts#}</a></td>
    <td align="right"><strong>{$orders_summ.oksendparts|numformat}</strong> {$currency_symbol}</td>
  </tr>
  <tr>
    <td><span class="shop_status_wait">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=wait">{#Shop_status_wait#}</a></td>
    <td align="right"><strong>{$orders_summ.wait|numformat}</strong> {$currency_symbol}</td>
  </tr>
  <tr>
    <td><span class="shop_status_failed">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=failed">{#Shop_status_failed#}</a></td>
    <td align="right"><strong>{$orders_summ.failed|numformat}</strong> {$currency_symbol}</td>
  </tr>
  <tr>
    <td><span class="shop_status_progress">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {#GlobalStatus#}: <a href="index.php?p=shop&amp;action=myorders&amp;show=progress">{#Shop_status_progress#}</a></td>
    <td align="right"><strong>{$orders_summ.progres|numformat}</strong> {$currency_symbol}</td>
  </tr>
</table>
{/if}
