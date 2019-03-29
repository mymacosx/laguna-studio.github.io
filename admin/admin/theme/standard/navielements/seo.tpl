{if perm('seo')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/seo.png" alt="" /> {#Seo_mod#}</a>
  <div class="submenu">
    <ul>
      {if admin_active('seomod')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'seo' && isset($smarty.request.sub) && $smarty.request.sub == 'description'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=seo&amp;sub=description">{#Description#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'seo' && isset($smarty.request.sub) && ($smarty.request.sub == 'seotags' || $smarty.request.sub == 'aktiv_seotags' || $smarty.request.sub == 'add_seotags' || $smarty.request.sub == 'del_seotags')}nav_subs_active{else}nav_subs{/if}" href="index.php?do=seo&amp;sub=seotags">{#Seotags#}</a></li>
        {if admin_active('ping')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'seo' && isset($smarty.request.sub) && $smarty.request.sub == 'ping'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=seo&amp;sub=ping">{#Ping#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'seo' && isset($smarty.request.sub) && $smarty.request.sub == 'sitemap'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=seo&amp;sub=sitemap">{#Sitemap#}</a></li>
    </ul>
  </div>
{/if}
