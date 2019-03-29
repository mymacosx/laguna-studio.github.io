<div class="popup_header">{#Forums_Complaint#}</div>
{if (isset($smarty.request.send) && $smarty.request.send == 1) && empty($error)}
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

<form name="fc" id="cf" action="index.php?p=forums&amp;action=complaint" method="post">
  <div class="popup_content padding5">
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
        <legend>{#GlobalMessage#}</legend>
        {$title|sanitize}
      </fieldset>
      <fieldset>
        <legend>{#SendEmail_Email#}</legend>
        <input name="email" type="text" class="input" style="width: 98%" value="{$smarty.request.email|default:$smarty.session.login_email|sanitize}" maxlength="35" />
      </fieldset>
      <fieldset>
        <legend>{#Forums_ComplaintText#}</legend>
        <textarea name="body" cols="" rows="8" class="input" style="width: 98%">{$smarty.request.body|default:''|escape: html}</textarea>
      </fieldset>
      {include file="$incpath/other/captcha.tpl"}
    </div>
  </div>
  <p align="center">
    <input type="submit" class="button" value="{#SendEmail_Send#}" />&nbsp;
    <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
    <input name="pid" type="hidden" value="{$smarty.request.pid|default:''|sanitize}" />
    <input name="fid" type="hidden" value="{$smarty.request.fid|default:''|sanitize}" />
    <input name="send" type="hidden" value="1" />
  </p>
</form>
{/if}
