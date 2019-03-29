<script type="text/javascript">
<!-- //
togglePanel('navpanel_counter', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_counter" title="{#Stats#}">
    <div class="boxes_body">
      <div class="nav_table_left">{#Stats_UserOnline#}</div>
      <div class="nav_table_right">{$Counter_Online}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_Visits#}</div>
      <div class="nav_table_right">{$Counter_Gesamt}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_VisitsToday#}</div>
      <div class="nav_table_right">{$Counter_Heute}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_VisitsTWeek#}</div>
      <div class="nav_table_right">{$Counter_Woche}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_VisitsTMonth#}</div>
      <div class="nav_table_right">{$Counter_Monat}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_VisitsTYear#}</div>
      <div class="nav_table_right">{$Counter_Jahr}</div>
      <br style="clear: both" />
      <div class="nav_table_left">{#Stats_RecordOn#} {$Counter_RekordAm}</div>
      <div class="nav_table_right">{$Counter_Rekord}</div>
      <br style="clear: both" />
    </div>
  </div>
</div>
