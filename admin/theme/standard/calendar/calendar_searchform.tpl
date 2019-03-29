<script type="text/javascript">
<!-- //
function check_searchform() {
    if(document.sf.qc.value == '') {
        return false;
    }
}
//-->
</script>

<div class="infobox">
  <strong>{#Calendar_search#}</strong>&nbsp;
  <form name="sf" action="index.php" method="get" onsubmit="return check_searchform();">
    <input class="input" style="width: 200px" type="text" name="qc" value="{$smarty.request.qc|default:''|sanitize}" />&nbsp;
    <input class="button" type="submit" value="{#Calendar_search#}" />
    <input name="area" type="hidden" value="{$area}" />
    <input name="p" type="hidden" value="calendar" />
    <input name="action" type="hidden" value="search" />
  </form>
</div>
