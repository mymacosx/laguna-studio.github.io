<div class="infobox">
  <form method="post" action="index.php?p=links&amp;area={$area}&amp;action=search">
    <input type="text" name="ql" style="width: 200px" value="{$smarty.request.ql|default:''|sanitize|replace: '-': ''}" class="input" />&nbsp;
    <input type="submit" class="button" value="{#Search#}" />
  </form>
</div>
