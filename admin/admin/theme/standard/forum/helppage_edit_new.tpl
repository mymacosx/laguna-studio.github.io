<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#editform').validate({
        rules: {
            Name_1: { required: true, minlength: 5 },
            Text_1: { required: true, minlength: 5 }
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

<div class="popbox">
  <div class="header"> {$smarty.request.n|sanitize} </div>
  <div id="content_popup">
    <form autocomplete="off" method="post" action="" name="editform" id="editform">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td colspan="2" class="headers">{#Forums_Help_q#}</td>
        </tr>
        <tr>
          <td width="170" class="row_left">{$language.name.1}</td>
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
        <tr>
          <td colspan="2" class="headers">{#Forums_Help_a#}</td>
        </tr>
        <tr>
          <td class="row_left">{$language.name.1}</td>
          <td class="row_right">{$Text_1}</td>
        </tr>
        <tr>
          <td class="row_left">{$language.name.2}</td>
          <td class="row_right">{$Text_2}</td>
        </tr>
        <tr>
          <td class="row_left">{$language.name.3}</td>
          <td class="row_right">{$Text_3}</td>
        </tr>
      </table>
      <input class="button" type="submit" id="s" value="{#Save#}" />
      <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
      <input name="save" type="hidden" id="save" value="1" />
    </form>
  </div>
</div>
