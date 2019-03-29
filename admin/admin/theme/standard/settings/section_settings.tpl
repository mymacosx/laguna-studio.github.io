<div class="header">{#Global_SettingsSections#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <div class="maintable">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td width="300" class="headers">{#Global_SettingsSections#}</td>
        <td width="130" align="center" class="headers">{#Global_Type#}</td>
        <td width="100" align="center" class="headers">{#Global_Active#}</td>
        <td width="60" align="center" class="headers">{#Global_Actions#}</td>
        <td class="headers">{#Active_SectionsLink#}</td>
      </tr>
      {foreach from=$bereiche item=b}
        <tr class="{cycle values='second,first'}">
          <td width="300" class="row_spacer">{$b.BName}</td>
          <td width="130" align="center" nowrap="nowrap" class="row_spacer">{$b.Typ}</td>
          <td width="100" align="center" nowrap="nowrap" class="row_spacer">
            {if isset($b.Install) && $b.Install == 'ok'}
              <label><input type="radio" name="Aktiv[{$b.Id}]" value="1" {if $b.Aktiv == 1}checked="checked" {/if}/>{#Yes#}</label>
              <label><input type="radio" name="Aktiv[{$b.Id}]" value="0" {if $b.Aktiv == 0}checked="checked" {/if}/>{#No#}</label>
              {else}
                {#NoInstall#}
              {/if}
          </td>
          {if $b.Type == 'extmodul'}
            {if isset($b.Install) && $b.Install == 'ok'}
              <td class="row_spacer">
                <img class="absmiddle stip" title="{$b.ModulInf|sanitize}" src="{$imgpath}/sysinfo.png" alt="" border="0" />
                <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do={$b.Name}"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
                <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#UnInstall#}{$b.BName} ?');" href="index.php?do=settings&amp;sub=moduldel&amp;name={$b.Name}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
              </td>
            {else}
              <td class="row_spacer">
                <img class="absmiddle stip" title="{$b.ModulInf|sanitize}" src="{$imgpath}/sysinfo.png" alt="" border="0" />
                <img class="absmiddle" src="{$imgpath}/edit_no.png" alt="" border="0" />
                <a class="stip" title="{$lang.Install|sanitize}" href="index.php?do=settings&amp;sub=modulinstall&amp;modul={$b.Modul}"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a>
              </td>
            {/if}
          {else}
            <td class="row_spacer">&nbsp;</td>
          {/if}
          <td class="row_spacer">
            {if !empty($b.Link)}
              <input type="text" class="input" style="width: 200px" value="{$b.Link}" readonly="readonly" />&nbsp;
              <a target="_blank" href="../{$b.Link}"><img border="0" class="absmiddle" src="{$imgpath}/view.png" alt="" /></a>
              {else}
              &nbsp;
            {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input name="save" type="hidden" id="save" value="1" />
  <input class="button" type="submit" value="{#Save#}" />
</form>
