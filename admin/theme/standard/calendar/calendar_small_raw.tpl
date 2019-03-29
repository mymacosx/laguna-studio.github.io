<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options_prev = {
        target: '#calraw', url: '{$prevMonth}', timeout: 3000
    };
    var options_next = {
        target: '#calraw', url: '{$nextMonth}', timeout: 3000
    };
    $('#switchcal_prev').on('click', function() {
        $(this).ajaxSubmit(options_prev);
        return false;
    });
    $('#switchcal_next').on('click', function() {
        $(this).ajaxSubmit(options_next);
        return false;
    });
});
//-->
</script>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" class="calendarBackground">
  <tr>
    <td align="left" valign="top"><a id="switchcal_prev" href="javascript: void(0);">&lt;&lt;</a></td>
    <td align="center" valign="top" colspan="6"><strong>{$header}</strong></td>
    <td align="right" valign="top"><a id="switchcal_next" href="javascript: void(0);">&gt;&gt;</a></td>
  </tr>
  <tr>
    <td class="calendarHeader">&nbsp;</td>
    {foreach from=$DayNamesShortArray item=day}
      <td title="{$day}" class="calendarHeader"> {$day|truncate: '2': false}. </td>
    {/foreach}
  </tr>
  {foreach from=$cal_data item=cd}
    <tr>
      <td class="calendarBlanc" style="text-align: center"><a title="{$cd->StartWeek|date_format: '%d.%m.%Y'} - {$cd->EndWeek|date_format: '%d.%m.%Y'} " style="font-weight: bold" href="{$baseurl}/index.php?p=calendar&amp;show=public&amp;action=week&amp;weekstart={$cd->StartWeek}&amp;weekend={$cd->EndWeek}&amp;area={$area}">&gt;</a></td>
      {foreach from=$cd->CalDataInner item=td}
        <td class="{$td->tdclass}" align="right" valign="top">{$td->thelink}</td>
      {/foreach}
    </tr>
  {/foreach}
</table>
