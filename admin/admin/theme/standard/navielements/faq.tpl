{if perm('faq') && admin_active('faq')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/faq.png" alt="" /> {#Faq#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'faq' && isset($smarty.request.sub) && $smarty.request.sub == 'sendfaq'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=faq&amp;sub=sendfaq">{#NewSendFaq#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'faq' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=faq&amp;sub=overview">{#Global_Overview#} {#Faq#}</a></li>
      <li><a title="{#Faq_new#}" class="colorbox nav_subs" href="index.php?do=faq&amp;sub=new&amp;noframes=1">{#Faq_new#}</a></li>
        {if perm('faq_category')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'faq' && isset($smarty.request.sub) && $smarty.request.sub == 'categories'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=faq&amp;sub=categories">{#Global_Categories#} {#Faq#}</a></li>
        <li><a title="{#Global_NewCateg#}" class="colorbox_small nav_subs" href="index.php?do=faq&amp;sub=addcateg&amp;noframes=1">{#Global_NewCateg#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
