<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	ocument.forms['editForm'].submit();
    }
});

$(document).ready(function(){
    $('#editForm').validate({
	rules: {
	    title: { required: true, minlength: 1 }
	},
	messages: { }
    });
});
//-->
</script>

{if !empty($errors)}
  <ul>
    {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
  </ul>
{/if}
<form name="editForm" id="editForm" action="" method="post">
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="f_id" value="{$forum->id}" />
  <input type="hidden" name="c_id" value="{$forum->category_id|default:$smarty.get.id}" />
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
    <tr>
      <td width="40%" class="row_left">{#Global_Name#}</td>
      <td class="row_right"><input class="input" type="text" name="title" value="{$smarty.post.title|default:$forum->title}" size="30" maxlength="200" /></td>
    </tr>
    <tr>
      <td width="40%" valign="top" class="row_left">{#Forums_descr#}</td>
      <td class="row_right"><textarea class="input" name="comment" cols="50" rows="10">{$smarty.post.comment|default:$forum->comment}</textarea></td>
    </tr>
    {if $smarty.request.sub != "addforum"}
      <tr>
        <td width="40%" valign="top" class="row_left">{#Forums_editPGroups#}</td>
        <td class="row_right">
          {foreach from=$groups item=group}
            {if in_array($group->ugroup, $forum->group_id) || in_array($group->ugroup, $smarty.post.group_id)}
              <a class="colorbox" href="?do=forums&amp;sub=userpermissions&amp;g_id={$group->ugroup}&amp;f_id={$forum->id}&amp;noframes=1">{$group->groupname}</a>
              <br />
              <input type="hidden" name="group_id[]" value="{$group->ugroup}" />
            {/if}
          {/foreach}
        </td>
      </tr>
    {/if}
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfOffline|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_factive#}</td>
      <td class="row_right">
        <select class="input" name="active">
          <option value="1" {if $forum->active == 1 || $smarty.request.sub == "addforum"}selected="selected"{/if}>{#Yes#}</option>
          <option value="0" {if $forum->active == 0 && $smarty.request.sub != "addforum"}selected="selected"{/if}>{#No#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfClosed|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_Status#}</td>
      <td class="row_right">
        <select class="input" name="status">
          <option value="0" {if $forum->status == 0}selected="selected"{/if}>{#Forums_sopened#}</option>
          <option value="1" {if $forum->status == 1}selected="selected"{/if}>{#Forums_sclosed#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfNotiThread|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_tnemails#}</td>
      <td class="row_right"><input class="input" name="topic_emails" type="text" id="topic_emails" value="{$forum->topic_emails|escape: "htmlall"}" size="30" /></td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfNotiPost|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_bnemails#} </td>
      <td class="row_right"><input class="input" name="post_emails" type="text" id="post_emails" value="{$forum->post_emails|escape: "htmlall"}" size="30" /></td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfFMod|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_fismoderated#}</td>
      <td class="row_right"><input class="input" name="moderated" type="checkbox" value="1" {if $forum->moderated == 1}checked="checked"{/if} /></td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfTMod|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_bismoderated#}</td>
      <td class="row_right"><input class="input" name="moderated_posts" type="checkbox" value="1" {if $forum->moderated_posts == 1}checked="checked"{/if} /></td>
    </tr>
    <tr>
      <td width="40%" class="row_left"><img class="absmiddle stip" title="{$lang.Forums_InfPassF|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Forums_fpass#}</td>
      <td class="row_right"><input class="input" type="text" name="password" value="{$forum->password_raw}" /></td>
    </tr>
  </table>
  <br />
  <input accesskey="s" class="button" type="submit" value="{#Save#}" />
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
</form>
