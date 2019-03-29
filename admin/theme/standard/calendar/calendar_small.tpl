{if get_active('calendar')}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_kalender', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_kalender" title="{#Calendar#}">
    <div class="boxes_body" style="padding: 4px; text-align: center" id="calraw">
      {include file="$incpath/calendar/calendar_small_raw.tpl"}
    </div>
  </div>
</div>
{/if}
