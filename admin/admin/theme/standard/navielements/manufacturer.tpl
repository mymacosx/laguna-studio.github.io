{if perm('manufacturer') && admin_active('manufacturer')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/worker.png" alt="" /> {#Manufacturer#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'manufacturer' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=manufacturer&amp;sub=overview">{#Global_Overview#}</a></li>
      <li><a class="nav_subs colorbox" title="{#Manufacturer_new#}" href="index.php?do=manufacturer&amp;sub=new&amp;noframes=1">{#Manufacturer_new#}</a></li>
    </ul>
  </div>
{/if}
