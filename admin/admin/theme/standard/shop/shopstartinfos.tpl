<div class="header">{#ShopInfoM#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form  name="shopinfos" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td class="headers" colspan="2"> {$language.name.1|upper}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSN#} </td>
      <td class="row_right"><input type="text" class="input" style="width: 200px" name="Name_1" value="{$row->Name_1}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSNShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="Name_1_zeigen" value="1" {if $row->Name_1_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Name_1_zeigen" value="0" {if $row->Name_1_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMT#}</td>
      <td class="row_right">{$ShopInfo_1}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMTShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="StartText_1_zeigen" value="1" {if $row->StartText_1_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="StartText_1_zeigen" value="0" {if $row->StartText_1_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="headers"> {$language.name.2|upper}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSN#} </td>
      <td class="row_right"><input type="text" class="input" style="width: 200px" name="Name_2" value="{$row->Name_2}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSNShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="Name_2_zeigen" value="1" {if $row->Name_2_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Name_2_zeigen" value="0" {if $row->Name_2_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMT#} </td>
      <td class="row_right">{$ShopInfo_2}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMTShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="StartText_2_zeigen" value="1" {if $row->StartText_2_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="StartText_2_zeigen" value="0" {if $row->StartText_2_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="headers"> {$language.name.3|upper}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSN#} </td>
      <td class="row_right"><input type="text" class="input" style="width: 200px" name="Name_3" value="{$row->Name_3}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoSNShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="Name_3_zeigen" value="1" {if $row->Name_3_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Name_3_zeigen" value="0" {if $row->Name_3_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMT#}</td>
      <td class="row_right">{$ShopInfo_3}</td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#ShopInfoMTShow#}</td>
      <td class="row_right">
        <label><input type="radio" name="StartText_3_zeigen" value="1" {if $row->StartText_3_zeigen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="StartText_3_zeigen" value="0" {if $row->StartText_3_zeigen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Global_savesettings#}" />
</form>
