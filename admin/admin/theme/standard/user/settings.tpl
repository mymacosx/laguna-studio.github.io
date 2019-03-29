<div class="header">{#SettingsModule#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td colspan="2" class="headers_row">{#Groups_Avatar#}</td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GlobalImageSize#}</td>
      <td class="row_right"><input class="input" name="AvatarWidth" type="text" value="{$row.AvatarWidth}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GlobalImageCompres#}</td>
      <td class="row_right"><input class="input" name="AvatarCompres" type="text" value="{$row.AvatarCompres}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td colspan="2" class="headers_row">{#UserGallery#}</td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserGalleryAktiv#}</td>
      <td class="row_right">
        <label><input type="radio" name="UserGallery" value="1" {if $row.UserGallery == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="UserGallery" value="0" {if $row.UserGallery == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserLimitAlbom#}</td>
      <td class="row_right"><input class="input" name="LimitAlbom" type="text" value="{$row.LimitAlbom}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserLimitFotos#}</td>
      <td class="row_right"><input class="input" name="LimitFotos" type="text" value="{$row.LimitFotos}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GroupLimitFotosStr#}</td>
      <td class="row_right"><input class="input" name="LimitFotosStr" type="text" value="{$row.LimitFotosStr}" size="4" maxlength="2" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#Groups_MaxWidth#}</td>
      <td class="row_right"><input class="input" name="WidthFotos" type="text" value="{$row.WidthFotos}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GlobalImageCompres#}</td>
      <td class="row_right"><input class="input" name="ImageCompres" type="text" value="{$row.ImageCompres}" size="4" maxlength="2" /></td>
    </tr>
    <tr>
      <td colspan="2" class="headers_row">{#GroupFriends#}</td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GroupFriendsLimitStr#}</td>
      <td class="row_right"><input class="input" name="LimitFriendsStr" type="text" value="{$row.LimitFriendsStr}" size="4" maxlength="2" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#AvatarFriends#}</td>
      <td class="row_right"><input class="input" name="AvatarFriends" type="text" value="{$row.AvatarFriends}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GroupFriendsAktiv#}</td>
      <td class="row_right">
        <label><input type="radio" name="UserFriends" value="1" {if $row.UserFriends == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="UserFriends" value="0" {if $row.UserFriends == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#GroupFriendsLimit#}</td>
      <td class="row_right"><input class="input" name="LimitFriends" type="text" value="{$row.LimitFriends}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserVisits#}</td>
      <td class="row_right">
        <label><input type="radio" name="UserVisits" value="1" {if $row.UserVisits == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="UserVisits" value="0" {if $row.UserVisits == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#LimitVisits#}</td>
      <td class="row_right"><input class="input" name="LimitVisits" type="text" value="{$row.LimitVisits}" size="4" maxlength="2" /></td>
    </tr>
    <tr>
      <td colspan="2" class="headers_row">{#UserActions#}</td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserActionsAktiv#}</td>
      <td class="row_right">
        <label><input type="radio" name="UserActions" value="1" {if $row.UserActions == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="UserActions" value="0" {if $row.UserActions == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="300" class="row_left">{#UserActionsLimit#}</td>
      <td class="row_right"><input class="input" name="LimitActions" type="text" value="{$row.LimitActions}" size="4" maxlength="3" /></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" value="{#Save#}" class="button" />
</form>

