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
	    Name: { required: true }
	},
        messages: { }
    });
    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
});
//-->
</script>

<form name="editform" id="editform" method="post" action="">
  <fieldset>
    <legend>{#InsertKey#}</legend>
    <input type="text" class="input" style="width: 200px" name="Name" value="{$res->Name}" />
  </fieldset>
  <fieldset>
    <legend>{#Global_Active#}</legend>
    <label><input type="radio" name="Active" value="1" {if $res->Active == 1} checked="checked"{/if}/>{#Yes#}</label>
    <label><input type="radio" name="Active" value="0" {if $res->Active == 0} checked="checked"{/if}/>{#No#}</label>
  </fieldset>
  <fieldset>
    <legend>{#InsertMarker#}</legend>
    <textarea cols="" rows="" style="width: 400px; height: 50px" name="Marker">{$res->Marker|sanitize}</textarea>
  </fieldset>
    <fieldset>
      <legend>{#InsertText#}</legend>
      <div id="container-options">
        <ul>
          <li><a href="#opt-1"><span>{$language.name.1|upper}</span></a></li>
          <li><a href="#opt-2"><span>{$language.name.2|upper}</a></li>
          <li><a href="#opt-3"><span>{$language.name.3|upper}</span></a></li>
        </ul>
        <div id="opt-1">{$text1}</div>
        <div id="opt-2">{$text2}</div>
        <div id="opt-3">{$text3}</div>
      </div>
    </fieldset>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
</form>
