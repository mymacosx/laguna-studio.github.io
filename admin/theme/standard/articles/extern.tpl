{if !empty($externArticles)}
  <div class="box_innerhead">{#ParentArticles#}</div>
  <div class="infobox">
    {foreach from=$externArticles item=ne}
      <a href="index.php?p=articles&amp;area={$area}&amp;action=displayarticle&amp;id={$ne.Id}&amp;name={$ne.Titel|translit}" class="stip" title="{$ne.News|tooltip:200}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
      <a href="index.php?p=articles&amp;area={$area}&amp;action=displayarticle&amp;id={$ne.Id}&amp;name={$ne.Titel|translit}" class="stip" title="{$ne.News|tooltip:200}"><strong>{$ne.Titel|sanitize}</strong></a>
      <br />
    {/foreach}
  </div>
{/if}
