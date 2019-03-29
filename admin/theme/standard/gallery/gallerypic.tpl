{script file="$jspath/jcarousel.js" position='head'}
<script type="text/javascript">
<!-- //
function loadCallback(carousel) {
    if (!carousel.has(carousel.first, carousel.last)) {
        $.get('index.php?aj=1&p=gallery&action=ajaxrandom&id={$smarty.request.galid}&ascdesc={$smarty.request.ascdesc}&categ={$smarty.request.categ}', {
            first: carousel.first,
            last: carousel.last
        },
        function(xml) {
            addCallback(carousel, carousel.first, carousel.last, xml);
        }, 'xml');
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
        itemLoadCallback: loadCallback,
        start: 1
    });
});
{if $Galsettings->Favoriten == 1}
function addFavorite(id, image, galid) {
    $.get('index.php?action=addfavorite&p=gallery', {
        img_id: image,
        gal_id: galid
    },
    function(data){
        document.getElementById(id).innerHTML = data;
        document.getElementById('favolink_' + image).style.display = 'none';
    });
}
function deleteFavorite(id, image, galid) {
    $.get('index.php?action=deletefavorite&p=gallery', {
        img_id: image,
        gal_id: galid
    },
    function(data) {
        document.getElementById(id).innerHTML = data;
        document.getElementById('del_favolink_' + image).style.display = 'none';
    });
}
{/if}
//-->
</script>

<div align="center">
  <div class="gallery_pic_preview_container">
    <div class="gallery_pic_preview">
      <div class="gallery_box_anibig"><img id="gallery_image_thumb" src="{$data->Image}" alt="{$item->name}" title="{$item->name}" /></div>
      <br />
      <br />
      <h2>{$data->ImageName|sanitize|default:$lang.GlobalNoName}</h2>
      <br />
      {$data->ImageText|specialchars}
      <br />
      <br />
      <div class="gallery_pic_navelem">
        <div style="float: left; width: 200px; text-align: left;">
          {if $data->PrefImageLink}
            <a href="{$data->PrefImageLink}"><img class="absmiddle" src="{$imgpath_page}arrow_left_small.png" alt="" /></a>
            <a href="{$data->PrefImageLink}">{#Gallery_ImagePrev#}</a> <a href="{$data->PrefImageLink}">{$data->PrefImage}</a>
          {/if}
        </div>
        <div style="float: right; width: 200px; text-align: right;">
          {if $data->NextImageLink}
            <a href="{$data->NextImageLink}">{#Gallery_ImageNext#}</a>
            <a href="{$data->NextImageLink}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
            <a href="{$data->NextImageLink}">{$data->NextImage}</a>
          {/if}
        </div>
        <div class="clear"></div>
      </div>
      <br />
      <a href="javascript: history.go(-1);">{#GlobalBack#}</a>
      {if permission('gallery_download') && $gs->Download == 1}
        - <a href="javascript: void(0);" onclick="location.href='index.php?download=1&amp;p=gallery&amp;action=showimage&amp;id={$smarty.request.id}&amp;galid={$smarty.request.galid}';">{#Gallery_Download#}</a> ({$data->Image_Width} X {$data->Image_Height})
      {/if}
      - <a href="javascript: void(0);" onclick="openWindow('index.php?p=gallery&amp;action=showimage&amp;id={$smarty.request.id}&amp;galid={$smarty.request.galid}&amp;blanc=1&amp;first_id={$first_id}&amp;ascdesc={$smarty.request.ascdesc}&amp;categ={$smarty.request.categ}', '', 800, 800, 1);">{#Gallery_Diashow#}</a>
      <br />
      {if $Galsettings->Favoriten == 1 && $loggedin}
        {if $Favorites && in_array($data->Id,$Favorites)}
          <span id="del_favolink_{$data->Id}">
            <br />
            <a href="javascript: void(0);" onclick="deleteFavorite('favo_{$data->Id}',{$data->Id},{$smarty.request.galid});"><img class="absmiddle" src="{$imgpath_page}delete_small.png" alt="{#Gallery_DeleteFavorite#}" /></a>
            <a href="javascript: void(0);" onclick="deleteFavorite('favo_{$data->Id}',{$data->Id},{$smarty.request.galid});">{#Gallery_DeleteFavorite#}</a></span>
          {else}
          <span id="favolink_{$data->Id}">
            {if get_active('gallery_favorites')}
              <br />
              <a href="javascript: void(0);" onclick="{if !$loggedin}alert('{#Gallery_JsAlertNotLogged#}');{else}addFavorite('favo_{$data->Id}',{$data->Id},{$smarty.request.galid});{/if}"><img class="absmiddle" src="{$imgpath_page}faves.png" alt="{#Gallery_AddFavorite#}" /></a>
              <a href="javascript: void(0);" onclick="{if !$loggedin}alert('{#Gallery_JsAlertNotLogged#}');{else}addFavorite('favo_{$data->Id}',{$data->Id},{$smarty.request.galid});{/if}">{#Gallery_AddFavorite#}</a>
            {/if}
          </span>
        {/if}
        <span id="favo_{$data->Id}"></span>
      {/if}
      <br />
      <br />
    </div>
  </div>
  <a name="moreimages"></a>
  <div class="jcarousel_div">
    <div id="mycarousel" class="jcarousel-slider">
<script type="text/javascript">
<!-- //
document.write('<ul></ul>');
//-->
</script>
    </div>
  </div>
  <br />
</div>
{$GetComments|default:''}
