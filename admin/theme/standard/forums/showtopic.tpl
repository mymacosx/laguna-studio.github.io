{include file="$incpath/forums/user_panel_forums.tpl"}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.forum_images a').colorbox({
        photo: true,
        transition: "elastic",
        maxHeight: "98%",
        maxWidth: "98%",
        slideshow: true,
        slideshowAuto: false,
        slideshowSpeed: 2500,
        current: "{#GlobalImage#} {ldelim}current{rdelim} {#PageNavi_From#} {ldelim}total{rdelim}",
        slideshowStart: "{#GlobalStart#}",
        slideshowStop: "{#GlobalStop#}",
        previous: "{#GlobalBack#}",
        next: "{#GlobalNext#}",
        close: "{#GlobalGlose#}"
    });
    $('.user_pop').colorbox({ height: "600px", width: "550px", iframe: true });
});
//-->
</script>

<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      {include file="$incpath/forums/tree.tpl"}
    </td>
    <td align="right">
      <a class="forumlinks" href="index.php?p=forum&amp;action=related&amp;t_id={$topic->id}">{#Forums_Related#}</a>
      {if $loggedin}
          &nbsp;|&nbsp;
          <a class="forumlinks" href="index.php?p=forum&amp;action=friendsend&amp;t_id={$topic->id}">{#FriendSend#}</a>
          &nbsp;|&nbsp;
          {if $canabo == 1}
              <a class="forumlinks" href="index.php?p=forum&amp;action=addsubscription&amp;t_id={$topic->id}">{#Forums_Link_subscription#}</a>
          {else}
              <a class="forumlinks" href="index.php?p=forum&amp;action=unsubscription&amp;t_id={$topic->id}">{#Forums_ThreadAboCancel#}</a>
          {/if}
      {/if}
    </td>
  </tr>
</table>
<table cellpadding="0" cellspacing="1" style="width: 100%;">
  <tr>
    <td colspan="2">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>
            <h2>{$topic->title|sanitize}</h2>
            {if $topic->status == 1}
                <img src="{$imgpath_forums}topic_closed.png" alt="{#Forums_TopicClosed#}" class="absmiddle" />
            {else}
                {if ($permissions.6 == 1) || ($permissions.7 == 1)}
                    <div class="forum_buttons_big"><a href="index.php?p=newpost&amp;toid={$smarty.request.toid}"><img src="{$imgpath_forums}reply.png" alt="{#GlobalReply#}" />{#GlobalReply#}</a></div>
                      {/if}
                    {/if}
          </td>
          <td align="right">
            {if !empty($pages)}
                {$pages}
            {/if}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />

{foreach from=$postings item=post name=postings}
    <div class="forum_posts">
      <a name="pid_{$post->id}"></a><a name="{$post->id}"></a>
      <div class="forum_post_header">
        <div class="forum_topicheader" style="float: right">
          <a class="forum_topicheader" title="{#LinkPostcount#}" href="index.php?p=forums&amp;action=postcount&amp;pid={$post->id}">{#LinkPostcount#}</a>&nbsp;&nbsp;&nbsp;
          <a class="forum_topicheader" href="index.php?p=showtopic&amp;print_post={$post->id}&amp;toid={$smarty.request.toid}&amp;t={$post->postlink|translit}">{#LinkPostPage#}</a>&nbsp;&nbsp;
          <a class="forum_topicheader user_pop" title="{#Forums_Complaint#}" href="index.php?p=forums&amp;action=complaint&amp;fid={$topic->forum_id}&amp;pid={$post->id}"><img class="absmiddle" src="{$imgpath_forums}complaint.png" alt="{#Forums_Complaint#}" /></a>&nbsp;
          <a class="forum_topicheader" title="{#GlobalTop#}" href="javascript: scroll(0,0);"><img class="absmiddle" src="{$imgpath_forums}arrow.png" alt="{#GlobalTop#}" /></a>
        </div>
        {if get_active('calendar')}
            <a title="{#CalendarNewEvents#}" href="index.php?p=calendar&amp;action=events&amp;show=public&amp;month={$post->array_datum.monat}&amp;year={$post->array_datum.jahr}&amp;day={$post->array_datum.tag}&amp;area={$area}"><img src="{$imgpath_forums}event.png" alt="{#CalendarNewEvents#}" class="absmiddle" /></a>
            {/if}
            {if $post->datum|date_format: $lang.DateFormatSimple == $smarty.now|date_format: $lang.DateFormatSimple}
            <strong>{#today#},&nbsp;{$post->datum|date_format: '%H:%M'}</strong>
        {else}
            <strong>{$post->datum|date_format: $lang.DateFormatExtended}</strong>
        {/if}
      </div>
      <table width="100%" cellpadding="4" cellspacing="0" class="forum_tableborder">
        <tr class="{cycle name=switch values='forum_post_first,forum_post_second'}">
          <td valign="top" class="forum_post_first">
            {if isset($post->poster->user_regdate) && $post->poster->user_regdate > 0 && $post->poster->ugroup != 2}
                <h2><a id="topic_user_{$post->anchorId}" onclick="toggleContent('topic_user_{$post->anchorId}', 'topic_data_{$post->anchorId}');" href="javascript: void(0);">{$post->poster->uname}</a></h2>
                <div class="status" style="display: none" id="topic_data_{$post->anchorId}">{$post->poster->UserPop}</div>
            {/if}
            <div class="user_avatar">{$post->poster->avatar|default:'<img src="uploads/avatars/no_avatar.png" alt="" />'}</div>
            {if isset($post->poster->user_regdate) && $post->poster->user_regdate > 0 && $post->poster->ugroup != 2}
                {if isset($post->poster->uid) && $post->poster->uid == $topic->uid && !$smarty.foreach.postings.first}
                    <div class="user_bar"><strong>{#Forums_Label_topic_starter#}</strong></div>
                      {/if}
                <div class="user_bar">{#Forums_Group#}: <strong>{$post->poster->groupname_single}</strong></div>
                {if !empty($post->poster->rank) && $post->poster->Team != 1}
                    <div class="user_bar">{#Profile_Rank#}: <strong>{$post->poster->rank|escape:'html'|sslash}</strong></div>
                {/if}
                <div class="user_bar">{#Forums_Field_posts#}: <strong><a href="index.php?p=forum&amp;action=print&amp;what=posting&amp;id={$post->poster->uid}">{$post->poster->user_posts}</a></strong></div>
                <div class="user_bar">{#Forums_Field_membersince#}: <strong>{$post->poster->user_regdate|date_format: $lang.DateFormatSimple}</strong></div>
                {if !empty($post->poster->gorod) && $post->poster->gorod_all == 1}
                    <div class="user_bar">{#Town#}: <strong>{$post->poster->gorod|escape: 'html'}</strong></div>
                {/if}
            {/if}
          </td>
          <td valign="top" class="forum_post_second">
            {if !empty($post->title)}
                <strong> {$post->title|sanitize} </strong>
                <br />
            {/if}
            <!--START_NO_REWRITE-->
            {$post->message|specialchars}
            <!--END_NO_REWRITE-->
            {if !empty($post->files) || !empty($post->images)}
                <br />
                <fieldset>
                  <legend> {#Forums_Attachments#} </legend>
                  <table border="0" cellpadding="1" cellspacing="0">
                    <tr>
                      <td colspan="3">
                        <div class="forum_images">
                          {foreach from=$post->images item=image}
                              {if isset($image.access) && $image.access == 1}
                                  <a rel="pop_im_{$post->id}" class="stip" title="{$image.popup|tooltip}" href="index.php?p=forum&amp;action=getimage&amp;id={$image.id}&amp;f_id={$topic->forum_id}&amp;t_id={$topic->id}-{$image.orig_name}">
                                    <img src="{$image.link}" hspace="5" vspace="2" border="0" align="top" alt="" />
                                  </a>
                              {else}
                                  <img class="stip" title="{$image.popup|tooltip}" src="{$image.link}" hspace="5" vspace="2" border="0" align="top" alt="" />
                              {/if}
                          {/foreach}
                        </div>
                      </td>
                    </tr>
                    {foreach from=$post->files item=file}
                        <tr>
                          <td><img hspace="2" vspace="4" src="{$imgpath_forums}attachment.gif" alt="" class="absmiddle" /><a href="{$file.link}"> {$file.orig_name} </a></td>
                          <td nowrap="nowrap"><small>&nbsp;&nbsp;({$file.hits} {#Forums_HitsDownloads#} | {$file.filesize})</small></td>
                          <td>&nbsp;&nbsp;{$file.musik}</td>
                        </tr>
                    {/foreach}
                  </table>
                </fieldset>
            {/if}
            {if $post->use_sig == 1 && $post->uid != 0 && !empty($post->poster->user_sig)}
                <br />
                <br />
                <br />
                <div class="user_sig_bar"></div>
                <div class="user_sig">{$post->poster->user_sig|specialchars}</div>
            {/if}
            {if $post->thanks}
                <fieldset>
                  <legend> {#ThanksPost#} </legend>
                  {foreach from=$post->user_thanks item=thanks name="thanks_post"}
                      <a href="index.php?p=user&amp;id={$thanks->Id}&amp;area={$area}">{$thanks->Benutzername|sanitize}</a>{if !$smarty.foreach.thanks_post.last}, {/if}
                  {/foreach}
                </fieldset>
            {/if}
            {if !$loggedin && ($smarty.foreach.postings.first || isset($smarty.request.print_post))}
                <br />
                <br />
                <div align="center">{banner}</div>
            {/if}
          </td>
        </tr>
        <tr class="{cycle name=switch2 values='forum_post_first,forum_post_second'}">
          <td class="forum_post_first">
            {if isset($post->poster->user_regdate) && $post->poster->user_regdate > 0 && $post->poster->ugroup != 2}
                {onlinestatus uname=$post->poster->uname}
            {/if}
          </td>
          <td class="forum_post_second" align="right">
            {if ($post->opened == 2) && ($ismod == 1)}
                {if !$smarty.foreach.postings.first}
                    {assign var="ispost" value=1}
                {/if}
                <div class="forum_buttons_small"><a title="{#Forums_f_unlock#}" href="index.php?open=1&amp;p=showtopic&amp;toid={$smarty.request.toid}&amp;fid={$smarty.request.fid}&amp;id={$post->id}{if $ispost}&amp;ispost={$ispost}{/if}">{#Forums_f_unlock#}</a></div>
                {/if}
                {if ($permissions.6 == 1) || ($permissions.7 == 1)}
                <div class="forum_buttons_small"><a title="{#Forums_Reply#}" href="index.php?p=newpost&amp;toid={$smarty.request.toid}&amp;pp=15&amp;num_pages={$next_site}"><img class="absmiddle" src="{$imgpath_forums}reply_small.png" alt="{#Forums_Reply#}" />{#Forums_Reply#}</a></div>
                  {/if}
                  {if $loggedin}
                <div class="forum_buttons_small"><a title="{#Forums_PostQuote#}" href="index.php?p=newpost&amp;action=quote&amp;pid={$post->id}&amp;toid={$smarty.request.toid}"><img class="absmiddle" src="{$imgpath_forums}quote_small.png" alt="{#Forums_PostQuote#}" />{#Forums_PostQuote#}</a></div>
                  {/if}
                  {if ($permissions.10 == 1 && (isset($post->poster->uid) && $post->poster->uid == $smarty.session.benutzer_id)) || $permissions.16 == 1}
                <div class="forum_buttons_small"><a title="{#GlobalEdit#}" href="index.php?p=newpost&amp;action=edit&amp;pid={$post->id}&amp;toid={$smarty.request.toid}"><img src="{$imgpath_forums}edit_small.png" alt="{#GlobalEdit#}" />{#GlobalEdit#}</a></div>
                  {/if}
                  {if ($permissions.11 == 1 && (isset($post->poster->uid) && $post->poster->uid == $smarty.session.benutzer_id)) || $permissions.15 == 1}
                <div class="forum_buttons_small"><a title="{#Delete#}" onclick="return confirm('{#Forums_DelConfirm#}');" href="index.php?p=forums&amp;action=delpost&amp;pid={$post->id}&amp;toid={$smarty.request.toid}"><img src="{$imgpath_forums}delete_small.png" alt="{#Delete#}" />{#Delete#}</a></div>
                  {/if}
                  {if ($permissions.20 == 1) || ($permissions.12 == 1)}
                <div class="forum_buttons_small"><a title="{#Mess_Move#}" href="index.php?p=forums&amp;action=movepost&amp;pid={$post->id}&amp;fid={$topic->forum_id}"><img src="{$imgpath_forums}move_post.png" alt="{#Mess_Move#}" />{#Mess_Move#}</a></div>
                  {/if}
                  {if $loggedin}
                      {if isset($post->user_del_thanks.del) && $post->user_del_thanks.del == 1}
                  <div class="forum_buttons_small"><a title="{#DelThanks#}" href="index.php?p=forums&amp;action=delthanks&amp;pid={$post->id}"><img class="absmiddle" src="{$imgpath_forums}thanks.png" alt="{#Forums_PostQuote#}" />{#DelThanks#}</a></div>
                    {else}
                  <div class="forum_buttons_small"><a title="{#Thanks#}" href="index.php?p=forums&amp;action=addthanks&amp;pid={$post->id}"><img class="absmiddle" src="{$imgpath_forums}thanks.png" alt="{#Forums_PostQuote#}" />{#Thanks#}</a></div>
                    {/if}
                  {/if}
          </td>
        </tr>
      </table>
    </div>
{/foreach}

<table width="100%" cellpadding="0" cellspacing="0" class="forum_tableborder">
  <tr>
    <td colspan="2" class="forum_info_main">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>
            {if $topic->status == 1}
                <img src="{$imgpath_forums}topic_closed.png" alt="{#Forums_TopicClosed#}" class="absmiddle" />
            {else}
                {if ($permissions.6 == 1) || ($permissions.7 == 1)}
                    <div class="forum_buttons_big"><a href="index.php?p=newpost&amp;toid={$smarty.request.toid}"><img src="{$imgpath_forums}reply.png" alt="{#GlobalReply#}" />{#GlobalReply#}</a></div>
                    <div class="clear"></div>
                {/if}
            {/if}
            <br />
            <br />
          </td>
          <td align="right" valign="top" style="padding: 4px; padding-right: 0px">
            {if !empty($pages)}
                {$pages}
            {/if}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td>
      <a class="forumlinks" href="index.php?p=forum&amp;action=related&amp;t_id={$topic->id}">{#Forums_Related#}</a>
      {if $loggedin}
          &nbsp;|&nbsp;
          <a class="forumlinks" href="index.php?p=forum&amp;action=friendsend&amp;t_id={$topic->id}">{#FriendSend#}</a>
          &nbsp;|&nbsp;
          {if $canabo == 1}
              <a class="forumlinks" href="index.php?p=forum&amp;action=addsubscription&amp;t_id={$topic->id}">{#Forums_Link_subscription#}</a>
          {else}
              <a class="forumlinks" href="index.php?p=forum&amp;action=unsubscription&amp;t_id={$topic->id}">{#Forums_ThreadAboCancel#}</a>
          {/if}
      {/if}
      {if !empty($topic->prev_topic->id)}
          &nbsp;|&nbsp;&nbsp; <a class="forumlinks" href="index.php?p=showtopic&amp;toid={$topic->prev_topic->id}&amp;fid={$smarty.request.fid|default:$topic->forum_id}&amp;t={$topic->prev_topic->title|translit}">{#Forums_ShowLastTopic#}</a>
      {/if}
      {if !empty($topic->next_topic->id)}
          &nbsp;|&nbsp;&nbsp; <a class="forumlinks" href="index.php?p=showtopic&amp;toid={$topic->next_topic->id}&amp;fid={$smarty.request.fid|default:$topic->forum_id}&amp;t={$topic->next_topic->title|translit}">{#Forums_ShowNextTopic#}</a>
      {/if}
      <br />
      <br />
    </td>
  </tr>
  <tr>
    <td align="right">{#Forums_JumpTo#}: &nbsp;
      {include file="$incpath/forums/selector.tpl"}</td>
  </tr>
  {if $permissions.17 == 1 || $permissions.18 == 1 || $permissions.14 == 1 || $permissions.21 == 1 || $permissions.12 == 1 || $permissions.20 == 1 || $permissions.19 == 1}
      <tr>
        <td align="right" nowrap="nowrap">
          {#Forums_AdminOptionsSelections#}&nbsp;
          <select id="move_sel" name="select" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
            {if $topic->status eq 1}
                {if ($permissions.13 == 1) || ($permissions.18 == 1)}
                    <option value="location.href='index.php?p=forums&amp;action=opentopic&amp;fid={$topic->forum_id}&amp;toid={$smarty.request.toid}';"> {#Forums_OpenTopic#} </option>
                {/if}
            {else}
                {if ($permissions.13 == 1) || ($permissions.18 == 1)}
                    <option value="location.href='index.php?p=forums&amp;action=closetopic&amp;fid={$topic->forum_id}&amp;toid={$smarty.request.toid}';"> {#Forums_CloseTopic#} </option>
                {/if}
            {/if}
            {if ($permissions.14 == 1) || ($permissions.21 == 1)}
                <option value="if(confirm('{#Forums_ConfirmDelTopic#}')) location.href='index.php?p=forums&amp;action=deltopic&amp;fid={$topic->forum_id}&amp;toid={$smarty.request.toid}';"> {#Forums_DeleteTopicNow#} </option>
            {/if}
            {if ($permissions.20 == 1) || ($permissions.12 == 1)}
                <option value="location.href='index.php?p=forums&amp;action=move&amp;item=t&amp;id={$smarty.request.toid}&amp;fid={$topic->forum_id}';"> {#Forums_MoveTopic#} </option>
            {/if}
            {if $permissions.19 == 1}
                <option value="location.href='index.php?p=forum&amp;action=change_type&amp;id={$smarty.request.toid}&amp;fid={$topic->forum_id}';"> {#Forums_ChangeTypePost#} </option>
            {/if}
          </select>&nbsp;
          <input onclick="eval(document.getElementById('move_sel').value);" type="button" class="button" value="{#GotoButton#}" />
        </td>
      </tr>
  {/if}
  {if ($permissions.9 == 1) && ($display_rating == 1)}
      <tr>
        <td align="center">
          {include file="$incpath/forums/rating.tpl"}
        </td>
      </tr>
  {/if}
</table>
{include file="$incpath/forums/forums_footer.tpl"}
