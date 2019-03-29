{if $topnewsitems}
  {assign var=topnews_count value=0}
  {foreach from=$topnewsitems item=news name=dn}
    {assign var=length value=400}
    {assign var=topnews_count value=$topnews_count+1}
    {if !empty($news.TopnewsBild)}
      <div class="topnews_img"><a href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}"><img src="uploads/news/{$news.TopnewsBild}" alt="" /></a></div>
        {else}
      <div class="topnews">
        {if !empty($news.Bild)}
          {assign var=length value=220}
          <span class="newsstart_icon"><a href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}"><img  style="{if $news.BildAusrichtung == 'right'}margin: 0 0 0 5px{else}margin: 0 5px 0 0{/if}" src="{$news.Thumb}" alt="" align="{$news.BildAusrichtung|default:'left'}" /></a></span>
            {/if}
        <h3><a class="ticker" href="index.php?p=news&amp;area={$news.Sektion}&amp;newsid={$news.Id}&amp;name={$news.LinkTitle|translit}">{$news.Titel|sanitize}</a></h3>
        <br />
        {$news.News|truncate: $length|sslash}
      </div>
    {/if}
    <div class="clear"></div>
  {/foreach}
{/if}
