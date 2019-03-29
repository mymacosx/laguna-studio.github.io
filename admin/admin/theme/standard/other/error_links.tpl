{if $downloads || $cheats || $links}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('links_navi', 'links_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="links_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/warning.gif" alt="" /> {#Links_broken#}
</div>
<div id="links_open" class="sysinfos">
  <div class="maintable">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      {foreach from=$downloads item=down}
        <tr class="{cycle values='second,first'}">
          <td width="10%"><a href="index.php?do=downloads&sub=overview">{#Downloads#}</a></td>
          <td width="9%" class="stip" title="{$down->DDatum|date_format: "%d/%m/%Y - %H:%M"}">{$down->DDatum|date_format: "%d/%m/%y"}</td>
          <td width="40%" class="stip" title="{$lang.Global_Author|sanitize}">{$down->DName} (<a href="mailto: {$down->DEmail}">{$down->DEmail}</a>)</td>
          <td width="40%" class="stip" title="{$lang.Global_Name|sanitize}">{$down->Name|sanitize}</td>
          <td width="1%" nowrap="nowrap"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=downloads&amp;sub=edit&amp;id={$down->Id}&amp;noframes=1&amp;langcode=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
        </tr>
      {/foreach}
      {foreach from=$cheats item=che}
        <tr class="{cycle values='second,first'}">
          <td width="10%"><a href="index.php?do=cheats&sub=show">{#Gaming_cheats#}</a></td>
          <td width="9%" class="stip" title="{$che->DDatum|date_format: "%d/%m/%Y - %H:%M"}">{$che->DDatum|date_format: "%d/%m/%y"}</td>
          <td width="40%" class="stip" title="{$lang.Global_Author|sanitize}">{$che->DName} (<a href="mailto: {$che->DEmail}">{$che->DEmail}</a>)</td>
          <td width="40%" class="stip" title="{$lang.Global_Name|sanitize}">{$che->Name|sanitize}</td>
          <td width="1%" nowrap="nowrap"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=cheats&amp;sub=edit&amp;id={$che->Id}&amp;noframes=1&amp;langcode=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
        </tr>
      {/foreach}
      {foreach from=$links item=lin}
        <tr class="{cycle values='second,first'}">
          <td width="10%"><a href="index.php?do=links&sub=overview">{#Links#}</a></td>
          <td width="9%" class="stip" title="{$lin->DDatum|date_format: "%d/%m/%Y - %H:%M"}">{$lin->DDatum|date_format: "%d/%m/%y"}</td>
          <td width="40%" class="stip" title="{$lang.Global_Author|sanitize}">{$lin->DName} (<a href="mailto: {$lin->DEmail}">{$lin->DEmail}</a>)</td>
          <td width="40%" class="stip" title="{$lang.Global_Name|sanitize}">{$lin->Name|sanitize}</td>
          <td width="1%" nowrap="nowrap"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=links&amp;sub=edit&amp;id={$lin->Id}&amp;noframes=1&amp;langcode=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
        </tr>
      {/foreach}
    </table>
  </div>
</div>
{/if}
