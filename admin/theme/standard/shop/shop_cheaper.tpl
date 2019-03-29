<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#cheaper_link').colorbox({ width: '600px', inline: true, href: '#cheaper_content' });
    $('#cheaper_form').validate({
        rules: {
            cheaper_email: { required: true, email: true },
            cheaper_where: { required: true, minlength: 10 }
        },
        messages: { },
        submitHandler: function() {
            document.forms['cheaper'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
    $('#cheaper_link').on('click', function() {
        var price = $('#new_price').text();
	$('#cheaper_price').val(price);
        $('#cheaper_current').text(price);
    });
});
//-->
</script>

<div style="display: none">
  <div id="cheaper_content">
    <fieldset>
      <legend>{#cheaper_content#}</legend>
      {#cheaper_content_inf#}
    </fieldset>
    <fieldset>
      <legend>{#cheaper_action#}</legend>
      {#cheaper_action_inf#}
    </fieldset>
    <fieldset>
      <legend>{#cheaper_product#}</legend>
      {#GlobalTitle#}: <span>{$cheaper_product}</span><br />
      {#Products_price#}: <span id="cheaper_current">0</span>
    </fieldset>
    <form name="cheaper" method="post" id="cheaper_form" action="">
      <fieldset>
        <legend>{#SendEmail_Email#}</legend>
        <input name="cheaper_email" type="text" class="input" style="width: 90%" value="{$smarty.request.cheaper_email|default:$smarty.session.login_email|sanitize}" maxlength="35" />
      </fieldset>
      <fieldset>
        <legend>{#cheaper_where#}</legend>
        <textarea name="cheaper_where" cols="" rows="3" class="input" style="width: 90%">{$smarty.request.cheaper_where}</textarea>
      </fieldset>
      <fieldset>
        <legend>{#GlobalMessage#}</legend>
        <textarea name="cheaper_text" cols="" rows="6" class="input" style="width: 90%">{$smarty.request.cheaper_text}</textarea>
      </fieldset>
      <p align="center">
        <input type="submit" class="button" value="{#SendEmail_Send#}" />&nbsp;
        <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
        <input type="hidden" name="cheaper_price" id="cheaper_price" value="0" />
        <input type="hidden" name="cheaper_link" value="{page_link}" />
        <input type="hidden" name="cheaper_send" value="1" />
      </p>
    </form>
  </div>
</div>
