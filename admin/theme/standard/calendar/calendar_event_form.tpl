{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
{include file="$incpath/other/jsform.tpl"}
$(document).ready(function() {
    $("#cf").validate({
        rules: {
            name: { required: true },
            text: { required: true }
        },
        messages: { },
        submitHandler: function() {
            document.forms['f'].submit();
        },
        success: function(label) {
            label.html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;").addClass("checked");
        }
    });
});
//-->
</script>

<div class="box_innerhead">
  {if isset($smarty.request.action) && $smarty.request.action == 'editevent'}
    {#Calendar_editEvent#}
  {else}
    {#Calendar_newEvent#}
  {/if} </div>
<div class="infobox">
  <form method="post" action="index.php?p=calendar" name="f" id="cf" onsubmit="closeCodes();">
    <a name="new" id="new"></a>
    <table width="100%" cellpadding="1" cellspacing="1" class="box_inner">
      <tr>
        <td width="120" class="row_left">{#Date#}</td>
        <td class="row_right">
          <select class="input" name="day">
            {if !empty($smarty.request.day)}
              {assign var=n_tag value=$smarty.request.day}
              {assign var=n_monat value=$smarty.request.month}
              {assign var=n_jahr value=$smarty.request.year}
            {else}
              {assign var=n_tag value=$smarty.now|date_format: '%d'}
              {assign var=n_monat value=$currentmonth}
              {assign var=n_jahr value=$smarty.request.year}
            {/if}
            {section name=day loop=31 start=0}
              <option value="{$smarty.section.day.index+1}" {if $n_tag == $smarty.section.day.index+1}selected="selected"{/if}>{if $smarty.section.day.index+1 < 10}0{/if}{$smarty.section.day.index+1}</option>
            {/section}
          </select> &nbsp;
          <select class="input" name="month">
            {foreach from=$month name=month item=m}
              <option value="{$smarty.foreach.month.index+1}" {if $smarty.foreach.month.index+1 == $n_monat}selected="selected"{/if}>{$m}</option>
            {/foreach}
          </select> &nbsp;
          <select class="input" name="year">
            {section name=year loop=$startYear+10 name=year start=$startYear}
              <option value="{$smarty.section.year.index}" {if $n_jahr == $smarty.section.year.index}selected="selected"{/if}>{$smarty.section.year.index}</option>
            {/section}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Calendar_Start#}</td>
        <td class="row_right">
          {if isset($smarty.request.action) && $smarty.request.action == 'editevent'}
            {assign var=startHour value=$row->Start|date_format: "%H"}
            {assign var=startMinute value=$row->Start|date_format: "%M"}
            {assign var=endHour value=$row->Ende|date_format: "%H"}
            {assign var=endMinute value=$row->Ende|date_format: "%M"}
          {else}
            {assign var=startHour value=8}
            {assign var=startMinute value=0}
            {assign var=endHour value=15}
            {assign var=endMinute value=0}
          {/if}
          <select class="input" name="s_std">
            <option value="0">00</option>
            {section name=std loop=23 start=0 step=1}
              <option value="{$smarty.section.std.index+1}" {if $smarty.section.std.index+1 == $startHour}selected="selected"{/if}>{if $smarty.section.std.index+1 < 10}0{/if}{$smarty.section.std.index+1}</option>
            {/section}
          </select>:
          <select class="input" name="s_min">
            <option value="0">00</option>
            {section name=min loop=59 start=0 step=1}
              <option value="{$smarty.section.min.index+1}" {if $smarty.section.min.index+1 == $startMinute}selected="selected"{/if}>{if $smarty.section.min.index+1 < 10}0{/if}{$smarty.section.min.index+1}</option>
            {/section}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Calendar_End#}</td>
        <td class="row_right">
          <select class="input" name="e_std">
            <option value="0">00</option>
            {section name=estd loop=23 start=0 step=1}
              <option value="{$smarty.section.estd.index+1}" {if $smarty.section.estd.index+1 == $endHour}selected="selected"{/if}>{if $smarty.section.estd.index+1 < 10}0{/if}{$smarty.section.estd.index+1}</option>
            {/section}
          </select>:
          <select class="input" name="e_min">
            <option value="0">00</option>
            {section name=emin loop=59 start=0 step=1}
              <option value="{$smarty.section.emin.index+1}" {if $smarty.section.emin.index+1 == $endMinute}selected="selected"{/if}>{if $smarty.section.emin.index+1 < 10}0{/if}{$smarty.section.emin.index+1}</option>
            {/section}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Calendar_wholeDay#}</td>
        <td class="row_right"><label><input name="s_wday" type="checkbox" value="1" {if isset($row->wd) && $row->wd == 1}checked="checked"{/if} />{#Yes#}</label></td>
      </tr>
      {if isset($smarty.request.action) && $smarty.request.action == 'editevent'}
        <tr>
          <td class="row_left">{#GlobalOk#}</td>
          <td class="row_right"><label><input name="done" type="checkbox" value="1" {if isset($row->Erledigt) && $row->Erledigt == 1}checked="checked"{/if} />{#Yes#}</label></td>
        </tr>
      {/if}
      {if $smarty.request.action != 'editevent'}
        <tr>
          <td class="row_left">{#Calendar_moreDays#}</td>
          <td class="row_right">
            <select class="input" name="days" id="days">
              <option value="0" selected="selected">{#No#}</option>
              {section name=days loop=21 start=1}
                <option value="{$smarty.section.days.index+1}">{$smarty.section.days.index+1} {#Calendar_eDays#}</option>
              {/section}
            </select>
          </td>
        </tr>
      {/if}
      <tr>
        <td class="row_left">{#Title#}</td>
        <td class="row_right"><input class="input" name="name" type="text" size="50" value="{$row->Titel|default:''|sanitize}" /></td>
      </tr>
      <tr>
        <td valign="top" class="row_left">{#Description#}</td>
        <td class="row_right">
          {include file="$incpath/comments/format.tpl"}
          <textarea name="text" cols="" rows="10" class="input" id="msgform" style="width: 97%">{$row->Beschreibung|default:''|sanitize}</textarea>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Calendar_weight#}</td>
        <td class="row_right">
          <select class="input" name="weight" id="weight">
            {assign var=plus value=0}
            {foreach from=$weight item=we}
              {assign var=plus value=$plus+1}
              <option value="{$plus}" {if $plus == $row->Gewicht|default:''}selected="selected"{/if}>{$we}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Calendar_selCal#}</td>
        <td class="row_right">
          {if ($smarty.request.show == 'public' || empty($smarty.request.show))}
            <input name="show" type="hidden" id="show" value="public" />
          {else}
            <input name="show" type="hidden" id="show" value="private" />
          {/if}
          <select class="input" name="show">
            {if permission('calendar_event_new')}
              <option value="public" {if $smarty.request.show == 'public'}selected="selected"{/if}>{#Calendar_public#}</option>
            {/if}
            {if permission('calendar_event')}
              <option value="private" {if $smarty.request.show == 'private'}selected="selected"{/if}>{#Calendar_private#}</option>
            {/if}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left">&nbsp;</td>
        <td class="row_right">
          <input name="Submit" type="submit" class="button" value="{#Save#}" />
          <input name="newevent" type="hidden" value="1" />
          <input name="area" type="hidden" value="{$area}" />
          {if isset($smarty.request.action) && $smarty.request.action == 'newevent'}
            <input name="action" type="hidden" value="insertevent" />
          {else}
            <input name="month" type="hidden" id="month" value="{$smarty.request.month}" />
            <input name="year" type="hidden" id="year" value="{$smarty.request.year}" />
            <input name="day" type="hidden" id="day" value="{$smarty.request.day}" />
            <input name="action" type="hidden" value="editevent" />
            <input name="subaction" type="hidden" id="subaction" value="save" />
            <input name="id" type="hidden" id="id" value="{$smarty.request.id}" />
          {/if}
        </td>
      </tr>
    </table>
  </form>
</div>
