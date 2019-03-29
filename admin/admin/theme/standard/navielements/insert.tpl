{if perm('settings')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/content.png" alt="" /> {#InsertContent#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'insert' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=insert">{#Global_Overview#}</a></li>
      <li><a title="{#Global_Add#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'insert' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=insert&amp;sub=new&amp;noframes=1">{#Global_Add#}</a></li>
      <li><a title="{#Global_Add#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'insert' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=insert&amp;sub=new&amp;html=1&amp;noframes=1">{#Global_Add#} + CKEditor</a></li>
      {if admin_active('phrases')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'phrases'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=phrases">{#S_vivod#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
