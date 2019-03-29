<div class="box_innerhead">{#Search#}</div>
<div class="box_data">{#SearchHelp#}</div>
<div class="infobox">
  <form method="get" action="index.php">
    <input name="q" type="text" class="input" style="width: 280px" value="{$smarty.get.q|sanitize}" maxlength="35" />
    {include file="$incpath/search/search_areas.tpl"}&nbsp;
    <input type="hidden" name="p" value="search" />
    <input type="submit" class="button" value="{#Search#}" />
  </form>
</div>
{$Results}
