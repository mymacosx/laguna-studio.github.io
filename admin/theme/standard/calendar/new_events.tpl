{if $items}
  <div class="box_innerhead">{#CalendarNewEvents#}</div>
  {foreach from=$items item=event}
    <div class="{cycle values='row_second,row_first'}" style="padding: 2px"> {#Arrow#} <a href="{$event->link_event_only}">{$event->name}&nbsp;&nbsp;({$event->Start|date_format: '%d.%m.%Y'})</a> </div>
  {/foreach}
  <br />
{/if}