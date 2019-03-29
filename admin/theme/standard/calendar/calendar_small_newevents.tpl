{if get_active('calendar') && $NewCalEvents}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_newevents', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_newevents" title="{#CalendarNewEvents#}">
    <div class="boxes_body">
      {foreach from=$NewCalEvents item=nce name=nextevents}
        <a class="stip" title="{$nce->Beschreibung|tooltip:200}" href="{$nce->EventLink}"><strong>{$nce->Titel|sanitize}</strong></a>
        <br />
        {$nce->Beschreibung|striptags|truncate: 100|sanitize}
        <br />
        {$nce->Start|date_format: '%d-%m-%Y'}&nbsp;{#Arrow#}<a href="{$nce->EventLink}">{#MoreDetails#}</a>
        {if !$smarty.foreach.nextevents.last}
          <br />
          <br />
        {/if}
      {/foreach}
    </div>
  </div>
</div>
{/if}
