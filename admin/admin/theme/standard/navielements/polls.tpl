{if perm('polls') && admin_active('poll')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/chart.png" alt="" /> {#Polls#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'poll'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=poll">{#Global_Overview#}</a></li>
      <li><a class="nav_subs colorbox" title="{#Polls_new#}" href="index.php?do=poll&amp;sub=new&amp;noframes=1">{#Polls_new#}</a></li>
    </ul>
  </div>
{/if}
