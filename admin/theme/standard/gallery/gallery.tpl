{if empty($smarty.request.action) || $smarty.request.action == "showincluded"}
  {include file="$incpath/gallery/galleryshow.tpl"}
{/if}
{if $smarty.request.action == "showimages"}
  {include file="$incpath/gallery/galleryimages.tpl"}
  {$linked_news}
  {$linkes_articles}
{/if}
