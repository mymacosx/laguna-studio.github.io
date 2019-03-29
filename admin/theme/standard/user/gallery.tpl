<script type="text/javascript">
<!-- //
function galBrowser(ziel){
    var w = {$width};
    var h = {$height} - 20;
    var x = 0, y = 0, parameter = '';
    if (w < screen.availWidth || h < screen.availHeight) {
        x = (screen.availWidth - w - 12) / 2;
        y = (screen.availHeight - h - 104) / 2;
        if (window.opera) {
            y = 0;
        }
        if (x < 0 || y < 0) {
            x = 0;
            y = 0;
        } else {
            parameter = "width=" + w + ",height=" + h + ",";
        }
    }
    parameter += "left=" + x + ",top=" + y;
    parameter += ",menubar=no,location=no,toolbar=no,status=no";
    parameter += ",resizable=yes";
    var Fenster = window.open(ziel,"PopUp",parameter);
    if (Fenster) {
        Fenster.focus();
    }
    return !Fenster;
}
//-->
</script>

<a name="gallery"></a>
<div class="box_innerhead"><strong>{#Gallery_Name#}</strong></div>
<div class="infobox">
  {#Albums#} - {$gal}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{#Images#} - {$num_img}
  <br />
  <br />
  <table>
    <tr>
      {assign var='nnn' value=-1}
      {foreach from=$gallery item=g}
        {if !empty($g.Datei)}
          {assign var='nnn' value=$nnn+1}
          {if $nnn == $NLines}
          </tr>
          <tr>
            {assign var='nnn' value=0}
          {/if}
          <td width="90" height="90" valign="bottom" style="text-align: center">
            <a href="index.php?p=user&amp;action=gallery&amp;do=images&amp;id={$g.Id}&amp;area={$area}&amp;image={$g.Bild}" onclick="return galBrowser(this.href);"><img src="{$g.Image}" border="0" style="margin-bottom: 5px;" alt="" /></a>
            <br />
            <a href="index.php?p=user&amp;action=gallery&amp;do=images&amp;id={$g.Id}&amp;area={$area}&amp;image={$g.Bild}" onclick="return galBrowser(this.href);">{$g.Name}</a>
          </td>
        {/if}
      {/foreach}
    </tr>
  </table>
  {if !$gallery}
    <small>{#NoGallery#}</small>
    <br />
  {/if}
</div>
