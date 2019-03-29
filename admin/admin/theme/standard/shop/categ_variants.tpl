<div class="header">{#Shop_categvars_hf#} &bdquo;{$smarty.get.name|sanitize}&rdquo;</div>
<div class="header_inf">{#Shop_categvars_inf#}</div>
<form method="post" action="" name="kform">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    {if $vars}
      <tr>
        <td class="headers">&nbsp;</td>
        <td class="headers">{$language.name.1}</td>
        <td class="headers">{$language.name.2}</td>
        <td class="headers">{$language.name.3}</td>
        <td align="center" class="headers">{#Global_Position#}</td>
        <td align="center" class="headers">{#Global_Active#}</td>
        <td class="headers"><label class="stip" title="{$lang.Global_SelAll|sanitize}"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></label></td>
      </tr>
    {/if}
    {foreach from=$vars item=v name=va}
      <tr class="{cycle values='second,first'}">
        <td>{#Global_Name#}</td>
        <td><input class="input" type="text" style="width: 180px" name="Name_1[{$v->Id}]" id="textfield" value="{$v->Name_1}" /></td>
        <td><input class="input" type="text" style="width: 180px" name="Name_2[{$v->Id}]" id="textfield" value="{$v->Name_2}" /></td>
        <td><input class="input" type="text" style="width: 180px" name="Name_3[{$v->Id}]" id="textfield" value="{$v->Name_3}" /></td>
        <td align="center"><input class="input" type="text" style="width: 30px" name="Position[{$v->Id}]" id="textfield" value="{$v->Position}" /></td>
        <td align="center">
          <label><input type="radio" name="Aktiv[{$v->Id}]" value="1" {if $v->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$v->Id}]" value="0" {if $v->Aktiv == 0}checked="checked"{/if} />{#No#}</label>
        </td>
        <td><label class="stip" title="{$lang.Global_Delete|sanitize}"><input class="absmiddle" name="Del[{$v->Id}]" type="checkbox" id="Del[]" value="1" />{#Global_Delete#}</label></td>
      </tr>
      <tr class="{cycle values='second,first'}">
        <td>{#Global_descr#}</td>
        <td><textarea cols="" rows="" class="input" style="width: 180px; height: 80px" name="Beschreibung_1[{$v->Id}]">{$v->Beschreibung_1|sanitize}</textarea></td>
        <td><textarea cols="" rows="" class="input" style="width: 180px; height: 80px" name="Beschreibung_2[{$v->Id}]">{$v->Beschreibung_2|sanitize}</textarea></td>
        <td><textarea cols="" rows="" class="input" style="width: 180px; height: 80px" name="Beschreibung_3[{$v->Id}]">{$v->Beschreibung_3|sanitize}</textarea></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      {if $smarty.foreach.va.last}
        {assign var=newpos value=$v->Position+1}
      {/if}
    {/foreach}
    <tr>
      <td colspan="7" class="headers">{#Global_NewCateg#}</td>
    </tr>
    <tr class="second">
      <td>{#Global_Name#}</td>
      <td><input class="input newform" type="text" style="width: 180px" name="Name_1_n" /></td>
      <td><input class="input newform" type="text" style="width: 180px" name="Name_2_n" /></td>
      <td><input class="input newform" type="text" style="width: 180px" name="Name_3_n" /></td>
      <td align="center"><input class="input newform" type="text" style="width: 30px" name="Position_n" value="{$newpos|default:1}" /></td>
      <td align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="second">
      <td>{#Global_descr#}</td>
      <td><textarea cols="" rows="" class="input newform" style="width: 180px; height: 80px" name="Beschreibung_1_n" /></textarea></td>
      <td><textarea cols="" rows="" class="input newform" style="width: 180px; height: 80px" name="Beschreibung_2_n" /></textarea></td>
      <td><textarea cols="" rows="" class="input newform" style="width: 180px; height: 80px" name="Beschreibung_3_n" /></textarea></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <input class="button" type="submit" name="button" id="button" value="{#Save#}" />
  {if $smarty.request.back == 1}
    <input class="button" type="button" value="{#Shop_categvars_back#}" onclick="location.href = 'index.php?do=shop&sub=article_variants&id={$smarty.request.bid}&cat={$smarty.request.id}&name={$smarty.request.name}&noframes=1';" />
  {/if}
  <input class="button" type="button" name="button" onclick="closeWindow();" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
