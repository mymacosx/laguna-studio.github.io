{script file="$jspath/jsuggest.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#ms').suggest('index.php?p=manufacturer&action=quicksearch&key=' + Math.random());
});
//-->
</script>

<div class="box_innerhead">{#Manufacturers#}</div>
{if $items}
  <div class="infobox">
    <form method="post" action="index.php?p=manufacturer&amp;area={$area}">
      <input type="text" name="q" id="ms" style="width: 200px" value="{$smarty.request.q|sanitize}" class="input" />&nbsp;
      <input type="submit" class="button" value="{#Manufacturer_search#}" />
    </form>
  </div>
  {#SortBy#}:
  <a style="text-decoration: none" href="index.php?p=manufacturer&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'dateasc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#SortDate#}&nbsp;<img src="{$imgpath_page}{$img_date|default:'sorter_none'}.png" alt="{#SortDate#}" /></a>&nbsp;&nbsp;
  <a style="text-decoration: none" href="index.php?p=manufacturer&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'nameasc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#SortName#}&nbsp;<img src="{$imgpath_page}{$img_name|default:'sorter_none'}.png" alt="{#SortName#}" /></a>&nbsp;&nbsp;
  <a style="text-decoration: none" href="index.php?p=manufacturer&amp;area={$area}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hitsdesc'}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q}{/if}">{#Global_Hits#}&nbsp;<img src="{$imgpath_page}{$img_hits|default:'sorter_none'}.png" alt="{#Global_Hits#}" /></a>&nbsp;&nbsp;
  <br />
  <br />
  <span id="plick"></span>
  {foreach from=$items item=res}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern_{$res->Id}').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?action=updatehitcount&p=manufacturer&id={$res->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>
    <div class="{cycle name='gb' values='links_list,links_list_second'}">
      <div class="links_list_title">
        <h3><a title="{$res->Name|sanitize}" href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$res->Id}&amp;name={$res->Name|translit}" id="linkextern_{$res->Id}">{$res->Name|sanitize}</a></h3>
      </div>
      {if !empty($res->Bild)}
        <a href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$res->Id}&amp;name={$res->Name|translit}"><img class="links_list_img" src="uploads/manufacturer/{$res->Bild}" align="right" alt="{$res->Name|sanitize}" /></a>
        {/if}
      <div align="justify">{$res->Beschreibung|truncate: 400}</div>
      <br />
      <div class="links_list_foot">
        {#Added#}{$res->Datum|date_format: $lang.DateFormatSimple}&nbsp;|&nbsp;
        {#Global_Hits#}: {$res->Hits}&nbsp;|&nbsp;
        {#Products#}: {$res->ProdCount}&nbsp;|&nbsp;
        <a href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$res->Id}&amp;name={$res->Name|translit}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="{#MoreDetails#}" /></a>
        <a href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$res->Id}&amp;name={$res->Name|translit}">{#MoreDetails#}</a>
      </div>
    </div>
  {/foreach}
  <br />
  {if !empty($Navi)}
    {$Navi}
  {/if}
{/if}
