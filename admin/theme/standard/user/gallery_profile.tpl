{if $smarty.request.id == $smarty.session.benutzer_id}
<script type="text/javascript">
<!-- //
function selectWert(obj) {
    with (obj) return options[selectedIndex].value;
}
function getGal() {
    var ziel = "index.php?p=user&action=gal&do=" + selectWert(document.getElementById('gal_sec')) + "&area={$area}";
    var w = 800, h = 480, x = 0, y = 0, parameter = '';
    if (w < screen.availWidth || h < screen.availHeight) {
        x = (screen.availWidth - w - 12) / 2;
    }
    y = (screen.availHeight - h - 104) / 2;
    if (window.opera) {
        y = 0;
    }
    if (x < 0 || y < 0) {
        x = 0; y = 0;
    } else {
        parameter = "width=" + w + ",height=" + h + ",";
    }
    parameter += "left=" + x + ",top=" + y;
    parameter += ",menubar=no,location=no,toolbar=no,status=no";
    parameter += ",resizable=yes,scrollbars=yes";
    var Fenster = window.open(ziel, "PopUp", parameter);
    if (Fenster) {
        Fenster.focus();
    }
    return !Fenster;
}
//-->
</script>

<div class="infobox">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <small>{#GalleryInfo#}</small>
        <br />
        <br />
        <strong>{#Global_Action#}</strong>
        <select style="width: 250px; margin-left: 20px;" name="album" id="gal_sec">
          {if $gal_con == 0 || $gal_con > $gal_count}
            <option value="new">{#NewAlbum#}</option>
          {/if}
          {if $gal_count > 0}
            <option value="edit">{#EditAlbum#}</option>
            <option value="del">{#DelAlbum#}</option>
          {/if}
        </select>&nbsp;&nbsp;
        <input type="button" name="gal" class="button" onclick="getGal();" value="{#GotoButton#}" />
        <br />
      </td>
    </tr>
  </table>
</div>
{/if}
