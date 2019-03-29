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
	    Titel_1: { required: true, minlength: 2 },
	    Dokument: { required: true, minlength: 5 },
	    Position: { required: true, number: true }
	},
	messages: { }
    });
});
//-->
</script>

<div class="popbox">
  <form id="editForm" name="editForm" action="" method="post">
    <input name="save" type="hidden" id="save" value="1" />
    <input name="id" type="hidden" value="{$smarty.request.id}" />
    <input name="categ" type="hidden" value="{$res->NaviCat}" />
    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
      <tr>
        <td width="150" class="row_left">{#Global_Name#} ({$language.name.1})</td>
        <td width="170" class="row_right"><input class="input" type="text" name="Titel_1" value="{$res->Titel_1|sanitize}" style="width: 150px" maxlength="200" /></td>
        <td width="150" class="row_left">{#NaviHrefTitle#} ({$language.name.1})</td>
        <td class="row_right"><input class="input" type="text" name="Link_Titel_1" value="{$res->Link_Titel_1|sanitize}" style="width: 150px" maxlength="200" /></td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Global_Name#} ({$language.name.2})</td>
        <td width="170" class="row_right"><input class="input" type="text" name="Titel_2" value="{$res->Titel_2|sanitize}" style="width: 150px" maxlength="200" /></td>
        <td width="150" class="row_left">{#NaviHrefTitle#} ({$language.name.2})</td>
        <td class="row_right"><input class="input" type="text" name="Link_Titel_2" value="{$res->Link_Titel_2|sanitize}" style="width: 150px" maxlength="200" /></td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Global_Name#} ({$language.name.3})</td>
        <td width="170" class="row_right"><input class="input" type="text" name="Titel_3" value="{$res->Titel_3|sanitize}" style="width: 150px" maxlength="200" /></td>
        <td width="150" class="row_left">{#NaviHrefTitle#} ({$language.name.3})</td>
        <td class="row_right"><input class="input" type="text" name="Link_Titel_3" value="{$res->Link_Titel_3|sanitize}" style="width: 150px" maxlength="200" /></td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Navigation_doc#}</td>
        <td width="170" class="row_right"><input class="input" type="text" name="Dokument" value="{$res->Dokument|sanitize}" style="width: 150px" maxlength="200" /></td>
        <td width="150" class="row_left">&nbsp;</td>
        <td class="row_right">&nbsp;</td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Global_Target#}</td>
        <td width="170" class="row_right">
          <select class="input" name="Ziel" style="width: 150px">
            <option value="_self" {if $res->Ziel == '_self'}selected="selected"{/if}>{#Navigation_self#}</option>
            <option value="_new" {if $res->Ziel == '_new'}selected="selected"{/if}>{#Navigation_new#}</option>
          </select>
        </td>
        <td width="150" class="row_left">&nbsp;</td>
        <td class="row_right">&nbsp;</td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Navigation_expand#}</td>
        <td width="170" class="row_right">
          <label><input type="radio" name="openonclick" value="1" {if $res->openonclick == 1 || isset($smarty.request.sub) && $smarty.request.sub == 'newnaviitem'}checked="checked"{/if} /> {#Yes#}</label>
          <label><input type="radio" name="openonclick" value="0" {if $res->openonclick == 0}checked="checked"{/if} /> {#No#}</label></td>
        <td width="150" class="row_left">&nbsp;</td>
        <td class="row_right">&nbsp;</td>
      </tr>
      <tr>
        <td width="150" class="row_left">{#Global_Position#}</td>
        <td width="170" class="row_right"><input class="input" type="text" name="Position" value="{if isset($smarty.request.sub) && $smarty.request.sub == 'newnaviitem'}1{else}{$smarty.post.position|default:$res->Position}{/if}" size="3" maxlength="3" /></td>
        <td width="150" class="row_left">&nbsp;</td>
        <td class="row_right">&nbsp;</td>
      </tr>
      <tr>
        <td width="150" valign="top" class="row_left">{#Forums_PGroups#}</td>
        <td width="170" class="row_right">
          <select name="group_id[]" size="8" multiple="multiple" class="input" style="width: 155px">
            {foreach from=$groups item=group}
              <option value="{$group->ugroup}" {if in_array($group->ugroup, $res->Gruppen) || isset($smarty.request.sub) && $smarty.request.sub == "newnaviitem"}selected="selected"{/if}>{$group->groupname}</option>
            {/foreach}
          </select>
        </td>
        <td width="150" class="row_left">&nbsp;</td>
        <td class="row_right">&nbsp;</td>
      </tr>
      {if isset($smarty.request.sub) && $smarty.request.sub == "newnaviitem"}
        <tr>
          <td width="150" class="row_left">{#Navigation_parentdoc#}</td>
          <td width="170" class="row_right">
            <select name="ParentId" id="ParentId" class="input" style="width: 155px">
              <option value="0">{#Navigation_newdoc_blanc#}</option>
              {foreach from=$items item=n}
                <option value="{$n->Id}" style="font-weight: bold">{$n->Titel_1|sanitize}</option>
                {foreach from=$n->sub1 item=s1 name=second}
                  <option value="{$s1->Id}">&nbsp;-&nbsp;{$s1->Titel_1|sanitize}</option>
                {/foreach}
              {/foreach}
            </select>
          </td>
          <td width="150" class="row_left">&nbsp;</td>
          <td class="row_right">&nbsp;</td>
        </tr>
      {/if}
      {if $smarty.request.sub != "newnaviitem"}
        <tr>
          <td width="150" valign="top" class="row_left">{#Navigation_setpermsall#}</td>
          <td width="170" class="row_right"><input name="perms_to_section" type="checkbox" id="perms_to_section" value="1" /></td>
          <td width="150" class="row_left">&nbsp;</td>
          <td class="row_right">&nbsp;</td>
        </tr>
      {/if}
    </table>
    <br />
    <input class="button" type="submit" value="{#Save#}" />
    <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
  </form>
</div>
