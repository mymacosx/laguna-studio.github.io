<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#editform').validate({
	rules: {
	    Name_1: { required: true,minlength: 3 }
	},
	messages: { },
	submitHandler: function() {
	    document.forms['editform'].submit();
	},
	success: function(label) {
	    label.html("&nbsp;").addClass("checked");
	}
    });
});
//-->
</script>

<form method="post" action="" name="editform" id="editform">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td class="row_left">{$language.name.1}</td>
      <td class="row_right"><input name="Name_1" type="text" class="input" size="30" value="{$res->Name_1|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{$language.name.2}</td>
      <td class="row_right"><input name="Name_2" type="text" class="input" size="30" value="{$res->Name_2|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{$language.name.3}</td>
      <td class="row_right"><input name="Name_3" type="text" class="input" size="30" value="{$res->Name_3|sanitize}" /></td>
    </tr>
  </table>
  <input class="button" type="submit" id="s" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
