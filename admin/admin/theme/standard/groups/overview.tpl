<div class="header">{#Groups_Name#} - {#Global_Overview#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
  <tr>
    <td width="100" align="center" class="headers">{#Groups_GroupId#}</td>
    <td width="200" class="headers">{#Global_Name#}</td>
    <td width="50" align="center" class="headers">{#Groups_User#}</td>
    <td class="headers">{#Global_Actions#}</td>
    <td class="headers">&nbsp;</td>
  </tr>
  {foreach from=$groups item=g}
    <tr class="{cycle values='second,first'}">
      <td align="center">{$g->Id}</td>
      <td><strong>{$g->Name_Intern|sanitize}</strong></td>
      <td align="center">{if $g->Id == 2}&nbsp;{else}{$g->uc->UCount}{/if}</td>
      <td>
        <a class="colorbox stip" title="{$lang.Groups_Edit|sanitize}" href="index.php?do=groups&amp;sub=groupedit&amp;id={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
          {if $g->Id > 5}
          <a onclick="return confirm('{#Groups_DelC#}');" class="stip" title="{$lang.Groups_Del|sanitize}" href="index.php?do=groups&amp;sub=delgroup&amp;id={$g->Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          {/if}
      </td>
      <td>&nbsp;</td>
    </tr>
  {/foreach}
</table>
<br />
<br />
<form method="post" action="">
  <fieldset>
    <legend>{#Groups_New#}</legend>
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="60"><label for="Name">{#Global_Name#}</label></td>
        <td>
          <input type="text" name="Name" id="Name" class="input" />
          <input class="button" type="submit" value="{#Save#}" />
          <input name="new" type="hidden" id="new" value="1" />
        </td>
      </tr>
    </table>
  </fieldset>
</form>
