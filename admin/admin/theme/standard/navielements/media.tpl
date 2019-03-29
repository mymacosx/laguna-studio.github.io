{if perm('mediapool')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/films.png" alt="" /> {#Media#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'media' && isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=media&amp;sub=overview">{#Videos#}</a></li>
      <li><a title="{#VideoNew#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'media' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=media&amp;sub=new&amp;noframes=1">{#VideoNew#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'media' && isset($smarty.request.sub) && $smarty.request.sub == 'audio_overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=media&amp;sub=audio_overview">{#Audios#}</a></li>
      <li><a title="{#VideoNew#}" class="{if isset($smarty.request.do) && $smarty.request.do == 'media' && isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if} colorbox" href="index.php?do=media&amp;sub=audio_new&amp;noframes=1">{#AudioNew#}</a></li>
    </ul>
  </div>
{/if}
