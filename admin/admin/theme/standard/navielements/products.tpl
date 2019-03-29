{if perm('products') && admin_active('products')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/product.png" alt="" /> {#Products#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'products' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=products&amp;sub=overview">{#Global_Overview#}</a></li>
      <li><a title="{#Products_new#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'products' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=products&amp;sub=new&amp;noframes=1">{#Products_new#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'products' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=products&amp;sub=settings">{#SettingsModule#} </a></li>
        {if perm('genres')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'products' && isset($smarty.request.sub) && $smarty.request.sub == 'genres'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=products&amp;sub=genres">{#Global_Categories#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
