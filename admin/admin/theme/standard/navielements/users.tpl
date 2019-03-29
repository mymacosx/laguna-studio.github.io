{if perm('users') || perm('settings')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/user.png" alt="" /> {#User_nameS#}</a>
  <div class="submenu">
    <ul>
      {if perm('users')}
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'showusers'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=user&amp;sub=showusers">{#Global_Overview#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'banned'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=banned">{#Banned#}</a></li>
      <li><a title="{#User_Add#}" class="colorbox {if isset($smarty.request.sub) && $smarty.request.sub == 'adduser'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=user&amp;sub=adduser&amp;new=1&amp;noframes=1">{#User_Add#}</a></li>
      {/if}
      {if perm('settings')}
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'user' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=user&amp;sub=settings">{#SettingsModule#}</a></li>
      {/if}
    </ul>
  </div>
{/if}
