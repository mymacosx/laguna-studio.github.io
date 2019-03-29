{if perm('stats')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/stats.png" alt="" /> {#Statistik#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'stats' && isset($smarty.request.sub) && $smarty.request.sub == 'search'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=stats&amp;sub=search">{#StatSearch#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'stats' && isset($smarty.request.sub) && $smarty.request.sub == 'autorize'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=stats&amp;sub=autorize">{#Stats_Autorize#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'stats' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=stats&amp;sub=overview">{#Stats#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'stats' && isset($smarty.request.sub) && $smarty.request.sub == 'referer'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=stats&amp;sub=referer">{#Stats_Referer#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'stats' && isset($smarty.request.sub) && $smarty.request.sub == 'user_map'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=stats&amp;sub=user_map">{#SiteMapUser#}</a></li>
    </ul>
  </div>
{/if}
