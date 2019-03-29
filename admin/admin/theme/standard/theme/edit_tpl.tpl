{if !empty($smarty.request.file) && $admin_settings.EditArea == 1}
<script type="text/javascript" src="{$baseurl}/lib/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
<!-- //
editAreaLoader.init({
    id: 'tpl', syntax: 'html', start_highlight: true,
    {if $browser == 'ie9' || $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
    language: 'en'
    {else}
    language: '{$langcode|default:"ru"}'
    {/if}
});
//-->
</script>
{/if}

<div class="header">{#Templates#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if $zone == 1}
  <div class="maintable">
    <div class="headers"><a href="index.php?do=theme&amp;sub=show_all_tpl">{#Global_Overview#}</a>{if !empty($smarty.request.path)} / {$smarty.request.path}{/if}</div>
  </div>
  <div class="tplbox"><strong>{#Templates_selMain#}</strong>
    <br />
    <br />
    <form action="">
      <select size="20" class="input" style="font-size: 12px; width: 300px" onchange="eval(this.options[this.selectedIndex].value);">
        {foreach from=$folders item=f}
          <option  style="font-size: 12px; font-weight: bold" value="location.href='index.php?do=theme&amp;path={if !empty($smarty.request.path)}{$smarty.request.path}/{/if}{$f}&amp;sub={if !empty($smarty.request.path)}show_tpl{else}show_all_tpl{/if}'" {if isset($smarty.request.file) && $smarty.request.file == $f}selected="selected"{/if}>{$f|upper}</option>
        {/foreach}
      </select>
    </form>
  </div>
{else}
  <div class="maintable">
    <div class="headers"><a href="index.php?do=theme&amp;sub=show_all_tpl">{#Global_Overview#}</a> / <a href="index.php?do=theme&amp;path={$topnav.0}&amp;sub=show_all_tpl">{$topnav.0}</a> {if !empty($topnav.1)}/ <a href="index.php?do=theme&amp;path={$topnav.0}/{$topnav.1}&amp;sub=show_tpl">{$topnav.1}</a>{/if}</div>
  </div>
  <div class="tplbox"><strong>{#Templates_selfile#}</strong><br />
      {if !empty($file_edit)}
      <div class="info_green">{#Templ_file#}: <strong><em>/theme/{$smarty.get.path|default:''}/{$smarty.get.file|default:''}</em></strong></div>
    {/if}
    <table width="100%">
      <tr>
        <td width="200" valign="top">
          <form action="">
            <select size="20" class="input" style="font-size: 11px; font-weight: bold; width: 200px; height: 455px" onchange="eval(this.options[this.selectedIndex].value);">
              {foreach from=$folders item=f}
                <option style="font-size: 11px; font-weight: bold" value="location.href='index.php?do=theme&amp;path={$smarty.request.path|default:''}&amp;sub=show_tpl&amp;file={$f}'" {if isset($smarty.request.file) && $smarty.request.file == $f}selected="selected"{/if}>{$f|upper}</option>
              {/foreach}
            </select>
          </form>
        </td>
        <td valign="top">
          {if !empty($file_edit)}
            <form method="post" action="">
              <textarea cols="" rows="" id="tpl" class="input" wrap="off" style="width: 98%; height: 450px; font: 12px 'Courier New', Courier, monospace" name="file_content">{$file_content|escape: html}</textarea>
              <input type="hidden" name="path" value="{$smarty.request.path|default:''}" />
              <input type="hidden" name="file" value="{$smarty.request.file|default:''}" />
              <input name="save" type="hidden" id="save" value="1" />
              <input class="button" type="submit" value="{#Save#}" />
            </form>
          {/if}
        </td>
      </tr>
    </table>
  </div>
{/if}