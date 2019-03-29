<div class="box_innerhead">{#Newsarchive#}</div>
<div id="archivebox">
  {include file="$incpath/news/categ_jump.tpl"}
  {if $newsitems}
    {foreach from=$newsitems item=news name=dn}
      {assign var=length value=400}
      {assign var=year value=$news.ZeitStart|date_format: "%Y"}
      {if $news.ZeitStart|date_format: "%Y" != $YearTemp|default:''}
        <div class="time_header">{#Newsarchive#}&nbsp;{$year}</div>
      {/if}
      {if $news.ZeitStart|date_format: "%d.%Y" != $DateTemp|default:''}
        <div class="newsticker_header"><strong> {$news.ZeitStart|date_format: $lang_settings.Zeitformat}</strong></div>
      {/if}
      <div class="">
        <div class="news_title_archive">
          <h2><a class="ticker" href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}">{$news.Titel|sanitize}</a></h2>
        </div>
        {if !empty($news.Bild)}
          {assign var=length value=220}
          <a href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}"><img class="news_icon_{if $news.BildAusrichtung == 'left'}left{else}right{/if}" src="{$news.Thumb}" alt="" align="{$news.BildAusrichtung|default:'right'}"/></a>
          {/if}
          {if $news.Intro}
          <div class="text_ticker">{$news.Intro|html_truncate: $length|sslash}</div>
        {else}
          <div class="newstext"> {$news.News|html_truncate: $length|sslash} </div>
        {/if}
        <div class="news_footer" style="clear: both">
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
      <div style="padding: 3px;clear: both">&nbsp;</div>
      {assign var=DateTemp value=$news.ZeitStart|date_format: "%d.%Y"}
      {assign var=YearTemp value=$news.ZeitStart|date_format: "%Y"}
    {/foreach}
  {else}
    <div class="box_data">
      <h3>{#NoNews#}</h3>
    </div>
  {/if}
  {if !empty($pages)}
    {$pages}
  {/if}
  <br />
  <div class="noprint">
    <a title="{#RSSAboT#}" style="text-decoration: none" target="_blank" href="{$rss_newslink}"><img class="absmiddle" src="{$imgpath}/page/syndicate.gif" alt="{#RSSAboT#}" /> {#RSSAbo#}</a>
    <br />

<script type="text/javascript">
<!-- //
togglePanel('navpanel_news_search', 'toggler', 30, '{$basepath}');
//-->
</script>
      <br />
      <div class="round">
        <div class="opened" id="navpanel_news_search" title="{#Search#}">
          {include file="$incpath/news/archive_search.tpl"}
        </div>
      </div>
    <br />
  </div>
</div>
