<html>
<head>
<title>{#MpPreview#}</title>
{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
<link type="text/css" rel="stylesheet" href="{$csspath}/areaselect.css" />
<script type="text/javascript" src="{$jspath}/jareaselect.js"></script>
<script type="text/javascript" src="{$jspath}/jsimplecolor.js"></script>
<script type="text/javascript">
<!-- //
{$reload}
function selfile(src) {
    if (src === undefined) {
        src = '';
    }
    parent.document.dat.fn.value = src;
}
$(document).ready(function() {
    showImage = function() {
        var width = $('#image_right').width();
        var height = $('#image_right').height();
        $('#image_right').imgAreaSelect({
            x1: width / 4,
            y1: height / 4,
            x2: width / 4 + width / 2,
            y2: height / 4 + height / 2,
            handles: true,
            onSelectEnd: function (id, selection) {
                $('input[name="crop-x"]').val(selection.x1);
                $('input[name="crop-y"]').val(selection.y1);
                $('input[name="crop-w"]').val(selection.x2 - selection.x1);
                $('input[name="crop-h"]').val(selection.y2 - selection.y1);
                $('input[name="thumb-w"]').val(width);
                $('input[name="thumb-h"]').val(height);
            }
        });
    };
    $('#image_crop').on('change', function() {
        $('html, body').animate({ scrollTop: 0 }, 400, showImage);
    });
    $('#choosercolor').simpleColor({
        displayColorCode: true,
        boxWidth: 45,
        boxHeight: 18,
        displayCSS: {
            'float': 'left',
            'margin': '2px 0 0 4px'
        },
        onSelect: function() {
            $('#border').prop('checked', true);
        }
    });
    selradio = function(id) {
        $('#' + id).prop('checked', true);
    };
});
//-->
</script>
</head>
<body topmargin="0" leftmargin="0" id="mediapool" oncontextmenu="return false">
  <div class="header">{#MpPreview#}</div>
  <div class="popbox">
    <form method="post" action="">
      <table width="99%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td align="center" valign="top">
            <fieldset>
              <legend>
                {#GalleryActual#}
                {if !empty($sizes)}
                  {$sizes}
                {/if}
              </legend>
              <div style="cursor: pointer" onclick="selfile('{$image}');">
                {if !empty($thumb)}
                  {$thumb}
                  <br />
                  <strong>{$image}</strong>
                {else}
                  <br />
                  <h3>{#MpNoFile#}</h3>
                {/if}
              </div>
            </fieldset>
          </td>
        </tr>
        {if !empty($thumb) && perm('mediapool_edit')}
          <tr>
            <td valign="top">
              <fieldset>
                <legend>{#GalleryAction#}</legend>
                <table width="100%" border="0" cellspacing="0" cellpadding="1">
                  <tr>
                    <td>
                      <input id="rotate" name="edit_action" type="radio" value="rotate" />
                      <select style="width: 240px" class="input" name="degrees" onclick="document.getElementById('rotate').checked = 'true';">
                        <option value="90" selected="selected">{#GalleryRclockwise#}</option>
                        <option style="float: left;" value="-90">{#GalleryRaclockwise#}</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input id="brightness" name="edit_action" type="radio" value="brightness" />
                      <select style="width: 240px" class="input" name="percent" onclick="selradio('brightness');">
                        <option value="5" selected="selected">{$lang.GalleryLighter|replace: '__NUM__': '5'}</option>
                        <option value="10">{$lang.GalleryLighter|replace: '__NUM__': '10'}</option>
                        <option value="15">{$lang.GalleryLighter|replace: '__NUM__': '15'}</option>
                        <option value="20">{$lang.GalleryLighter|replace: '__NUM__': '35'}</option>
                        <option value="25">{$lang.GalleryLighter|replace: '__NUM__': '25'}</option>
                        <option value="30">{$lang.GalleryLighter|replace: '__NUM__': '30'}</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input id="brightness2" name="edit_action" type="radio" value="brightness2" />
                      <select style="width: 240px" class="input" name="percent2" onclick="selradio('brightness2');">
                        <option value="-5" selected="selected">{$lang.GalleryDarker|replace: '__NUM__': '5'}</option>
                        <option value="-10">{$lang.GalleryDarker|replace: '__NUM__': '10'}</option>
                        <option value="-15">{$lang.GalleryDarker|replace: '__NUM__': '15'}</option>
                        <option value="-20">{$lang.GalleryDarker|replace: '__NUM__': '20'}</option>
                        <option value="-25">{$lang.GalleryDarker|replace: '__NUM__': '25'}</option>
                        <option value="-30">{$lang.GalleryDarker|replace: '__NUM__': '30'}</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input id="contrast" name="edit_action" type="radio" value="contrast" />
                      <select style="width: 240px" class="input" name="percent" onclick="selradio('contrast');">
                        <option value="5" selected="selected">{$lang.GalleryContrLighter|replace: '__NUM__': '5'}</option>
                        <option value="10">{$lang.GalleryContrLighter|replace: '__NUM__': '10'}</option>
                        <option value="15">{$lang.GalleryContrLighter|replace: '__NUM__': '15'}</option>
                        <option value="20">{$lang.GalleryContrLighter|replace: '__NUM__': '35'}</option>
                        <option value="25">{$lang.GalleryContrLighter|replace: '__NUM__': '25'}</option>
                        <option value="30">{$lang.GalleryContrLighter|replace: '__NUM__': '30'}</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input id="contrast2" name="edit_action" type="radio" value="contrast2" />
                      <select style="width: 240px" class="input" name="percent2" onclick="selradio('contrast2');">
                        <option value="-5" selected="selected">{$lang.GalleryContrDarker|replace: '__NUM__': '5'}</option>
                        <option value="-10">{$lang.GalleryContrDarker|replace: '__NUM__': '10'}</option>
                        <option value="-15">{$lang.GalleryContrDarker|replace: '__NUM__': '15'}</option>
                        <option value="-20">{$lang.GalleryContrDarker|replace: '__NUM__': '20'}</option>
                        <option value="-25">{$lang.GalleryContrDarker|replace: '__NUM__': '25'}</option>
                        <option value="-30">{$lang.GalleryContrDarker|replace: '__NUM__': '30'}</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div style="float: left">
                        <label><input id="border" type="radio" name="edit_action" value="border" /> {#GalleryBorder#}</label>&nbsp;
                          {strip}
                          <select style="width: 60px" class="input" name="border_width" onclick="selradio('border');">
                            {section name=min loop=100 start=0 step=1}
                              <option value="{$smarty.section.min.index+1}"{if $smarty.section.min.index+1 == 5} selected="selected"{/if}>{$smarty.section.min.index+1}px</option>
                            {/section}
                          </select>
                        {/strip}
                      </div>
                      <input name="color" type="text" class="input" id="choosercolor" value="#FFF" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <label><input id="corners" type="radio" name="edit_action" value="corners" /> {#GalleryCorners#}</label>
                        {strip}
                        <select style="width: 60px" class="input" name="pixel" onclick="selradio('corners');">
                          {section name=min loop=30 start=0 step=1}
                            <option value="{$smarty.section.min.index+1}"{if $smarty.section.min.index+1 == 5} selected="selected"{/if}>{$smarty.section.min.index+1}px</option>
                          {/section}
                        </select>
                      {/strip}
                    </td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="smooth" /> {#GallerySmooth#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="emboss" /> {#GalleryEmboss#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="meanremoval" /> {#GalleryMean#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="sepia" /> {#GallerySepia#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="picture" /> {#GalleryPicture#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="edgedetect" /> {#GalleryEdgedetect#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="blurgaussian" /> {#GalleryBlurGauss#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="blurselective" /> {#GalleryBlurSelect#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="grayscale" /> {#GalleryGrayscale#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="flip" /> {#GalleryFlip#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="flop" /> {#GalleryFlop#}</label></td>
                  </tr>
                  <tr>
                    <td><label><input type="radio" name="edit_action" value="negate" /> {#GalleryNegate#}</label></td>
                  </tr>
                  <tr>
                    <td>
                      <label><input id="image_crop" type="radio" name="edit_action" value="crop" /> {#GalleryCrop#}</label>
                      <input type="hidden" name="crop-x" value="" />
                      <input type="hidden" name="crop-y" value="" />
                      <input type="hidden" name="crop-w" value="" />
                      <input type="hidden" name="crop-h" value="" />
                      <input type="hidden" name="thumb-w" value="" />
                      <input type="hidden" name="thumb-h" value="" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <label>
                        <input id="resize" type="radio" name="edit_action" value="resize" /> {#GlobalSize#}
                        <select style="width: 140px" class="input" name="type" onclick="selradio('resize');">
                          <option value="auto">{#Automatically#}</option>
                          <option value="width">{#GlobalWidth#}</option>
                          <option value="height">{#GlobalHeight#}</option>
                        </select>
                        <input size="3" name="size" type="text" class="input" value="{$size}" />px
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td><label><input name="copy" type="checkbox" value="1" /> {#Copy#}</label></td>
                  </tr>
                </table>
                <input name="save" type="hidden" value="1" />
                <input name="image" type="hidden" value="{$image}" />
                <input name="width" type="hidden" value="{$width}" />
                <input name="height" type="hidden" value="{$height}" />
                <input name="dir" type="hidden" value="{$smarty.request.dir}" />
                <input name="typ" type="hidden" value="{$smarty.request.typ}" />
                <input name="target" type="hidden" value="{$smarty.request.target}" />
                <input type="submit" class="button" name="button" value="{#GalleryButton#}" />
              </fieldset>
            </td>
          </tr>
        {/if}
      </table>
    </form>
  </div>
</body>
</html>