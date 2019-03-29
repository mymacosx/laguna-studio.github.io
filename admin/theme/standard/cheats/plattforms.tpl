<div class="box_innerhead">{#Gaming_cheats#}</div>
{include file="$incpath/cheats/search.tpl"}
{if !empty($Categs) && empty($smarty.request.plattform)}
  {foreach from=$Categs item=c}
    {assign var=lcc value=$lcc+1}
    <div style="float: left; width: 49%">
      {if $c->LinkCount>=1}
        <a href="{$c->HLink}"><img style="margin-right: 5px" class="absmiddle" src="{$imgpath_page}folder.png" alt="" /></a> <a href="{$c->HLink}"><strong>{$c->Name|sanitize} ({$c->LinkCount})</strong></a>
        {else}
        <img style="margin-right: 5px" class="absmiddle" src="{$imgpath_page}folder.png" alt="" /> <strong>{$c->Name|sanitize}</strong>
      {/if}
    </div>
    {if $lcc % 2 == 0}
      <div style="clear: both"></div>
    {/if}
  {/foreach}
  <br style="clear: both" />
  <br />
{/if}
{if !empty($smarty.request.plattform)}
  {if $Entries}
    {#SortBy#}:
    <a style="text-decoration: none" href="index.php?p=cheats&amp;area={$area}&amp;plattform={$smarty.request.plattform|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'dateasc'}">{#SortDate#}&nbsp;<img src="{$imgpath_page}{$img_date|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
    <a style="text-decoration: none" href="index.php?p=cheats&amp;area={$area}&amp;plattform={$smarty.request.plattform|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'nameasc'}">{#SortName#}&nbsp;<img src="{$imgpath_page}{$img_name|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
    <a style="text-decoration: none" href="index.php?p=cheats&amp;area={$area}&amp;plattform={$smarty.request.plattform|default:1}&amp;name={$CategName}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hitsdesc'}">{#Downloads#}&nbsp;<img src="{$imgpath_page}{$img_hits|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
      {include file="$incpath/cheats/items.tpl"}
    {else}
    <br />
    <p align="center"><strong>{#Gaming_cheats_no#}</strong></p>
      {/if}
    {else}
      {if $Entries}
    <div class="box_innerhead">{#New_Cheats#}</div>
    {include file="$incpath/cheats/items.tpl"}
  {/if}
{/if}
