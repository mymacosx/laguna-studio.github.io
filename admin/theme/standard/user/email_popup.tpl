{if isset($not_logged) && $not_logged == 1}
<div class="popup_header">{$sname}</div>
<div class="popup_content" style="padding: 5px">
  <div class="popup_box"> {#Profile_Email_Error#} </div>
</div>
<p align="center">
  <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
</p>
{else}
{if (isset($smarty.request.mailsend) && $smarty.request.mailsend == 1) && !$error}
<div class="popup_header">{$sname}</div>
<table width="100%" cellpadding="4" cellspacing="1" class="box_inner">
  <tr>
    <td width="10%" class="row_first">
      <div align="center">
        {#SendEmail_Ok#}
        <br />
        <br />
        <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
      </div>
    </td>
  </tr>
</table>
{else}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#cf').validate({
        rules: {
            {if !$loggedin}
            email: { required: true, email: true },
            {/if}
            subject: { required: true },
            body: { required: true, minlength: 10 }
        },
        submitHandler: function() {
            document.forms['fc'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>

<form name="fc" id="cf" action="index.php" method="post">
  <div class="popup_header">{$sname}</div>
  <div class="popup_content" style="padding: 5px">
    <div class="popup_box">
      {if !empty($error)}
      <div class="error_box">
        <ul>
          {foreach from=$error item=err}
          <li>{$err}</li>
          {/foreach}
        </ul>
      </div>
      {/if}
      <br />
      <fieldset>
        <legend>{#SendEmail_Email#}</legend>
        <input name="email" type="text" class="input" style="width: 98%" value="{$smarty.request.email|default:$smarty.session.login_email|escape: html}" maxlength="35" />
      </fieldset>
      <fieldset>
        <legend>{#SendEmail_Title#}</legend>
        <input name="subject" type="text" class="input" id="subject" style="width: 98%" value="{$smarty.request.subject|default:''|escape: html}" maxlength="55" />
      </fieldset>
      <fieldset>
        <legend>{#GlobalMessage#}</legend>
        <textarea name="body" cols="" rows="8" class="input" style="width: 98%">{$smarty.request.body|default:''|escape: html}</textarea>
      </fieldset>
      {include file="$incpath/other/captcha.tpl"}
    </div>
  </div>
  <p align="center">
    <input type="submit" class="button" value="{#SendEmail_Send#}" />&nbsp;
    <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
    <input name="uid" type="hidden" value="{$smarty.request.uid}" />
    <input name="p" type="hidden" value="misc" />
    <input name="do" type="hidden" value="email" />
    <input name="mailsend" type="hidden" value="1" />
  </p>
</form>
{/if}
{/if}
