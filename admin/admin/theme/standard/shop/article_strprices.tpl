<div class="popbox">
  <form name="kform" method="post" action="index.php?do=shop&amp;sub=article_stprices&amp;id={$smarty.request.id}&amp;noframes=1&amp;save=1">
    <div class="header">{#Shop_articles_stprices#}</div>
    <div class="header_inf">{#Shop_articles_stprices_inf#}</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100" class="headers">{#Shop_articles_stprices_from#}</td>
        <td width="100" class="headers">{#Shop_articles_stprices_till#}</td>
        <td class="headers">{#Shop_articles_stprices_red#}</td>
        <td class="headers"><label class="stip" title="{$lang.Global_SelAll|sanitize}"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
      </tr>
      {foreach from=$stprices item=s}
        <tr class="{cycle values='second,first'}">
          <td width="100"><input class="input" style="width: 80px" type="text" name="Von[{$s.Id}]" value="{$s.Von}"/></td>
          <td width="100"><input class="input" style="width: 80px" type="text" name="Bis[{$s.Id}]" value="{$s.Bis}"/></td>
          <td width="200">
            <input class="input" style="width: 80px" type="text" name="Wert[{$s.Id}]" value="{$s.Wert}"/>
            <select name="Operand[{$s.Id}]">
              <option value="pro"{if $s.Operand == 'pro'} selected="selected"{/if}>%</option>
              <option value="wert"{if $s.Operand == 'wert'} selected="selected"{/if}>{#GlobalValue#}</option>
            </select>
            <input type="hidden" name="Id[{$s.Id}]" value="{$s.Id}" />
          </td>
          <td><label class="stip" title="{$lang.Global_Delete|sanitize}"><input class="absmiddle" name="Del[{$s.Id}]" type="checkbox" id="Del[]" value="1" />{#Global_Delete#}</label></td>
          {assign var=min_next value=$s.Bis+1}
          {assign var=max_next value=$s.Bis+10}
          {assign var=percent value=$s.Prozent+1}
        </tr>
      {/foreach}
      <tr class="second">
        <td><input class="input newform" style="width: 80px" type="text" name="VonNeu" onclick="this.value = '{$min_next|default:'2'}';" value="" /></td>
        <td><input class="input newform" style="width: 80px" type="text" name="BisNeu" onclick="this.value = '{$max_next|default:'10'}';" value="" /></td>
        <td>
          <input class="input newform" style="width: 80px" type="text" name="WertNeu" onclick="this.value = '{$percent|jsnum|default:'10.00'}';" value=""/>
          <select name="OperandNeu">
            <option value="pro">%</option>
            <option value="wert">{#GlobalValue#}</option>
          </select>
          {#Shop_articles_stprices_new#}
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <div class="button_save_div">
      <input type="submit" class="button" value="{#Save#}" />
      <input type="button" class="button" onclick="closeWindow();" value="{#Close#}" />
    </div>
  </form>
</div>
