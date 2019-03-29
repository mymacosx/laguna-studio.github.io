<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?action=updatehitcount&p=links&id={$link_res->Id}',
            timeout: 3000
        };
	$(this).ajaxSubmit(options);
	return true;
    });
    $('.user_pop').colorbox({ height: "600px", width: "700px", iframe: true });
});
//-->
</script>

<div class="box_innerhead">
  {if permission('links_sent')}
    <a style="float: right" class="user_pop" title="{#LinksSent#}" href="index.php?p=links&amp;area={$area}&amp;action=links_sent">{#LinksSent#}</a>
  {/if}
  {#Links#}
</div>
<div class="links_list_title">
  <span id="plick"></span>
  <h3>
    {if !empty($link_res->Sprache) && $Linksettings->Flaggen == 1}
      <img class="absmiddle" src="{$imgpath}/flags/{$link_res->Sprache}.png" alt="" />&nbsp;
    {/if}
    <a href="{$link_res->Url}" id="linkextern" target="_blank">{$link_res->Name|sanitize}</a>
  </h3>
</div>
{if !empty($link_res->Bild)}
  <img class="links_list_img" src="uploads/links/{$link_res->Bild}" align="right" alt="" />
{/if}
{$link_res->Beschreibung}
<br />
<br />
{#Added#}{$link_res->Datum|date_format: $lang.DateFormatSimple} {#GlobalAutor#}: <a href="index.php?p=user&amp;id={$link_res->Autor}&amp;area={$area}">{$link_res->UserName}</a>
<div class="links_list_foot">
  {if $Linksettings->Kommentare == 1}
    <a href="#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a><a href="#comments">{#Comments#}</a>
    {/if}
    {if $Linksettings->DefektMelden == 1}
    &nbsp;&nbsp; <img class="absmiddle" src="{$imgpath_page}warning_small.png" alt="" />
    {if !empty($link_res->DefektGemeldet)}
      {#Links_ErrorSendBrokenImpos#}
    {else}
      <a onclick="document.getElementById('broken_onclick').style.display = ''" href="javascript: void(0);">{#Links_ErrorSendBroken#}</a>
    {/if}
  {/if}
</div>
<br />
{if $Linksettings->DefektMelden == 1}
  {include file="$incpath/other/broken_link.tpl"}
{/if}
{$RatingForm|default:''}
{$GetComments|default:''}
