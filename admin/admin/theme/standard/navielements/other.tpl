<a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/ghost.png" alt="" /> {#Nav_Other#}</a>
<div class="submenu">
  <ul>
    {if perm('settings')}
    <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'rss'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=rss">{#SettingsModule#} RSS</a></li>
    {/if}
    {if perm('guestbook') && admin_active('guestbook')}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'comments'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=comments&amp;where=guestbook&amp;object=9999999">{#Guestbook_t#}</a></li>
      {/if}
      {if perm('glossar')}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'glossar'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=glossar">{#Glossar#}</a></li>
      {/if}
      {if perm('partners') && admin_active('partners')}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'partners'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=partners">{#Partners#}</a></li>
      {/if}
    <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'help'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=help">{#GlobalNavHelp#}</a></li>
    <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'support'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1">{#SendOrder#}</a></li>
  </ul>
</div>
