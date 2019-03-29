<br />
<div class="box_innerhead">{#Calendar_jumpdate#}</div>
<form method="post" action="index.php">
  <select class="input" name="month">
    {foreach from=$month name=month item=m}
      <option value="{$smarty.foreach.month.index+1}" {if $smarty.foreach.month.index+1 == $currentmonth}selected="selected"{/if}>{$m}</option>
    {/foreach}
  </select>&nbsp;
  <select class="input" name="year">
    {section name=year loop=$startYear+10 name=year start=$startYear}
      <option value="{$smarty.section.year.index}" {if $Year == $smarty.section.year.index}selected="selected"{/if}>{$smarty.section.year.index}</option>
    {/section}
  </select>&nbsp;
  <select class="input" name="show" style="width: 103px">
    <option value="public" {if $privatePublic == 'public'}selected="selected"{/if}>{#Calendar_public#}</option>
    {if $loggedin}
      <option value="private" {if $privatePublic == 'private'}selected="selected"{/if}>{#Calendar_private#}</option>
    {/if}
  </select>&nbsp;
  <input type="hidden" name="p" value="calendar" />
  <input type="hidden" name="area" value="{$area}" />
  <input name="submit" type="submit" class="button" value="{#Calendar_jumpB#}" />
</form>
