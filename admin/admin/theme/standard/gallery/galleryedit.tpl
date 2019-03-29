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
            Tags: { maxlength: 255 }
        },
        messages: { }
    });
});
//-->
</script>

<div class="popbox">
  <form id="editForm" name="editForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="180" class="row_left">{#Global_Name#} ({$language.name.1})</td>
        <td class="row_right"><input name="Name_1" type="text" class="input" value="{$res->Name_1|sanitize}" size="50" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Name#} ({$language.name.2})</td>
        <td class="row_right"><input name="Name_2" type="text" class="input" value="{$res->Name_2|sanitize}" size="50" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Name#} ({$language.name.3})</td>
        <td class="row_right"><input name="Name_3" type="text" class="input" value="{$res->Name_3|sanitize}" size="50" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Global_descr#} ({$language.name.1})</td>
        <td class="row_right">{$Beschreibung_1}</td>
      </tr>
      <tr>
        <td class="row_left">{#Global_descr#} ({$language.name.2})</td>
        <td class="row_right">{$Beschreibung_2}</td>
      </tr>
      <tr>
        <td class="row_left">{#Global_descr#} ({$language.name.3})</td>
        <td class="row_right">{$Beschreibung_3}</td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Active#}</td>
        <td class="row_right">
          <label><input type="radio" name="Aktiv" value="1" {if $res->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Aktiv" value="0" {if $res->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><span class="stip" title="{$lang.GalleryTagHelp|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </td>
        <td class="row_right"><input name="Tags" type="text" class="input" value="{$res->Tags|sanitize}" size="50" maxlength="255" /></td>
      </tr>
    </table>
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
  </form>
</div>
