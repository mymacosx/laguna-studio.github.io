<script type="text/javascript">
<!-- //
togglePanel('navpanel_pagesearch', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_pagesearch" title="{#Search#}">
    <div class="boxes_body">
      <form method="get" action="index.php">
        <input name="q" type="text" class="input" style="width: 160px; margin-bottom: 2px" value="{$smarty.get.q|default:''|sanitize}" maxlength="35" />
        <br />
        {include file="$incpath/search/search_areas.tpl"}
        <input type="hidden" name="p" value="search" />
        <input type="submit" class="button" value="{#Search#}" style="width: 55px; margin-left: 2px" />
      </form>
    </div>
  </div>
</div>
