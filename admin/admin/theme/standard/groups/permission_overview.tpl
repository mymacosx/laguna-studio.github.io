<div class="header">{#Groups_Name#} - {#GlobalPerm#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
  {foreach from=$sections item=c}
    <tr>
      <td colspan="2" class="headers">{#Sections_spez#}: {$c->Name|sanitize}</td>
    </tr>
    {foreach from=$c->Groups item=g}
      <tr class="{cycle values=$cn|default:'second,first'}">
        <td width="200"><strong>{$g->Name_Intern|sanitize}</strong></td>
        <td>
          {if $g->Id == 1}
            {#Groups_CannotEdit#}
          {else}
            <a class="colorbox stip" title="{$lang.Groups_PermissionsEdit|sanitize}" href="index.php?do=groups&amp;sub=editpermissions&amp;id={$g->Id}&amp;area={$c->Id}&amp;groupname={$g->Name_Intern|sanitize|urlencode}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
            <span id="group_{$c->Id}_{$g->Id}"></span>
          {/if}
        </td>
      </tr>
    {/foreach}
    {assign var=cn value='second,first'}
  {/foreach}
</table>
