<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['editform'].submit();
    }
});

$(document).ready(function() {
    $('#editform').validate({
        rules: {
	    Name: { required: true },
	    Inhalt: { required: true }
	},
        messages: { }
    });
});
//-->
</script>

<div style="margin-top: 15px; padding: 10px; background: #fff; border: 2px solid #FF0000; color: #000">{#CodeWidgetsWarn#}</div>
<form name="editform" id="editform" method="post" action="">
  <fieldset>
    <legend>{#Global_Name#}</legend>
    <input type="text" class="input" style="width: 200px" name="Name" value="" />
  </fieldset>
  <fieldset>
    <legend>{#CodeWidgetsContent#}</legend>
    {$text}
  </fieldset>
  <fieldset>
    <legend>{#CodeWidgetsGroupsLegend#}</legend>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="250" valign="top">
          <label><input type="checkbox" class="absmiddle" name="AlleGruppen" value="1" checked="checked" /><strong>{#All_Grupp#}</strong></label>
          <br />
          <br />
          {#Shop_allowed_select#} </td>
        <td valign="top">
          <select name="Gruppen[]" size="6" multiple="multiple" class="input" style="width: 250px">
            {foreach from=$UserGroups item=group}
              <option value="{$group->Id}" {if in_array($group->Id,$groups)}selected="selected" {/if}>{$group->Name_Intern}</option>
            {/foreach}
          </select>
        </td>
      </tr>
    </table>
  </fieldset>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
</form>
