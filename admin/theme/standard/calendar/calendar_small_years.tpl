<table cellpadding="0" cellspacing="0">
  <tr>
    <td class="calendarBorder">
      <table cellpadding="1" cellspacing="1" class="calendarBackground">
        <tr>
          <td align="center" valign="top" colspan="8"><strong><a href="index.php?p=calendar&amp;month={$linkmonth}&amp;year={$linkyear}&amp;area={$area}&amp;show={$showtype}">{$header}</a></strong></td>
        </tr>
        <tr>
          <td class="calendarHeader">&nbsp;</td>
          {foreach from=$DayNamesShortArray item=day}
            <td title="{$day}" class="calendarHeader"> {$day|truncate: '2': false}. </td>
          {/foreach}
        </tr>
        {foreach from=$cal_data item=cd}
          <tr>
            <td class="calendarBlanc" style="text-align: center"><a title="{$cd->StartWeek|date_format: '%d.%m.%Y'} - {$cd->EndWeek|date_format: '%d.%m.%Y'} " style="font-weight: bold" href="index.php?p=calendar&amp;show={$showtype}&amp;action=week&amp;weekstart={$cd->StartWeek}&amp;weekend={$cd->EndWeek}&amp;area={$area}">&gt;</a></td>
            {foreach from=$cd->CalDataInner item=td}
              <td class="{$td->tdclass}" align="right" valign="top">{$td->thelink}</td>
            {/foreach}
          </tr>
        {/foreach}
      </table>
    </td>
  </tr>
</table>
<br />
