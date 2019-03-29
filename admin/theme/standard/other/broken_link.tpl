{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#BrokenLink').validate({
        rules: {
            'name': { required: true },
            'email': { required: true, email: true }
        },
        messages: { },
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                target: '#BrokenLinkOk',
                url: '{$BrokenLinkSubmit}',
                success: function() {
                    document.getElementById('broken_link').style.display='none';
                },
                clearForm: false,
                resetForm: true
            });
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>

<div id="broken_onclick" style="display: none">
  <div class="box_innerhead"><img class="absmiddle" src="{$imgpath_page}warning_small.png" alt="" /> {#Links_ErrorSendBroken#}</div>
  <div class="box_data">
    <div id="BrokenLinkOk" style="font-size: 140%; font-weight: bold"></div>
    <form method="post" action="" id="BrokenLink">
      <div id="broken_link">
        <table>
          <tr>
            <td valign="top" class="row_left">{#Title#}</td>
            <td class="row_right"><strong>{$link_res->Name|sanitize}</strong></td>
          </tr>
          <tr>
            <td valign="top" class="row_left">{#Links_ErrorM#}</td>
            <td class="row_right">
              <label><input type="radio" name="BrokenReason" value="Links_Broken_dnserror" checked="checked" /> {#Links_Broken_dnserror#}</label>
              <br />
              <label><input type="radio" name="BrokenReason" value="Links_Broken_noconnection" /> {#Links_Broken_noconnection#}</label>
              <br />
              <label><input type="radio" name="BrokenReason" value="Links_Broken_auth" /> {#Links_Broken_auth#}</label>
              <br />
              <label><input type="radio" name="BrokenReason" value="Links_Broken_notfound" /> {#Links_Broken_notfound#}</label>
              <br />
              <label><input type="radio" name="BrokenReason" value="Links_Broken_servererror" /> {#Links_Broken_servererror#}</label>
              <br />
              <label><input type="radio" name="BrokenReason" value="ActionOther" /> {#ActionOther#}</label>
              <br />
            </td>
          </tr>
          <tr>
            <td class="row_left">{#Contact_myName#}</td>
            <td><input class="input" type="text" name="name" /></td>
          </tr>
          <tr>
            <td class="row_left">{#SendEmail_Email#}</td>
            <td><input class="input" type="text" name="email" /></td>
          </tr>
          <tr>
            <td class="row_left">&nbsp;</td>
            <td>
              <input type="hidden" name="dpage" value="{page_link|base64_encode}" />
              <input type="submit" class="button" value="{#Links_ErrorSendBroken#}" />
            </td>
          </tr>
        </table>
        <br />
      </div>
    </form>
  </div>
</div>
