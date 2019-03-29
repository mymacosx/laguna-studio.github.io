<script type="text/javascript" src="{$jspath}/jsimplecolor.js"></script>
<script type="text/javascript" src="{$jspath}/jform.js"></script>
<script type="text/javascript">
<!-- //
$(document).ready(function() {
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
    $('#editform').submit(function() {
        var options = {
            target: '#output',
            success: function() { }
        };
        $(this).ajaxSubmit(options);
        return false;
    });
});
//-->
</script>

<div class="popbox">
  <form name="editform" id="editform" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td valign="top">
          <fieldset>
            <legend>{#GalleryActual#}</legend>
            <div class="gallery_preload" id="output">
              {$res->Img}
              <div style="clear: both"></div>
            </div>
          </fieldset></td>
        <td width="300" valign="top">
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
                      <select style="width: 60px" class="input" name="width" onclick="selradio('border');">
                        {section name=min loop=100 start=0 step=1}
                          <option value="{$smarty.section.min.index+1}"{if $smarty.section.min.index+1 == 5} selected="selected"{/if}>{$smarty.section.min.index+1}px</option>
                        {/section}
                      </select>
                    {/strip}
                  </div>
                  <input name="color" type="text" class="input" id="choosercolor" value="#fff" />
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
            </table>
            <input name="save" type="hidden" id="save" value="1" />
            <input type="submit" class="button" name="button" id="button" value="{#GalleryButton#}" />
            <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
          </fieldset>
          {if $ImgWarning}
            <div class="error_box" style="padding: 20px"> {$lang.Gallery_E_Warning|replace: '__DIM__': $ImgDim} </div>
          {/if}
        </td>
      </tr>
    </table>
  </form>
</div>
