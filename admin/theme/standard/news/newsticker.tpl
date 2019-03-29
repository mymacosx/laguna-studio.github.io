<div class="box_innerhead">{#Newsarchive#}</div>
{assign var=news_count value=0}
{foreach from=$newsitems item=news name=dn}
  {assign var=length value=400}
  {assign var=news_count value=$news_count+1}
  {assign var=year value=$news.ZeitStart|date_format: "%Y"}
  {if $news.ZeitStart|date_format: "%d.%Y" != $DateTemp|default:''}
    <div class="newsticker_header"><strong> {$news.ZeitStart|date_format: $lang_settings.Zeitformat}</strong></div>
  {/if}
  <div class="news_startpage">
    <h3><a class="ticker" href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}">{$news.Titel|sanitize}</a></h3>
    <br />
    {if !empty($news.Bild)}
      {assign var=length value=220}
      <span class="newsstart_icon"><a href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}"><img  style="{if $news.BildAusrichtung == 'right'}margin: 0 0 5px 5px{else}margin: 0 5px 5px 0{/if}" src="{$news.Thumb}" alt="" align="{$news.BildAusrichtung|default:'left'}" /></a></span>
        {/if}
    <div class="justify news_startpage_text">
      {if $news.Intro}
        {$news.Intro|truncate: $length|sslash}
      {else}
        {$news.News|truncate: $length|sslash}
      {/if}
    </div>
    <div class="clear"></div>
    <div class="newsstart_footer">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left">
            {#GlobalAutor#}: <a href="index.php?p=user&amp;id={$news.Autor}&amp;area={$area}">{$news.User}</a>,
            {$news.Zeit|date_format: '%H:%M'} | {$news.Hits} {#Hits#}
            {if !empty($news.Kommentare)} | <a title="{#Comments#}" href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}#comments">{#Comments#}</a>{/if}
          </td>
          <td align="right">
            <img class="absmiddle" src="{$imgpath}/page/arrow_right_small.png" alt="{#ReadAll#}" />
            <a title="{#ReadAll#}" href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}">{#ReadAll#}</a>
          </td>
        </tr>
      </table>
    </div>
  </div>
  {assign var=DateTemp value=$news.ZeitStart|date_format: "%d.%Y"}
  {assign var=YearTemp value=$news.ZeitStart|date_format: "%Y"}
{/foreach}
<br style="clear: both" />
