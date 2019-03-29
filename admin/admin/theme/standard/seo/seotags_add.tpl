<script type="text/javascript" src="{$jspath}/jmaxlength.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['seotags'].submit();
    }
});
$(document).ready(function() {
    $('#seotags').validate( {
	rules: {
            url: { required: true }
        },
        messages: { },
	success: function(label) {
            label.html("&nbsp;").addClass("checked");
        }
    });
    $('#title').maxlength({
        maxCharacters: {$settings.CountTitle},
        statusText: '{#SeotagsMaxlength#}',
        slider: true
    });
    $('#keywords').maxlength({
        maxCharacters: {$settings.CountKeywords},
        statusText: '{#SeotagsMaxlength#}',
        slider: true
    });
    $('#description').maxlength({
        maxCharacters: {$settings.CountDescription},
        statusText: '{#SeotagsMaxlength#}',
        slider: true
    });
});
//-->
</script>

<div class="header">{#Seotags#} - {#Global_Add#}</div>
<form method="post" id="seotags" action="index.php?do=seo&amp;sub=add_seotags">
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td>
        <fieldset>
          <legend><label for="url">{#Global_Page#}</label></legend>
          <input name="url" id="url" type="text" class="input" style="width: 380px" value="" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="title">&lt;canonical&gt;</label></legend>
          <input name="canonical" id="canonical" type="text" class="input" style="width: 380px" value="" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="title">&lt;title&gt;</label></legend>
          <textarea name="title" id="title" cols="" rows="4" style="width: 60%"></textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="keywords">&lt;keywords&gt;</label></legend>
          <textarea name="keywords" id="keywords" cols="" rows="4" style="width: 60%"></textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="description">&lt;description&gt;</label></legend>
          <textarea name="description" id="description" cols="" rows="4" style="width: 60%"></textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="aktiv">{#Global_Status#}</label></legend>
          <label><input type="radio" name="aktiv" value="1" checked="checked" />{#Global_Active#}</label>
          <label><input type="radio" name="aktiv" value="0" />{#Global_Inactive#}</label>
        </fieldset>
      </td>
    </tr>
  </table>
</div>
<input type="submit" class="button" value="{#Save#}" />
<input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
<input name="save" type="hidden" id="save" value="1" />
</form>
