<div class="box_innerhead">{#Gaming_cheats#}</div>
<div class="links_list_title">
  <h3>
    {if !empty($cheat_res->Sprache) && $CheatSettings->Flaggen == 1}
      <img class="absmiddle" src="{$imgpath}/flags/{$cheat_res->Sprache}.png" alt="" />&nbsp;
    {/if}
    {$cheat_res->Name|sanitize}
  </h3>
</div>
<table width="100%" cellpadding="1" cellspacing="1">
  <tr>
    <td valign="top">{$cheat_res->Beschreibung}</td>
    <td width="25">&nbsp;</td>
    <td width="140" valign="top">
      {if !empty($cheat_res->Bild)}
        <div align="center"><img class="links_list_img" src="uploads/cheats/{$cheat_res->Bild}" alt="" /></div>
        <br />
      {/if}
      <div class="download_link_infbox">
        {if $cheat_res->Pf}
          <div class="download_link_infheader">{#Gaming_cheats_plattform#}</div>
          {$cheat_res->Pf}
        {/if}
        {if !empty($cheat_res->Size)}
          <div class="download_link_infheader">{#Downloads_Size#}</div>
          {$cheat_res->Size}
          <div class="download_link_infheader">{#Downloads_Hits#}</div>
          {$cheat_res->DownloadHits} / {$cheat_res->Traffic}
        {/if}
        {if $cheat_res->Webseite}
          <div class="download_link_infheader">{#Web#}</div>
          <a rel="nofollow" target="_blank" href="{$cheat_res->Webseite}">{$cheat_res->Webseite}</a>
        {/if}
        {if $cheat_res->Mf}
          <div class="download_link_infheader">{#Manufacturer#}</div>
          {$cheat_res->Mf}
        {/if}
        <div class="download_link_infheader">{#Gaming_cheats_update#}</div>
        {$cheat_res->DatumUpdate|date_format: $lang.DateFormatSimple} (<a href="index.php?p=user&amp;id={$cheat_res->Benutzer}&amp;area={$area}">{$cheat_res->UserName}</a>)
        {if $CheatSettings->Wertung == 1}
          <div class="download_link_infheader">{#Rating_Rating#}</div>
          <input name="starrate_x" type="radio" value="1" class="star" disabled="disabled" {if $cheat_res->Wertung == 1}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="2" class="star" disabled="disabled" {if $cheat_res->Wertung == 2}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="3" class="star" disabled="disabled" {if $cheat_res->Wertung == 3}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="4" class="star" disabled="disabled" {if $cheat_res->Wertung == 4}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="5" class="star" disabled="disabled" {if $cheat_res->Wertung == 5}checked="checked"{/if} />
          <br style="clear: both" />
        {/if}
      </div>
    </td>
  </tr>
</table>
{if !empty($cheat_res->Download) || !empty($cheat_res->DownloadLink)}
  {if permission('cheats_candownload')}
    <span id="plick"></span>
    <div class="download_link"><a style="text-decoration: none" rel="nofollow" {if !empty($cheat_res->DownloadLink)}id="linkextern" target="_blank"{/if} href="{if !empty($cheat_res->DownloadLink)}{$cheat_res->DownloadLink}{else}index.php?p=cheats&amp;action=getfile&amp;id={$cheat_res->Id}{/if}"><img src="{$imgpath_page}file_download.png" alt="" class="absmiddle" />&nbsp;{#Downloads#}</a></div>
        {if $alternatives}
      <div class="box_innerhead">{#Links#}</div>
      <span id="plickex"></span>
      {foreach from=$alternatives item=a}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern_{$a->Id}').on('click', function() {
    var options = {
        target: '#plickex',
        url: 'index.php?action=updatehitcount&p=downloads&id={$cheat_res->Id}',
        timeout: 3000
    };
    $(this).ajaxSubmit(options);
    return true;
    });
});
//-->
</script>
        {#Arrow#}&nbsp;<a rel="nofollow" href="{$a->Link}" id="linkextern_{$a->Id}" target="_blank">{$a->Name}</a>
        <br />
      {/foreach}
    {/if}
  {else}
    <div class="download_link"> {#Downloads_NoPerm#} </div>
  {/if}
{/if}
<br />
{$IncludedGalleries|default:''}
{$RatingForm|default:''}
<div class="links_list_foot">
  {if $CheatSettings->Kommentare == 1}
    <a href="#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a><a href="#comments">{#Comments#}</a>
    {/if}
    {if $CheatSettings->DefektMelden == 1}
    &nbsp;&nbsp; <img class="absmiddle" src="{$imgpath_page}warning_small.png" alt="" />
    {if !empty($cheat_res->DefektGemeldet)}
      {#Links_ErrorSendBrokenImpos#}
    {else}
      <a onclick="document.getElementById('broken_onclick').style.display = '';" href="javascript: void(0);">{#Links_ErrorSendBroken#}</a>
    {/if}
  {/if}
</div>
<br />
{if $CheatSettings->DefektMelden == 1}
  {include file="$incpath/other/broken_link.tpl"}
{/if}
{$GetComments|default:''}
