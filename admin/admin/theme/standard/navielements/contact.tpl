{if perm('contact_forms')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/mail.png" alt="" /> {#ContactForms#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && isset($smarty.request.do) && $smarty.request.do == 'contactforms'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=contactforms">{#Global_Overview#}</a></li>
      <li><a class="nav_subs colorbox" title="{#ContactForms_new#}" href="index.php?do=contactforms&amp;sub=new&amp;noframes=1">{#ContactForms_new#}</a></li>
    </ul>
  </div>
{/if}
