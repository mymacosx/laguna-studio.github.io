<div class="round">
  <div class="forum_infobox">
    {if get_active('forums')}
      <a title="{#Forums_Title#}" class="stip" href="index.php?p=showforums"><img src="{$imgpath_forums}forums_small.png" alt="" class="absmiddle" /> {#Forums_Title#}</a>&nbsp;&nbsp;
      {/if}
      {if permission('showuserpage')}
      <a title="{#Users#}" class="stip" href="index.php?p=members&amp;area={$area}"><img src="{$imgpath_forums}users_small.png" alt="" class="absmiddle" /> {#Users#}</a>&nbsp;&nbsp;
      {/if}
      {if get_active('calendar')}
      <a title="{#Calendar#}" class="stip" href="index.php?p=calendar&amp;month={$smarty.now|date_format: 'm'}&amp;year={$smarty.now|date_format: 'Y'}&amp;area={$area}&amp;show=public"><img src="{$imgpath_forums}event.png" alt="" class="absmiddle" /> {#Calendar#}</a>&nbsp;&nbsp;
      {/if}
      {if get_active('forums')}
      <a title="{#Help_General#}" class="stip" href="index.php?p=forum&amp;action=help"><img src="{$imgpath_forums}help_small.png" alt="" class="absmiddle" /> {#Help_General#}</a>&nbsp;&nbsp;
      <a id="search_link" title="{#Search#}" class="stip" onclick="toggleContent('search_link', 'search_content');" href="javascript: void(0);"><img src="{$imgpath_forums}search_small.png" alt="" class="absmiddle" /> {#Search#}</a>&nbsp;&nbsp;
      <div id="search_content" class="status" style="display:none">
        {include file="$incpath/forums/search_small.tpl"}
      </div>
      <a title="{#Forums_NewPostings#}" class="stip" href="index.php?p=forum&amp;action=print&amp;what=lastposts"><img src="{$imgpath_forums}newest_small.png" alt="" class="absmiddle" /> {#Forums_NewPostings#}</a>&nbsp;&nbsp;
      <a title="{#Forums_ShowLastActive#}" class="stip" href="index.php?p=forum&amp;action=show&amp;unit=h&amp;period=24"><img src="{$imgpath_forums}threads_last24.png" alt="" class="absmiddle" /> {#Forums_ShowLastActiveShort#}</a>&nbsp;&nbsp;
      <a title="{#Forums_ThreadsEmpty#}" class="stip" href="index.php?p=forum&amp;action=print&amp;what=topicsempty"><img src="{$imgpath_forums}threads_last24.png" alt="" class="absmiddle" /> {#Forums_ThreadsEmpty#}</a>&nbsp;&nbsp;
        {if $loggedin}
        <a title="{#Forums_ShowAllAbos#}" class="stip" href="index.php?p=forum&amp;action=print&amp;what=subscription"><img src="{$imgpath_forums}small_abos.png" alt="" class="absmiddle" /> {#Forums_ShowAllAbos#}</a>&nbsp;&nbsp;
        <a title="{#Forums_ShowOwnPosts#}" class="stip" href="index.php?p=forum&amp;action=print&amp;what=posting&amp;id={$smarty.session.benutzer_id}"><img src="{$imgpath_forums}small_ownposts.png" alt="" class="absmiddle" /> {#Forums_ShowOwnPosts#}</a>&nbsp;&nbsp;
        <a title="{#Forums_MarkForumsRead#}" class="stip" href="index.php?p=forum&amp;action=markread&amp;what=forum&amp;ReadAll=1"><img src="{$imgpath_forums}small_readall.png" alt="" class="absmiddle" /> {#Forums_MarkForumsRead#}</a>
        {/if}
      {/if}
  </div>
</div>
<br />
