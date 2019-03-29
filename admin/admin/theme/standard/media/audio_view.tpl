<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function(){
    $('#edit').validate({
	rules: {
            Name: { required: true },
            Width: { required: true }
        },
	messages: { },
	submitHandler: function() {
	    document.forms['new'].submit();
	}
    });
});
//-->
</script>

<h3>{$res->Name|sanitize}</h3>
<form method="post" action="" id="edit" name="edit">
  <fieldset>
    <legend>{#Global_props#}</legend>
    <table>
      <tr>
        <td>{#GlobalTeg#}: </td>
        <td><input class="input" disabled="disabled" type="text" value="[AUDIO:{$res->Id}]" /></td>
      </tr>
      <tr>
        <td>{#Global_Name#}: </td>
        <td><input class="input" style="width: 350px" type="text" name="Name" value="{$res->Name|sanitize}" /></td>
      </tr>
      <tr>
        <td>{#GlobalWidth#}: </td>
        <td><input class="input" type="text" name="Width" value="{$res->Width}" /> px</td>
      </tr>
      <tr>
        <td colspan="2">{$playAudio}</td>
      </tr>
    </table>
  </fieldset>
  <br />
  <input class="button" type="submit" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
