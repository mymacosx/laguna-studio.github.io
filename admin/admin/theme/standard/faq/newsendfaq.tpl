<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('faq_navi', 'faq_open', 30, '{$basepath}');
});
//-->
</script>

{if $newfaq && perm('faq')}
  <div class="header">
    <div id="faq_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
    <img class="absmiddle" src="{$imgpath}/faq.png" alt="" /> {#NewSendFaq#} - <a href="index.php?do=faq&amp;sub=sendfaq">{#Global_ShowAll#}</a>
  </div>
  <div id="faq_open" class="sysinfos">
    <div class="maintable">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        {foreach from=$newfaq item=down}
          <tr class="{cycle values='second,first'}">
            <td width="84%" class="stip" title="{$lang.GlobalQuest|sanitize}">{$down->Name|sanitize}</td>
            <td width="15%" class="stip" title="{$down->Datum|date_format: "%d/%m/%Y - %H:%M"}">{$down->Datum|date_format: "%d/%m/%y"}</td>
            <td width="1%" nowrap="nowrap"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=faq&amp;sub=editsendfaq&amp;categ={$down->Kategorie}&amp;id={$down->Id}&amp;noframes=1&amp;langcode=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
          </tr>
        {/foreach}
      </table>
    </div>
  </div>
{/if}
