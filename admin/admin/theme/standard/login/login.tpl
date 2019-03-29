<br />
<br />
<br />
<form method="post" action="index.php?do=login">
  <table width="350" border="0" align="center" cellpadding="20" cellspacing="5" class="loginborder">
    <tr>
      <td valign="top" class="loginmiddle">
        <table width="100%" border="0" cellspacing="3" cellpadding="0">
          <tr>
            <td colspan="2" align="center"><img src="{$imgpath}/login_logo.png" border="0" alt="" /></td>
          </tr>
          {if !isset($no_ip)}
            <tr>
              <td colspan="2">
                <strong> {#LoginNameInf#} </strong>
                <br />
                <br />
              </td>
            </tr>
            <tr>
              <td width="100">{#Global_Email#}</td>
              <td><input class="input" name="login_email_a" style="width: 150px" type="text" id="login_email_a" value="{if isset($smarty.request.login_email_a)}{$smarty.request.login_email_a|sanitize}{/if}" /></td>
            </tr>
            <tr>
              <td width="100">{#LoginPass#}</td>
              <td><input class="input" name="login_pass_a" style="width: 150px" type="password" id="login_pass_a" value="{if isset($smarty.request.login_pass_a)}{$smarty.request.login_pass_a|sanitize}{/if}" /></td>
            </tr>
            <tr>
              <td width="100">{#LoginSection#}</td>
              <td>
                <select class="input" style="width: 130px" name="area" id="area">
                  {foreach from=$sections item=s}
                    <option value="{$s->Id}">{$s->Name|sanitize}</option>
                  {/foreach}
                </select>
              </td>
            </tr>
            <tr>
              <td width="100">{#Sections_theme#}</td>
              <td>
                <select class="input" style="width: 130px" name="theme">
                  {foreach from=$themes item=t}
                    <option value="{$t}" {if isset($smarty.cookies.pre_admin_theme) && $smarty.cookies.pre_admin_theme == $t}selected="selected"{/if}>{$t|sanitize}</option>
                  {/foreach}
                </select>
              </td>
            </tr>
            <tr>
              <td width="100">{#LoginLang#}</td>
              <td>
                <select class="input" style="width: 130px" name="lang">
                  {foreach from=$languages item=l}
                    <option value="{$l->Sprachcode}" {if isset($smarty.cookies.pre_admin_lang) && $smarty.cookies.pre_admin_lang == $l->Sprachcode}selected="selected"{/if}>{$l->Sprache|sanitize}</option>
                  {/foreach}
                </select>
              </td>
            </tr>
            <tr>
              <td width="100">{#LoginRememberInf#}</td>
              <td><label><input name="save_logindata" type="checkbox" id="save_logindata" value="1" />{#LoginRemember#}</label></td>
            </tr>
            <tr>
              <td width="100">&nbsp;</td>
              <td>
                <input name="action" type="hidden" id="action" value="login" />
                <input class="button_login" type="submit" name="Submit" value="{#LoginName#}" />
              </td>
            </tr>
          {/if}
        </table>
        {if isset($message)}
          <br />
          <br />
          <strong>{$message}</strong>
        {/if}
      </td>
    </tr>
  </table>
</form>
