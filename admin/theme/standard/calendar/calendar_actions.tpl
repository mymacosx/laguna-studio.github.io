{if permission('calendar_event') || permission('calendar_event_new')}
  <div class="padding5">
    <img class="absmiddle" src="{$imgpath}/calendar/newevent.png" alt="" /> <strong><a href="index.php?p=calendar&amp;area={$area}&amp;action=newevent&amp;month={$currentmonth|default:$smarty.now|date_format: '%m'}&amp;year={$Year}&amp;area={$area}&amp;show={$showtype}">{#Calendar_newEvent#}</a></strong> &nbsp;&nbsp;
    <img class="absmiddle" src="{$imgpath}/calendar/newevent.png" alt="" /> <strong><a href="index.php?p=calendar&amp;area={$area}&amp;action=myevents">{#Calendar_MyEvents#}</a></strong>
  </div>
{/if}
