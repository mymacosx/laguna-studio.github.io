<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['editForm'].submit();
    }
});

$(document).ready(function() {
    $('#editForm').validate({
	rules: {
	    title: { required: true, minlength: 2 },
	    position: { required: true, number: true }
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
<form id="editForm" name="editForm" action="" method="post">
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="c_id" value="{$category->id}" />
  <input type="hidden" name="f_id" value="{$category->parent_id|default:$smarty.get.id}" />
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
    <tr>
      <td class="row_left">{#Global_Name#}</td>
      <td class="row_right"><input class="input" type="text" name="title" id="title" value="{$smarty.post.title|default:$category->title|escape: "htmlall"|sslash}" size="30" maxlength="200" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Position#}</td>
      <td class="row_right"><input class="input" type="text" name="position" value="{if $smarty.request.action == "add"}1{else}{$smarty.post.position|default:$category->position}{/if}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td valign="top" class="row_left">{#Forums_PGroups#}</td>
      <td class="row_right">
        <select name="group_id[]" size="8" multiple="multiple" class="input" style="width: 200px">
          {foreach from=$groups item=group}
            <option value="{$group->ugroup}" {if (!empty($category->group_id) && in_array($group->ugroup, $category->group_id)) || (!empty($smarty.post.group_id) && in_array($group->ugroup, $smarty.post.group_id)) || isset($smarty.request.sub) && $smarty.request.sub == 'newcategory'}selected="selected"{/if}>{$group->groupname}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top" class="row_left">{#Global_Comments#}</td>
      <td class="row_right"><textarea class="input" name="comment" cols="50" rows="10">{$smarty.post.comment|default:$category->comment|escape: "htmlall"|sslash}</textarea></td>
    </tr>
  </table>
  <br />
  <input class="button" type="submit" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
</form>
