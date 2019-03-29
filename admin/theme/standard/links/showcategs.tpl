<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "600px", width: "700px", iframe: true });
});
//-->
</script>

<div class="box_innerhead">{#Links#}</div>
{include file="$incpath/links/search.tpl"}
{if !empty($Categs)}
  {assign var=lcc value=0}
  {foreach from=$Categs item=c}
    {assign var=lcc value=$lcc+1}
    <div style="float: left; width: 49%">
      {if $c->LinkCount>=1}
        <a href="{$c->HLink}"><img style="margin-right: 5px" align="left" src="{$imgpath_page}folder.png" alt="" /></a>
        <a href="{$c->HLink}"><strong>{$c->Name|sanitize} ({$c->LinkCount})</strong></a>
      {else}
        <img style="margin-right: 5px" align="left" src="{$imgpath_page}folder.png" alt="" />
        <strong>{$c->Name|sanitize}</strong>
      {/if}
      <br />
      <small>{$c->Beschreibung|sanitize}</small>
    </div>
    {if $lcc % 2 == 0}
      <div style="clear: both"></div>
    {/if}
  {/foreach}
  <br style="clear: both" />
  <br />
{/if}
{if !empty($smarty.request.categ)}
  <div class="box_innerhead">
    {if  permission('links_sent')}
      <a style="float: right" class="user_pop" title="{#LinksSent#}" href="index.php?p=links&amp;area={$area}&amp;action=links_sent">{#LinksSent#}</a>
    {/if}
    {#Links_Overview#}
  </div>
  {if isset($Entries)}
    {#SortBy#}:
    <a style="text-decoration: none" href="index.php?p=links&amp;area={$area}&amp;categ={$smarty.request.categ|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'dateasc'}">{#SortDate#}&nbsp;<img src="{$imgpath_page}{$img_date|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
    <a style="text-decoration: none" href="index.php?p=links&amp;area={$area}&amp;categ={$smarty.request.categ|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'nameasc'}">{#SortName#}&nbsp;<img src="{$imgpath_page}{$img_name|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
    <a style="text-decoration: none" href="index.php?p=links&amp;area={$area}&amp;categ={$smarty.request.categ|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hitsdesc'}">{#Global_Hits#}&nbsp;<img src="{$imgpath_page}{$img_hits|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
      {include file="$incpath/links/entries.tpl"}
    {else}
    <br />
    <p align="center"><strong>{#Links_NoInCateg#}</strong></p>
      {/if}
    {else}
      {if isset($Entries)}
    <div class="box_innerhead">
      {if  permission('links_sent')}
        <a style="float: right" class="user_pop" title="{#LinksSent#}" href="index.php?p=links&amp;area={$area}&amp;action=links_sent">{#LinksSent#}</a>
      {/if}
      {#Links_New#}
    </div>
    {include file="$incpath/links/entries.tpl"}
  {/if}
{/if}
