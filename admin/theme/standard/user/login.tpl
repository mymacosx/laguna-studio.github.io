{if !isset($smarty.request.subaction) || ($smarty.request.subaction != 'step2' && $smarty.request.subaction != 'step3' && $smarty.request.subaction != 'step4')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options = {
        target: '#login_form_users', timeout: 3000
    };
    $('#ajloginform').submit(function() {
        $(this).ajaxSubmit(options);
        document.getElementById('ajl').style.display='none';
        document.getElementById('ajlw').style.display='';
        return false;
    });
});
togglePanel('navpanel_userpanel', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_userpanel" title="{#Login#}">
    <div class="boxes_body user_back_small">
      <div id="login_form_users">
        {include file="$incpath/user/login_raw.tpl"}
      </div>
      <div id="ajlw" style="display: none">
        <div style="text-align: center; padding: 10px"><img src="{$imgpath_page}loading.gif" alt="" border="0" /></div>
      </div>
      <div id="ajl">
        <form  method="post" action="{$baseurl}/index.php?p=userlogin&amp;action=ajaxlogin" name="login" id="ajloginform">
          <label for="login_email_r">{#LoginMailUname#}&nbsp;</label>
          <div>
            <input class="input_fields" type="text" name="login_email" id="login_email_r" style="width: 140px" />
          </div>
          <label for="login_pass_r">{#Pass#}&nbsp;</label>
          <div>
            <input class="input_fields" type="password" name="login_pass" id="login_pass_r" style="width: 140px" />
          </div>
          <label>
            <input name="staylogged" type="checkbox" value="1" checked="checked" class="absmiddle" />
            <span class="tooltip stip" title="{$lang.PassCookieT|tooltip}">{#PassCookieHelp#}</span> </label>
          <div>
            <input type="hidden" name="p" value="userlogin" />
            <input type="hidden" name="action" value="ajaxlogin" />
            <input type="hidden" name="area" value="{$area}" />
            <input type="hidden" name="backurl" value="{page_link|base64encode}" />
            <input type="submit" class="button" value="{#Login_Button#}" />
            <br />
            <br />
            {if get_active('Register')}
              {#Arrow#}<a href="index.php?p=register&amp;lang={$langcode}&amp;area={$area}">{#RegNew#}</a>
              <br />
            {/if}
            {#Arrow#}<a href="index.php?p=pwlost">{#PassLost#}</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{/if}
