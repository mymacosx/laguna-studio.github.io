{if perm('links') && admin_active('links')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/links.png" alt="" /> {#Links#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'links' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=links&amp;sub=overview">{#Links_ov#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'links' && isset($smarty.request.sub) && $smarty.request.sub == 'add'}nav_subs_active{else}nav_subs{/if} colorbox" title="{#Links_add#}" href="index.php?do=links&amp;sub=new&amp;noframes=1">{#Links_add#}</a></li>
        {if perm('links_categs')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'links' && isset($smarty.request.sub) && $smarty.request.sub == 'categs'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=links&amp;sub=categs">{#Links_categs#}</a></li>
        <li><a title="{#Global_NewCateg#}" class="colorbox_small nav_subs" href="index.php?do=links&amp;sub=addcateg&amp;noframes=1">{#Global_NewCateg#}</a></li>
        {/if}
        {if perm('links_settings')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'links' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=links&amp;sub=settings">{#SettingsModule#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
{if perm('downloads') && admin_active('downloads')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/link_download.png" alt="" /> {#Downloads#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'downloads' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=downloads&amp;sub=overview">{#Download_ov#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'downloads' && isset($smarty.request.sub) && $smarty.request.sub == 'add'}nav_subs_active{else}nav_subs{/if} colorbox" title="{#Download_add#}" href="index.php?do=downloads&amp;sub=new&amp;noframes=1">{#Download_add#}</a></li>
        {if perm('downloads_categs')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'downloads' && isset($smarty.request.sub) && $smarty.request.sub == 'categs'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=downloads&amp;sub=categs">{#Download_categs#}</a></li>
        <li><a title="{#Global_NewCateg#}" class="colorbox_small nav_subs" href="index.php?do=downloads&amp;sub=addcateg&amp;noframes=1">{#Global_NewCateg#}</a></li>
        {/if}
        {if perm('downloads_settings')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'downloads' && isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=downloads&amp;sub=settings">{#SettingsModule#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
