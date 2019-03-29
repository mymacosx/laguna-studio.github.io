{if $loggedin}
  {if isset($smarty.get.success) && $smarty.get.success == 1}
    <div class="infobox"><strong>{#LoginExternSuccess#}</strong></div>
      {/if}
  <div class="infobox">
    {if get_active('shop')}
      {$welcome}, <strong>{$smarty.session.benutzer_vorname} {$smarty.session.benutzer_nachname}!</strong> {#LoginExternCustomerNr#}: <strong>{$smarty.session.benutzer_id}</strong>
      <br />
    {else}
      {$welcome}, <strong>{$smarty.session.user_name}</strong>!
    {/if}
  </div>
  <strong>{#LoginExternActions#}</strong>
  <div class="infobox user_back_small" style="margin-top: 10px">
    {#Arrow#}<a href="{$baseurl}/index.php?p=useraction&amp;action=profile">{#LoginExternPc#}</a><br />
    {#Arrow#}<a href="{$baseurl}/index.php?p=user&amp;id={$smarty.session.benutzer_id}&amp;area={$area}">{#LoginExternVp#}</a><br />
    {#Arrow#}<a href="{$baseurl}/index.php?p=useraction&amp;action=changepass">{#LoginExternCp#}</a><br />
    {if get_active('shop')}
      {#Arrow#}<a href="{$baseurl}/index.php?p=shop&amp;action=myorders">{#Shop_go_myorders#}</a><br />
      {#Arrow#}<a href="{$baseurl}/index.php?p=shop&amp;action=mydownloads">{#LoginExternVd#}</a><br />
    {/if}
    {if get_active('calendar')}
      {#Arrow#}<a href="{$baseurl}/index.php?p=calendar&amp;month={$smarty.now|date_format: 'm'}&amp;year={$smarty.now|date_format: 'Y'}&amp;area={$area}&amp;show=private">{#UserCalendar#}</a><br />
    {/if}
  </div>
  <div class="infobox">
    {if permission('adminpanel')}
      {#Arrow#}<a href="javascript: void(0);" onclick="openWindow('{$baseurl}/admin', 'admin', '', '', 1);">{#LoginExternAd#}</a> - <a target="_blank" href="{$baseurl}/admin">{#LoginExternAd2#}</a>
      <br />
    {/if}
    <form method="post" name="logout_form_user" action="index.php">
      <input type="hidden" name="p" value="userlogin" />
      <input type="hidden" name="action" value="logout" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="backurl" value="{"index.php"|base64encode}" />
      {#Arrow#}<a onclick="return confirm('{#Confirm_Logout#}');" href="javascript: document.forms['logout_form_user'].submit();">{#Logout#}</a>
    </form>
    <br />
    {if permission('deleteaccount') && $smarty.session.benutzer_id != 1}
      <br />
      {#Arrow#}<a href="index.php?p=useraction&amp;action=deleteaccount">{#AccountDel#}</a>
    {/if}
  </div>
{else}
  <div id="logh">
    {if isset($LoginError)}
      <div class="error_box"><strong>{#Error#}</strong> {#WrongLoginData#} </div>
        {/if}
    <div class="infobox user_back_small">
      <strong>{#LoginExternHeader#}</strong>
      <br />
      <br />
      <form  method="post" action="index.php?p=userlogin" name="login" id="ajlogintrue">
        <label for="login_email_r2"><strong>{#LoginMailUname#}</strong></label>
        <br />
        <input class="input_fields" type="text" name="login_email" id="login_email_r2" style="width: 180px" />
        <br />
        <label for="login_pass_r2"><strong>{#Pass#}</strong></label>
        <br />
        <input class="input_fields" type="password" name="login_pass" id="login_pass_r2" style="width: 180px" />
        <br />
        <label><input name="staylogged" type="checkbox" value="1" checked="checked" class="absmiddle" /> {#LoginExternSave#}</label>
        <input type="hidden" name="p" value="userlogin" />
        <input type="hidden" name="action" value="newlogin" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="backurl" value="{page_link|base64encode}" />
        <br />
        <br />
        <input type="submit" class="button" value="{#Login_Button#}" onclick="document.getElementById('logh').style.display = 'none';
          document.getElementById('logspinner').style.display = ''" />
      </form>
    </div>
    <div class="box_data">
      {if get_active('Register')}
        {#Arrow#}<a href="index.php?p=register&amp;area={$area}">{#LoginExternNew#}</a>
        <br />
      {/if}
      {#Arrow#}<a href="index.php?p=pwlost">{#LoginExternPwLost#}</a>
    </div>
  </div>
  <div id="logspinner" style="display: none">
    <div style="padding: 30px; text-align: center"><img src="{$imgpath_page}loading.gif" alt="" border="0" /></div>
  </div>
{/if}
