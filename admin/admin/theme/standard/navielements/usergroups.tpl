{if perm('user_groups')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/users.png" alt="" /> {#Groups_Name#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'useroverview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=groups&amp;sub=useroverview">{#Global_Overview#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'permissions'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=groups&amp;sub=permissions">{#GlobalPerm#}</a></li>
    </ul>
  </div>
{/if}
