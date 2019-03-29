<div class="box_innerhead">{$galname|sanitize}</div>
{if !$galleries}
  <br />
  <div align="center">
    <div class="h2">{#Gallery_NoGalleries#}</div>
    <br />
    <br />
    <a href="javascript: history.go(-1);"><img class="absmiddle" src="{$imgpath_page}arrow_left_small.png" alt="" /></a> <a href="javascript: history.go(-1);">{#GlobalBack#}</a>
  </div>
{else}
  <div class="gallery_categs_search">
    <form method="post" action="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name=-&amp;area={$area}">
      <input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|urldecode|sanitize|replace: 'empty': ''}" />&nbsp;
      <select class="input" name="searchtype">
        <option value="full" {if $smarty.request.searchtype == 'full'}selected="selected" {/if}>{#SearchFull#}</option>
        <option value="tags" {if $smarty.request.searchtype == 'tags'}selected="selected" {/if}>{#SearchTags#}</option>
      </select>
      <input name="log" type="hidden" value="1" />&nbsp;
      <input type="submit" class="button" value="{#Search#}" />
    </form>
  </div>
  {foreach from=$galleries item=item}
    <div class="gallery_categs">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign="top" class="gallery_iconleft">
            {if $item->Thumb}
              <a class="stip" title="{$item->GalText|tooltip}" href="{$item->Link}">{$item->Thumb}</a>
            {else}
              <img class="gallery_categs_img" src="uploads/other/noimage.png" />
            {/if}
          </td>
          <td valign="top">
            <h2><a class="stip" title="{$item->GalText|tooltip}" href="{$item->Link}">{$item->GalName|sanitize}</a></h2>
            <br />
            {$item->GalText|truncate: 150}
            <br />
            <br />
            <div class="gallery_info_small">
              {if $item->subGalleries}
                <strong>{#Gallery_Included#}: </strong>
                {foreach from=$item->subGalleries name=c item=child}
                  <a class="light" href="{$child->Link}">{$child->SubGalName|sanitize}</a>{if !$smarty.foreach.c.last}, {/if}
                {/foreach}
                <br />
              {/if}
              <strong>{#GlobalAutor#}: </strong> <a href="{$item->AuthorLink}">{$item->Author|sanitize}</a>&nbsp; <strong>{#Images#}: </strong>{$item->ImageCount->ImageCount}&nbsp; <strong>{#Gallery_GalCreated#}: </strong>{$item->Datum|date_format: $lang.DateFormatSimple}
              {if $item->Tags}
                <br />
                <img class="absmiddle" src="{$imgpath}/page/tags.png" alt="{#Tags#}" /><strong>{#Tags#}: </strong>
                {foreach from=$item->Tags item=ttags name=tag}
                  {if !empty($ttags)}
                    <a href="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name={$galname|translit}&amp;q={$ttags|urlencode|tagchars}&amp;searchtype=tags&amp;page=1&amp;sort=nameasc&amp;area={$area}">{$ttags|tagchars}</a>{if !$smarty.foreach.tag.last}, {/if}
                  {/if}
                {/foreach}
              {/if}
            </div>
          </td>
        </tr>
      </table>
    </div>
  {/foreach}
  <br />
  <strong>{#SortBy#}: </strong>
  <a class="page_navigation" href="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name={$galname|translit}&amp;q={$smarty.request.q|sanitize|default:'empty'}&amp;searchtype={$smarty.request.searchtype|sanitize}&amp;page={$smarty.request.page|default:1}&amp;sort={$def_sort_name|default:'namedesc'}&amp;area={$area}">{#SortName#} <img class="absmiddle" src="{$imgpath}/page/{$def_sort_img_name}.png" alt="" /></a>
  <a class="page_navigation" href="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name={$galname|translit}&amp;q={$smarty.request.q|sanitize|default:'empty'}&amp;searchtype={$smarty.request.searchtype|sanitize}&amp;page={$smarty.request.page|default:1}&amp;sort={$def_sort_date|default:'datedesc'}&amp;area={$area}">{#SortDate#} <img class="absmiddle" src="{$imgpath}/page/{$def_sort_img_date}.png" alt="" /></a>
  <a class="page_navigation" href="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name={$galname|translit}&amp;q={$smarty.request.q|sanitize|default:'empty'}&amp;searchtype={$smarty.request.searchtype|sanitize}&amp;page={$smarty.request.page|default:1}&amp;sort={$def_sort_author|default:'userdesc'}&amp;area={$area}">{#SortBy_Author#} <img class="absmiddle" src="{$imgpath}/page/{$def_sort_img_author}.png" alt="" /></a>
  <br />
  <br />
  {$GalNavi}
  {if $tagCloud}
    <div class="tagcloud">
      <div>{#Tagcloud#}</div>
      <br />
      {foreach from=$tagCloud item=tC}
        <span class="{$tC->Class}"> <a href="index.php?p=gallery&amp;action=showincluded&amp;categ={$smarty.request.categ}&amp;name={$galname|translit}&amp;q={$tC->Name|urlencode|tagchars}&amp;searchtype=tags&amp;page=1&amp;sort=nameasc&amp;area={$area}">{$tC->Name|tagchars}
        <!--({$tC->GCount})-->
          </a>
        </span>
      {/foreach}
    </div>
  {/if}
{/if}
