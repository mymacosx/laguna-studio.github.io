{if $toparticleitems}
  <div id="topcontent">
    {foreach from=$toparticleitems item=content name=dn}
      {if !empty($content.TopcontentBild)}
        <div class="topcontent_img"><a href="index.php?p=articles&amp;area={$content.Sektion}&amp;action=displayarticle&amp;id={$content.Id}&amp;name={$content.Titel|translit}"><img src="uploads/articles/{$content.TopcontentBild}" alt="" /></a></div>
          {else}
        <div class="topcontent">
          {if !empty($content.Bild)}
            <a href="index.php?p=articles&amp;area={$content.Sektion}&amp;action=displayarticle&amp;id={$content.Id}&amp;name={$content.Titel|translit}"><img style="{if $content.Bildausrichtung == 'right'}margin: 0 0 0 5px{else}margin: 0 5px 0 0{/if}" src="uploads/articles/{$content.Bild}" alt="" align="{$content.Bildausrichtung|default:'left'}" /></a>
            {/if}
          <h3><a class="ticker" href="index.php?p=articles&amp;area={$content.Sektion}&amp;action=displayarticle&amp;id={$content.Id}&amp;name={$content.Titel|translit}">{$content.Titel|sanitize}</a></h3>
          <br />
          {if !empty($content.Bild)}
            <br />
          {/if}
          {$content.Inhalt|truncate: 400|sslash}
          <br style="clear: both" />
        </div>
      {/if}
    {/foreach}
  </div>
{/if}
