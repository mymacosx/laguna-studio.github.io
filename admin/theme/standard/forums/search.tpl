<form action="index.php" method="post">
  {#Search#}&nbsp;
  <input type="hidden" name="p" value="forum" />
  <input type="hidden" name="action" value="xsearch" />
  <input name="search_post" type="hidden" value="1" />
  <input name="pattern" type="text" class="input" value="{$smarty.request.pattern|escape}" size="30" />&nbsp;
  <input class="button" type="submit" value="{#Search#}" />&nbsp;
  <input class="button" type="button" onclick="location.href = 'index.php?p=forum&amp;action=search_mask';" value="{#ExtendedSearch#}" />
</form>
