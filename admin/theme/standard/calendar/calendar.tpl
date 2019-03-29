{$calendar}
<br />
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"> {$calendar_prev} </td>
    <td valign="top">&nbsp;</td>
    <td valign="top">{$calendar_next}</td>
  </tr>
</table>
{if permission('calendar_event') || permission('calendar_event_new')}
  <div class="padding5">
    <img class="absmiddle" src="{$imgpath}/calendar/newevent.png" alt="" /> <strong><a href="index.php?p=calendar&amp;area={$area}&amp;action=newevent&amp;month={$currentmonth}&amp;year={$Year}&amp;area={$area}&amp;show={$showtype}">{#Calendar_newEvent#}</a></strong> &nbsp;&nbsp;
    <img class="absmiddle" src="{$imgpath}/calendar/newevent.png" alt="" /> <strong><a href="index.php?p=calendar&amp;area={$area}&amp;action=myevents">{#Calendar_MyEvents#}</a></strong>
  </div>
{/if}
{include file="$incpath/calendar/calendar_jumpform.tpl"}
{include file="$incpath/calendar/calendar_searchform.tpl"}
<div align="center"> <img class="absmiddle" src="{$imgpath}/calendar/birthday.png" alt="" />&nbsp;{#Birthdays_Today#}&nbsp;&nbsp; <img class="absmiddle" src="{$imgpath}/calendar/importantly.png" alt="" />&nbsp;{#Calendar_leg_import#}&nbsp;&nbsp; <img class="absmiddle" src="{$imgpath}/calendar/period.png" alt="" />&nbsp;{#Calendar_period#} </div>
