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
            dbhost: { required: true },
            dbuser: { required: true },
            dbname: { required: true },
            dbprefix: { required: true }
        },
        messages: { }
    });
});
//-->
</script>

<div class="content">
  <div class="headers">{#Step2#}</div>
  {if !empty($errors_path)}
    <div class="errorbox">
      <h3>{#Errors#}</h3>
      <br />
      <strong> {#Step1ErrInf#} </strong> </div>
    <div class="eulas">
      <div class="eula"> {foreach from=$error_not_writables item=nw}
        <div class="error_nw">{$nw}</div>
        {/foreach} </div>
      </div>
      <div class="button_steps">
        <form method="post" action="{$setupdir}/setup.php">
          <input type="hidden" name="step" value="1" />
          <input type="submit" value="{#Step1ErrB#}" />
        </form>
      </div>
      {else}
        <form autocomplete="off" name="Form" id="Form"  method="post" action="{$setupdir}/setup.php">
          <div class="box">
            {if isset($db_no_connection) && $db_no_connection == 1}
              <div class="error_conn"> <img src="{$setupdir}/images/warning.png" alt="" style="vertical-align: middle" /> {#Step1_NoConn#} </div>
              {/if}
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="280"><span class="stip" title="{$lang.Step1_ainf|sanitize}"> <img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_a#}</strong></span></td>
                <td><input class="input" type="text" name="dbhost" value="{$smarty.post.dbhost|sanitize|default: 'localhost'}" /></td>
              </tr>
              <tr>
                <td width="280"><span class="stip" title="{$lang.Step1_pinf|sanitize}"> <img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_p#}</strong></span></td>
                <td><input class="input" type="text" name="dbport" value="{$smarty.post.dbport|sanitize|default: '3306'}" /></td>
              </tr>
              <tr>
                <td width="280"><span class="stip" title="{$lang.Step1_binf|sanitize}"><img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_b#}</strong></span></td>
                <td><input class="input" type="text" name="dbuser" value="{if isset($smarty.post.dbuser)}{$smarty.post.dbuser|sanitize}{/if}" /></td>
              </tr>
              <tr>
                <td width="280"><span class="stip" title="{$lang.Step1_cinf|sanitize}"><img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_c#}</strong></span></td>
                <td><input class="input" type="text" name="dbpass" value="{if isset($smarty.post.dbpass)}{$smarty.post.dbpass|sanitize}{/if}" /></td>
              </tr>
              <tr>
                <td width="280"><span class="stip" title="{$lang.Step1_dinf|sanitize}"><img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_d#}</strong></span></td>
                <td><input class="input" type="text" name="dbname" value="{if isset($smarty.post.dbname)}{$smarty.post.dbname|sanitize}{/if}" /></td>
              </tr>
              <tr>
                <td width="180"><span class="stip" title="{$lang.Step1_einf|sanitize}"><img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_e#}</strong></span></td>
                <td><input class="input" type="text" name="dbprefix" value="{$smarty.post.dbprefix|sanitize|default: 'sx'}" /></td>
              </tr>
              <tr>
                <td width="180"><span class="stip" title="{$lang.Step1_ginf|sanitize}"><img style="vertical-align: middle" src="{$setupdir}/images/help.png" alt="" /> <strong>{#Step1_g#}</strong></span></td>
                <td>
                  <select class="input" name="type_sess">
                    <option value="auto" {if !isset($smarty.post.type_sess)}selected="selected"{/if}>{#AutoSessions#}</option>
                    <option value="base" {if isset($smarty.post.type_sess) && $smarty.post.type_sess == 'base'}selected="selected"{/if}>{#BaseSessions#}</option>
                    <option value="file" {if isset($smarty.post.type_sess) && $smarty.post.type_sess == 'file'}selected="selected"{/if}>{#FileSessions#}</option>
                  </select>
                </td>
              </tr>
            </table>
          </div>
          <div class="button_steps">
            <input type="hidden" name="step" value="3" />
            <input type="submit" value="{#Step1_Button#}" />
          </div>
        </form>
        {/if}
        </div>
