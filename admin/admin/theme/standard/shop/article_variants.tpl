{if $vars_for_copy}
  <fieldset>
    <legend>{#Shop_variants_copy#}</legend>
    <form method="post">
      <select name="copyvar" class="input" style="width: 300px">
        {foreach from=$vars_for_copy item=vtc}
          {if $flag != $vtc->arr}
            <option value="{$vtc->arr}">{$vtc->Name}</option>
          {/if}
          {assign var=flag value=$vtc->arr}
        {/foreach}
      </select>
      <input type="hidden" name="copy" value="1" />
      <input type="submit" class="button" value="{#Shop_variants_copyB#}" />
    </form>
  </fieldset>
  <br />
{/if}
{if !$variant_categs}
  <div class="header_inf"> {#Shop_order_vars_nocategs#} <br />
    <br />
    <a href="?do=shop&amp;sub=categvariants&amp;id={$smarty.request.cat}&amp;noframes=1&amp;name={$smarty.request.name|replace: '?KeepThis=true': ''}&amp;back=1&amp;bid={$smarty.request.id}"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> <strong>{#Shop_variants_categsN#}</strong></a> </div>
    {else}
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    {foreach from=$variant_categs item=vc}
      <form name="kform" method="post" action="index.php?do=shop&amp;sub=article_variants&amp;id={$smarty.request.id}&amp;cat={$smarty.request.cat}&amp;noframes=1&amp;save=1">
        {assign var=fs value='first,second'}
        <tr>
          <td align="center" class="headers">{$vc.Name_1} ({$language.name.1})</td>
          <td align="center" class="headers">{$vc.Name_2} ({$language.name.2})</td>
          <td align="center" class="headers">{$vc.Name_3} ({$language.name.3})</td>
          <td width="120" align="center" class="headers">{#Products_price#}</td>
          <td width="120" align="center" class="headers">{#Shop_order_vars_weight#}</td>
          <td width="50" align="center" class="headers">{#Global_Position#}</td>
          <td width="50" align="center" class="headers">{#Shop_articles_avbl#}</td>
          <td width="50" align="center" class="headers">{#Shop_order_vars_presel#}</td>
          <td class="headers">&nbsp;</td>
        </tr>
        {foreach from=$vc.vars item=v}
          <tr class="{cycle name=$vc.Id values=$fs}">
            <td><input class="input" style="width: 100%" name="Name_1[{$v.Id}]" type="text" value="{$v.Name_1}" /></td>
            <td><input class="input" style="width: 100%" name="Name_2[{$v.Id}]" type="text" value="{$v.Name_2}" /></td>
            <td><input class="input" style="width: 100%" name="Name_3[{$v.Id}]" type="text" value="{$v.Name_3}" /></td>
            <td width="120" align="center">
              <select name="Operant[{$v.Id}]" class="input" style="width: 40px" id="Operant[{$v.Id}]" />
          <option value="+" {if $v.Operant == '+'}selected="selected" {/if}>+</option>
          <option value="-" {if $v.Operant == '-'}selected="selected" {/if}>-</option>
          </select>
          <input class="input" style="width: 50px" name="Wert[{$v.Id}]" type="text" value="{$v.Wert}" /></td>
          <td width="120" align="center">
            <select name="GewichtOperant[{$v.Id}]" class="input" style="width: 40px" id="GewichtOperant[{$v.Id}]" />
          <option value="+" {if $v.GewichtOperant == '+'}selected="selected" {/if}>+</option>
          <option value="-" {if $v.GewichtOperant == '-'}selected="selected" {/if}>-</option>
          </select>
          <input class="input" style="width: 50px" name="Gewicht[{$v.Id}]" type="text" value="{$v.Gewicht}" /></td>
          <td width="50" align="center">
            <input name="Position[{$v.Id}]" type="text" class="input" style="width: 50px" value="{$v.Position}" />
            <input name="Id[{$v.Id}]" type="hidden" value="{$v.Id}" /></td>
          <td align="center"><input class="input" style="width: 50px" name="Bestand[{$v.Id}]" type="text" value="{$v.Bestand}" /></td>
          <td align="center"><input name="Vor[{$vc.Id}]" type="radio" value="{if $v.Vorselektiert == 1}{$v.Id}{/if}" onclick="this.value = '{$v.Id}';" {if $v.Vorselektiert == 1}checked="checked" {/if} /></td>
          <td>
            <label class="stip" title="{$lang.Global_Delete|sanitize}"><input class="absmiddle" name="Del[{$v.Id}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label>
          </td>
          </tr>
        {/foreach}
        <tr style="background-color: #F4A6A6">
          <td colspan="3"><input class="input newform" style="width: 100%" name="NewName[{$vc.Id}]" type="text" /></td>
          <td width="120" align="center">
            <select name="NewOperant[{$vc.Id}]" class="input newform" style="width: 40px;">
              <option value="+" {if $v.Operant == '+'}selected="selected" {/if}>+</option>
              <option value="-" {if $v.Operant == '-'}selected="selected" {/if}>-</option>
            </select>
            <input class="input newform" style="width: 50px;" name="WertNeu[{$vc.Id}]" type="text" /></td>
          <td width="120" align="center">
            <select name="GewichtOperantNeu[{$vc.Id}]" class="input newform" style="width: 40px;">
              <option value="+" {if $v.Operant == '+'}selected="selected" {/if}>+</option>
              <option value="-" {if $v.Operant == '-'}selected="selected" {/if}>-</option>
            </select>
            <input class="input newform" style="width: 50px;" name="GewichtNeu[{$vc.Id}]" type="text" /></td>
          <td width="50" align="center"><input class="input newform" style="width: 50px;" name="PositionNeu[{$vc.Id}]" type="text" /></td>
          <td colspan="3" align="center"><input type="submit" class="button" value="{#Save#}" /></td>
        </tr>
        <tr>
          <td colspan="9">&nbsp;</td>
        </tr>
      </form>
      {assign var=count value=0}
    {/foreach}
  </table>
{/if}
<div align="center"><input class="button" type="button" name="button" onclick="closeWindow();" value="{#Close#}" /></div>
