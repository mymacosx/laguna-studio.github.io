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
	    Name_1: { required: true },
	    Beschreibung_1: { required: true }
        ,
	messages: { }
    });
});
//-->
</script>

<form name="editForm" id="editForm" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    {if $new == 1}
      <tr>
        <td width="200" class="row_left">{#Global_ParentCategory#}</td>
        <td class="row_right">
          <select  style="width: 250px" class="input" id="c" name="categ">
            <option value="0">-- {#Global_noParent#} --</option>
            {foreach from=$Categs item=dd}
              <option {if $dd->Parent_Id == 0}style="font-weight: bold"{/if} value="{$dd->Id}">{$dd->visible_title} </option>
            {/foreach}
          </select>
        </td>
      </tr>
    {/if}
    <tr>
      <td width="200" class="row_left">{#Global_name#} ({$language.name.1})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Name_1" type="text" value="{$res->Name_1|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Links_categs_ShortD#} ({$language.name.1})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Beschreibung_1" type="text" value="{$res->Beschreibung_1|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Global_name#} ({$language.name.2})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Name_2" type="text" value="{$res->Name_2|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Links_categs_ShortD#} ({$language.name.2})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Beschreibung_2" type="text" value="{$res->Beschreibung_2|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Global_name#} ({$language.name.3})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Name_3" type="text" value="{$res->Name_3|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Links_categs_ShortD#} ({$language.name.3})</td>
      <td class="row_right"><input style="width: 200px" class="input" name="Beschreibung_3" type="text" value="{$res->Beschreibung_3|sanitize}" /></td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
