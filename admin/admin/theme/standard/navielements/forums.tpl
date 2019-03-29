{if perm('forum') && admin_active('forums')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/forum.png" alt="" /> {#Forums_nt#}</a>
  <div class="submenu">
    <ul>
      {if perm('forum')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'overview'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=overview">{#Global_Overview#}</a></li>
        {/if}
        {if perm('forum_deltopics')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'deltopics'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=deltopics">{#Forums_Del_Topics#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'delratings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=delratings">{#Forums_Del_Ratings#}</a></li>
        {/if}
        {if perm('forum_attachments')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'showattachments'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=showattachments">{#Forums_Att_Show#}</a></li>
        {/if}
        {if perm('forum_userrankings')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'userrankings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=userrankings">{#Forums_URank_title#}</a></li>
        {/if}
        {if perm('forum_helppages')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'forumshelp'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=forumshelp">{#Forums_Help#}</a></li>
        {/if}
        {if perm('comment_emoticons')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'emoticons'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=emoticons">{#ForumsEmoticons#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'posticons'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=posticons">{#Forums_TIcons_title#}</a></li>
        {/if}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=settings">{#SettingsModule#}</a></li>
    </ul>
  </div>
{else}
  {if perm('comment_emoticons')}
    <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/forum.png" alt="" /> {#ForumsEmoticons#} - {#Forums_TIcons_title#}</a>
    <div class="submenu">
      <ul>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'emoticons'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=emoticons">{#ForumsEmoticons#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'posticons'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=forums&amp;sub=posticons">{#Forums_TIcons_title#}</a></li>
      </ul>
    </div>
  {/if}
{/if}
