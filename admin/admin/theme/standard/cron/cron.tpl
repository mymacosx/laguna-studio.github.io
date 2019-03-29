<div class="header">{#Scheduler#}</div>
<div class="subheaders">
  <a title="{#CronAdd#}" class="colorbox" href="index.php?do=settings&amp;sub=cron&amp;type=add_cron&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="" /> {#CronAdd#}</a>&nbsp;&nbsp;&nbsp;
  <a href="index.php?do=settings&amp;sub=cron&amp;type=def_cron"><img class="absmiddle" src="{$imgpath}/help.png" alt="" border="" /> {#CronDef#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellpadding="3" cellspacing="1">
  <tr>
    <td class="headers" nowrap="nowrap"><strong>{#Global_descr#}</strong></td>
    <td width="100" align="center" class="headers" nowrap="nowrap"><strong>{#CronStart#}</strong></td>
    <td width="100" align="center" class="headers" nowrap="nowrap"><strong>{#GlobalOk#}</strong></td>
    <td width="80" align="center" class="headers" nowrap="nowrap"><strong>{#CronPeriod#}</strong></td>
    <td width="80" align="center" class="headers" nowrap="nowrap"><strong>{#Global_Type#}</strong></td>
    <td width="80" align="center" class="headers" nowrap="nowrap"><strong>{#Bereich#}</strong></td>
    <td width="85" align="center" class="headers" nowrap="nowrap"><strong>{#Global_Actions#}</strong></td>
  </tr>
  {foreach from=$cron item=e}
    <tr class="{cycle values='first,second'}">
      <td>{$e->Title|sanitize}</td>
      <td width="100" align="center">
        {if $e->Datum == 0}
          - - - -
        {else}
          {$e->Datum|date_format: '%d.%m.%Y, %H:%M'}
        {/if}
      </td>
      <td width="100" align="center">
        {if $e->PrevTime == 0}
          - - - -
        {else}
          {$e->PrevTime|date_format: '%d.%m.%Y, %H:%M'}
        {/if}
      </td>
      <td width="80" align="center">
        {if $e->NextTime == 0}
          - - - -
        {else}
          {$e->NextTime}
        {/if}
      </td>
      <td width="80" align="center">{$e->Typel}</td>
      <td width="80" align="center">{$e->Modul|sanitize}</td>
      <td width="85">
        {if $e->Modul != 'ping'}
          <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=settings&amp;sub=cron&amp;type=edit_cron&amp;id={$e->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          {else}
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {/if}
        {if $e->Aktiv == 1}
          <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=settings&amp;sub=cron&amp;type=aktiv_cron&amp;aktiv=0&amp;id={$e->Id}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
          {else}
          <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=settings&amp;sub=cron&amp;type=aktiv_cron&amp;aktiv=1&amp;id={$e->Id}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
          {/if}
        <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$e->Title|jsspecialchars}');" href="index.php?do=settings&amp;sub=cron&amp;type=del_cron&amp;id={$e->Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          {if !empty($e->Error)}
          <img class="absmiddle stip" title="{$e->Error|sanitize}" src="{$imgpath}/warning.gif" alt="" border="" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>
