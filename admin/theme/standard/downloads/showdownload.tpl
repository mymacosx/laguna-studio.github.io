<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?action=updatehitcount&p=downloads&id={$link_res->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>

<div class="box_innerhead">{#Downloads#}</div>
<div class="links_list_title">
  <span id="plick"></span>
  <h3>
    {if !empty($link_res->Sprache) && $Downloadssettings->Flaggen == 1}
      <img class="absmiddle" src="{$imgpath}/flags/{$link_res->Sprache}.png" alt="" />&nbsp;
    {/if}
    {$link_res->Name|sanitize}
  </h3>
</div>
<table width="100%" cellpadding="1" cellspacing="1">
  <tr>
    <td valign="top"> {$link_res->Beschreibung}</td>
    <td width="25">&nbsp;</td>
    <td width="140" valign="top">
      {if !empty($link_res->Bild)}
        <div align="center"><img class="links_list_img" src="uploads/downloads/{$link_res->Bild}" alt="" /></div>
        <br />
      {/if}
      <div class="download_link_infbox">
        <div class="download_link_infheader">{#Downloads_Size#}</div>
        {$link_res->Size}
        <div class="download_link_infheader">{#Downloads_Hits#}</div>
        {$link_res->Hits} / {$link_res->Traffic}
        <div class="download_link_infheader">{#AddedOn#}</div>
        {$link_res->Datum|date_format: $lang.DateFormatSimple} (<a href="index.php?p=user&amp;id={$link_res->Autor}&amp;area={$area}">{$link_res->UserName}</a>)
        {if $link_res->BetriebsOs}
          <div class="download_link_infheader">{#Downloads_Os#}</div>
          {$link_res->BetriebsOs|replace: '|': ', '}
        {/if}
        {if $link_res->SoftwareTyp}
          <div class="download_link_infheader">{#Downloads_SoftType#}</div>
          {$link_res->SoftwareTyp|sanitize}
        {/if}
        {if $link_res->Version}
          <div class="download_link_infheader">{#Downloads_SoftVersion#}</div>
          {$link_res->Version|sanitize}
        {/if}
        {if $Downloadssettings->Wertung == 1}
          <div class="download_link_infheader">{#Rating_Rating#}</div>
          <input name="starrate_x" type="radio" value="1" class="star" disabled="disabled" {if $link_res->Wertung == 1}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="2" class="star" disabled="disabled" {if $link_res->Wertung == 2}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="3" class="star" disabled="disabled" {if $link_res->Wertung == 3}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="4" class="star" disabled="disabled" {if $link_res->Wertung == 4}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="5" class="star" disabled="disabled" {if $link_res->Wertung == 5}checked="checked"{/if} />
          <br style="clear: both" />
        {/if}
      </div>
    </td>
  </tr>
</table>
{if permission('downloads_candownload')}
  <span id="plick"></span>
  <div class="download_link"><a style="text-decoration: none" rel="nofollow" {if !empty($link_res->Url_Direct)}id="linkextern" target="_blank"{/if} href="{if !empty($link_res->Url_Direct)}{$link_res->Url_Direct}{else}index.php?p=downloads&amp;action=getfile&amp;id={$link_res->Id}{/if}"><img src="{$imgpath_page}file_download.png" alt="" class="absmiddle" />&nbsp;{#Downloads_DlNow#}</a></div>
      {if !empty($alternatives)}
    <div class="box_innerhead">{#Downloads_Alternate#}</div>
    <span id="plickex"></span>
    {foreach from=$alternatives item=a}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern_{$a->Id}').on('click', function() {
        var options = {
            target: '#plickex',
            url: 'index.php?action=updatehitcount&p=downloads&id={$link_res->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>
      <img src="{$imgpath_page}file_download_small.png" alt="" class="absmiddle" />&nbsp;<a rel="nofollow" href="{$a->Link}" id="linkextern_{$a->Id}" target="_blank">{$a->Name}</a>
      <br />
    {/foreach}
  {/if}
{else}
  <div class="download_link"> {#Downloads_NoPerm#} </div>
{/if}
<br />
{$RatingForm|default:''}
<div class="links_list_foot">
  {if $Downloadssettings->Kommentare == 1}
    <a href="#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a><a href="#comments">{#Comments#}</a>
    {/if}
    {if $Downloadssettings->DefektMelden == 1}
    &nbsp;&nbsp; <img class="absmiddle" src="{$imgpath_page}warning_small.png" alt="" />
    {if !empty($link_res->DefektGemeldet)}
      {#Links_ErrorSendBrokenImpos#}
    {else}
      <a onclick="document.getElementById('broken_onclick').style.display = '';" href="javascript: void(0);">{#Links_ErrorSendBroken#}</a>
    {/if}
  {/if}
</div>
<br />
{if $Downloadssettings->DefektMelden == 1}
  {include file="$incpath/other/broken_link.tpl"}
{/if}
{$GetComments|default:''}
