<div class="box_innerhead">{#Calendar_search_res#} &bdquo;{$smarty.request.qc|sanitize}&ldquo;</div>
<div class="box_data">
  <table width="100%" cellpadding="3" cellspacing="1" class="box_inner">
    {if $results}
      {foreach from=$results item=res}
        <tr class="{cycle name='s1' values='data_first,data_second'}">
          <td>&raquo;&nbsp;<a href="index.php?p=calendar&amp;action=events&amp;show={$res->Typ}&amp;month={$res->month}&amp;year={$res->year}&amp;day={$res->day}&amp;area={$smarty.request.area}#{$res->Id}">{$res->Titel|truncate: 60|sanitize}</a></td>
          <td align="right">{$res->Start|date_format: $lang.DateFormatExtended}</td>
        </tr>
      {/foreach}
    {else}
      <tr>
        <td class="row_second" colspan="2"><div class="h3">{#Calendar_search_noinsert#}</div></td>
      </tr>
    {/if}
  </table>
</div>
{include file="$incpath/calendar/calendar_searchform.tpl"}
