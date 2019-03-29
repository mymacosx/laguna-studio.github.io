<div class="header">{#Banners#}</div>
<div class="subheaders">
  <a class="colorbox" title="{#BannersNew#}" href="index.php?do=banners&amp;sub=new&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#BannersNew#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$banner_categs}
  <div class="info_red"> {#GlobalNoCateg#} </div>
{else}
  <form method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="headers">
        <td width="220" class="headers">{#Global_Name#}&nbsp;</td>
        <td width="100" align="center" class="headers"> {#Global_Categ#}&nbsp;</td>
        <td width="120" align="center" class="headers">{#Global_Active#}&nbsp;</td>
        <td width="100" align="center" nowrap="nowrap" class="headers">{#BannersWeight#}&nbsp;</td>
        <td width="100" align="center" class="headers">{#BannersViews#}&nbsp;</td>
        <td width="100" align="center" nowrap="nowrap" class="headers">{#BannersViewsMax#}&nbsp;</td>
        <td width="100" align="center" nowrap="nowrap" class="headers">{#BannersClick#}&nbsp;</td>
        <td class="headers">{#Global_Actions#}</td>
      </tr>
      {foreach from=$banners item=b}
        <tr class="{cycle values='second,first'}">
          <td><strong>{$b->Name|sanitize}</strong>
            <input type="hidden" name="banner[{$b->Id}]" value="{$b->Id}"/></td>
          <td align="center" nowrap="nowrap"><select name="Kategorie[{$b->Id}]" class="input" style="width: 150px">
              {foreach from=$banner_categs item=c}
                <option value="{$c->Id|sanitize}" {if $c->Id == $b->Kategorie}selected="selected" {/if}>{$c->Name|sanitize}</option>
              {/foreach}
            </select>
          </td>
          <td align="center" nowrap="nowrap">
            <select name="Aktiv[{$b->Id}]" class="input">
              <option value="1" {if $b->Aktiv == 1}selected="selected" {/if}>{#Yes#}</option>
              <option value="0" {if $b->Aktiv == 0}selected="selected" {/if}>{#No#}</option>
            </select>
          </td>
          <td align="center">
            <select name="Gewicht[{$b->Id}]" class="input">
              <option value="1" {if $b->Gewicht == 1}selected="selected" {/if}>1</option>
              <option value="2" {if $b->Gewicht == 2}selected="selected" {/if}>2</option>
              <option value="3" {if $b->Gewicht == 3}selected="selected" {/if}>3</option>
            </select>
          </td>
          <td align="center"><input type="text" class="input" name="Anzeigen[{$b->Id}]" style="width: 40px" value="{$b->Anzeigen}" /></td>
          <td align="center"><input type="text" class="input" name="Anzeigen_Max[{$b->Id}]" style="width: 40px" value="{$b->Anzeigen_Max}" /></td>
          <td align="center"><input type="text" class="input" name="Click[{$b->Id}]" style="width: 40px" value="{$b->Click}" /></td>
          <td>
            <a class="colorbox stip" title="{$lang.BannersEdit|sanitize}" href="index.php?do=banners&amp;sub=edit&amp;id={$b->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
            <a class="stip" title="{$lang.BannersDelete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$b->Name|jsspecialchars}');" href="index.php?do=banners&amp;sub=delete&amp;id={$b->Id}&amp;name={$b->Name|sanitize}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          </td>
        </tr>
      {/foreach}
    </table>
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
{/if}
