<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('comment_navi', 'comment_open', 30, '{$basepath}');
});
//-->
</script>

{if perm('comments') && admin_active('comments') && $comments}
  <div class="header">
    <div id="comment_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
    <img class="absmiddle" src="{$imgpath}/message.png" alt="" /> {#StartNewComments#} - <a href="index.php?do=comments&amp;where=all">{#Global_ShowAll#}</a>
  </div>
  <div id="comment_open" class="sysinfos">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      {foreach from=$comments item=c}
        <tr class="{cycle values='second,first'}">
          <td class="row_spacer stip" title="{$c->Eintrag|truncate: 250|sanitize}">{$c->Eintrag|truncate: 70|sanitize}</td>
          <td width="1%" class="row_spacer"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=comments&amp;where={$c->Bereich}&amp;object={$c->Objekt_Id}&amp;id={$c->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
        </tr>
      {/foreach}
    </table>
  </div>
{/if}
