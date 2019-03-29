<div class="header">{#Global_Shop#} - {#Global_Categories#}</div>
<div class="subheaders">
  {if perm('shop_catdeletenew')}
    <a title="{#Global_AddCateg#}" class="colorbox" href="?do=shop&amp;sub=new_categ&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Global_AddCateg#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="?do=shop" method="post" enctype="multipart/form-data" name="kform" id="kform">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td class="headers">{#Global_Name#}</td>
        <td align="center" class="headers">{#Global_Id#}</td>
        <td align="center" class="headers">{#Global_Position#}</td>
        <td align="center" class="headers">{#Search#}</td>
        <td align="center" class="headers">{#Global_Active#}</td>
        <td class="headers">{#Global_Actions#}</td>
      </tr>
      {foreach from=$categs item=item}
        {assign var=subcateg value=$subcateg+1}
        {if $item->Parent_Id == 0}
          {assign var=trid value=$trid+1}
          {assign var=subcateg value=''}
          <tr class="second">
            <td class="firstrow">
              {if $smarty.request.trid == $trid}
                <input style="width: 15px; background: none; font-weight: bold; font-size: 12px" onclick="location.href = '?do=shop&amp;sub=categories&amp;open=0';" type="button" class="buttons" value=" - " />
              {else}
                <input style="width: 15px; background: none; font-weight: bold; font-size: 12px" onclick="location.href = '?do=shop&amp;sub=categories&amp;trid={$trid}&amp;open=1';" type="button" class="buttons" value=" + " />
              {/if}
              <input class="input" style="font-weight: bold" name="name[{$item->Id}]" type="text" value="{$item->Name_1|sanitize}" size="25" />
            </td>
            <td align="center" class="firstrow"><strong>{$item->Id}</strong></td>
            <td align="center" class="firstrow"><input class="input" type="text" value="{$item->posi}" name="posi[{$item->Id}]" size="3" maxlength="5" />
            </td>
            <td align="center" class="firstrow">
              <label><input type="radio" name="Search[{$item->Id}]" value="1" {if $item->Search == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="Search[{$item->Id}]" value="0" {if $item->Search == 0}checked="checked"{/if} />{#No#}</label>
            </td>
            <td align="center" class="firstrow">
              <label><input type="radio" name="Aktiv[{$item->Id}]" id="akt{$item->Id}" value="1" {if $item->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input onclick="if (!confirm('{#ShopCategActiveWarn#}')) document.getElementById('akt{$item->Id}').checked = 'true';" type="radio" name="Aktiv[{$item->Id}]" value="0" {if $item->Aktiv == 0}checked="checked"{/if} />{#No#}</label>
            </td>
            <td nowrap="nowrap" class="firstrow">
              {if perm('shop_catdeletenew')}
                <a class="colorbox stip" title="{$lang.Shop_cat_edit|sanitize}" href="?do=shop&amp;sub=edit_categ&amp;id={$item->Id}&amp;parent=0&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
                {/if}
                {if perm('shop_catdeletenew')}
                <a class="colorbox stip" title="{$lang.GlobalAddCateg|sanitize}" href="?do=shop&amp;sub=new_categ&amp;id={$item->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a>
                {/if}
              <a class="stip" title="{$lang.Global_Overview|sanitize}" target="_blank" href="../index.php?p=shop&amp;action=showproducts&amp;cid={$item->catid}&amp;page=1&amp;limit=10"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
                {if perm('shop_variants')}
                <a class="colorbox stip" title="{$lang.Shop_variants_categs|sanitize}" href="?do=shop&amp;sub=categvariants&amp;id={$item->catid}&amp;noframes=1&amp;name={$item->Name_1|sanitize}"><img class="absmiddle" src="{$imgpath}/various.png" border="0" alt="" /></a>&nbsp;
                {/if}
                {if perm('shop_specifications')}
                <a class="colorbox stip" title="{$lang.Shop_articles_spez|sanitize}" href="?do=shop&amp;sub=specifications&amp;id={$item->catid}&amp;noframes=1&amp;name={$item->Name_1|sanitize}"><img class="absmiddle" src="{$imgpath}/specifcations.png" border="0" alt="" /></a>&nbsp;
                {/if}
                {if perm('shop_catdeletenew')}
                <a onclick="return confirm('{#Shop_cat_JSDel#}');" class="stip" title="{$lang.Shop_DelCateg|sanitize}" href="index.php?do=shop&amp;sub=del_categ&amp;id={$item->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                {/if}
            </td>
          </tr>
        {else}
          {if $smarty.request.open == 1 && $smarty.request.trid == $trid}
            <tr class="secondrow">
              <td>
                {$item->Expander|replace: '-': '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'}
                <input class="input" name="name[{$item->Id}]" type="text" value="{$item->catname|escape: 'html'}" size="25" />
              </td>
              <td align="center"><strong>{$item->Id}</strong></td>
              <td align="center"><input class="input" type="text" value="{$item->posi}" name="posi[{$item->Id}]" size="3" maxlength="5" /></td>
              <td align="center" nowrap="nowrap">
                <label><input type="radio" name="Search[{$item->Id}]" value="1" {if $item->Search == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input type="radio" name="Search[{$item->Id}]" value="0" {if $item->Search == 0}checked="checked"{/if} />{#No#}</label>
              </td>
              <td align="center" nowrap="nowrap">
                <label><input id="akt{$item->Id}" type="radio" name="Aktiv[{$item->Id}]" value="1" {if $item->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input onclick="if (!confirm('{#ShopCategActiveWarn#}')) document.getElementById('akt{$item->Id}').checked = 'true';" type="radio" name="Aktiv[{$item->Id}]" value="0" {if $item->Aktiv == 0}checked="checked"{/if} />{#No#}</label>
              </td>
              <td nowrap="nowrap">
                {if perm('shop_catdeletenew')}
                  <a class="colorbox stip" title="{$lang.Shop_cat_edit|sanitize}" href="?do=shop&amp;sub=edit_categ&amp;id={$item->Id}&amp;parent={$item->Parent_Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
                  {/if}
                  {if perm('shop_catdeletenew')}
                    {if $item->Subcount < 32}
                    <a class="colorbox stip" title="{$lang.GlobalAddCateg|sanitize}" href="?do=shop&amp;sub=new_categ&amp;id={$item->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a>&nbsp;
                    {else}
                    <img class="absmiddle" src="{$imgpath}/add_disabled.png" alt="" border="0" />
                  {/if}
                {/if}
                <a class="stip" title="{$lang.Global_Overview|sanitize}" target="_blank" href="../index.php?p=shop&amp;action=showproducts&amp;cid={$item->catid}&amp;page=1&amp;limit=10"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
                  {if perm('shop_variants')}
                  <a class="colorbox stip" title="{$lang.Shop_variants_categs|sanitize}" href="?do=shop&amp;sub=categvariants&amp;id={$item->catid}&amp;noframes=1&amp;name={$item->Name_1|sanitize}"><img class="absmiddle" src="{$imgpath}/various.png" border="0" alt="" /></a>&nbsp;
                  {/if}
                  {if perm('shop_specifications')}
                  <a class="colorbox stip" title="{$lang.Shop_articles_spez|sanitize}" href="?do=shop&amp;sub=specifications&amp;id={$item->catid}&amp;noframes=1&amp;name={$item->Name_1|sanitize}"><img class="absmiddle" src="{$imgpath}/specifcations.png" border="0" alt="" /></a>&nbsp;
                  {/if}
                  {if perm('shop_catdeletenew')}
                  <a onclick="return confirm('{#Shop_cat_JSDel#}');" class="stip" title="{$lang.Shop_DelCateg|sanitize}" href="index.php?do=shop&amp;sub=del_categ&amp;id={$item->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                  {/if}
              </td>
            </tr>
          {/if}
        {/if}
      {/foreach}
    </table>
  </div>
  <br />
  <input name="trid" type="hidden" value="{$smarty.request.trid}" />
  <input name="sub" type="hidden" value="categories" />
  <input name="save" type="hidden" id="save" value="1" />
  <input name="open" type="hidden" value="1" />
  <input name="Senden" type="submit" class="button" value="{#Global_Quicksave#}" />
</form>
