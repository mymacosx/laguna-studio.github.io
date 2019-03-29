{if $contentitems}
  {assign var=topcontent_count value=0}
  {foreach from=$contentitems item=content name=dn}
    {assign var=topcontent_count value=$topcontent_count+1}
    {if !empty($content.TopcontentBild)}
      <div class="topcontent_img"><a href="index.php?p=content&amp;id={$content.Id}&amp;name={$content.Titel|translit}&amp;area={$content.Sektion}"><img src="uploads/content/{$content.TopcontentBild}" alt="" /></a></div>
        {else}
      <div class="topcontent">
        {if !empty($content.Bild)}
          <a href="index.php?p=content&amp;id={$content.Id}&amp;name={$content.Titel|translit}&amp;area={$content.Sektion}"><img  style="{if $content.BildAusrichtung == 'right'}margin: 0 0 0 5px{else}margin: 0 5px 0 0{/if}" src="uploads/content/{$content.Bild}" alt="" align="{$content.BildAusrichtung|default:'left'}" /></a>
          {/if}
        <h3><a class="ticker" href="index.php?p=content&amp;id={$content.Id}&amp;name={$content.Titel|translit}&amp;area={$content.Sektion}">{$content.Titel|sanitize}</a></h3>
        <br />
        {if !empty($content.Bild)}
          <br />
        {/if}
        <div class="justify">{$content.Inhalt|truncate: 400|sslash}</div>
        <br style="clear: both" />
      </div>
    {/if}
  {/foreach}
{/if}
