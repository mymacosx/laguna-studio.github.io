{if perm('bannerperm')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/content.png" alt="" /> {#Banners#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'banners' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=banners">{#Global_Overview#}</a></li>
      <li><a class="nav_subs colorbox" title="{#BannersNew#}" href="index.php?do=banners&amp;sub=new&amp;noframes=1">{#BannersNew#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'banners' && isset($smarty.request.sub) && $smarty.request.sub == 'categs'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=banners&amp;sub=categs">{#BannersCategs#}</a></li>
    </ul>
  </div>
{/if}
