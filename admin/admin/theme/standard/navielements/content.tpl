{if perm('content') && admin_active('content')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/content.png" alt="" /> {#Content#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'content' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=content&amp;sub=overview">{#Global_Overview#}</a></li>
        {if perm('content_new')}
        <li><a  title="{#Content_new#}" class="colorbox {if isset($smarty.request.do) && $smarty.request.do == 'content' && isset($smarty.request.sub) && $smarty.request.sub == 'addcontent'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=content&amp;sub=addcontent&amp;noframes=1">{#Content_new#}</a></li>
        {/if}
        {if perm('content_category')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'content' && isset($smarty.request.sub) && $smarty.request.sub == 'categories'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=content&amp;sub=categories">{#Global_Categories#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
