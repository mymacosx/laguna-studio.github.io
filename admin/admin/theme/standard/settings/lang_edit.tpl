{if !empty($smarty.request.file) && $admin_settings.EditArea == 1}
<script type="text/javascript" src="{$baseurl}/lib/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
<!-- //
editAreaLoader.init( {
    id: 'tpl', syntax: 'css', start_highlight: true,
    {if $browser == 'ie9' || $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
    language: 'en'
    {else}
    language: '{$langcode|default:"ru"}'
    {/if}
});
//-->
</script>
{/if}

<div class="header">{#SettingsLangEdit#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if $zone == 1}
  <div class="maintable">
    <div class="headers"><a href="?do=settings&amp;sub=lang_edit">{#Global_Overview#}</a></div>
  </div>
  <div class="tplbox">
    <strong>{#Templates_selMain#}</strong>
    <br />
    <br />
    <form action="">
      <table width="600" border="0">
        <tr>
          <td><strong>{#GlobalSystem#}</strong></td>
          <td><strong>{#GlobalModules#}</strong></td>
          <td><strong>{#GlobalWidgets#}</strong></td>
        </tr>
        <tr>
          <td>
            <select name="select" size="20" class="input" style="font-size: 12px; width: 200px" onchange="eval(this.options[this.selectedIndex].value);">
              {foreach from=$folders item=f}
                <option style="font-size: 12px; font-weight: bold" value="location.href='?do=settings&amp;sub=lang_edit&amp;path={$f->Name}&amp;type=files'" {if isset($smarty.request.file) && $smarty.request.file == $f->Name}selected="selected"{/if}>{$f->Name|upper}</option>
              {/foreach}
            </select>
          </td>
          <td>
            <select name="select" size="20" class="input" style="font-size: 12px; width: 200px" onchange="eval(this.options[this.selectedIndex].value);">
              {foreach from=$modul item=m}
                <option style="font-size: 12px; font-weight: bold" value="location.href='?do=settings&amp;sub=lang_edit&amp;path={$m->Name}&amp;type=modul'" {if isset($smarty.request.file) && $smarty.request.file == $m->Name}selected="selected"{/if}>{$m->Name|upper}</option>
              {/foreach}
            </select>
          </td>
          <td>
            <select name="select" size="20" class="input" style="font-size: 12px; width: 200px" onchange="eval(this.options[this.selectedIndex].value);">
              {foreach from=$widget item=b}
                <option style="font-size: 12px; font-weight: bold" value="location.href='?do=settings&amp;sub=lang_edit&amp;path={$b->Name}&amp;type=widget'" {if isset($smarty.request.file) && $smarty.request.file == $b->Name}selected="selected"{/if}>{$b->Name|upper}</option>
              {/foreach}
            </select>
          </td>
        </tr>
      </table>
    </form>
  </div>
{elseif $zone == 2}
  <div class="maintable">
    <div class="headers">
      <a href="?do=settings&amp;sub=lang_edit">{#Global_Overview#}</a> /
      {if !empty($smarty.request.path)}
        {if $type == 'modules'}
          <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type=modul">{$smarty.request.path}</a> /
        {elseif $type == 'widgets'}
          <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type=widget">{$smarty.request.path}</a> /
        {else}
          <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type={$type}">{$smarty.request.path}</a> /
        {/if}
      {/if}
    </div>
  </div>
  <div class="tplbox">
    <strong>{#Templates_selMain#}</strong>
    <br />
    <br />
    <form action="">
      <select name="select2" size="20" class="input" style="font-size: 12px; width: 200px" onchange="eval(this.options[this.selectedIndex].value);">
        {foreach from=$folders item=f}
          <option style="font-size: 12px; font-weight: bold" value="location.href='?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path|default:''}&amp;subpath={$f->Name}&amp;type={$type}'" {if isset($smarty.request.file) && $smarty.request.file == $f->Name}selected="selected"{/if}>{$f->Name|upper}</option>
        {/foreach}
      </select>
    </form>
  </div>
{else}
  <div class="maintable">
    <div class="headers">
      <a href="?do=settings&amp;sub=lang_edit">{#Global_Overview#}</a> /
      {if $type == 'modules'}
        <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type=modul">{$smarty.request.path}</a> /
      {elseif $type == 'widgets'}
        <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type=widget">{$smarty.request.path}</a> /
      {else}
        <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;type={$type}">{$smarty.request.path}</a> /
      {/if}
      {if !empty($smarty.request.subpath)}
        <a href="?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}&amp;subpath={$smarty.request.subpath|default:''}&amp;type={$type}">{$smarty.request.subpath|default:''}</a>
      {/if}
    </div>
  </div>
  <div class="tplbox"><strong>{#Templates_selfile#}</strong><br />
      {if !empty($file_edit)}
      <div class="info_green">{#Templ_file#}: <strong><em>{if $type != 'files'}/{$type}{/if}{$dir|sanitize}</em></strong></div>
    {/if}
    <table width="100%">
      <tr>
        <td width="200" valign="top">
          <form action="">
            <select size="20" class="input" style="font-size: 11px; font-weight: bold; width: 200px; height: 455px" onchange="eval(this.options[this.selectedIndex].value);">
              {foreach from=$folders item=f}
                <option style="font-size: 11px; font-weight: bold" value="location.href='?do=settings&amp;sub=lang_edit&amp;path={$smarty.request.path}{if !empty($smarty.request.subpath)}&amp;subpath={$smarty.request.subpath}{/if}&amp;type={$type}&amp;file={$f->Name}'" {if isset($smarty.request.file) && $smarty.request.file == $f->Name}selected="selected"{/if}>{$f->Name|upper}</option>
              {/foreach}
            </select>
          </form>
        </td>
        <td valign="top">
          {if !empty($file_edit)}
            <form method="post" action="">
              <textarea cols="" rows="" id="tpl" class="input" wrap="off" style="width: 98%; height: 450px; font: 12px 'Courier New', Courier, monospace" name="file_content">{$file_content|escape: html}</textarea>
              <input type="hidden" name="subpath" value="{$smarty.request.subpath|default:''}" />
              <input type="hidden" name="path" value="{$smarty.request.path|default:''}" />
              <input type="hidden" name="file" value="{$smarty.request.file|default:''}" />
              <input name="save" type="hidden" id="save" value="1" />
              {if $allowed}
              <input name="sort" type="checkbox" value="1" /> {#Sortable#}<br />
              {/if}
              <input class="button" type="submit" value="{#Save#}" />
            </form>
          {/if}
        </td>
      </tr>
    </table>
  </div>
{/if}
