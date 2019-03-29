<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('forum_navi', 'forum_open', 30, '{$basepath}');
});
//-->
</script>

{if perm('forum') && admin_active('forums') && $last_post_array}
  <div class="header">
    <div id="forum_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
    <img class="absmiddle" src="{$imgpath}/megaphone.png" alt="" /> {#Start_NPosts#}
  </div>
  <div id="forum_open" class="sysinfos">
    {if $last_post_array}
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        {foreach from=$last_post_array item=x}
          <tr class="{cycle values='second,first'}">
            <td> {$x->Datum|date_format: $lang.DateFormat}&nbsp;&nbsp;<a href="{$x->LpLink}" target="_blank"><strong>{$x->LpTitle|truncate: 40|sanitize}</strong></a></td>
          </tr>
        {/foreach}
      </table>
    {/if}
  </div>
{/if}
