{script file="$jspath/jsuggest.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#qs').suggest('index.php?p=gallery&action=categquicksearch&key=' + Math.random());
});
//-->
</script>

<div class="box_innerhead">{#Gallery_Name#}</div>
<div class="gallery_categs_search">
  <form method="post" action="index.php?p=gallery&amp;area={$area}">
    <input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" />&nbsp;
    <select class="input" name="ascdesc">
      <option value="desc" {if $smarty.request.ascdesc == 'desc'}selected="selected" {/if}>{#desc_t#}</option>
      <option value="asc" {if $smarty.request.ascdesc == 'asc'}selected="selected" {/if}>{#asc_t#}</option>
    </select>&nbsp;
    <select class="input" name="searchtype">
      <option value="full" {if $smarty.request.searchtype == 'full'}selected="selected" {/if}>{#SearchFull#}</option>
      <option value="tags" {if $smarty.request.searchtype == 'tags'}selected="selected" {/if}>{#SearchTags#}</option>
    </select>
    <input name="log" type="hidden" id="log" value="1" />&nbsp;
    <input type="submit" class="button" value="{#Search#}" />
  </form>
</div>
{foreach from=$galleries item=g}
  {assign var=count value=$count+1}
  <div class="gallery_categs">
    <a href="index.php?p=gallery&amp;action=showincluded&amp;categ={$g->Id}&amp;name={$g->Name|translit}&amp;area={$area}"><img class="gallery_categs_img" src="{if $g->Bild}uploads/galerie_icons/{$g->Bild}{else}uploads/other/noimage.png{/if}" alt="" align="left" /></a>
    <h2><a href="index.php?p=gallery&amp;action=showincluded&amp;categ={$g->Id}&amp;name={$g->Name|translit}&amp;area={$area}">{$g->Name|sanitize}</a></h2>
    <br />
    {$g->Text|default:'К сожалению описания нет.'|html_truncate: '200'}
    {if $g->Tags}
      <br />
      <br />
      <div class="gallery_info_small">
        <img class="absmiddle" src="{$imgpath}/page/tags.png" alt="{#Tags#}" /><strong>{#Tags#}: </strong>
        {foreach from=$g->Tags item=ttags name=tag}
          {if !empty($ttags)}
            <a href="index.php?p=gallery&amp;q={$ttags|urlencode|tagchars}&amp;searchtype=tags{$def_sort_n}&amp;page=1&amp;area={$area}">{$ttags|tagchars}</a>{if !$smarty.foreach.tag.last}, {/if}
          {/if}
        {/foreach}
      </div>
    {/if}
  </div>
  <div class="clear"></div>
  {if $count % $galsettings->Kategorien_zeile == 0}
  {/if}
{/foreach}
<br />
{if $GalNavi}
  <div align="center"> {$GalNavi} </div>
{/if}
{if $tagCloud}
  <div class="tagcloud">
    <div>{#Tagcloud#}</div>
    <br />
    {foreach from=$tagCloud item=tC}
    <span class="{$tC->Class}"> <a href="index.php?p=gallery&amp;q={$tC->Name|urlencode|tagchars}&amp;searchtype=tags{$def_sort_n}&amp;page=1&amp;area={$area}">{$tC->Name|tagchars} <!--({$tC->GCount})--> </a> </span>
    {/foreach}
  </div>
{/if}
