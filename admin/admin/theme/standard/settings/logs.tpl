<div class="header">{#Admin_Logs#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="index.php?do=settings&amp;sub=logs" method="post">
  <table width="100%" border="0" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="5">
        <select name="prior" id="prior">
          <option value="all" {if isset($smarty.request.prior) && $smarty.request.prior == 'all'}selected="selected"{/if}>{#Logs_All#}</option>
          <option value="admin" {if isset($smarty.request.prior) && $smarty.request.prior == 'admin'}selected="selected"{/if}>{#Logs_Sys#} / {#Admin_Global#}</option>
          <option value="adminshop" {if isset($smarty.request.prior) && $smarty.request.prior == 'adminshop'}selected="selected"{/if}>{#Logs_Sys#} / {#Admin_Global#} / {#Global_Shop#}</option>
          <option value="payment" {if isset($smarty.request.prior) && $smarty.request.prior == 'payment'}selected="selected"{/if}>{#Logs_Sys#} / {#SystemPayment#}</option>
          <option value="seite" {if isset($smarty.request.prior) && $smarty.request.prior == 'seite'}selected="selected"{/if}>{#Logs_Sys#} / {#User_nameS#}</option>
          <option value="sys" {if isset($smarty.request.prior) && $smarty.request.prior == 'sys'}selected="selected"{/if}>{#Logs_Sys#} / {#GlobalSystem#}</option>
          <option value="mysql" {if isset($smarty.request.prior) && $smarty.request.prior == 'mysql'}selected="selected"{/if}>{#Logs_MySQL#}</option>
          <option value="erphp" {if isset($smarty.request.prior) && $smarty.request.prior == 'erphp'}selected="selected"{/if}>{#Logs_PHP#}</option>
        </select>
        <input type="submit" class="button" value="{#Global_Show#}" />&nbsp;&nbsp;
        <input type="button" class="button" onclick="location.href = 'index.php?do=settings&amp;sub=logs&amp;action=download&amp;prior={$smarty.request.prior|default:'all'}';" value="{#Logs_Download#}" />&nbsp;&nbsp;
        <input type="button" class="button" onclick="location.href = 'index.php?do=settings&amp;sub=logs&amp;action=del&amp;prior={$smarty.request.prior|default:'all'}';" value="{#DelAll#}" />
      </td>
    </tr>
    <tr>
      <td width="35%" class="headers" nowrap="nowrap"><strong>{#Admin_Logs#}</strong></td>
      <td width="13%" align="center" class="headers" nowrap="nowrap"><strong>{#Global_Date#}</strong></td>
      <td width="12%" align="center" class="headers" nowrap="nowrap"><strong>{#Stats_IP_Adress#}</strong></td>
      <td width="27%" align="center" class="headers" nowrap="nowrap"><strong>{#Info#}</strong></td>
      <td width="13%" align="center" class="headers" nowrap="nowrap"><strong>{#Global_User#}</strong></td>
    </tr>
    {if !empty($errors)}
      {foreach from=$errors item=e}
        <tr class="{cycle values='first,second'}">
          <td width="35%" align="center" nowrap="nowrap">
            <textarea name="textfield" cols="70" rows="4">{$e->Aktion|escape: 'html'}</textarea>
            <br />
            <a class="colorbox" href="index.php?do=support&amp;sub=send_error&amp;id={$e->Id}&amp;noframes=1">{#SendError#}</a>
          </td>
          <td width="13%" align="center">{$e->Datum|date_format: '%d-%m-%Y, %H:%M:%S'}</td>
          <td width="12%" align="center">{if !empty($e->Ip)}<a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$e->Ip}/">{$e->Ip}</a>{else}---{/if}</td>
          <td width="27%" align="left">{$e->Agent}</td>
          <td width="13%" align="center"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$e->Benutzer}&amp;noframes=1">{$e->User}</a></td>
        </tr>
      {/foreach}
    {/if}
    <tr>
      <td colspan="5" align="center" nowrap="nowrap">
        <br />
        {if !empty($navi)}
          {$navi}
        {/if}
      </td>
    </tr>
  </table>
</form>
