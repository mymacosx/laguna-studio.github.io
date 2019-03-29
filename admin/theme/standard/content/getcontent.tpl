{if $content_ex_res}
  {if !empty($content_ex_res.TopcontentBild)}
    <div style="text-align: center"><a title="{$content_ex_res.Titel|sanitize}" href="index.php?p=content&amp;id={$content_ex_res.Id}&amp;name={$content_ex_res.Titel|translit}&amp;area={$content_ex_res.Sektion}"><img src="uploads/content/{$content_ex_res.TopcontentBild}" alt="" /></a></div>
      {else}
    <div class="margin5">
      {if !empty($content_ex_res.Bild)}
        <a title="{$content_ex_res.Titel|sanitize}" href="index.php?p=content&amp;id={$content_ex_res.Id}&amp;name={$content_ex_res.Titel|translit}&amp;area={$content_ex_res.Sektion}"><img  style="{if $content_ex_res.BildAusrichtung == 'right'}margin: 0 0 0 5px{else}margin: 0 5px 0 0{/if}" src="uploads/content/{$content_ex_res.Bild}" alt="" align="{$content_ex_res.BildAusrichtung|default:'left'}" /></a>
        {/if}
      <h3><a title="{$content_ex_res.Titel|sanitize}" class="ticker" href="index.php?p=content&amp;id={$content_ex_res.Id}&amp;name={$content_ex_res.Titel|translit}&amp;area={$content_ex_res.Sektion}">{$content_ex_res.Titel|sanitize}</a></h3>
      <br />
      {if !empty($content_ex_res.Bild)}
        <br />
      {/if}
      {$content_ex_res.Inhalt|sslash}
      <br style="clear: both" />
    </div>
  {/if}
{/if}
