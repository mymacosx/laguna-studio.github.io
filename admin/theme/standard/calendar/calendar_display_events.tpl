{if $events}
  {foreach from=$events item=event} <a name="{$event->Id}"></a>
    <div class="box_innerhead"> {$event->Titel|sanitize} </div>
    <div class="box_data">{$event->descr|sslash}</div>
    <div class="infobox">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="120"><strong>{#Calendar_addedfrom#}: </strong></td>
          <td><a href="index.php?p=user&amp;id={$event->Benutzer}&amp;area={$area}">{$event->UserName}</a></td>
        </tr>
        <tr>
          <td width="120"><strong>{#Calendar_addedon#}: </strong></td>
          <td>{$event->Datum}</td>
        </tr>
        <tr>
          <td><strong>{#Calendar_period#}: </strong></td>
          <td>
            {if $event->wd == 1}
              {#Calendar_wholeDay#}
              {if $event->Erledigt == 1}
                ({#GlobalOk#})
              {/if}
            {else}
              {$event->Start|date_format: $lang.DateFormat} - {$event->Ende|date_format: $lang.DateFormat}
              {if $event->Erledigt == 1}
                ({#GlobalOk#})
              {/if}
            {/if}
          </td>
        </tr>
        <tr>
          <td><strong>{#Calendar_weight#}: </strong></td>
          <td>{$event->weight}</td>
        </tr>
      </table>
    </div>
    <div>
      <br />
      {if $smarty.session.benutzer_id == $event->Benutzer || permission('edit_all_events')}
        <a href="index.php?p=calendar&amp;action=editevent&amp;show={$smarty.request.show}&amp;month={$smarty.request.month}&amp;year={$smarty.request.year}&amp;day={$smarty.request.day}&amp;id={$event->Id}&amp;area={$area}">{#Calendar_editEvent#}</a>&nbsp;|&nbsp;
        <a onclick="return confirm('{#Calendar_delC#}');" href="index.php?p=calendar&amp;action=delevent&amp;show={$smarty.request.show}&amp;month={$smarty.request.month}&amp;year={$smarty.request.year}&amp;day={$smarty.request.day}&amp;id={$event->Id}&amp;area={$area}">{#Calendar_eventDel#}</a>
      {/if}
    </div>
    <br />
    <br />
  {/foreach}
{else}
  <br />
  <div class="h2">{#Calendar_noEvents#}</div>
{/if}
