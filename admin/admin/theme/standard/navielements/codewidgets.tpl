{if perm('settings')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/codewidgets.png" alt="" /> {#CodeWidgets#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'codewidgets' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=codewidgets">{#Global_Overview#}</a></li>
      <li><a title="{#Global_Add#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'codewidgets' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=codewidgets&amp;sub=new&amp;noframes=1">{#Global_Add#}</a></li>
      <li><a title="{#Global_Add#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'codewidgets' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=codewidgets&amp;sub=new&amp;html=1&amp;noframes=1">{#Global_Add#} + CKEditor</a></li>
    </ul>
  </div>
{/if}
