<div class="box_innerhead">{$header} ({if $smarty.request.show == 'private'}{#Calendar_private#}{else}{#Calendar_public#}{/if})</div>
{include file="$incpath/calendar/calendar_actions.tpl"}
<br />
{assign var=num_week value=0}
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><a href="index.php?p=calendar&amp;month={$mp}&amp;year={$yp}&amp;area={$area}&amp;show={$showtype}">&lt;&lt; {$text_prev_m}</a></td>
    <td><div align="center"><a href="index.php?p=calendar&amp;area={$area}&amp;action=displayyear&amp;show={$showtype}&amp;year={$Year}">{#Calendar_yearView#}</a> | {#Calendar_weeks#}: {foreach from=$cal_data item=cd} {assign var=num_week value=$num_week+1} <a href="index.php?p=calendar&amp;show={$showtype}&amp;action=week&amp;weekstart={$cd->StartWeek}&amp;weekend={$cd->EndWeek}&amp;area={$area}">{$num_week}</a> {/foreach} </div></td>
    <td><div align="right"><a href="index.php?p=calendar&amp;month={$mn}&amp;year={$yn}&amp;area={$area}&amp;show={$showtype}">{$text_next_m} &gt;&gt; </a></div></td>
  </tr>
</table>
<br />
<div class="calendarBorder">
  <table width="100%" cellpadding="1" cellspacing="1" class="calendarBackground">
    <tr>
      <td class="calendarHeaderBig">&nbsp;</td>
      {foreach from=$DayNamesArray item=day}
        <td title="{$day}" class="calendarHeaderBig"> {$day|truncate: '21': false} </td>
      {/foreach}
    </tr>
    {foreach from=$cal_data item=cd}
      <tr>
        <td class="calendarBlanc" style="text-align: center"><a style="font-weight: bold" href="index.php?p=calendar&amp;show={$showtype}&amp;action=week&amp;weekstart={$cd->StartWeek}&amp;weekend={$cd->EndWeek}&amp;area={$area}"> &gt; <br /> &gt; <br /> &gt; </a></td>
            {foreach from=$cd->CalDataInner item=td}
          <td class="{$td->tdclass}" valign="top" style="width: 14.28%">
            {$td->thelink}
            {if $td->countitems > 2}
              <br />
              {if $td->packed_events_niy == 1}
                <span class="calendarInactiveDay">{#Calendar_moreEvents#} ({$td->countitems})</span>
                <br />
              {else}
                <a class="calendarEventLink" href="{$td->packed_events_link}">{#Calendar_moreEvents#} ({$td->countitems})</a>
                <br />
              {/if}
            {else}
              {foreach from=$td->Ereignisse item=sd name=Ereigniss}
                {if $smarty.foreach.Ereigniss.first}
                  <br />
                {/if}
                {if $sd->Gewicht == 1}
                  <img class="absmiddle" src="{$imgpath}/calendar/importantly.png" alt="" />
                {/if}
                <img class="absmiddle" src="{$imgpath}/calendar/period.png" alt="" />
                {assign var=niy value=""}
                {if $sd->is_not_inyear == 1}
                  {assign var=niy value=1}
                {/if}
                {if $sd->tdays != 0}
                  {if $sd->Erledigt == 1}
                    {assign var=style value='text-decoration: line-through'}
                  {/if}
                  {if $niy == 1}
                    <span class="calendarInactiveDay">{$sd->Titel|truncate: '20'|sanitize}</span>
                  {else}
                    <a class="calendarEventLink" {if $sd->Erledigt == 1}style="text-decoration: line-through"{/if} href="{$sd->link_event_only}">{$sd->Titel|truncate: '20'|sanitize}</a>
                    <br />
                  {/if}
                {else}
                  {if $sd->Erledigt == 1}
                    {assign var=style value='text-decoration: line-through'}
                  {/if}
                  {if $niy == 1}
                    <span class="calendarInactiveDay">{$sd->Titel|truncate: '20'|sanitize}</span>
                    <br />
                  {else}
                    <a class="calendarEventLink" style="{$style}" href="{$sd->link_event_only}">{$sd->Titel|truncate: '20'|sanitize}</a>
                    <br />
                  {/if}
                {/if}
                {assign var=style value=''}
              {/foreach}
            {/if}
            {if !empty($td->Geburtstage)}
              <br />
              {$td->Geburtstage}
            {/if}
          </td>
        {/foreach}
      </tr>
    {/foreach}
  </table>
</div>
