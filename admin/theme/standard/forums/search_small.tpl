{#Search#}
<br />
<form action="index.php" method="get">
  <input type="hidden" name="p" value="forum" />
  <input type="hidden" name="action" value="xsearch" />
  <input type="hidden" name="user_name" value="" />
  <input name="search_post" type="hidden" value="1" />
  <input name="pattern" type="text" class="input" value="{$smarty.request.pattern|default:''|escape}" size="30" />&nbsp;
  <input class="button" type="submit" value="{#Search#}" />
</form>
<br />
<img class="absmiddle" src="{$imgpath_forums}more.png" border="0" alt="" /> <a href="index.php?p=forum&amp;action=search_mask">{#ExtendedSearch#}</a>
