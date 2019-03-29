{if perm('settings')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('sx_navi', 'sx_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="sx_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/sysinfo.png" alt="" /> {#Start_SysInfos#}
</div>
<div  id="sx_open" class="sysinfos" style="font-weight: normal">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><strong>{#Start_Version#}: </strong></td>
      <td align="right">{$version}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_dbsize#}: </strong></td>
      <td align="right">{$dbsize}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_mysqlV#}: </strong></td>
      <td align="right">{$mysqlversion}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_GD#}: </strong></td>
      <td align="right">{$gdinfo}</td>
    </tr>
    <tr>
      <td width="180"><a class="colorbox stip" title="{$lang.PhpInfo|sanitize}" href="index.php?do=main&sub=phpinfo&amp;noframes=1"><strong>{#Start_phpV#}: </strong></a></td>
      <td align="right">{$phpversion}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_maxUpload#}: </strong></td>
      <td align="right">{$maxupload}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_maxRam#}: </strong></td>
      <td align="right">{$maxmemory}</td>
    </tr>
    <tr>
      <td width="180"><strong>SAFE_MODE: </strong></td>
      <td align="right">{$safemode}</td>
    </tr>
    <tr>
      <td width="180"><strong>MAGIC_QUOTES_GPC: </strong></td>
      <td align="right">{$magicquotes}</td>
    </tr>
    <tr>
      <td width="180"><strong>MAGIC_QUOTES_RUNTIME: </strong></td>
      <td align="right">{$runtime}</td>
    </tr>
    <tr>
      <td width="180"><strong>MAGIC_QUOTES_SYBASE: </strong></td>
      <td align="right">{$sybase}</td>
    </tr>
    <tr>
      <td width="180"><strong>{#Start_exeT#}: </strong></td>
      <td align="right">{$maxtime}</td>
    </tr>
  </table>
  <strong>{#Start_deacFuncs#}: </strong><br />
  <em>{$disabled|replace: ',': ', '}</em>
  <br />
  <br />
  {if perm('settings') && $apache}
    <br />
    <strong>{#Start_apacheMods#}</strong>
    <br />
    {foreach from=$apache item=am}
      {$am},
    {/foreach}
  {/if}
</div>
{/if}
