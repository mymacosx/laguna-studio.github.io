{script file="$jspath/jcarousel.js" position='head'}
{script file="$jspath/jcycle.js" position='head'}
<script type="text/javascript">
<!-- //
function loadCallback(carousel) {
    if (!carousel.has(carousel.first, carousel.last)) {
        $.get('index.php?a=1&p=gallery&action=ajaxtop&id={$smarty.request.id}&ascdesc={$smarty.request.ascdesc}&categ={$smarty.request.categ}', {
            first: carousel.first,
            last: carousel.last
        },
        function(xml) {
            addCallback(carousel, carousel.first, carousel.last, xml);
        },'xml');
    }
}
function addCallback(carousel, first, last, xml) {
    carousel.size(parseInt($('total', xml).text()));
    $('image', xml).each(function(i) {
        carousel.add(first + i, $(this).text());
    });
}

$(document).ready(function() {
    $('#mycarousel').jcarousel({
        itemLoadCallback: loadCallback
    });
    $('#slider').cycle();

    $('.colorbox_img').colorbox({
        photo: true,
        transition: "elastic",
        maxHeight: "98%",
        maxWidth: "98%",
        slideshow: true,
        slideshowAuto: false,
        slideshowSpeed: 2500,
        current: "{#GlobalImage#} {ldelim}current{rdelim} {#PageNavi_From#} {ldelim}total{rdelim}",
        slideshowStart: "{#GlobalStart#}",
        slideshowStop: "{#GlobalStop#}",
        previous: "{#GlobalBack#}",
        next: "{#GlobalNext#}",
        close: "{#GlobalGlose#}"
    });
});
//-->
</script>

