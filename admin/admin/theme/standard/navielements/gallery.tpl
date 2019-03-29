{if perm('gallery_overview') && admin_active('gallery')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/picture.png" alt="" /> {#Gallery#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'gallery' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=gallery">{#Global_Overview#}</a></li>
        {if perm('gallery_edit')}
        <li><a title="{#GlobalAddCateg#}" class="nav_subs colorbox" href="index.php?do=gallery&amp;sub=addcategory&amp;noframes=1">{#GlobalAddCateg#}</a></li>
        {/if}
        {if perm('gallery_settings')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'gallerysettings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=gallery&amp;sub=gallerysettings">{#SettingsModule#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
