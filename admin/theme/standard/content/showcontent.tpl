<div class="content_content">
  <h2>{$res.Titel|sanitize}</h2>
  {if !empty($res.Bild) && $smarty.request.artpage<2}
    <img class="news_icon_{if $res.BildAusrichtung == 'left'}left{else}right{/if}" src="uploads/content/{$res.Bild}" alt="" align="{$res.BildAusrichtung|default:'right'}" /> {/if}
    <div style="margin-top: 10px"> {$res.Inhalt} </div>
    <br style="clear: both" />
    {if !empty($article_pages)}
      {$article_pages}
      <br />
    {/if}
    <br />
    <br />
    <br />
    {if $res.Bewertung == 1}
      <table align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td>&nbsp;&nbsp;{#Rating_Rating#}&nbsp;&nbsp;</td>
          <td>
            <input name="starrate_res" type="radio" value="1" class="star" disabled="disabled" {if $res.Wertung == 1}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="2" class="star" disabled="disabled" {if $res.Wertung == 2}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="3" class="star" disabled="disabled" {if $res.Wertung == 3}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="4" class="star" disabled="disabled" {if $res.Wertung == 4}checked="checked"{/if} />
            <input name="starrate_res" type="radio" value="5" class="star" disabled="disabled" {if $res.Wertung == 5}checked="checked"{/if} />
          </td>
        </tr>
      </table>
    {/if}
    <br />
    {if $res.Bewertung == 1}
      {$RatingForm|default:''}
    {/if}
    {$IncludedGalleries|default:''}
    {$IncludedContent|default:''}
    {$IncludedArticles|default:''}
    {$IncludedNews|default:''}
    {$GetComments|default:''}
  </div>
