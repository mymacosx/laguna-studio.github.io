<div class="header">{#Roadmaps#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
  <tr>
    <td class="headers" width="7%">&nbsp;</td>
    <td width="7%" align="center" class="headers">{#Global_Id#}</td>
    <td class="headers">{#Global_Name#} </td>
    <td width="18%" align="center" class="headers">{#OpenTickets#}</td>
    <td width="18%" align="center" class="headers">{#ClosedTickets#}</td>
    <td class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td width="7%" align="center" class="row_spacer">
        {if $item.Aktiv  != 1}
          <img class="absmiddle stip" title="{$lang.Sys_off|sanitize}" src="{$imgpath}/closed.png" alt="" border="0" />
        {else}
          <img class="absmiddle stip" title="{$lang.Sys_on|sanitize}" src="{$imgpath}/opened.png" alt="" border="0" />
        {/if}
      </td>
      <td width="7%" align="center" class="row_spacer">{$item.Id}</td>
      <td class="row_spacer"><strong><a href="index.php?do=roadmap&amp;sub=showtickets&amp;id={$item.Id}&amp;closed=0">{$item.Name}</a></strong></td>
      <td width="18%" align="center" class="row_spacer">{$item.num_ufertig}</td>
      <td width="18%" align="center" class="row_spacer">{$item.num_fertig}</td>
      <td class="row_spacer" width="1%" align="center">
        <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=roadmap&amp;sub=editroadmap&amp;id={$item.Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
        <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=roadmap&amp;sub=delroadmap&amp;id={$item.Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
      </td>
    </tr>
  {/foreach}
</table>
<br />
