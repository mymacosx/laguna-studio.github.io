{include file="$incpath/forums/user_panel_forums.tpl"}
<p class="forum_navi"> {$navigation} </p>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header_bolder">{#Forums_SelectForumsToMove#}</td>
  </tr>
  <tr>
    <td class="forum_info_main"><strong>{$item->title|sslash}</strong>&nbsp;{#Forums_Label_moveitem#}</td>
  </tr>
  <tr>
    <td class="forum_info_main">
      <form action="index.php?p=forums&amp;action=move&amp;subaction=commit" method="post">
        {strip}
          <select class="input" name="dest">
            {if $smarty.request.item eq "c"}
              <option value="0"></option>
            {/if}
            {foreach from=$categories_dropdown item=category}
              <optgroup label="{$category->title}">
                {foreach from=$category->forums item=forum_dropdown}
                  {if $forum_dropdown->category_id == 0}
                    <option style="color: #000; font-weight: bold; font-style: italic;" value="{$forum_dropdown->id}" disabled="disabled">{$forum_dropdown->visible_title} </option>
                  {else}
                    <option value="{$forum_dropdown->id}" {if isset($smarty.request.fid) && $smarty.request.fid == $forum_dropdown->id} selected="selected" {/if}>{$forum_dropdown->visible_title} </option>
                  {/if}
                {/foreach}
              </optgroup>
            {/foreach}
          </select>&nbsp;
        {/strip}
        <input type="submit" class="button" value="{#ButtonSend#}" />&nbsp;
        <input type="button" class="button" value="{#Forums_JumpBack#}" onclick="history.go(-1);" />
        <input type="hidden" name="item" value="{$smarty.request.item}" />
        <input type="hidden" name="id" value="{$smarty.request.id}" />
        <input type="hidden" name="fid" value="{$smarty.request.fid}" />
      </form>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/forums_footer.tpl"}
