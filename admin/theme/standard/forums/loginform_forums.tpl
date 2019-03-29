<form method="post" action="index.php">
  <table width="100" cellpadding="0" cellspacing="1">
    <tr>
      <td width="1%" nowrap="nowrap"><label for="login_email_r"><strong>{#Email#}</strong></label>&nbsp;</td>
      <td width="1%" nowrap="nowrap"><input class="input" type="text" name="login_email" id="login_email_r" style="width: 130px" />&nbsp;</td>
      <td width="1%" nowrap="nowrap">
        <label>
          <input name="staylogged" type="checkbox" value="1" checked="checked" class="absmiddle" />
          <span class="tooltip stip" title="{$lang.PassCookieT|tooltip}">{#PassCookieHelp#}</span>
        </label>
      </td>
    </tr>
    <tr>
      <td width="1%" nowrap="nowrap"><label for="login_pass_r"><strong>{#Pass#}</strong></label>&nbsp;</td>
      <td width="1%" nowrap="nowrap"><input class="input" type="password" name="login_pass" id="login_pass_r" style="width: 130px" />&nbsp;</td>
      <td width="1%" nowrap="nowrap"><input type="submit" class="button" value="{#Login_Button#}" /></td>
    </tr>
  </table>
  <input type="hidden" name="p" value="userlogin" />
  <input type="hidden" name="action" value="newlogin" />
  <input type="hidden" name="backurl" value="{page_link|base64encode}" />
</form>
