{if isset($smarty.request.faqsend) && $smarty.request.faqsend == 1 && empty($error)}
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
    $('#cfaq').validate({
        rules: {
            {if !$loggedin}
            email: { required: true, email: true },
            {/if}
            body: { required: true, minlength: 10 }
        },
        submitHandler: function() {
            document.forms['faq'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>

<form name="faq" id="cfaq" action="index.php?p=faq&amp;action=mail" method="post">
  <div class="popup_header">{$sname}</div>
  <div class="popup_content" style="padding: 5px">
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
      <input name="email" type="text" class="input" style="width: 98%" value="{$smarty.request.email|default:$smarty.session.login_email|sanitize}" maxlength="35" />
    </fieldset>
    <fieldset>
      <legend>{#New_Categ#}</legend>
      <input name="newcateg" type="text" class="input" style="width: 98%" value="{$smarty.request.newcateg|default:''|sanitize}" maxlength="150" />
    </fieldset>
    {if $categs}
      <fieldset>
        <legend>{#Global_Categ#}</legend>
        <select style="width: 98%" class="input" name="faq_id">
          <option value="0">{#Global_Select_Categ#}</option>
          {foreach from=$categs item=dd}
            <option {if $dd->Id == $smarty.request.faq_id}selected="selected"{/if} value="{$dd->Id}">{$dd->visible_title} </option>
          {/foreach}
        </select>
      </fieldset>
    {/if}
    <fieldset>
      <legend>{#Global_Guest#}</legend>
      <textarea name="body" cols="" rows="8" class="input" style="width: 98%">{$smarty.request.body|default:''|escape: html}</textarea>
    </fieldset>
    {include file="$incpath/other/captcha.tpl"}
  </div>
</div>
<p align="center">
  <input type="submit" class="button" value="{#SendEmail_Send#}" />&nbsp;
  <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
  <input name="faqsend" type="hidden" value="1" />
</p>
</form>
{/if}
