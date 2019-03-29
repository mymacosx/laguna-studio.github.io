<div class="popbox">
  {if !empty($errors)}
    <table width="100%" border="0" cellpadding="5" cellspacing="1" class="tableborder">
      <tr>
        <td colspan="2" class="row_right">
          {foreach from=$errors item=error}
        <li>{$error}</li>
        {/foreach}
      </td>
      </tr>
    </table>
  {/if}
  <form action="" method="post">
    <input name="save" type="hidden" id="save" value="1" />
    <input type="hidden" name="f_id" value="{$smarty.get.f_id}" />
    <input type="hidden" name="g_id" value="{$smarty.request.g_id}" />
    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
      <tr>
        <th colspan="2" class="headers">{#Forums_Perm_Perms#}</th>
      </tr>
      <tr>
        <td width="60%" valign="top" class="row_left">{#Forums_Perm_view#}: </td>
        <td class="row_right">
          <label><input type="radio" name="can_see" value="1" {if !empty($permissions[0])}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="can_see" value="0" {if empty($permissions[0])}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td valign="top" class="row_left">{#Forums_Perm_viewother#}: </td>
        <td class="row_right">
          <label><input type="radio" name="can_see_topic" value="1" {if !empty($permissions[1])}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="can_see_topic" value="0" {if empty($permissions[1])}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td valign="top" class="row_left">{#Forums_Perm_dlatt#}: </td>
        <td class="row_right">
          <label><input type="radio" name="can_download_attachment" value="1" {if !empty($permissions[4])}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="can_download_attachment" value="0" {if empty($permissions[4])}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      {if $smarty.get.g_id != 2}
        <tr>
          <th colspan="2" class="headers">{#Forums_Perm_postPerms#}</th>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_open#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_create_topic" value="1" {if !empty($permissions[5])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_create_topic" value="0" {if empty($permissions[5])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_replyown#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_reply_own_topic" value="1" {if !empty($permissions[6])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_reply_own_topic" value="0" {if empty($permissions[6])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_replyother#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_reply_other_topic" value="1" {if !empty($permissions[7])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_reply_other_topic" value="0" {if empty($permissions[7])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
      {/if}
      {if $smarty.get.g_id != 2}
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_attachup#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_upload_attachment" value="1" {if !empty($permissions[8])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_upload_attachment" value="0" {if empty($permissions[8])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_ratethread#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_rate_topic" value="1" {if !empty($permissions[9])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_rate_topic" value="0" {if empty($permissions[9])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <th colspan="2" class="headers">{#Forums_Perm_posttopicPerms#}</th>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_editown#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_edit_own_post" value="1" {if !empty($permissions[10])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_edit_own_post" value="0" {if empty($permissions[10])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_deleteown#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_delete_own_post" value="1" {if !empty($permissions[11])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_delete_own_post" value="0" {if empty($permissions[11])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_editother#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_edit_other_post" value="1" {if !empty($permissions[16])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_edit_other_post" value="0" {if empty($permissions[16])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_deleteother#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_delete_other_post" value="1" {if !empty($permissions[15])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_delete_other_post" value="0" {if empty($permissions[15])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_moveown#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_move_own_topic" value="1" {if !empty($permissions[12])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_move_own_topic" value="0" {if empty($permissions[12])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_opencloseOwn#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_close_open_own_topic" value="1" {if !empty($permissions[13])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_close_open_own_topic" value="0" {if empty($permissions[13])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_deltown#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_delete_own_topic" value="1" {if !empty($permissions[14])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_delete_own_topic" value="0" {if empty($permissions[14])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_open#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_open_topic" value="1" {if !empty($permissions[17])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_open_topic" value="0" {if empty($permissions[17])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_close#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_close_topic" value="1" {if !empty($permissions[18])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_close_topic" value="0" {if empty($permissions[18])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <th colspan="2" class="headers">{#Forums_Perm_modifications#}</th>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_typechange#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_change_topic_type" value="1" {if !empty($permissions[19])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_change_topic_type" value="0" {if empty($permissions[19])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_moveother#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_move_topic" value="1" {if !empty($permissions[20])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_move_topic" value="0" {if empty($permissions[20])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="row_left">{#Forums_Perm_delother#}: </td>
          <td class="row_right">
            <label><input type="radio" name="can_delete_topic" value="1" {if !empty($permissions[21])}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="can_delete_topic" value="0" {if empty($permissions[21])}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
      {/if}
      <tr>
        <th colspan="2" class="selectrow">
          {if $smarty.get.g_id == 2}
            <input type="hidden" name="can_create_topic" value="0" />
            <input type="hidden" name="can_reply_own_topic" value="0" />
            <input type="hidden" name="can_reply_other_topic" value="0" />
            <input type="hidden" name="can_upload_attachment" value="0" />
            <input type="hidden" name="can_rate_topic" value="0" />
            <input type="hidden" name="can_edit_own_post" value="0" />
            <input type="hidden" name="can_delete_own_post" value="0" />
            <input type="hidden" name="can_edit_other_post" value="0" />
            <input type="hidden" name="can_delete_other_post" value="0" />
          {/if}
          <input name="settoall" type="checkbox" id="settoall" value="1" />
          {#Forums_Perm_settoall#}
          <br />
          <input class="button" type="submit" value="{#Save#}" />
          <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
        </th>
      </tr>
    </table>
  </form>
</div>
