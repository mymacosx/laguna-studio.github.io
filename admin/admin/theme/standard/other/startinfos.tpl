{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('infos_navi', 'infos_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="infos_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/about.png" alt="" /> {#Info#}
</div>
<div id="infos_open" class="sysinfos">
  {#SysInfosInf#}
</div>
{/if}