<div class="box_innerhead">{$Gallery_inf->TitleGalName|sanitize}</div>
<br />
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      {#Gallery_InfStart#} {$Gallery_inf->Datum|date_format: $lang.DateFormatSimple} {#Gallery_InfBy#} {$Gallery_inf->AutorLink|specialchars} {#Gallery_InfStart2#} {$Gallery_inf->Images} {#Gallery_InfStart3#}
      {if $Gallery_inf->GalText}
        <br />
        <br />
        <strong>{#Gallery_InfDescr#}</strong>
        <br />
        {$Gallery_inf->GalText|sslash}
      {/if}
      {if $subGalleries}
        <br />
        <strong>{#Gallery_IncludedSub#}</strong>
        <br />
        {foreach from=$subGalleries item=subgallery name=subgalleries}
          <a href="{$subgallery->Link}">{$subgallery->SubGalName|sanitize}</a> {if !$smarty.foreach.subgalleries.last},&nbsp;{/if}
        {/foreach}
        <br />
      {/if}
    </td>
    <td>
      <div class="gallery_fadebox">
        <div id="slider" class="gallery_fadebox_pics">
          {foreach from=$items item=gal name=BilderArray name=sls}
            <a href="index.php?p=gallery&amp;action=showimage&amp;id={$gal->Id}&amp;galid={$smarty.request.id}&amp;ascdesc={$smarty.request.ascdesc|default:'desc'}&amp;categ={$smarty.request.categ}&amp;area={$area}"><img src="{$gal->Thumbnail}" alt="" /></a>
            {/foreach}
        </div>
      </div>
    </td>
  </tr>
</table>
<br />
<div class="gallery_actions">
  {if $Galsettings->Favoriten == 1}
    {if $loggedin}
      {if isset($smarty.request.favorites) && $smarty.request.favorites == 1}
        <a href="index.php?p=gallery&amp;action=showgallery&amp;id={$smarty.request.id}&amp;categ={$smarty.request.categ}&amp;name={$Gallery_inf->GalName|translit}&amp;area={$area}"><img class="absmiddle" src="{$imgpath_page}faves.png" alt="" /></a>&nbsp;&nbsp;
        <a href="index.php?p=gallery&amp;action=showgallery&amp;id={$smarty.request.id}&amp;categ={$smarty.request.categ}&amp;name={$Gallery_inf->GalName|translit}&amp;area={$area}">{#Gallery_ShowAllImages#}</a>&nbsp;&nbsp;
        {if $items}
          <img class="absmiddle" src="{$imgpath_page}delete_small.png" alt="" />&nbsp; <a href="index.php?p=gallery&amp;action=delete_allfavorites&amp;galid={$smarty.request.id}&amp;categ={$smarty.request.categ}&amp;name={$Gallery_inf->GalName|translit}">{#Gallery_DelFavorites#}</a>
        {/if}
      {else}
        {if get_active('gallery_favorites')}
          <a href="index.php?p=gallery&amp;action=showgallery&amp;id={$smarty.request.id}&amp;categ={$smarty.request.categ}&amp;name={$Gallery_inf->GalName|translit}&amp;favorites=1&amp;area={$area}"><img class="absmiddle" src="{$imgpath_page}faves.png" alt="" /></a>&nbsp;&nbsp;
          <a href="index.php?p=gallery&amp;action=showgallery&amp;id={$smarty.request.id}&amp;categ={$smarty.request.categ}&amp;name={$Gallery_inf->GalName|translit}&amp;favorites=1&amp;area={$area}">{#Gallery_ShowFavorites#}</a>
        {/if}
      {/if}
    {/if}
  {/if}
  {foreach from=$items item=gal name=BilderArray name=sls}
    {if $smarty.foreach.sls.first}
      &nbsp;<a href="javascript: void(0);" class="stip" title="{$lang.Gallery_OpenInDiashow|tooltip}" onclick="openWindow('index.php?p=gallery&amp;action=showimage&amp;id={$gal->Id}&amp;galid={$smarty.request.id}&amp;blanc=1&amp;first_id={$Gallery_inf->First->Id}&amp;ascdesc={$smarty.request.ascdesc|default:'desc'}&amp;categ={$smarty.request.categ}','',800,800,1);"><img class="absmiddle" src="{$imgpath_page}diashow.png" alt="{#Gallery_Diashow#}" /></a>
      <a href="javascript: void(0);" class="stip" title="{$lang.Gallery_OpenInDiashow|tooltip}" onclick="openWindow('index.php?p=gallery&amp;action=showimage&amp;id={$gal->Id}&amp;galid={$smarty.request.id}&amp;blanc=1&amp;first_id={$Gallery_inf->First->Id}&amp;ascdesc={$smarty.request.ascdesc|default:'desc'}&amp;categ={$smarty.request.categ}&amp;area={$area}', '', 800, 800, 1);">{#Gallery_Diashow#}</a>
    {/if}
  {/foreach}
</div>
{if !$items}
  <div class="h2">{#Gallery_NoImages#}</div>
  <br />
{else}
  <div class="gallery_pic_box">
    <div id="x">
      <table width="100%" cellpadding="0" cellspacing="1">
        <tr>
          {foreach from=$items item=gal name=BilderArray}
            {assign var=c value=$c+1}
            <td class="gallery_box" valign="bottom">
              <div class="gallery_box_ani">
                <a href="index.php?p=gallery&amp;action=showimage&amp;id={$gal->Id}&amp;galid={$smarty.request.id}&amp;ascdesc={$smarty.request.ascdesc|default:'desc'}&amp;categ={$smarty.request.categ}&amp;area={$area}"><img class="gallery_box_img" src="{$gal->Thumbnail}" alt="" /></a>
              </div>
              <div>
                <a rel="pop_img" class="colorbox_img" href="{$gal->Thumbnail_Gross}">{#New_Window#}</a>
                {if $Galsettings->Info_Klein == 1}
                  <br />
                  <small>{#Global_Hits#}: {$gal->Klicks} {if !empty($gal->comments)}| {#Comments#}: {$gal->comments}{/if}</small>
                {/if}
              </div>
            </td>
            {if $c % $Galsettings->Bilder_Zeile == 0 && !$smarty.foreach.BilderArray.last}
            </tr>
            <tr>
            {/if}
          {/foreach}
        </tr>
      </table>
    </div>
  </div>
{/if}
{if $Galsettings->Meist_Gesehen == 1 && $smarty.request.favorites != 1}
  <br />
  <div class="box_innerhead">{#Gallery_MostPopular#}</div>
  <div class="jcarousel_div">
    <div id="mycarousel" class="jcarousel-slider">
<script type="text/javascript">
<!-- //
document.write('<ul></ul>');
//-->
</script>
    </div>
  </div>
{/if}
<br />
{if $GalNavi}
  {$GalNavi}
  <br />
{/if}
<form method="post" action="index.php">
  <input type="hidden" name="p" value="gallery" />
  <input type="hidden" name="action" value="showgallery" />
  <input type="hidden" name="id" value="{$smarty.request.id}" />
  <input type="hidden" name="categ" value="{$smarty.request.categ}" />
  <input type="hidden" name="name" value="xxx" />
  <select class="input" name="ascdesc">
    <option value="desc" {if isset($smarty.request.ascdesc) && $smarty.request.ascdesc == "desc"}selected="selected"{/if}>{#NewestFirst#}</option>
    <option value="asc" {if isset($smarty.request.ascdesc) && $smarty.request.ascdesc == "asc"}selected="selected"{/if}>{#OldestFirst#}</option>
  </select>&nbsp;
  <select class="input" name="pp">
    <option value="{$Galsettings->Bilder_Seite}" {if isset($smarty.request.pp) && $smarty.request.pp == $Galsettings->Bilder_Seite}selected="selected"{/if}>{$Galsettings->Bilder_Seite} {#Gallery_ImagesPP#}</option>
    {section name=pp loop=95 step=5}
      <option value="{$smarty.section.pp.index+10}" {if isset($smarty.request.pp) && $smarty.request.pp == $smarty.section.pp.index+10}selected="selected"{/if}>{$smarty.section.pp.index+10} {#Gallery_ImagesPP#}</option>
    {/section}
  </select>
  <input type="hidden" name="page" value="1" />&nbsp;
  {if isset($smarty.request.favorites) && $smarty.request.favorites == 1}
    <input type="hidden" name="favorites" value="1" />
  {/if}
  <input type="submit" class="button" value="{#Gallery_PPButton#}" />
</form>
