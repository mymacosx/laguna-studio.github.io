{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('cache_navi', 'cache_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="cache_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/lifebelt.png" alt="" /> {#Start_SOpts#}
</div>
<div id="cache_open" class="sysinfos" style="font-weight: normal; line-height: 20px">
  <img class="absmiddle" src="{$imgpath}/garbage_full.png" alt="" />&nbsp;<a class="stip" title="{$lang.Sys_clearcacheInf|sanitize}" id="cc" href="javascript: void(0);">{#Sys_clearcache#}</a><span style="margin-left: 5px; font-style: italic" id="ccc"></span><br />
  <img class="absmiddle" src="{$imgpath}/garbage_full.png" alt="" />&nbsp;<a class="stip" title="{$lang.Sys_clearTplcacheInf|sanitize}" id="ctc" href="javascript: void(0);">{#Sys_clearTplcache#}</a><span style="margin-left: 5px; font-style: italic" id="ctcc"></span><br />
</div>
{/if}
