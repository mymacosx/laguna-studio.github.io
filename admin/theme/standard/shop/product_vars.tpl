<script type="text/javascript">
<!-- //
function new_price() {
    var OutpuNetto = '';
    var addValue = new Array();
    var Search = new Array();
    {foreach from=$vars_categs item=vc}
    {foreach from=$vc.vars item=var}
    addValue[{$var.Id}] = {$var.Price|replace:',':'.'};
    {/foreach}
    var x = document.getElementById('varopt_{$vc.Kat_Id}').options[document.getElementById('varopt_{$vc.Kat_Id}').selectedIndex].value;Search[{$vc.Kat_Id}] = addValue[x];
    {/foreach}
    {if $p.Preis_Liste|jsnum != '0.00'}
    var ListPrice = {$p.Preis_Liste|replace:',':'.'}{foreach from=$vars_categs item=vc} + Search[{$vc.Kat_Id}]{/foreach};
    var OutputList = ListPrice.toFixed(2);
    {/if}
    var Updater = {$p.Preis|jsnum}{foreach from=$vars_categs item=vc} + Search[{$vc.Kat_Id}]{/foreach};
    var Output = Updater.toFixed(2);
    {if !empty($p.product_ust)}
    {if $p.EinheitCount && $p.EinheitCount != '0.00'}
    {if !empty($p.EinheitBezug)}
    {assign var=Mth value=$p.EinheitBezug/$p.EinheitCount}
    var Mth = '{$Mth}';
    var Mth = Mth.replace(/,/,'.');
    var ProdCount = {$p.EinheitCount}.toFixed(3);
    var ProdOnce = (Output * Mth).toFixed(2);
    var OutpuNetto = (Output / {$p.product_ust_js}).toFixed(2);
    {else}
    var ProdCount = {$p.EinheitCount}.toFixed(3);
    var ProdOnce = (Output / ProdCount).toFixed(2);
    var OutpuNetto = (Output / {$p.product_ust_js}).toFixed(2);
    {/if}
    if(document.getElementById('hidden_count').value != '') {
        document.getElementById('prodonce_display').innerHTML = ProdOnce.replace(/\./,',');
    }
    {/if}
    if(document.getElementById('hidden_vat').value != '') {
        {if $p.EinheitCount && $p.EinheitCount != '0.00'}
        {if !empty($p.EinheitBezug)}
        document.getElementById('prodonce_display_netto').innerHTML = (OutpuNetto*Mth).toFixed(2).replace(/\./,',');
        {else}
        document.getElementById('prodonce_display_netto').innerHTML = (OutpuNetto / {$p.EinheitCount}).toFixed(2).replace(/\./,',');
        {/if}
        {/if}
        if(OutpuNetto) {
            document.getElementById('netto_display').innerHTML = OutpuNetto.replace(/\./,',');
        }
    }
    {/if}
    var Out_Orig = Output;
    document.getElementById('new_price').innerHTML = Output.replace(/\./,',');
    {if $p.Preis_Liste|jsnum != '0.00'}
    document.getElementById('price_list').innerHTML = OutputList.replace(/\./,',');
    var LP_orig = ListPrice.toFixed(2);
    var Saved_Val = (OutputList - Output).toFixed(2);
    document.getElementById('you_saved').innerHTML = Saved_Val.replace(/\./,',');
    {/if}
}

$(window).load(function() {
    new_price();
});
//-->
</script>

{if !empty($vars_categs)}
  <div class="shop_product_vars">
    <strong>{#Shop_variants_d#}</strong>
    <br />
    <table width="100%" cellpadding="0" cellspacing="1">
      {foreach from=$vars_categs item=vc}
        <tr>
          <td width="150"><label for="varopt_{$vc.Kat_Id}"> {$vc.Kat_Name} </label></td>
          <td>
            <select{if $not_possible_to_buy == 1} disabled="disabled"{/if} onchange="new_price('{$vc.Kat_Id}');" id="varopt_{$vc.Kat_Id}" name="mod[]" class="input" style="width: 200px; margin-top: 1px">
              {foreach from=$vc.vars item=var}
                <option value="{$var.Id}" {if $var.Vorselektiert == 1}selected="selected"{/if}>{$var.VarName|sanitize} {if $var.Price != '0.00'}({if $var.Price > '0'}+{else}-{/if} {$var.Price|numf|replace: '.': ','|replace: '-': ''} {$currency_symbol}){/if}</option>
              {/foreach}
            </select>
            {if !empty($vc.Kat_Beschreibung)}
              &nbsp; <img style="cursor: pointer" title="{$vc.Kat_Beschreibung|tooltip}" class="stip absmiddle" src="{$imgpath}/page/help.png" border="0" alt="" />
            {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
{/if}
