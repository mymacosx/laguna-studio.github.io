<div class="box_innerhead">{#Gaming_articles#}</div>
<div id="archivebox">
  {include file="$incpath/articles/categ.tpl"}
  {if $articlesitems}
    {foreach from=$articlesitems item=articles name=dn}
      {assign var=year value=$articles.ZeitStart|date_format: "%Y"}
      {if $articles.ZeitStart|date_format: "%d.%Y" != $DateTemp|default:''}
        <div class="newsticker_header"><strong> {$articles.ZeitStart|date_format: $lang_settings.Zeitformat}</strong></div>
      {/if}
      <div class="news_content">
        <div class="news_title_archive">
          <h2><a class="ticker" href="index.php?p=articles&amp;area={$articles.Sektion}&amp;action=displayarticle&amp;id={$articles.Id}&amp;name={$articles.LinkTitle|translit}">{$articles.Titel|sanitize}</a></h2>
        </div>
        <br />
        {if !empty($articles.Kennwort)}
          <strong>{#Content_LoginText#}</strong>
        {else}
          {if !empty($articles.Bild)}
            <a href="index.php?p=articles&amp;area={$articles.Sektion}&amp;action=displayarticle&amp;id={$articles.Id}&amp;name={$articles.LinkTitle|translit}"><img class="news_icon_{if $articles.Bildausrichtung == 'left'}left{else}right{/if}" src="uploads/articles/{$articles.Bild}" alt="" align="{$articles.Bildausrichtung|default:'right'}"/></a>
            {/if}
            {if $articles.Intro}
            <div class="news_intro">{$articles.Intro|sanitize}</div>
          {/if}
          {if $articles.News}
            <div align="justify" class="news_text_archive"> {$articles.News|sslash|html_truncate: 500} </div>
          {/if}
        {/if}
        <br style="clear: both" />
        <div class="news_footer">
          <table width="100%">
            <tr>
              <td>
                {#Gaming_articles_type#}: <strong><a href="{$articles.TypLink}">{$articles.TypName}</a></strong>
                <br />
                {#Gaming_articles_from#}: <strong><a href="index.php?p=user&amp;id={$articles.Autor}&amp;area={$area}">{$articles.User}</a></strong>,
                {$articles.Zeit|date_format: $lang_settings.Stundenformat}&nbsp;&nbsp;{$articles.Hits} {#Hits#}
              </td>
              <td align="right">
                <a href="index.php?p=articles&amp;area={$articles.Sektion}&amp;action=displayarticle&amp;id={$articles.Id}&amp;name={$articles.LinkTitle|translit}"><img class="absmiddle" src="{$imgpath}/page/arrow_right_small.png" alt="" /></a>
                <a title="{#ReadAll#}" href="index.php?p=articles&amp;area={$articles.Sektion}&amp;action=displayarticle&amp;id={$articles.Id}&amp;name={$articles.LinkTitle|translit}">{#ReadAll#}</a>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div style="padding: 3px;clear: both">&nbsp;</div>
      {assign var=DateTemp value=$articles.ZeitStart|date_format: "%d.%Y"}
      {assign var=YearTemp value=$articles.ZeitStart|date_format: "%Y"}
    {/foreach}
  {else}
    <div class="box_data h3">{#Gaming_articles_no#}</div>
  {/if}
  {if !empty($pages)}
    {$pages}
  {/if}
  <br />
  <div class="noprint"> <a title="{#RSSAboT#}" style="text-decoration: none" target="_blank" href="{$rss_article_link}"><img class="absmiddle" src="{$imgpath}/page/syndicate.gif" alt="{#RSSAboT#}" /> {#RSSAbo#}</a>
    <br />
<script type="text/javascript">
<!-- //
togglePanel('navpanel_articles_search', 'toggler', 30, '{$basepath}');
//-->
</script>
      <br />
      <div class="opened" id="navpanel_news_search" title="{#Search#}">
        {include file="$incpath/articles/archive_search.tpl"}
      </div>
    <br />
  </div>
</div>
