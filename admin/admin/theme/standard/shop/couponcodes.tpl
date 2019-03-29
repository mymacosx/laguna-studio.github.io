<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$codes item=c name=dp}
    $('#dateinput_{$c->Id}').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    {/foreach}
    {foreach from=$nc item=n}
    {assign var=xcnn value=$xcnn+1}
    $('#dateinput2_{$xcnn}').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    {/foreach}

    $('.selectman').change(function() {
        var id = $(this).attr('id');
        if ($(this).val() === 'pro') {
            $('#def' + id).hide();
            $('#her' + id).show();
        } else {
            $('#her' + id).hide();
            $('#def' + id).show();
        }
    });
});
//-->
</script>

<div class="header">{#Shop_couponcodes_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <div class="header_inf">
    <select class="input" name="show">
      <option value="all" {if $smarty.request.show == all || empty($smarty.request.show)}selected="selected"{/if}>{#Global_All#}</option>
      <option value="free" {if $smarty.request.show == free}selected="selected"{/if}>{#CouponFree#}</option>
      <option value="red" {if $smarty.request.show == red}selected="selected"{/if}>{#CouponRed#}</option>
    </select>
    <select class="input" name="limit">
      {section name=lim loop=100 step=10}
        <option value="{$smarty.section.lim.index+10}" {if $smarty.section.lim.index+10 == $smarty.request.limit}selected="selected" {/if}> {#DataRecords#} {$smarty.section.lim.index+10}</option>
      {/section}
    </select>
    <input type="submit" class="button" value="{#Global_Show#}" />
  </div>
</form>
<form action="" method="post" name="kform">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_coupon#}</td>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_reduction#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cmin#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cmulti#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cguest#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cgtill#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Manufacturer#}</th>
        <th class="headers" nowrap="nowrap">{#Nav_Other#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Info#}</th>
        <th width="40px" class="headers"><label class="stip" title="{$lang.Global_SelAll|sanitize}"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></label></th>
      </tr>
      {foreach from=$codes item=c}
        <tr class="{cycle values='first,second'}">
          <td width="80" class="row_spacer">
            <input class="input" name="Code[{$c->Id}]" type="text" size="9" maxlength="10" value="{$c->Code}" {if $c->Eingeloest>1}disabled{/if} />
            {if $c->Eingeloest>1}
              <input type="hidden" name="Code[{$c->Id}]" value="{$c->Code}" />
            {/if}
          </td>
          <td align="center" nowrap="nowrap" class="row_spacer">
            <input class="input" name="Wert[{$c->Id}]" type="text" value="{if $c->Typ == 'pro'}{$c->Wert}{else}{$c->Wert|string_format: '%.2f'}{/if}" size="5" maxlength="8" {if $c->Eingeloest>1}disabled="disabled"{/if} />
            <select class="input selectman" id="sel{$c->Id}" name="Typ[{$c->Id}]" {if $c->Eingeloest>1}disabled="disabled"{/if}>
              <option value="pro" {if $c->Typ == 'pro'}selected="selected"{/if}>%</option>
              <option value="wert" {if $c->Typ == 'wert'}selected="selected"{/if}>-</option>
            </select>
          </td>
          <td align="center" class="row_spacer"><input class="input" name="MinBestellwert[{$c->Id}]" type="text" size="8" maxlength="10" value="{$c->MinBestellwert|string_format: '%.2f'}" {if $c->Eingeloest>1}disabled="disabled" readonly="readonly"{/if} /></td>
          <td align="center"class="row_spacer" nowrap="nowrap">
            <label><input type="radio" name="Endlos[{$c->Id}]" value="1" {if $c->Endlos == 1}checked="checked"{/if} {if $c->Eingeloest>1}disabled="disabled"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Endlos[{$c->Id}]" value="0" {if $c->Endlos != 1}checked="checked"{/if} {if $c->Eingeloest>1}disabled="disabled"{/if} />{#No#}</label>
          </td>
          <td align="center" nowrap="nowrap" class="row_spacer">
            <label><input type="radio" name="Gastbestellung[{$c->Id}]" value="1" {if $c->Gastbestellung == 1}checked="checked"{/if} {if $c->Eingeloest>1}disabled="disabled"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Gastbestellung[{$c->Id}]" value="0" {if $c->Gastbestellung != 1}checked="checked"{/if} {if $c->Eingeloest>1}disabled="disabled"{/if} />{#No#}</label>
          </td>
          <td align="center" nowrap="nowrap" class="row_spacer">
            {if $c->Eingeloest>1}
              {$c->GueltigBis|date_format: '%d.%m.%Y'}
            {else}
              <input class="input" id="dateinput_{$c->Id}" name="GueltigBis[{$c->Id}]" type="text" size="8" maxlength="10" value="{$c->GueltigBis|date_format: '%d.%m.%Y'}" {if $c->Eingeloest>1}disabled="disabled"{/if} />
            {/if}
          </td>
          <td nowrap="nowrap" class="row_spacer">
            <div id="hersel{$c->Id}"{if $c->Typ == 'wert'} style="display: none;"{/if}>
              <select name="Hersteller[{$c->Id}][]" size="5" multiple class="input" style="width: 200px">
                {foreach from=$hersteller item=h}
                    <option value="{$h.Id}" {if in_array($h.Id, $c->Hersteller)}selected="selected"{/if}>{$h.Name|sanitize}</option>
                {/foreach}
              </select>
            </div>
            <div id="defsel{$c->Id}" style="text-align: center;{if $c->Typ == 'pro'} display: none;{/if}">
              {#Global_All#}
            </div>
          </td>
          <td nowrap="nowrap" class="row_spacer"><textarea cols="" rows="" class="input" style="min-width: 150px;width: 98%;height: 60px" name="CommentCupon[{$c->Id}]">{$c->CommentCupon|sanitize}</textarea></td>
          <td nowrap="nowrap" class="row_spacer">
            <a class="stip" title="{$c->InfT|sanitize}" href="javascript: void(0)"><img class="absmiddle" src="{$imgpath}/info.png" alt="" /></a>
            {include file="$incpath/shop/couponorders_inf.tpl"}
          </td>
          <td width="40px" class="row_spacer">
            <label class="stip" title="{$lang.Global_Delete|sanitize}">
              <input class="absmiddle" name="Del[{$c->Id}]" type="checkbox" id="Del[]" value="1" />
              <img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" />
            </label>
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
{if !empty($navi)}
  <div class="navi_div"> {$navi} </div>
{/if}
<br />
<form method="post" action="">
  <fieldset>
    <legend>{#Shop_couponcodes_new#}</legend>
    <table width="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_coupon#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_reduction#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cmin#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cmulti#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cguest#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Shop_couponcodes_cgtill#}</th>
        <th width="2%" class="headers" nowrap="nowrap">{#Manufacturer#}</th>
        <th align="center" class="headers">{#Nav_Other#}</th>
      </tr>
      {foreach from=$nc item=n}
        {assign var=cnn value=$cnn+1}
        <tr class="{cycle values='second,first'}">
          <td><input class="input" size="9" maxlength="10" type="text" name="Code[{$cnn}]" value="{$n|upper}" /></td>
          <td nowrap="nowrap" class="row_spacer">
            <input class="input" name="Wert[{$cnn}]" type="text" value="10" size="5" maxlength="8" />
            <select class="input selectman" id="add{$cnn}" name="Typ[{$cnn}]">
              <option value="pro">%</option>
              <option value="wert">-</option>
            </select>
          </td>
          <td align="center" class="row_spacer"><input class="input" name="MinBestellwert[{$cnn}]" type="text" size="8" maxlength="10" value="0.00" /></td>
          <td align="center" nowrap="nowrap" class="row_spacer">
            <label><input type="radio" name="Endlos[{$cnn}]" value="1" checked="checked" />{#Yes#}</label>
            <label><input type="radio" name="Endlos[{$cnn}]" value="0" />{#No#}</label>
          </td>
          <td align="center" nowrap="nowrap" class="row_spacer">
            <label><input type="radio" name="Gastbestellung[{$cnn}]" value="1" checked="checked" />{#Yes#}</label>
            <label><input type="radio" name="Gastbestellung[{$cnn}]" value="0" />{#No#}</label>
          </td>
          <td nowrap="nowrap" class="row_spacer"><input id="dateinput2_{$cnn}" class="input" name="GueltigBis[{$cnn}]" type="text" size="12" maxlength="10" value="{$new_till|date_format: '%d.%m.%Y'}" /></td>
          <td nowrap="nowrap" class="row_spacer">
            <div id="heradd{$cnn}">
              <select name="Hersteller[{$cnn}][]" size="5" multiple class="input" style="width: 200px">
                {foreach from=$hersteller item=h}
                    <option value="{$h.Id}" selected="selected">{$h.Name|sanitize}</option>
                {/foreach}
              </select>
            </div>
            <div id="defadd{$cnn}" style="text-align: center; display: none;">
              {#Global_All#}
            </div>
          </td>
          <td nowrap="nowrap" class="row_spacer"><textarea cols="" rows="" class="input" style="min-width: 150px;width: 98%;height: 60px" name="CommentCupon[{$cnn}]"></textarea></td>
        </tr>
      {/foreach}
    </table>
    <input class="button" type="submit" value="{#Save#}" />
    <input name="new" type="hidden" id="new" value="1" />
  </fieldset>
</form>
