{if perm('navigation_edit')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/navi.png" alt="" /> {#Navigation#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'navigation' && isset($smarty.request.sub) && $smarty.request.sub == 'list'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=navigation&amp;sub=list">{#Global_Overview#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'navigation' && isset($smarty.request.sub) && $smarty.request.sub == 'speedbar'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=navigation&amp;sub=speedbar">{#Quicknavi#}</a></li>
        {if admin_active('flashtag')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'navigation' && isset($smarty.request.sub) && $smarty.request.sub == 'flashtag'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=navigation&amp;sub=flashtag">{#Flashtag#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
