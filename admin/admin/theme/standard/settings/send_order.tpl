<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#send_order').validate( {
        rules: {
            text: { required: true }
        },
        messages: {
        },
        submitHandler: function() {
            document.forms['send_order'].submit();
            closeWindow();
        },
        success: function(label) {
            label.html("&nbsp;").addClass("checked");
        }
    });
});
//-->
</script>

<div class="header">{#SendOrder#}</div>
<form method="post" id="send_order" action="{$flink}">
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td>
        <fieldset>
          <legend><label>{#Info#}</label></legend>
          {#SendOrderInf#}
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="text">{#SendOrderText#}</label></legend>
          <textarea name="text" id="text" cols="" rows="10" style="width: 70%"></textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="data">{#SendOrderData#}</label></legend>
          <input name="data" type="text" class="input" style="width: 400px" value="" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="Sum">{#SendOrderSum#}</label></legend>
          <input name="sum" type="text" class="input" style="width: 350px" value="" />
          <select class="input" name="currency">
            <option value="RUR" selected="selected">RUR</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
          </select>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="url">{#SendUrl#}</label></legend>
          <input name="url" type="text" class="input" style="width: 400px" value="{$baseurl}" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="name">{#SendName#}</label></legend>
          <input name="name" type="text" class="input" style="width: 400px" value="{$settings.Mail_Name}" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="mail">{#SendMail#}</label></legend>
          <input name="mail" type="text" class="input" style="width: 400px" value="{$settings.Mail_Absender}" />
        </fieldset>
      </td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Go_Button#}" />&nbsp;&nbsp;
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  <input type="hidden" name="url2" value="{$baseurl}" />
  <input type="hidden" name="name2" value="{$settings.Mail_Name}" />
  <input type="hidden" name="mail2" value="{$settings.Mail_Absender}" />
</form>
