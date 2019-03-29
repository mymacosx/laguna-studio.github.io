{if !empty($smarty.request.file) && $admin_settings.EditArea == 1}
<script type="text/javascript" src="{$baseurl}/lib/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
<!-- //
editAreaLoader.init({
    id: 'php', syntax: '{if $smarty.request.file == "robots.txt"}robotstxt{else}php{/if}', start_highlight: true,
    {if $browser == 'ie9' || $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
    language: 'en'
    {else}
    language: '{$langcode|default:"ru"}'
    {/if}
});
//-->
</script>
{/if}

<div class="header">{#ConfPhp#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="maintable">
  <div class="headers">
    <a href="?do=settings&amp;sub=phpedit">{#Global_Overview#}</a>
    {if !empty($smarty.request.file)}
      / <a href="?do=settings&amp;sub=phpedit&amp;file={$smarty.request.file}">{$smarty.request.file}</a>
    {/if}
  </div>
</div>
<div class="tplbox">
  <strong>{#Templates_selfile#}</strong>
  <br />
  {if !empty($file_edit)}
    <div class="info_green">{#Templ_file#}: <strong><em>{if isset($smarty.get.file) && $smarty.get.file != 'robots.txt'}/config{/if}/{$smarty.get.file|default:''}</em></strong></div>
  {/if}
  <table width="100%">
    <tr>
      <td width="200" valign="top">
        <form action="">
          <select size="20" class="input" style="font-size: 11px; font-weight: bold; width: 200px; height: 455px" onchange="eval(this.options[this.selectedIndex].value);">
            {foreach from=$folders item=f}
              <option style="font-size: 11px; font-weight: bold" value="location.href='?do=settings&amp;sub=phpedit&amp;file={$f->Name}'" {if isset($smarty.request.file) && $smarty.request.file == $f->Name}selected="selected"{/if}>{$f->Name}</option>
            {/foreach}
          </select>
        </form>
      </td>
      <td valign="top">
        {if !empty($file_edit)}
          <form method="post" action="">
            <textarea cols="" rows="" id="php" class="input" wrap="off" style="width: 98%; height: 450px; font: 12px 'Courier New', Courier, monospace" name="file_content">{$file_content|escape: html}</textarea>
            <input type="hidden" name="file" value="{$smarty.request.file|default:''}" />
            <input name="save" type="hidden" id="save" value="1" />
            <input class="button" type="submit" value="{#Save#}" />
          </form>
        {/if}
      </td>
    </tr>
  </table>
</div>
