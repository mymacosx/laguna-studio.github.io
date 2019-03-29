{if $externNews}
  <div class="box_innerhead">{#News_extern#}</div>
  <div class="infobox">
    {foreach from=$externNews item=ne}
      <div style="float: left">
        <a href="index.php?p=news&amp;area={$area}&amp;newsid={$ne.Id}&amp;name={$ne.Titel|translit}" class="stip" title="{$ne.News|tooltip:200}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
        <a href="index.php?p=news&amp;area={$area}&amp;newsid={$ne.Id}&amp;name={$ne.Titel|translit}" class="stip" title="{$ne.News|tooltip:200}"><strong>{$ne.Titel|sanitize}</strong></a>
      </div>
      <div style="float: right"> {#GlobalAutor#} <a href="index.php?p=user&amp;id={$ne.Autor}&amp;area={$area}">{$ne.User}</a></div>
      <br style="clear: both" />
    {/foreach}
  </div>
{/if}
