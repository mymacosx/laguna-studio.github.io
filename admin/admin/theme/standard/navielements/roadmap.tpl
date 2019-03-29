{if perm('roadmaps') && admin_active('roadmap')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/roadmap.png" alt="" /> {#Roadmaps#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'roadmap' && isset($smarty.request.sub) && empty($smarty.request.sub)}nav_subs_active{else}nav_subs{/if}" href="?do=roadmap">{#Global_Overview#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'newroadmap'}nav_subs_active{else}nav_subs{/if}" href="?do=roadmap&amp;sub=newroadmap">{#NewRoadmap#}</a></li>
    </ul>
  </div>
{/if}