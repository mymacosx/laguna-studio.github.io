{script file="$jspath/jrating.js" position='head'}
{script file="$jspath/jsuggest.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#ms').suggest('index.php?p=products&action=quicksearch&key=' + Math.random());
});
//-->
</script>

<div class="box_innerhead">{#Products#}</div>
<div class="infobox">
  <form method="post" action="index.php?p=products&amp;area={$area}">
    <input type="text" name="q" id="ms" style="width: 200px" value="{$smarty.request.q|sanitize}" class="input" />&nbsp;
    <input type="submit" class="button" value="{#Products_search#}" />
  </form>
</div>
{if !$items}
  <div class="h3">{#Products_none#}</div>
{else}
  {#SortBy#}:
  <a style="text-decoration: none" href="index.php?p=products&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'dateasc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#SortDate#}&nbsp;<img src="{$imgpath_page}{$img_date|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
  <a style="text-decoration: none" href="index.php?p=products&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'nameasc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#SortName#}&nbsp;<img src="{$imgpath_page}{$img_name|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
  <a style="text-decoration: none" href="index.php?p=products&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hitsdesc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#Global_Hits#}&nbsp;<img src="{$imgpath_page}{$img_hits|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
  <a style="text-decoration: none" href="index.php?p=products&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$genresort|default:'genredesc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#Global_Categ#}&nbsp;<img src="{$imgpath_page}{$img_genre|default:'sorter_none'}.png" alt="" /></a>&nbsp;&nbsp;
  <br />
  <br />
  {foreach from=$items item=res}
    <div class="{cycle name='gb' values='links_list,links_list_second'}">
      <div class="links_list_title">
        <h3><a title="{$res->Name|sanitize}" href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}">{$res->Name|sanitize}</a></h3>
      </div>
      {if !empty($res->Bild)}
        <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}"><img class="links_list_img" src="uploads/products/{$res->Bild}" align="right" alt="" /></a>
        {/if}
        {$res->Beschreibung|truncate: 550}
      <br />
      <div class="links_list_foot">
        <table align="center">
          <tr>
            <td> {#Added#} {$res->Datum|date_format: $lang.DateFormatSimple}&nbsp;&nbsp; </td>
            <td>{#Global_Hits#}: {$res->Hits}</td>
            {if $res->Wertung && $product_rate == 1}
              <td>&nbsp;&nbsp;{#Rating_Rating#}: </td>
              <td>
                <input name="starrate{$res->Id}" type="radio" value="1" class="star" disabled="disabled" {if $res->Wertung == 1}checked="checked"{/if} />
                <input name="starrate{$res->Id}" type="radio" value="2" class="star" disabled="disabled" {if $res->Wertung == 2}checked="checked"{/if} />
                <input name="starrate{$res->Id}" type="radio" value="3" class="star" disabled="disabled" {if $res->Wertung == 3}checked="checked"{/if} />
                <input name="starrate{$res->Id}" type="radio" value="4" class="star" disabled="disabled" {if $res->Wertung == 4}checked="checked"{/if} />
                <input name="starrate{$res->Id}" type="radio" value="5" class="star" disabled="disabled" {if $res->Wertung == 5}checked="checked"{/if} />
              </td>
            {/if}
          </tr>
        </table>
        {if $res->Genre}
          {#Global_Categ#}: {$res->Genre}&nbsp;&nbsp;
        {/if}
        <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
        <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}">{#MoreDetails#}</a>&nbsp;&nbsp;
        {if $res->CCount>=1}
          <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a>
          <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}#comments">{#Comments#} ({$res->CCount})</a>
        {/if}
      </div>
    </div>
  {/foreach}
  <br />
  {if !empty($Navi)}
    {$Navi}
  {/if}
{/if}
