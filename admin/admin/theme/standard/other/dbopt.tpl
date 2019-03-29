{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('db_navi', 'db_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="db_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/database_options.png" alt="" /> {#Start_DbOpts#}
</div>
<div id="db_open" class="sysinfos" style="font-weight: normal; text-align: center">
  <form onsubmit="" id="dbopt" method="post" action="index.php?do=main&sub=db">
    <select style="width: 99%" size="10" name="ta[]" multiple="multiple">
      {$db_fields}
    </select>
    <div style="padding: 3px">
      <label class="stip" title="{$lang.Sys_db_optimizeInf|sanitize}"><input type="radio" name="what" value="optimize" checked="checked" />{#Start_DbOpt#}</label>
      <label class="stip" title="{$lang.Sys_db_repairInf|sanitize}"><input type="radio" name="what" value="repair" />{#Start_Dbrep#}</label>
    </div>
    <div id="db_res" style="font-style: italic"></div>
    <input class="button" type="submit" />
  </form>
</div>
{/if}
