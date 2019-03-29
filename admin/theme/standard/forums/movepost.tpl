{include file="$incpath/forums/user_panel_forums.tpl"}
<br />
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header_bolder"><strong>{#Forums_SelectPostToMove#}</strong></td>
  </tr>
  <tr>
    <td class="forum_info_main">
      {if !empty($post_item->title)}
        <strong> {$post_item->title|sslash} </strong>
        <br />
      {/if}
      {$post_item->message}
      <br />
      <br />
      {#Forums_Label_moveitem#}
    </td>
  </tr>
  <tr>
    <td class="forum_info_main">
      <form action="index.php?p=forums&amp;action=movepost&amp;subaction=postmove" method="post">
        {strip}
          <select name="post_m">
            {foreach from=$categories_dropdown item=category name=cdd}
              <optgroup label="{$category->title|sslash}">
                {foreach from=$category->forums item=forum_dropdown name=fdd}
                  {if $forum_dropdown->category_id != 0}
                  <optgroup label="&nbsp;&nbsp;&nbsp;{$forum_dropdown->visible_title|sslash}">
                    {foreach from=$post_destinations item=topic}
                      {if $forum_dropdown->id == $topic->forum_id}
                        <option value="{$topic->id}" {if $post_item->topic_id == $topic->id} selected="selected" {/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$topic->title|sslash}</option>
                      {/if}
                    {/foreach}
                  </optgroup>
                {/if}
              {/foreach}
              </optgroup>
            {/foreach}
          </select>
        {/strip}
        &nbsp;
        <input type="submit" class="button" value="{#ButtonSend#}" />&nbsp;
        <input type="button" class="button" value="{#Forums_JumpBack#}" onclick="history.go(-1);" />
        <input type="hidden" name="pid" value="{$smarty.request.pid|default:''}" />
        <input type="hidden" name="fid" value="{$smarty.request.fid|default:''}" />
      </form>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/forums_footer.tpl"}
