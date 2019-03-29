{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
{include file="$incpath/other/jsform.tpl"}

$(document).ready(function() {
    $('#cf').validate({
        rules: {
            {if !$loggedin}
            Autor: { required: true },
            Email: { required: true, email: true },
            {/if}
            Webseite: { url: true },
            text: { required: true, minlength: 10 }
        },
        submitHandler: function() {
            document.forms['f'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});

togglePanel('navpanel_newcomment', 'toggler', 30, '{$basepath}');
//-->
</script>

<div class="opened" id="navpanel_newcomment" title="{#Guestbook_new#}">
  <div class="comment_form">
    {if !empty($error)}
      <a name="error"></a><img src="{$imgpath_page}pixel.gif" alt="" width="1" height="1" style="display: none" onload="location.href='#error';" />
      <div class="error_box">
        <ul>
          {foreach from=$error item=err}
            <li>{$err}</li>
            {/foreach}
        </ul>
      </div>
    {/if}
    <form method="post" action="" name="f" id="cf" onsubmit="closeCodes();">
      {if !$loggedin}
        <input class="input" style="width: 200px" name="Autor" type="text" value="{$smarty.post.Autor|default:''|sanitize}" id="c_a" />&nbsp; <strong>{#Contact_myName#}</strong>
        <br />
        <input class="input" style="width: 200px" name="Email" type="text" value="{$smarty.post.Email|default:''|sanitize}" id="c_e" />&nbsp;
        <label for="c_e"><strong>{#Comment_YourEmail#}</strong></label>
        <br />
      {else}
        <input name="Autor" type="hidden" value="{$smarty.session.user_name|default:''}" id="c_a" />
        <input name="Email" type="hidden" value="{$smarty.session.login_email|default:''}" id="c_e" />
      {/if}
      <input class="input" style="width: 200px" name="Webseite" type="text" value="{$smarty.post.Webseite|default:''|sanitize}" id="c_w" />&nbsp;
      <label for="c_w"><strong>{#Web#}{#GlobalOption#}</strong></label>
      <br />
      <input class="input" style="width: 200px" name="Herkunft" type="text" value="{$smarty.post.Herkunft|default:''|sanitize}" id="c_h" />&nbsp;
      <label for="c_h"><strong>{#Town#}{#GlobalOption#}</strong></label>
      <br />
      {if $settings.KommentarFormat == 1}
        {if $settings.SysCode_Smilies == 1}
          {$listemos}
        {/if}
        {include file="$incpath/comments/format.tpl"}
      {/if}
      <textarea name="text" cols="40" rows="5" class="input" id="msgform" style="width: 99%; height: 150px">{$smarty.post.text|default:''|sanitize}</textarea>
      {include file="$incpath/other/captcha.tpl"}
      <br />
      <br />
      <input type="hidden" name="Redir" value="{page_link}" />
      <input type="hidden" name="Eintrag" value="1" />
      <input type="hidden" name="id" value="{$smarty.request.id|default:''|sanitize}" />
      <input type="submit" class="button" value="{#ButtonSend#}" />&nbsp;
      <input type="button" class="button" onclick="closeCodes(); countComments({$settings.Kommentar_Laenge});" value="{#Comment_ButtonChecklength#}" />
    </form>
  </div>
</div>
