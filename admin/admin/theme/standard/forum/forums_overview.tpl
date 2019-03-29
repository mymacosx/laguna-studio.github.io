<div class="header">{#Forums_title#}</div>
<div class="subheaders">
  <a title="{#GlobalAddCateg#}" class="colorbox" href="?do=forums&amp;sub=newcategory&amp;id={$smarty.get.id|default:0}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="" /> {#GlobalAddCateg#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !empty($errors)}
  <ul>
    {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
  </ul>
{/if}
{if $categories}
  <form action="" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td colspan="3" class="header">{#Forums_fheader#}</td>
        <th class="header" style="font-weight: bold">{#Global_Position#}</th>
        <td nowrap="nowrap" class="header" style="font-weight: bold">{#Global_Actions#}</td>
      </tr>
      {foreach from=$categories item=category}
        <tr>
          <td colspan="3" class="headers">
            {$category->title|sanitize}
            <br />
            <span style="font-weight: normal"><small>{$category->comment|sanitize}</small></span>
          </td>
          <th class="headers" style="font-weight: normal"><input type="hidden" name="c_id[]" value="{$category->id}" />
            <input class="input" type="text" name="c_position[{$category->id}]" size="2" maxlength="2" value="{$category->position}" /></th>
          <td width="10%" nowrap="nowrap" class="headers" style="font-weight: normal">
            {if perm('forum_categoryedit')}
              <a class="colorbox stip" title="{$lang.Global_CategEdit|sanitize}" href="index.php?do=forums&amp;sub=editcategory&amp;id={$category->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
              {/if}
              {if perm('forum_add')}
              <a class="colorbox stip" title="{$lang.Forums_new|sanitize}" href="index.php?do=forums&amp;sub=addforum&amp;id={$category->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="" /></a>
              {/if}
              {if perm('forum_categoryedit')}
              <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$category->title|jsspecialchars}');" href="index.php?do=forums&amp;sub=deletecategory&amp;id={$category->id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
              {/if}
          </td>
        </tr>
        {if count($category->forums)}
          {foreach from=$category->forums item=forum}
            <tr>
              <td class="forum_rows">
                <strong>{$forum->visible_title|sanitize}</strong>
                <div style="padding-left: 8px"><small>{$forum->comment|sanitize}</small></div>
              </td>
              <td width="10%" nowrap="nowrap" class="forum_rows" style="font-weight: normal">
                <div align="center">
                  {if $forum->status == 1}
                    <strong>{$lang.Forums_sclosed}</strong>
                  {else}
                    <strong>{$lang.Forums_sopened}</strong>
                  {/if}
                </div>
              </td>
              <td width="10%" class="forum_rows">
                <div align="center">
                  {if $forum->active == 1}
                    <strong>{$lang.Global_Active}</strong>
                  {else}
                    <strong>{$lang.Global_Inactive}</strong>
                  {/if}
                </div>
              </td>
              <td width="10%" align="center" nowrap="nowrap" class="forum_rows">
                <input type="hidden" name="f_id[]" value="{$forum->id}" />
                <input class="input" type="text" name="f_position[{$forum->id}]" value="{$forum->position}" size="2" maxlength="2" />
              </td>
              <td nowrap="nowrap" class="forum_rows">
                {if perm('forum_edit')}
                  <a class="colorbox stip" title="{$lang.Forums_fEdit|sanitize}"  href="index.php?do=forums&amp;sub=editforum&amp;id={$forum->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
                    {if $forum->status == 0}
                    <a class="stip" title="{$lang.Forums_close|sanitize}"  href="index.php?do=forums&amp;sub=closeforum&amp;id={$forum->id}{if isset($smarty.request.id)}&amp;fid={$smarty.request.id}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>
                    {else}
                    <a class="stip" title="{$lang.Forums_open|sanitize}" href="index.php?do=forums&amp;sub=openforum&amp;id={$forum->id}{if isset($smarty.request.id)}&amp;fid={$smarty.request.id}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>
                    {/if}
                  {/if}
                  {if perm('forum_add')}
                  <a class="colorbox stip" title="{$lang.GlobalAddCateg|sanitize}" href="?do=forums&amp;sub=newcategory&amp;id={$forum->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="" /></a>
                  {/if}
                  {if perm('forum_mods')}
                  <a class="colorbox stip" title="{$lang.Forums_mods|sanitize}" href="?do=forums&amp;sub=editmods&amp;id={$forum->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/mods.png" alt="" border="" /></a>
                  {/if}
                  {if perm('forum_delete')}
                  <a class="stip" title="{$lang.Forums_del|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$forum->visible_title|jsspecialchars}');" href="index.php?do=forums&amp;sub=deleteforum&amp;id={$forum->id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
                  {/if}
              </td>
            </tr>
            {foreach from=$forum->categories item=sub_category}
              <tr>
                <td class="forum_rows" colspan="6"> - - <a class="link" href="index.php?do=forums&amp;sub=overview&amp;id={$forum->id}">{$sub_category->title}</a></td>
              </tr>
            {/foreach}
          {/foreach}
        {/if}
      {/foreach}
    </table>
    <input accesskey="s" class="button" type="submit" value="{#Global_SavePos#}" />
    <input name="subaction" type="hidden" id="subaction" value="{if isset($smarty.request.action)}{$smarty.request.action}{/if}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input name="id" type="hidden" id="id" value="{if isset($smarty.request.id)}{$smarty.request.id}{/if}" />
  </form>
{/if}
