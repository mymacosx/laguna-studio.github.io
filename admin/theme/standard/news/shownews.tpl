<div class="news_content">
  <h2>{$row.Titel|sanitize}</h2>
  {if !empty($row.Bild) && $smarty.request.artpage < 2}
      <img class="news_icon_{if $row.BildAusrichtung == 'left'}left{else}right{/if}" src="uploads/news/{$row.Bild}" alt="" align="{$row.BildAusrichtung|default:'right'}" />
  {/if}
  <br />
  {if $row.Intro}
      <div class="news_intro">{$row.Intro}</div>
      <br />
  {/if}
  {$row.News}
  <br style="clear: both" />
  <br />
  {if !empty($article_pages)}
      {$article_pages}
  {/if}
  <br />
  <br />
  <table cellpadding="0" cellspacing="0">
    <tr>
      <td>{#GlobalAutor#}: <a href="index.php?p=user&amp;id={$row.Autor}&amp;area={$area}">{$row.User}</a> {#Global_artdate#} {$row.ZeitStart|date_format: $lang.DateFormatSimple}</td>
      {if $row.Bewertung == 1}
          <td>&nbsp;&nbsp;|&nbsp;&nbsp;{#Rating_Rating#}&nbsp;&nbsp;</td>
          <td>
            <input name="starrate_res" type="radio" value="1" class="star" disabled="disabled" {if $row.Wertung == 1}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="2" class="star" disabled="disabled" {if $row.Wertung == 2}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="3" class="star" disabled="disabled" {if $row.Wertung == 3}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="4" class="star" disabled="disabled" {if $row.Wertung == 4}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="5" class="star" disabled="disabled" {if $row.Wertung == 5}checked="checked"{/if} />
          </td>
      {/if}
    </tr>
  </table>
  <br />
  <br />
  <br />
  <br />
  {if $row.Bewertung == 1}
      {$RatingForm|default:''}
  {/if}
  {$IncludedGalleries|default:''}
  {$IncludedNews|default:''}
  {$IncludedArticles|default:''}
  {$IncludedContent|default:''}
  {$GetComments|default:''}
</div>
