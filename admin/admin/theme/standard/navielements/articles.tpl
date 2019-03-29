{if admin_active('articles') && perm('articles')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/articles.png" alt="" /> {#Articles#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'articles' && isset($smarty.request.sub) && $smarty.request.sub == 'show'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=articles&amp;sub=show">{#Gaming_articles#}</a></li>
        {if perm('articles_new')}
        <li><a title="{#Gaming_articles_new#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'articles' && isset($smarty.request.sub) && $smarty.request.sub == 'add'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=articles&amp;sub=add&amp;noframes=1">{#Gaming_articles_new#}</a></li>
        {/if}
        {if perm('articles_category')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'articles' && isset($smarty.request.sub) && $smarty.request.sub == 'showcategs'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=articles&amp;sub=showcategs">{#Gaming_articles_category#}</a></li>
        <li><a title="{#Global_NewCateg#}" class="colorbox_small nav_subs" href="index.php?do=articles&amp;sub=addcateg&amp;noframes=1">{#Global_NewCateg#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
