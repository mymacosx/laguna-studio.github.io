{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.MySQLBigger').colorbox({
        width: "80%",
        maxHeight: "90%",
        inline: true,
        href: "#mysql_big"
    });
    $('#sqlquery_big').submit(function() {
        var options = {
            target: '#query_res_big',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });
    toggleCookie('sql_navi', 'sql_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="sql_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/sysinfo.png" alt="" /> {#MySQLButton#}
</div>
<div class="sysinfos" id="sql_open">
  <div id="query_res" style="width: 95%"></div>
  <form method="post" action="index.php" id="sqlquery">
    <input type="hidden" name="sqlin" value="1" />
    <textarea cols="" rows="" style="width: 97%; height: 80px; margin-bottom: 5px" id="sqlq" name="sql"></textarea>
    <br />
    <input type="submit" class="button" value="{#MySQLButton#}" onclick="return confirm('{#MySQLButtonC#}\n\n' + document.getElementById('sqlq').value);" />
    <input title="{#MySQLButton#}" type="button" class="MySQLBigger button_second" value="{#Navigation_new#}" />
  </form>
</div>
<div style="display: none">
  <div id="mysql_big">
    <div id="query_res_big" style="width: 96%"></div>
    <form method="post" action="" id="sqlquery_big">
      <input type="hidden" name="sqlin" value="1" />
      <textarea cols="" rows="" style="width: 97%; height: 400px" id="sqlq" name="sql"></textarea>
      <br />
      <br />
      <input type="submit" class="button" value="{#MySQLButton#}" onclick="return confirm('{#MySQLButtonC#}\n\n' + document.getElementById('sqlq').value);" />
    </form>
    <br />
    <br />
  </div>
</div>
{/if}
