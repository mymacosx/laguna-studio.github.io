{if perm('news') && admin_active('News')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/news.png" alt="" /> {#News#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'news' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=news&amp;sub=overview">{#News_show#}</a></li>
        {if perm('news_new')}
        <li><a title="{#News_new#}" class="colorbox {if isset($smarty.request.do) && $smarty.request.do == 'news' && isset($smarty.request.sub) && $smarty.request.sub == 'addnews'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=news&amp;sub=addnews&amp;noframes=1">{#News_new#}</a></li>
        {/if}
        {if perm('news_category')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'news' && isset($smarty.request.sub) && $smarty.request.sub == 'categories'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=news&amp;sub=categories">{#Global_Categories#}</a></li>
        <li><a title="{#Global_NewCateg#}" class="colorbox_small nav_subs" href="index.php?do=news&amp;sub=addcateg&amp;noframes=1">{#Global_NewCateg#}</a></li>
        {/if}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'news' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=news&amp;sub=settings">{#SettingsModule#}</a></li>
    </ul>
  </div>
{/if}
