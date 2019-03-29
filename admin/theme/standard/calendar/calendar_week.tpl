<div class="box_innerhead"> {#Calendar#} - {#Calendar_weekview#}
  ({$smarty.request.weekstart|date_format: "%d.%m.%Y"} - {$smarty.request.weekend|date_format: "%d.%m.%Y"})
</div>
<br />
{assign var=y_1 value=$smarty.request.weekstart|date_format: "%Y"}
{assign var=y_2 value=$smarty.request.weekend|date_format: "%Y"}
{assign var=m_1 value=$smarty.request.weekstart|date_format: "%m"}
{assign var=m_2 value=$smarty.request.weekend|date_format: "%m"}
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><a href="index.php?p=calendar&amp;show={$smarty.request.show}&amp;action=week&amp;weekstart={$week_pref_start}&amp;weekend={$week_pref_end}&amp;area={$area}">&lt;&lt; {#Calendar_prefWeek#} ({$week_pref_start|date_format: "%d.%m.%Y"})</a></td>
    <td>&nbsp;</td>
    <td><div align="right"><a href="index.php?p=calendar&amp;show={$smarty.request.show}&amp;action=week&amp;weekstart={$week_next_start}&amp;weekend={$week_next_end}&amp;area={$area}">({$week_next_start|date_format: "%d.%m.%Y"}) {#Calendar_nextWeek#} &gt;&gt;</a></div></td>
  </tr>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      {assign var=cal_month value=$m_1|regex_replace: '/^0/': ''}
      {assign var=cal_year  value=$y_1}
      <table width="100%" cellpadding="5" cellspacing="1">
        {section name=wotag loop=7 step=1 max=10}
          {assign var=t value=$smarty.section.wotag.index}
          {assign var=day_start value=$smarty.request.weekstart|date_format: "%d"}
          {assign var=month_start_pre value=$smarty.request.weekstart|date_format: "%m"|regex_replace: '/^0/': ''}
          {assign var=month_start value=$month_start_pre-1}
          {assign var=day_date value=$dates_array[$t]|date_format: '%d'|regex_replace: '/^0/': ''}
          {if $smarty.section.wotag.first}
            <tr>
              <td colspan="2" class="calendarHeadeWeekBig">
                {assign var=m_p value=$smarty.request.weekstart|date_format: "%m"}
                {assign var=m_a value=$m_p-1}
                {$month_array[$m_a]} {$smarty.request.weekstart|date_format: "%Y"}
              </td>
            </tr>
          {/if}
          <tr>
            <td colspan="2" class="calendarHeaderWeek">
              <table width="100%" cellpadding="1" cellspacing="0">
                <tr>
                  <td><strong>{$days.$t}</strong></td>
                  <td align="right">
                    {if permission('calendar_event') || permission('calendar_event_new')}
                      <a href="index.php?p=calendar&amp;action=newevent&amp;day={$day_date}&amp;month={$cal_month}&amp;year={$cal_year}&amp;area={$area}&amp;show={$smarty.request.show}">{#Calendar_newEvent#}</a>
                    {/if}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td width="30" class="row_first" align="center"><div class="h2" style="display: inline" title="{$day_date}.{$cal_month}.{$cal_year}"> {$day_date} </div></td>
            <td class="row_second">
              {foreach from=$items item=ditem name=is}
                {assign var=dow_pref value=$ditem->Start|date_format: "%w"}
                {assign var=dow value=$dow_pref-1}
                {if $dow == "-1"}
                  {assign var=dow value=6}
                {/if}
                {if $dow == $t}
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><a href="index.php?p=calendar&amp;action=events&amp;show=public&amp;month={$ditem->Start|date_format: '%m'}&amp;year={$ditem->Start|date_format: '%Y'}&amp;day={$ditem->Start|date_format: '%d'}&amp;area={$area}#{$ditem->Id}">&gt;&gt; {$ditem->Titel|sanitize}</a></td>
                      <td>
                        <div align="right">
                          {if $ditem->wd == 1}
                            {#Calendar_wholeDay#}
                          {else}
                            {$ditem->Start|date_format: '%H:%M'} - {$ditem->Ende|date_format: '%H:%M'}
                          {/if}
                        </div>
                      </td>
                    </tr>
                  </table>
                {/if}
              {/foreach}
              {if !empty($birthdays.$day_date)}
                {$birthdays.$day_date}&nbsp;
              {/if}
            </td>
          </tr>
          {if $day_date == $days_inmonth[$month_start]}
            <tr>
              <td colspan="2" class="box_innerhead">
                {if $day_date == $days_inmonth[$month_start]}
                  {assign var=cal_month value=$second_month[0]|regex_replace: '/^0/': ''}
                  {assign var=cal_year  value=$second_month[1]}
                {/if}
                {assign var=m_n_pref value=$second_month[0]|regex_replace: '/^0/': ''}
                {assign var=m_n value=$m_n_pref-1}
                {$month_array[$m_n]} {$second_month[1]}
              </td>
            </tr>
          {/if}
        {/section}
      </table>
    </td>
    <td valign="top" nowrap="nowrap">&nbsp;&nbsp;&nbsp;</td>
    <td valign="top" width="170px">
      {$calendar_now|default:''}
      <br />
      {$calendar_next|default:''}
      <br />
    </td>
  </tr>
</table>
{include file="$incpath/calendar/calendar_jumpform.tpl"}
