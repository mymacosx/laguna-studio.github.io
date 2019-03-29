<div class="header">{#Stats#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
  <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
  {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<script type="text/javascript" src="{$jspath}/chart/json.js"></script>
<script type="text/javascript" src="{$jspath}/chart/chartplugin.js"></script>
<script type="text/javascript" src="{$jspath}/chart/excanvas.js"></script>
<script type="text/javascript" src="{$jspath}/chart/wz_jsgraphics.js"></script>
<script type="text/javascript" src="{$jspath}/chart/chart.js"></script>
<script type="text/javascript" src="{$jspath}/chart/canvaschartpainter.js"></script>
<script type="text/javascript" src="{$jspath}/chart/jgchartpainter.js"></script>
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#chartdata')
    .chartInit({ "painterType": "jsgraphics","backgroundColor": "","textColor": "","axesColor": "","yMin": "0","yMax": "","xGrid": "0","yGrid": "15","xLabels": [{#Calendar_monthNamesShort#}],"showLegend": false })
    .chartAdd({ "label": "{#Stats_Visits#}","type": "Bar","color": "#ff9900","values": [{foreach from=$res_mon item=rd name=m}{if !$rd.Alle}"0"{else}"{$rd.Alle}"{/if}{if !$smarty.foreach.m.last},{/if}{/foreach}] })
    .chartClear()
    .chartDraw();

    $('#chartdata_hits')
    .chartInit({ "painterType": "jsgraphics","backgroundColor": "","textColor": "","axesColor": "","yMin": "0","yMax": "","xGrid": "0","yGrid": "15","xLabels": [{#Calendar_monthNamesShort#}],"showLegend": false })
    .chartAdd({ "label": "{#Stats_Hits#}","type": "Bar","color": "#ccc","values": [{foreach from=$res_mon item=rd name=m}{if !$rd.Hits}"0"{else}"{$rd.Hits}"{/if}{if !$smarty.foreach.m.last},{/if}{/foreach}],"stackedOn": "{#Stats_Visits#}" })
    .chartClear()
    .chartDraw();

    $('#chartdata_month')
    .chartInit({ "painterType": "jsgraphics","backgroundColor": "","textColor": "","axesColor": "","yMin": "0","yMax": "","xGrid": "0","yGrid": "15","xLabels": [{foreach from=$res_day item=rd name=m}"{$smarty.foreach.m.index+1}"{if !$smarty.foreach.m.last},{/if}{/foreach}] })
    .chartAdd({ "label": "{#Stats_Hits#}","type": "Bar","color": "#ccc","values": [{foreach from=$res_day item=rd name=m}{if !$rd.Hits}"0"{else}"{$rd.Hits}"{/if}{if !$smarty.foreach.m.last},{/if}{/foreach}],"stackedOn": "{#Stats_Visits#}" })
    .chartClear()
    .chartDraw();

    $('#chartdata_monthvisits')
    .chartInit({ "painterType": "jsgraphics","backgroundColor": "","textColor": "","axesColor": "","yMin": "0","yMax": "","xGrid": "0","yGrid": "15","xLabels": [{foreach from=$res_day item=rd name=m}"{$smarty.foreach.m.index+1}"{if !$smarty.foreach.m.last},{/if}{/foreach}] })
    .chartAdd({ "label": "{#Stats_Visits#}","type": "Bar","color": "#ff9900","values": [{foreach from=$res_day item=rd name=m}{if !$rd.Alle}"0"{else}"{$rd.Alle}"{/if}{if !$smarty.foreach.m.last},{/if}{/foreach}] })
    .chartClear()
    .chartDraw();

    $('#chartdata_system')
    .chartInit({ "painterType": "jsgraphics","backgroundColor": "","textColor": "","axesColor": "","yMin": "0","yMax": "","xGrid": "0","yGrid": "","xLabels": [""],"showLegend": false })
    .chartAdd({ "label": "Windows 10","type": "Bar","color": "#fc0cf8","values": ["{$s.w10}"] })
    .chartAdd({ "label": "Windows 8.1","type": "Bar","color": "#ffaeB9","values": ["{$s.w8_1}"] })
    .chartAdd({ "label": "Windows 8","type": "Bar","color": "#8b0000","values": ["{$s.w8}"] })
    .chartAdd({ "label": "Windows 7","type": "Bar","color": "#000000","values": ["{$s.w7}"] })
    .chartAdd({ "label": "Windows Vista","type": "Bar","color": "#ffff00","values": ["{$s.wv}"] })
    .chartAdd({ "label": "Windows 2003","type": "Bar","color": "#9acd32","values": ["{$s.w3}"] })
    .chartAdd({ "label": "Windows 2000","type": "Bar","color": "#f5deb3","values": ["{$s.w0}"] })
    .chartAdd({ "label": "Windows XP","type": "Bar","color": "#ee82ee","values": ["{$s.wxp}"] })
    .chartAdd({ "label": "Windows NT","type": "Bar","color": "#40e0d0","values": ["{$s.wnt}"] })
    .chartAdd({ "label": "Windows 98","type": "Bar","color": "#ff6347","values": ["{$s.w98}"] })
    .chartAdd({ "label": "Windows 95","type": "Bar","color": "#008080","values": ["{$s.w95}"] })
    .chartAdd({ "label": "N/A Windows OS","type": "Bar","color": "#d2b48c","values": ["{$s.uw}"] })
    .chartAdd({ "label": "Mac OS X","type": "Bar","color": "#4682b4","values": ["{$s.mx}"] })
    .chartAdd({ "label": "Intel Mac","type": "Bar","color": "#00ff7f","values": ["{$s.im}"] })
    .chartAdd({ "label": "PowerPC Mac","type": "Bar","color": "#708090","values": ["{$s.pm}"] })
    .chartAdd({ "label": "PowerPC","type": "Bar","color": "#6a5acd","values": ["{$s.pp}"] })
    .chartAdd({ "label": "Cygwin","type": "Bar","color": "#a0522d","values": ["{$s.cy}"] })
    .chartAdd({ "label": "Linux","type": "Bar","color": "#f4a460","values": ["{$s.li}"] })
    .chartAdd({ "label": "Debian","type": "Bar","color": "#ff0000","values": ["{$s.de}"] })
    .chartAdd({ "label": "OpenVMS","type": "Bar","color": "#800080","values": ["{$s.ov}"] })
    .chartAdd({ "label": "Sun Solaris","type": "Bar","color": "#98fb98","values": ["{$s.ss}"] })
    .chartAdd({ "label": "Amiga","type": "Bar","color": "#ffa500","values": ["{$s.am}"] })
    .chartAdd({ "label": "BeOS","type": "Bar","color": "#808000","values": ["{$s.bo}"] })
    .chartAdd({ "label": "ApacheBench","type": "Bar","color": "#000080","values": ["{$s.ab}"] })
    .chartAdd({ "label": "FreeBSD","type": "Bar","color": "#800000","values": ["{$s.fb}"] })
    .chartAdd({ "label": "NetBSD","type": "Bar","color": "#ff00ff","values": ["{$s.nb}"] })
    .chartAdd({ "label": "BSDi","type": "Bar","color": "#00ff00","values": ["{$s.bs}"] })
    .chartAdd({ "label": "OpenBSD","type": "Bar","color": "#1e90ff","values": ["{$s.ob}"] })
    .chartAdd({ "label": "AIX","type": "Bar","color": "#ff8c00","values": ["{$s.ai}"] })
    .chartAdd({ "label": "Irix","type": "Bar","color": "#006400","values": ["{$s.ir}"] })
    .chartAdd({ "label": "DEC OSF","type": "Bar","color": "#dc143c","values": ["{$s.do}"] })
    .chartAdd({ "label": "HP-UX","type": "Bar","color": "#0000ff","values": ["{$s.hu}"] })
    .chartAdd({ "label": "N/A Unix OS","type": "Bar","color": "#8a2be2","values": ["{$s.uu}"] })
    .chartClear()
    .chartDraw();
});
//-->
</script>

<div class="subheaders">
  <form method="post" action="">
    <select name="monat" class="input">
      {section name=m loop=12}
        <option value="{$smarty.section.m.index+1}" {if $smarty.section.m.index+1 == $smarty.post.monat}selected="selected"{/if}>{$smarty.section.m.index+1}</option>
      {/section}
    </select>
    <select name="jahr" class="input">
      {section name=j loop=$end start=$start}
        <option value="{$smarty.section.j.index+1}" {if $smarty.section.j.index+1 == $smarty.post.jahr}selected="selected"{/if}>{$smarty.section.j.index+1}</option>
      {/section}
    </select>
    <input type="submit" class="button" value="{#Stats_Button#}" />
  </form>
</div>
<div class="subheaders">{#Stats_Year#}</div>
<table width="500" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td class="row_left"><strong>{#Global_Date#}: </strong></td>
    {foreach from=$res_mon item=rd name=m}
      <td class="row_left"><small><strong>{$smarty.foreach.m.index+1}.</strong></small></td>
          {/foreach}
  </tr>
  <tr>
    <td class="row_right"><strong>{#Stats_Visits#}: </strong></td>
    {foreach from=$res_mon item=rd name=m}
      {assign var=td value=$smarty.now|date_format: '%m'}
      <td class="row_right" {if $td == $smarty.foreach.m.index+1}style="background: #eee; font-weight: bold"{/if}>
        {if !$rd.Alle}
          0
        {else}
          {$rd.Alle}
        {/if}
      {if !$smarty.foreach.m.last}{/if}
    </td>
  {/foreach}
</tr>
</table>
<div id="chartdata" class="chart" style="width: 700px; height: 300px;"></div>
<div id="chartdata_hits" class="chart" style="width: 700px; height: 300px;"></div>
<div class="subheaders">{#Stats_Month#}</div>
<table border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td class="row_left"><strong>{#Global_Date#}: </strong></td>
    {foreach from=$res_day item=rd name=m}
      <td class="row_left"><small><strong>{$smarty.foreach.m.index+1}.</strong></small></td>
          {/foreach}
  </tr>
  <tr>
    <td class="row_right"><strong>{#Stats_Visits#}: </strong></td>
    {foreach from=$res_day item=rd name=m}
      {assign var=td value=$smarty.now|date_format: '%d'}
      <td class="row_right" {if $td == $smarty.foreach.m.index+1}style="background: #eee; font-weight: bold"{/if}>
      {if !$rd.Alle}0{else}{$rd.Alle}{/if}
    </td>
  {/foreach}
</tr>
</table>
<div id="chartdata_monthvisits" class="chart" style="width: 700px; height: 300px;"></div>
<div id="chartdata_month" class="chart" style="width: 700px; height: 300px;"></div>
<div class="subheaders">{#Stats_Os#}</div>
<div id="chartdata_system" class="chart" style="width: 700px; height: 450px;"></div>
