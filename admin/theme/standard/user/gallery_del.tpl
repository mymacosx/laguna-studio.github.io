<script type="text/javascript">
<!-- //
function selectWert(obj) {
    with (obj) return options[selectedIndex].value;
}
function deleteId() {
    var check = confirm('{#ConfirmDel#}');
    if(check !== false) {
        location = "{$baseurl}/index.php?p=user&action=gal&do=del&id=" + selectWert(document.getElementById('Aid')) + "&area={$area}";
    }
}
//-->
</script>

<div class="popup_header h4">{#DelAlbum#}</div>
<div id="body_blanc" style="padding: 5px" align="center">
  <table width="100%" border="0" cellspacing="0" cellpadding="10">
    <tr>
      <td width="30%" align="left"><strong><label for="l_gal_new_name">{#DelAlbum#}</label></strong></td>
      <td align="left">
        <select style="width: 180px" name="album" id="Aid">
          {foreach from=$albums item=a}
            <option value="{$a.Id}">{$a.Name}</option>
          {/foreach}
        </select>&nbsp;&nbsp;
        <input type="button" name="ch" class="button" onclick="deleteId();" value="{#Delete#}" />
      </td>
    </tr>
  </table>
  <div style="border: 2px dashed red;padding: 10px;margin: 10px" align="center"><small>{#DelInfoGal#}</small></div>
  <input type="button" class="button" onclick="window.opener.location.reload();self.close();" value="{#WinClose#}" />
</div>
