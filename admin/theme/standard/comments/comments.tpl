{if $comments == 1}
{if $eintrag}
<script type="text/javascript">
<!-- //
function edit_entry(ID) {
    {foreach from=$eintrag item=e}
    document.getElementById('eintrag_edit_' + {$e.Id}).style.display = 'none';
    document.getElementById('eintrag_' + {$e.Id}).style.display = '';
    {/foreach}
    document.getElementById('eintrag_' + ID).style.display = 'none';
    document.getElementById('eintrag_edit_' + ID).style.display = '';
}
//-->
</script>

<br />
<a name="comments"></a>
<div class="box_innerhead"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /> {#Comments#} </div>
<div class="comments">
  {foreach from=$eintrag item=e}
    <a name="comment_{$e.Id}"></a>
    <div class="{cycle name='gb' values='comment_box,comment_box_second'}"{if $e.Aktiv != 1} style="border: 2px solid red; padding: 2px; padding: 1px"{/if}>
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top" style="width: 75px">{$e.Avatar}</td>
          <td valign="top">
            <div>
              <div class="comment_head">
                {#Date#}: {$e.Datum|date_format: $lang.DateFormatExtended}
                {#GlobalAutor#}:
                {if $e.Autor_Id}
                  <a href="index.php?p=user&amp;id={$e.Autor_Id}&amp;area={$area}">{$e.Autor|cleantext}</a>
                {else}
                  {$e.Autor|cleantext}
                {/if}
                {if $e.Autor_Web}
                  <a rel="nofollow" target="_blank" href="{$e.Autor_Web|sanitize}"><img class="absmiddle" src="{$imgpath}/comment/home_small.png" alt="" /></a>
                  {/if}
                  {if $e.Autor_Herkunft}
                    {#Town#}: {$e.Autor_Herkunft|cleantext}
                {/if}
              </div>
              <div class="comment_entry">
                <!--START_NO_REWRITE-->
                <div id="eintrag_{$e.Id}"> {$e.Eintrag} </div>
                <!--END_NO_REWRITE-->
                {if permission('edit_comments')}
                  <div id="eintrag_edit_{$e.Id}" style="display: none">
                    <form method="post" action="{page_link}#comment_{$e.Id}">
                      <table width="100%" cellpadding="0" cellspacing="1">
                        <tr>
                          <td width="160">{#GlobalName#}</td>
                          <td><input class="input" style="width: 200px" name="E_Autor" type="text" value="{$e.Autor|sanitize}" /></td>
                        </tr>
                        <tr>
                          <td width="160">{#Email#}</td>
                          <td><input class="input" style="width: 200px" name="E_Email" type="text" value="{$e.Autor_Email|sanitize}" /></td>
                        </tr>
                        <tr>
                          <td width="160">{#Web#}</td>
                          <td><input class="input" style="width: 200px" name="E_Webseite" type="text" value="{$e.Autor_Web|sanitize}" /></td>
                        </tr>
                        <tr>
                          <td width="160">{#Town#}</td>
                          <td><input class="input" style="width: 200px" name="E_Herkunft" type="text" value="{$e.Autor_Herkunft|sanitize}" /></td>
                        </tr>
                        <tr>
                          <td width="160">{#Comments#}</td>
                          <td><textarea name="E_Eintrag" cols="" rows="" class="input" id="GbComment" style="width: 99%; height: 150px">{$e.Eintrag_Raw}</textarea></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>
                            <input type="hidden" name="id" value="{$smarty.request.id}" />
                            <input type="hidden" name="comment_action" value="edit" />
                            <input type="hidden" name="comment_id" value="{$e.Id}" />
                            <input type="hidden" name="page" value="{$smarty.get.page|default:1}" />
                            <input type="submit" class="button" value="{#Save#}" />
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                  <div align="right">
                    {if $e.Aktiv != 1}
                      <a href="index.php?p=comments&amp;action=change&amp;page={$smarty.request.page|default:'1'}&amp;id={$e.Id}">{#Comment_SetActive#}</a>
                    {/if}
                    {if permission('delete_comments')}
                      <a onclick="return confirm('{#Comment_DeleteC#}');" href="index.php?p=comments&amp;action=delete&amp;page={$smarty.request.page|default:'1'}&amp;id={$e.Id}"><img class="absmiddle" src="{$imgpath}/comment/delete_small.png" alt="" border="" /></a>
                      {/if}
                    <a href="javascript: void(0);" onclick="edit_entry('{$e.Id}'); return false;"><img class="absmiddle" src="{$imgpath}/comment/edit_small.png" alt="" border="" /></a>
                  </div>
                {/if}
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  {/foreach}
</div>
{/if}
{if !empty($pages)}
  <br />
  <div align="right"> {$pages} </div>
{/if}
<br />
<a name="comment_new"></a>
{if  permission('comments')}
  {if !isset($noComment)}
    {include file="$incpath/comments/form.tpl"}
  {/if}
{else}
  {#Comment_NoPerm#}
{/if}
{/if}
