<script type="text/javascript">
<!-- //
{include file="$theme/validate.txt"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['Form'].submit();
    }
});
$(document).ready(function() {
    $('#Form').validate({
        rules: {
            first: { required: true },
            last: { required: true },
            username: { required: true },
            pass: { required: true, minlength: 5 },
            email: { required: true, email: true },
            street: { required: true },
            zip: { required: true, number: true },
            town: { required: true },
            company: { required: true },
            websitename: { required: true }
        },
        messages: { }
    });
});
//-->
</script>

<div class="content">
  <div class="headers">{#Step3#}</div>
  <form name="Form" id="Form" method="post" action="{$setupdir}/setup.php">
    <div class="box">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td width="280"><span class="stip" title="{$lang.s2_3inf|sanitize}"> <img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#s2_3#}</strong> </span></td>
          <td><input name="username" type="text" class="input" id="username" value="{$smarty.post.username|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><span class="stip" title="{$lang.s2_5inf|sanitize}"> <img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#s2_5#}</strong> </span></td>
          <td><input name="email" type="text" class="input" value="{$smarty.post.email|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><span class="stip" title="{$lang.s2_4inf|sanitize}"> <img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#s2_4#}</strong> </span></td>
          <td><input name="pass" type="password" class="input" id="pass" value="{$smarty.post.pass|sanitize|default: ''}" /></td>
        </tr>
      </table>
    </div>
    <div class="box">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td width="280"><strong>{#s2_1#}</strong></td>
          <td><input name="first" type="text" class="input" id="first" value="{$smarty.post.first|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_2#}</strong></td>
          <td><input name="last" type="text" class="input" id="last" value="{$smarty.post.last|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_6#}</strong></td>
          <td><input name="street" type="text" class="input"  value="{$smarty.post.street|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_7#}</strong></td>
          <td><input name="zip" type="text" class="input" value="{$smarty.post.zip|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_8#}</strong></td>
          <td><input name="town" type="text" class="input"  value="{$smarty.post.town|sanitize|default: ''}" /></td>
        </tr>
      </table>
    </div>
    <div class="box">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td width="280"><strong>{#s2_9#}</strong></td>
          <td><input name="company" type="text" class="input" value="{$smarty.post.company|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_11#}</strong></td>
          <td><input name="websitename" type="text" class="input"  value="{$smarty.post.websitename|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_12#}</strong></td>
          <td><input name="phone" type="text" class="input" value="{$smarty.post.phone|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td width="280"><strong>{#s2_13#}</strong></td>
          <td><input name="fax" type="text" class="input" id="email" value="{$smarty.post.fax|sanitize|default: ''}" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </div>
    <div class="button_steps">
      <input type="hidden" name="step" value="4" />
      <input type="submit" value="{#Step2_Button#}" />
    </div>
  </form>
</div>
