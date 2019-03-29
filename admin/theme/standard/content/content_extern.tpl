{if $externContent}
  <div class="box_innerhead">{#ParentDocs#}</div>
  <div class="infobox">
    {foreach from=$externContent item=ne}
      <a style="text-decoration: none" href="index.php?p=content&amp;id={$ne.Id}&amp;name={$ne.Titel|translit}&amp;area={$area}" class="stip" title="{$ne.Inhalt|tooltip:300}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
      <a href="index.php?p=content&amp;id={$ne.Id}&amp;name={$ne.Titel|translit}&amp;area={$area}" class="stip" title="{$ne.Inhalt|tooltip:300}"><strong>{$ne.Titel|sanitize}</strong></a>
      <br />
    {/foreach}
  </div>
{/if}
