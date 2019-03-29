<script type="text/javascript">
<!-- //
function selIcon(elem, id) {
    if (elem.options[elem.selectedIndex].value == '') {
        document.getElementById(id).innerHTML = '';
    } else {
        document.getElementById(id).innerHTML = '<img class="absmiddle" src="{$smi_path}' + elem.options[elem.selectedIndex].value + '">';
    }
}
//-->
</script>
<div class="header">{#Forums_TIcons_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post" enctype="multipart/form-data" name="kform">
  <table width="100%" border="0" cellpadding="2" cellspacing="0" class="tableborder">
    <tr class="secondrow">
      <th width="20" class="headers">{#Forums_TIcons_icon#}</th>
      <th width="130" class="headers">{#GlobalValue#}&nbsp;</th>
      <th width="130" class="headers">{#Forums_Smi_active#}</th>
      <th width="100" class="headers">{#Forums_Smi_icon#}</th>
      <th width="100" class="headers">{#Global_Position#}</th>
      <td class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
    </tr>
    {foreach from=$smileys item="sm"}
      <tr  class="{cycle values='second,first'}">
        <td width="5%" align="center" nowrap="nowrap">{$sm->Icon}<input type="hidden" name="smilie[{$sm->id}]" value="1" /></td>
        <td align="center"><input class="input" name="title[{$sm->id}]" type="text" id="title[{$sm->id}]" value="{$sm->title}" size="20" /></td>
        <td align="center" nowrap="nowrap">
          <label><input type="radio" name="active[{$sm->id}]" value="1" {if $sm->active == 1}checked="checked"{/if} /> {#Yes#}</label>
          <label><input type="radio" name="active[{$sm->id}]" value="2" {if $sm->active == 2}checked="checked"{/if} /> {#No#}</label>
        </td>
        <td align="center"><input class="input" name="path[{$sm->id}]" type="text" id="path[{$sm->id}]" value="{$sm->path}" size="15" /></td>
        <td align="center"><input class="input" name="posi[{$sm->id}]" type="text" id="posi[{$sm->id}]" value="{$sm->posi}" size="5" /></td>
        <td><label><input name="del[{$sm->id}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label></td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<form action="" method="post" enctype="multipart/form-data" name="adds" id="adds">
  <fieldset>
    <legend>{#Forums_TIcons_new#}</legend>
    {if !$smi}
      {$lang.Forums_TIcons_AllUsed|replace: "__PATH__": $icon_t_path}
    {else}
      <table width="100%" border="0" cellpadding="2" cellspacing="0" class="tableborder">
        <tr>
          <td class="firstrow">{$info}</td>
        </tr>
        <tr>
          <td>{#GlobalValue#}</td>
          <td>{#Forums_Smi_icon#}</td>
        </tr>
        {section name="new" loop=5}
          {assign var="count" value=$count+1}
          <tr>
            <td width="10%"><div align="center"><input class="input" name="title[]" type="text" id="title_{$count}" /></div></td>
            <td>
              <select onchange="selIcon(this, 'smi_{$count}');" class="input" name="path[]" type="text" id="path[]">
                <option value=""></option>
                {foreach from=$smi item=s}
                  <option value="{$s->Name}">{$s->Name}</option>
                {/foreach}
              </select>
              {foreach from=$smi item=s}
                <input type="hidden" id="{$s->Name}" value="{$s->Short}" />
              {/foreach}
              <span id="smi_{$count}"></span>
            </td>
          </tr>
        {/section}
        <tr>
          <td class="thirdrow"><input name="new" type="hidden" id="new" value="1" /></td>
        </tr>
      </table>
      <input type="submit" class="button" value="{#Save#}" />
    {/if}
  </fieldset>
</form>
