{if perm('newsletter') && admin_active('newsletter')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/newsletter.png" alt="" /> {#Newsletter#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'newsletter' && isset($smarty.request.sub) && $smarty.request.sub == 'showabos'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=newsletter&amp;sub=showabos">{#Newsletter_nAbos#}</a></li>
        {if perm('newslettersend')}
        <li><a class="nav_subs colorbox" title="{#Newsletter_toAbos#}" href="index.php?do=newsletter&amp;sub=new&amp;to=abos&amp;noframes=1">{#Newsletter_toAbos#}</a></li>
        <li><a class="nav_subs colorbox" title="{#Newsletter_toGroups#}" href="index.php?do=newsletter&amp;sub=new&amp;to=groups&amp;noframes=1">{#Newsletter_toGroups#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'newsletter' && isset($smarty.request.sub) && $smarty.request.sub == 'categs'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=newsletter&amp;sub=categs">{#Newsletter_Categs#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'newsletter' && isset($smarty.request.sub) && $smarty.request.sub == 'archive'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=newsletter&amp;sub=archive&amp;sys=one">{#Newsletter_archive#}</a></li>
    </ul>
  </div>
{/if}
