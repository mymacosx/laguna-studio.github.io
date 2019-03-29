{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('modul_navi', 'modul_open', 30, '{$basepath}');
    toggleCookie('widgets_navi', 'widgets_open', 30, '{$basepath}');
});
//-->
</script>

  <div class="header">
    <div id="modul_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
    <img class="absmiddle" src="{$imgpath}/cubes.png" alt="" /> {#Global_SettingsSections#} (<a href="index.php?do=settings&amp;sub=sectionsettings">{#Edit#}</a>)
  </div>
  <div id="modul_open" class="sysinfos">
    <div style="height: 150px; overflow: auto">
      {foreach from=$bereiche item=b}
        <div style="padding: 0px"><div style="float: right">{$b.Typ}</div>{$b.BName}</div>
        {/foreach}
    </div>
  </div>
  {if $widgets}
    <div class="header">
      <div id="widgets_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
      <img class="absmiddle" src="{$imgpath}/codewidgets.png" alt="" /> {#Global_Widgets#} (<a href="index.php?do=settings&amp;sub=widgets">{#Edit#}</a>)
    </div>
    <div id="widgets_open" class="sysinfos">
      <div style="height: 100px; overflow: auto">
        {foreach from=$widgets item=b}
          <div style="padding: 0px">{$b.BName}</div>
        {/foreach}
      </div>
    </div>
  {/if}
{/if}
