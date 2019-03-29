{if perm('settings') && perm('templates')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/copy.png" alt="" /> {#ThemeSite#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.sub) && ($smarty.request.sub == 'show_all_tpl' || $smarty.request.sub == 'show_tpl')} nav_subs_active{else}nav_subs{/if}" href="index.php?do=theme&amp;sub=show_all_tpl">{#Templates#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && ($smarty.request.sub == 'show_all_css' || $smarty.request.sub == 'show_css')} nav_subs_active{else}nav_subs{/if}" href="index.php?do=theme&amp;sub=show_all_css">{#ThemeStyle#}</a></li>
    </ul>
  </div>
{/if}
