<script type="text/javascript">
<!-- //
$(document).ready(function() {
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
{if $Gallery_inf->GalText}
  {$Gallery_inf->GalText|sslash}
{/if}
{if $subGalleries}
  <br />
  <strong>{#Gallery_IncludedSub#}</strong>
  <br />
  {foreach from=$subGalleries item=subgallery name=subgalleries}
    <a href="{$subgallery->Link}">{$subgallery->SubGalName|sanitize}</a>{if !$smarty.foreach.subgalleries.last},&nbsp;{/if}
  {/foreach}
  <br />
{/if}
{if empty($items)}
  <div class="h2">{#Gallery_NoImages#}</div>
  <br />
{else}
  <div class="gallery_pic_box">
    <div id="x">
      <br />
      <table width="100%" cellpadding="0" cellspacing="1">
        <tr>
          {assign var=c value=0}
          {foreach from=$items item=gal name=BilderArray}
            {assign var=c value=$c+1}
            <td valign="top" class="gallery_box">
              <div class="gallery_box_ani"><a class="colorbox_img" rel="group" title="{$gal->ImageName}"  href="{$gal->Thumbnail_Gross}"><img class="gallery_box_img" src="{$gal->Thumbnail}" alt="{$gal->ImageName}" /></a></div>
            </td>
            {if $c % $Galsettings->Bilder_Zeile == 0 &&! $smarty.foreach.BilderArray.last}
            </tr>
            <tr>
            {/if}
          {/foreach}
        </tr>
      </table>
      <br />
      {#Gallery_InfStart#} {$Gallery_inf->Datum|date_format: $lang.DateFormatSimple} {#Gallery_InfBy#} {$Gallery_inf->AutorLink|specialchars} {#Gallery_InfStart2#} {$Gallery_inf->Images} {#Gallery_InfStart3#}
    </div>
  </div>
{/if}
<br />
{if !empty($GalNavi)}
  {$GalNavi}
  <br />
{/if}
<form method="post" action="index.php">
  <input type="hidden" name="p" value="gallery" />
  <input type="hidden" name="action" value="showgallery" />
  <input type="hidden" name="id" value="{$smarty.request.id}" />
  <input type="hidden" name="categ"  value="{$smarty.request.categ}" />
  <input type="hidden" name="name"  value="xxx" />
  <select class="input" name="ascdesc">
    <option value="desc" {if isset($smarty.request.ascdesc) && $smarty.request.ascdesc == "desc"}selected="selected"{/if}>{#NewestFirst#}</option>
    <option value="asc" {if isset($smarty.request.ascdesc) && $smarty.request.ascdesc == "asc"}selected="selected"{/if}>{#OldestFirst#}</option>
  </select>
  <select class="input" name="pp">
    <option value="{$Galsettings->Bilder_Seite}" {if isset($smarty.request.pp) && $smarty.request.pp == $Galsettings->Bilder_Seite}selected="selected"{/if}>{$Galsettings->Bilder_Seite} {#Gallery_ImagesPP#}</option>
    {section name=pp loop=95 step=5}
      <option value="{$smarty.section.pp.index+10}" {if isset($smarty.request.pp) && $smarty.request.pp == $smarty.section.pp.index+10}selected="selected"{/if}>{$smarty.section.pp.index+10} {#Gallery_ImagesPP#}</option>
    {/section}
  </select>
  <input type="hidden" name="page"  value="1" />
  {if isset($smarty.request.favorites) && $smarty.request.favorites == 1}
    <input type="hidden" name="favorites"  value="1" />
  {/if}
  <input type="submit" class="button" value="{#Gallery_PPButton#}" />
</form>
