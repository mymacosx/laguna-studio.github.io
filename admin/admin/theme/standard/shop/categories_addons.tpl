<script type="text/javascript">
<!-- //
function checkNew() {
    if(document.getElementById('first').value == '') {
	alert('{#Validate_required#}\n{#Global_Name#} 1');
	document.getElementById('first').focus();
	return false;
    }
}
//-->
</script>

<div class="header">{#Global_Shop#} - {#Shop_CategoriesAddons#}</div>
{if !$shop_search_small_categs}
  <div class="info_red"> {#ShopNoCateg#} </div>
{else}
  {if $error}
    <div class="error_box">
      <ul>
        {foreach from=$error item=e}
          <li>{$e}</li>
          {/foreach}
      </ul>
    </div>
  {/if}
  <div class="subheaders">
    <a href="#new"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a><a href="#new"> {#GlobalAddCateg#}</a>&nbsp;&nbsp;&nbsp;
      {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <form method="post" action="">
    <select class="input" name="lc">
      <option value="1" {if $lc == 1}selected="selected"{/if}>{$language.name.1|upper}</option>
      <option value="2" {if $lc == 2}selected="selected"{/if}>{$language.name.2|upper}</option>
      <option value="3" {if $lc == 3}selected="selected"{/if}>{$language.name.3|upper}</option>
    </select>
    <input type="submit" class="button" value="{#Global_Show#}" />
  </form>
  <br />
  <br />
  <form method="post" action="">
    <div class="maintable">
      <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
        <tr>
          <td class="headers">{#Global_Categ#}</td>
          <td class="headers">{#Global_Name#} 1 ({$language.name.$lc})</td>
          <td class="headers">{#Global_Name#} 2 ({$language.name.$lc})</td>
          <td class="headers">{#Global_Name#} 3 ({$language.name.$lc})</td>
          <td class="headers">{#Global_Actions#}</td>
        </tr>
        {foreach from=$categ_addons item=ca}
          <tr class="{cycle values='second,first'}">
            <td><strong>{$ca->CategInfo->Name} </strong></td>
            <td>
              <input type="hidden" name="id[{$ca->Id}]" value="{$ca->CategInfo->Id}" />
              <input type="hidden" name="name[{$ca->Id}]" value="{$ca->CategInfo->Name}" />
              <input style="width:180px" class="input" type="text" name="Teile_1_Name_1[{$ca->Id}]" value="{$ca->Teile_1_Name_1}" />
              <input type="hidden" name="name_old[{$ca->Id}]" value="{$ca->Teile_1_Name_1}" />
            </td>
            <td>
              <input style="width:180px" class="input" type="text" name="Teile_2_Name_1[{$ca->Id}]" value="{$ca->Teile_2_Name_1}" />
              <input type="hidden" name="name_old_2[{$ca->Id}]" value="{$ca->Teile_2_Name_1}" />
            </td>
            <td>
              <input style="width:180px" class="input" type="text" name="Teile_3_Name_1[{$ca->Id}]" value="{$ca->Teile_3_Name_1}" />
              <input type="hidden" name="name_old_3[{$ca->Id}]" value="{$ca->Teile_3_Name_1}" />
            </td>
            <td>
              <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#Shop_CDelCategAddon#}');" href="index.php?do=shop&amp;sub=categories_addons&amp;del=1&amp;categ={$ca->Id}&amp;shop_categ={$ca->CategInfo->Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                {if perm('shop_catdeletenew')}
                <a title="{#Global_CategEdit#}" class="colorbox" href="?do=shop&amp;noframes=1&amp;sub=edit_categ&amp;id={$ca->CategInfo->Id}&amp;parent=0"><img class="absmiddle stip" title="{$lang.Global_CategEdit|sanitize}" src="{$imgpath}/edit.png" border="0" /></a>
                {/if}
            </td>
          </tr>
        {/foreach}
      </table>
    </div>
    <br />
    <input type="hidden" name="lc" value="{$lc}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
  <br />
  <br />
  <a name="new"></a>
  <div class="header">{#GlobalAddCateg#}</div>
  <form method="post" action="" onsubmit="return checkNew();">
    <table width="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td width="150" class="row_left"><label for="Categ">{#Global_Categ#}</label></td>
        <td class="row_right">
          <select name="Categ" id="Categ" class="input" style="width: 200px">
            {foreach from=$shop_search_small_categs item=scs}
              <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if $smarty.request.id == $scs->catid}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left"><label for="first">{#Global_Name#} 1</label></td>
        <td class="row_right"><input style="width:200px" class="input" type="text" name="Teile_1_Name_1" id="first" /> {#Shop_CategAddonNewT1#} </td>
      </tr>
      <tr>
        <td class="row_left"><label for="second">{#Global_Name#} 2</label></td>
        <td class="row_right"><input style="width:200px" class="input" type="text" name="Teile_2_Name_1" id="second" /> {#Shop_CategAddonNewT2#}</td>
      </tr>
      <tr>
        <td class="row_left"><label for="third">{#Global_Name#} 3</label></td>
        <td class="row_right"><input style="width:200px" class="input" type="text" name="Teile_3_Name_1" id="third" /> {#Shop_CategAddonNewT3#}</td>
      </tr>
    </table>
    <input type="hidden" name="new" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
{/if}
