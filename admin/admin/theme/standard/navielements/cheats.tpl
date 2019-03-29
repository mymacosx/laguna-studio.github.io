{if admin_active('cheats') && perm('cheats')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/gaming.png" alt="" /> {#GamingArea#}</a>
  <div class="submenu">
    <ul>
      {if perm('plattforms')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'cheats' && isset($smarty.request.sub) && $smarty.request.sub == 'plattforms'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=cheats&amp;sub=plattforms">{#Gaming_plattforms#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'cheats' && isset($smarty.request.sub) && $smarty.request.sub == 'show'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=cheats&amp;sub=show">{#Gaming_cheats#}</a></li>
      <li><a title="{#Gaming_cheats_new#}" class="nav_subs colorbox" href="index.php?do=cheats&amp;sub=add&amp;noframes=1">{#Gaming_cheats_new#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'cheats' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=cheats&amp;sub=settings">{#SettingsModule#}</a></li>
    </ul>
  </div>
{/if}
