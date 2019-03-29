{script file="$jspath/jcarousel.js" position='head'}
<script type="text/javascript">
<!-- //
function loadCallback(carousel) {
    if (!carousel.has(carousel.first, carousel.last)) {
        $.get('index.php?aj=1&p=gallery&action=ajaxrandom&id={$smarty.request.galid}&ascdesc={$smarty.request.ascdesc}&blanc=1&first_id={$smarty.get.first_id}&categ={$smarty.request.categ}', {
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
function initImage() {
    imageId = 'gallery_image_thumb';
    image = document.getElementById(imageId);
    image.style.visibility = 'visible';
}
window.onload = function() {
    initImage();
}
//-->

</script>

<div id="body_blanc">
  <div class="box_innerhead">{$pagetitle}</div>
  <div align="center">
    <div class="gallery_diashow_toolbox">
      <table width="100%" cellspacing="0" cellpadding="3">
        <tr>
          <td colspan="2" align="center">
            {if $data->NextImage}
              <button class="gfx_buttons" onclick="location.href='index.php?diashow=1&amp;ascdesc={$smarty.request.ascdesc}&amp;stop=1&amp;p=gallery&amp;action=showimage&amp;id={$smarty.get.id}&amp;galid={$smarty.get.galid}&amp;blanc=1{if !empty($smarty.request.refreshtime)}&amp;refreshtime={$smarty.request.refreshtime}{/if}&amp;first_id={$smarty.get.first_id}&amp;categ={$smarty.request.categ}&amp;area={$area}';">{#Gallery_Pause#}</button>&nbsp;
              <button class="gfx_buttons" onclick="location.href = '{$data->NextImageLink_Blanc}';">{#Gallery_Play#}</button>&nbsp;
            {/if}
            <button class="gfx_buttons" onclick="location.href = 'index.php?diashow=1&amp;ascdesc={$smarty.request.ascdesc}&amp;p=gallery&amp;action=showimage&amp;id={$smarty.get.first_id}&amp;galid={$smarty.get.galid}&amp;blanc=1&amp;first_id={$smarty.get.first_id}&amp;categ={$smarty.request.categ}&amp;area={$area}';">{#Gallery_PlayAgain#}</button>&nbsp;
            <button class="gfx_buttons" onclick="javascript: window.close();">{#WinClose#}</button>&nbsp;
          </td>
        </tr>
        <tr>
          <td>
            {if permission('gallery_download') && $gs->Download == 1}
              <strong><a href="index.php?diashow=1&amp;p=gallery&amp;action=showimage&amp;id={$smarty.request.id}&amp;galid={$smarty.request.galid}&amp;blanc=1&amp;download=1&amp;area={$area}">{#Gallery_Download#}</a></strong> ({#GlobalSize#}: {$data->Image_Width} X {$data->Image_Height})
              {/if}
          </td>
          <td align="right">{#Gallery_ImageNextIn#} <strong><span id="numberCountdown">{#Gallery_ImageNextPause#}</span></strong> {#Gallery_ImageNextSeconds#}</td>
        </tr>
      </table>
      <br />
    </div>
    <div class="gallery_pic_preview_container">
      <div class="gallery_pic_diashow">
        <div class="gallery_diashowload"><img id="gallery_image_thumb" onload="nextImg();
            startCountdown();" src="{$data->Image}" alt="{$data->ImageName|sanitize}" vspace="0" /></div>
        <br />
        <h2>{$data->ImageName|sanitize}</h2>
        <br />
        {$data->ImageText}
        <br />
        <br />
      </div>
    </div>
    {if $data->NextImage}
      {if $smarty.request.blanc == 1 && $smarty.request.stop != 1}
<script language ="JavaScript" type="text/javascript">
<!-- //
var ccontinue = true;
var g_iCount = new Number();
var g_iCount = {$Galsettings->Diashow_Zeit} + 1;
var time = {$Galsettings->Diashow_Zeit}000;
function startCountdown() {
    if ((g_iCount - 1) >= 0) {
        g_iCount = g_iCount - 1;
        document.getElementById('numberCountdown').innerHTML = g_iCount;
        setTimeout('startCountdown()', 1000);
    }
}
function nextImg(status) {
    if (status == 'stop') {
        stop();
    } else {
        setTimeout("document.location.href ='{$data->NextImageLink_Blanc|replace: '&amp;': '&'}'", time);
    }
}
function gstop() {
    nextImg('stop');
}
//-->
</script>
      {/if}
    {/if}
    <div class="jcarousel_div">
      <div id="mycarousel" class="jcarousel-slider">
        <ul>
        </ul>
      </div>
    </div>
    {if !empty($gs->Banner_Code)}
      <div class="gallery_bannercode">{$gs->Banner_Code}</div>
    {/if}
  </div>
</div>
