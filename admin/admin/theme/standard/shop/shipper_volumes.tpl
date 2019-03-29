<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['kform'].submit();
    }
});

$(document).ready(function() {
    $('#kform').validate({
	rules: {
            {foreach from=$volumes item=v}
	    'Von[{$v->Id}]': { required: true, number: true },
	    'Bis[{$v->Id}]': { required: true, number: true },
	    'Gebuehr[{$v->Id}]': { required: true, number: true },
            {/foreach}
            save: { required: true }
	},
	messages: {
            {foreach from=$volumes item=v}
	    'Von[{$v->Id}]': { required: '', number: '' },
	    'Bis[{$v->Id}]': { required: '', number: '' },
	    'Gebuehr[{$v->Id}]': { required: '', number: '' },
            {/foreach}
            save: { required: '' }
	}
    });
});
//-->
</script>

<div class="popbox">
  <div class="header">{$row->Name|sanitize}</div>
  <div class="subheaders">{#Shop_shipper_w_editInf#}</div>
  <form method="post" name="kform" id="kform" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td width="60" class="headers">{#Shop_shipper_w_from#}</td>
        <td width="60" align="center" class="headers">{#Shop_shipper_w_to#}</td>
        <td width="60" align="center" class="headers">{#Shop_shipper_NG#}</td>
        <td class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
      </tr>
      {foreach from=$volumes item=v name=vols}
        <tr class="{cycle values='first,second'}">
          <td><input name="Von[{$v->Id}]" type="text" class="input" value="{$v->Von}" size="5" maxlength="10" /></td>
          <td align="center"><input name="Bis[{$v->Id}]" type="text" class="input" value="{$v->Bis}" size="5" maxlength="10" /></td>
          <td align="center"><input name="Gebuehr[{$v->Id}]" type="text" class="input" value="{$v->Gebuehr}" size="5" maxlength="10" /></td>
          <td>
            {if !$smarty.foreach.vols.first}
              <label><input name="Del[{$v->Id}]" type="checkbox" id="Del[]" value="1" />{#Global_Delete#}</label>
              {/if}
            &nbsp;
          </td>
        </tr>
      {/foreach}
    </table>
    <input type="submit" name="button" class="button" value="{#Save#}" />
    <input name="save" type="hidden" id="save" value="1" />
  </form>
  <br />
  <fieldset>
    <legend>{#Global_Datasheet#}</legend>
    <form method="post" name="kforma" action="">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr>
          <td width="60" class="headers">{#Shop_shipper_w_from#}</td>
          <td width="60" align="center" class="headers">{#Shop_shipper_w_to#}</td>
          <td class="headers">{#Shop_shipper_NG#}</td>
        </tr>
        <tr class="{cycle values='first,second'}">
          <td><input name="Von_Disabled" disabled="disabled" type="text" class="input" value="{$new_f|numf}" size="5" maxlength="10" /></td>
          <td align="center"><input name="Bis" type="text" class="input" value="{$new_t|numf}" size="5" maxlength="10" /></td>
          <td><input name="Gebuehr" type="text" class="input" value="{$new_g|numf}" size="5" maxlength="10" /></td>
        </tr>
      </table>
      <input type="submit" name="button" class="button" value="{#Save#}" />
      <input name="new" type="hidden" value="1" />
      <input name="GebuehrCheck" type="hidden" id="GebuehrCheck" value="{$new_g|numf}" />
      <input name="Von" type="hidden" value="{$new_f|numf}" />
    </form>
  </fieldset>
</div>
