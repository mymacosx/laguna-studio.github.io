<div class="popbox">
  <div class="header">{#Groups_PermsWeb#} ({$smarty.get.groupname|sanitize})</div>
  <div class="subheaders">
    {if $smarty.request.id != 2}
      <a href="#admin">{#Groups_EditAdmin#}</a> |
    {/if}
    <a href="javascript: void(0);" onclick="document.forms['kform'].submit();">{#Groups_PermSave#}</a>
  </div>
  <form method="post" action="" name="kform">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="row_left"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></td>
        <td class="row_right"><label for="d"><strong>{#Global_SelAll#}</strong></label></td>
      </tr>
      {foreach from=$perms_page key=k item=pp}
        {assign var=descr value=$pp}
        {if $smarty.request.id == 2 && ($k == 'canpn' || $k == 'adminpanel' || $k == 'username' || $k == 'email_change' || $k == 'own_avatar'  || $k == 'delete_comments' || $k == 'edit_comments')}
        {else}
          <tr>
            {if !$pp}
              <td colspan="2" class="headers_row">
                {#Groups_PermsPre#}
                {assign var=title value=$k|replace: '___': ''}
                {if $k == 'Adminlink___'}{#Settings_general#}
                {elseif $k == 'User_comments___'}{#Global_Comments#}
                {elseif $k == 'User_profile___'}{#User_profile#}
                {elseif $k == 'Shop___'}{#Global_Shop#}
                {elseif $k == 'Cheats___'}{#Gaming_cheats#}
                {elseif $k == 'Other___'}{#Nav_Other#}
                {elseif $lang.$title}
                  {$lang.$title}
                {else}
                  {$title}
                {/if}
              </td>
            {else}
              <td width="10" class="row_left"><input name="Rechte[]" type="checkbox" id="Rechte_{$k}" value="{$k}" {if in_array($k,$res->Page)}checked="checked"{/if} /></td>
              <td class="row_right">
                {if !$pp}
                {else}
                  <label for="Rechte_{$k}">{if $pp}{$pp}{else}{$k}{/if}</label>
                {/if}
              </td>
            {/if}
          </tr>
        {/if}
      {/foreach}
    </table>
    {if $smarty.request.id == 2}
      <label><input name="setall" type="checkbox" id="setall" value="1" /><strong>{#Groups_SetAllSections#}</strong></label>
      <br />
      <input class="button" type="submit" value="{#Save#}" />
      <input class="button" type="button" value="{#Close#}" onclick="closeWindow();"/>
      <input name="save" type="hidden" id="save" value="1" />
    {else}
      <a name="admin"></a>
      <br />
      <div class="header">{#Groups_PermsAdmin#} ({$smarty.get.groupname|sanitize})</div>
      <div class="subheaders"><a href="#">{#Groups_EditPage#}</a> | <a href="javascript: void(0);" onclick="document.forms['kform'].submit();">{#Groups_PermSave#}</a></div>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        {foreach from=$perms_admin key=k item=pp}
          {assign var=descr value=$pp}
          <tr>
            {if !$pp}
              <td colspan="2" class="headers_row">
                {#Groups_PermsPre#}
                {assign var=title value=$k|replace: '___': ''}
                {if $k == 'Global___'}{#Settings_general#}
                {elseif $k == 'Users___'}{#User_nameS#}
                {elseif $k == 'Forum___'}{#Forums_nt#}
                {elseif $k == 'Shop___'}{#Global_Shop#}
                {elseif $k == 'Mediapool___'}{#Server_upload#}
                {elseif $k == 'Screenshots___'}{#Gallery_sIm#}
                {elseif $k == 'Contactforms___'}{#ContactForms#}
                {elseif $k == 'comments___'}{#Global_Comments#}
                {elseif $k == 'Cheats___'}{#Gaming_cheats#}
                {elseif $k == 'Seo___'}{#Seo_mod#}
                {elseif $k == 'Other___'}{#Nav_Other#}
                {elseif $lang.$title}
                  {$lang.$title}
                {else}
                  {$title}
                {/if}
              </td>
            {else}
              <td width="10" class="row_left"><input name="Rechte_Admin[]" type="checkbox" id="RechteAdmin_{$k}" value="{$k}" {if in_array($k,$res->Admin)}checked="checked"{/if} /></td>
              <td class="row_right">
                {if !$pp}
                {else}
                  <label for="RechteAdmin_{$k}">{if $pp}{$pp}{else}{$k}{/if}</label>
                {/if}
              </td>
            {/if}
          </tr>
        {/foreach}
      </table>
      <label><input name="setall" type="checkbox" id="setall" value="1" /><strong>{#Groups_SetAllSections#}</strong></label>
      <br />
      <input class="button" type="submit" value="{#Save#}" />
      <input class="button" type="button" value="{#Close#}" onclick="closeWindow();"/>
      <input name="save" type="hidden" id="save" value="1" />
    {/if}
  </form>
</div>
