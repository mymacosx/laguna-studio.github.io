{assign var='langcode' value=$smarty.request.lc|default:1}
<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['editForm'].submit();
    }
});

$(document).ready(function() {
    $('#editForm').validate( {
	rules: {
            {if $smarty.request.Id == 6}
            customer_nr: { required: true, minlength: 4, number: true },
            project_id: { required: true, minlength: 4, number: true },
            project_pass: { required: true, minlength: 4 },
            {/if}
	    Name: { required: true,minlength: 2 },
	    Versanddauer: { required: true, minlength: 1 },
	   'Laender[]': { required: true },
	   'Gruppen[]': { required: true },
	   'Versandarten[]': { required: true }
        },
        messages: {
            'Laender[]': { required: '{#Shop_shipper_NoGC#}' },
            'Gruppen[]': { required: '{#Shop_shipper_NoGI#}' },
            'Versandarten[]': { required: '{#Shop_payment_SM#}' }
        }
    });
});

function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    var resize = document.getElementById('resizeUpload_' + divid).value;
    $.ajaxFileUpload({
	url: 'index.php?do=shop&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
	secureuri: false,
	fileElementId: 'fileToUpload_' + divid,
	dataType: 'json',
	success: function (data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                }
	    }
	},
	error: function (data, status, e) {
	    document.getElementById('UpInf_' + divid).innerHTML = e;
	}
    });
    return false;
}
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        location.href='index.php?do=shop&sub=editpaymentmethod&Id={$smarty.request.Id}&noframes=1&lc=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected = true;
    }
}
//-->
</script>

<div class="popbox">
  <div class="header">{#Shop_payment_edit#} - {$row->Name|sanitize} ({$language.name.$langcode})</div>
  <div class="header_inf">
    <form method="post" action="">
      <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
        <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_payment_editin#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_payment_editin#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_payment_editin#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
      </select>
      <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
    </form>
  </div>
  <form method="post" action="" name="editForm" id="editForm" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="3" cellspacing="0">
      {if $langcode == 1}
        <tr>
          <td width="250" class="row_left">{#SavDataAllLangsT#}</td>
          <td class="row_right"><label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label></td>
        </tr>
      {/if}
      <tr>
        <td width="250" class="row_left">{#Global_Name#} ({$language.name.$langcode})</td>
        <td class="row_right"><input style="width: 200px" name="Name" type="text" class="input" id="Name" value="{$row->Name|sanitize}" /></td>
      </tr>
      <tr>
        <td width="250" class="row_left"> {#Global_descr#} ({$language.name.$langcode})</td>
        <td class="row_right">{$intro}</td>
      </tr>
      <tr>
        <td width="250" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_pdLongInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_pdLong#} ({$language.name.$langcode})</td>
        <td class="row_right">{$text}</td>
      </tr>
      {if $langcode == 1}
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_pInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_p#}</td>
          <td class="row_right">{$payinf}</td>
        </tr>
        {if $row->Icon}
          <tr>
            <td class="row_left">{#Image#}</td>
            <td class="row_right">
              <img src="../uploads/shop/payment_icons/{$row->Icon}" alt="" border="0" />
              <br />
              <input type="hidden" name="IconDelOld" value="{$row->Icon}" />
              <label><input type="checkbox" name="IconDel" value="1" />{#Global_ImgDel#}</label>
            </td>
          </tr>
        {/if}
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_ImgInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_imgNew#}</td>
          <td class="row_right">
            {if $writable == 1}
              <div id="UpInf_1"></div>
              <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
              <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
              <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
              <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('payment_icons', 1);" value="{#UploadButton#}" />
              {if perm('mediapool')}
                <input type="button" class="button" onclick="uploadBrowser('image', 'shop/payment_icons', 1);" value="{#Global_ImgSel#}" />
              {/if}
              <input type="hidden" name="newImg_1" id="newFile_1" />
            {else}
              {#Shop_payment_NW#}
            {/if}
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_aCInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_aC#} </td>
          <td class="row_right">
            <select name="Laender[]" size="8" multiple class="input" id="Laender" style="width: 200px">
              {foreach from=$countries item=c}
                <option value="{$c.Code}" {if in_array($c.Code,$countries_in)}selected="selected"{/if}>{$c.Name|sanitize}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_aGInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_aG#} </td>
          <td class="row_right">
            <select name="Gruppen[]" size="8" multiple class="input" id="Gruppen" style="width: 200px">
              {foreach from=$groups item=g}
                <option value="{$g->Id}" {if in_array($g->Id,$groups_in)}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_aSInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_aS#} </td>
          <td class="row_right">
            <select name="Versandarten[]" size="8" multiple class="input" id="Versandarten" style="width: 200px">
              {foreach from=$shipper item=s}
                <option value="{$s->Id}" {if in_array($s->Id,$shipper_in)}selected="selected"{/if}>{$s->Name|sanitize}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_payment_RZInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_RZ#} </td>
          <td class="row_right">
            <select class="input" name="KostenOperant" id="KostenOperant">
              <option value="+" {if $row->KostenOperant == '+'}selected="selected"{/if} />{#Shop_payment_ZZ#}</option>
              <option value="-" {if $row->KostenOperant == '-'}selected="selected"{/if} />{#Shop_payment_AZ#}</option>
            </select>
            <input style="width: 50px" name="Kosten" type="text" class="input" value="{$row->Kosten}">
            <select class="input" name="KostenTyp">
              <option value="pro" {if $row->KostenTyp == 'pro'}selected="selected"{/if} />{#Shop_payment_PR#}</option>
              <option value="wert" {if $row->KostenTyp == 'wert'}selected="selected"{/if} />{#Shop_payment_WE#}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_paymentmethod_lgInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_paymentmethod_lg#}</td>
          <td class="row_right"><input style="width: 50px" name="MaxWert" type="text" class="input" value="{$row->MaxWert}"></td>
        </tr>
        {if $smarty.request.Id == 1 || $smarty.request.Id == 2 || ($row->ZTyp == 'Eigen')}
        {else}
          {if $smarty.request.Id == 8}
            <tr>
              <td class="row_left">{#Id_Shop#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
          {elseif $smarty.request.Id == 9}
            <tr>
              <td class="row_left">{#Id_Payer#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="13" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Shop_payment_TM#}</td>
              <td class="row_right">
                <select class="input" name="Testmodus">
                  <option value="0" {if $row->Testmodus == 0} selected="selected"{/if}>{#WM_Test0#}</option>
                  <option value="1" {if $row->Testmodus == 1} selected="selected"{/if}>{#WM_Test1#}</option>
                  <option value="2" {if $row->Testmodus == 2} selected="selected"{/if}>{#WM_Test2#}</option>
                  <option value="3" {if $row->Testmodus == 3} selected="selected"{/if}>{#Sys_off#}</option>
                </select>
              </td>
            </tr>
          {elseif $smarty.request.Id == 13}
            <tr>
              <td class="row_left">{#Id_Shop#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="30" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
          {elseif $smarty.request.Id == 14}
            <tr>
              <td class="row_left">{#Id_Shop#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#CodeValut#}</td>
              <td class="row_right"><input name="Testmodus" type="text" class="input" style="width: 200px" value="{$row->Testmodus|sanitize}" maxlength="3" /></td>
            </tr>
          {elseif $smarty.request.Id == 15}
            <tr>
              <td class="row_left">{#Id_Shop#}</td>
              <td class="row_right"><input style="width: 200px" name="IdSeller" type="text" class="input" value="{$IdSeller|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Id_Payer#}</td>
              <td class="row_right"><input style="width: 200px" name="IdPayer" type="text" class="input" value="{$IdPayer|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SelectValut#}</td>
              <td class="row_right">
                <select class="input" name="Testmodus">
                  <option value="USD" {if $row->Testmodus == 'USD'} selected="selected"{/if}>{#Shop_curr2#}</option>
                  <option value="RUR" {if $row->Testmodus == 'RUR'} selected="selected"{/if}>{#Shop_curr1#}</option>
                  <option value="EUR" {if $row->Testmodus == 'EUR'} selected="selected"{/if}>{#Shop_curr3#}</option>
                  <option value="UAH" {if $row->Testmodus == 'UAH'} selected="selected"{/if}>{#Shop_curr4#}</option>
                </select>
              </td>
            </tr>
          {elseif $smarty.request.Id == 16}
            <tr>
              <td class="row_left">{#Id_Merchant#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#} #1</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#} #2</td>
              <td class="row_right"><input style="width: 200px" name="Testmodus" type="text" class="input" value="{$row->Testmodus|sanitize}" maxlength="20" /></td>
            </tr>
          {elseif $smarty.request.Id == 17}
            <tr>
              <td class="row_left">{#Id_Merchant#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 200px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SelectValut#}</td>
              <td class="row_right">
                <select class="input" name="Testmodus">
                  <option value="USD" {if $row->Testmodus == 'USD'} selected="selected"{/if}>{#Shop_curr2#}</option>
                  <option value="RUR" {if $row->Testmodus == 'RUR'} selected="selected"{/if}>{#Shop_curr1#}</option>
                  <option value="EUR" {if $row->Testmodus == 'EUR'} selected="selected"{/if}>{#Shop_curr3#}</option>
                  <option value="UAH" {if $row->Testmodus == 'UAH'} selected="selected"{/if}>{#Shop_curr4#}</option>
                </select>
              </td>
            </tr>
          {elseif $smarty.request.Id == 18}
            <tr>
              <td class="row_left">{#Id_Merchant#}</td>
              <td class="row_right"><input style="width: 100px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#} #1</td>
              <td class="row_right"><input name="IdSeller" type="text" class="input" style="width: 200px" value="{$IdSeller|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#} #2</td>
              <td class="row_right"><input name="IdPayer" type="text" class="input" style="width: 200px" value="{$IdPayer|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Shop_payment_TM#} </td>
              <td class="row_right">
                <label><input type="radio" name="Testmodus" value="1" {if $row->Testmodus == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input type="radio" name="Testmodus" value="0" {if $row->Testmodus == 0}checked="checked"{/if} />{#No#}</label>
              </td>
            </tr>
          {elseif $smarty.request.Id == 19}
            <tr>
              <td class="row_left">{#Id_Payer#}</td>
              <td class="row_right"><input style="width: 100px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" maxlength="20" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SekretKey#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 300px" value="{$row->Betreff|sanitize}" maxlength="40" /></td>
            </tr>
            <tr>
              <td class="row_left">{#SelectValut#} ISO 4217</td>
              <td class="row_right">
                <select class="input" name="Testmodus">
                  <option value="840" {if $row->Testmodus == '840'} selected="selected"{/if}>USD</option>
                  <option value="643" {if $row->Testmodus == '643'} selected="selected"{/if}>RUB</option>
                  <option value="978" {if $row->Testmodus == '978'} selected="selected"{/if}>EUR</option>
                  <option value="980" {if $row->Testmodus == '980'} selected="selected"{/if}>UAH</option>
                  <option value="974" {if $row->Testmodus == '974'} selected="selected"{/if}>BYR</option>
                </select>
              </td>
            </tr>
	  {elseif $smarty.request.Id == 20}
	    <tr>
	      <td class="row_left"></td>
	      <td class="row_right" style="color:red;">Денежной единицей в системе EasyPay является белорусский рубль.</td>
	   </tr>
            <tr>
              <td class="row_left">{#EP_MerNo#}</td>
              <td class="row_right"><input name="IdSeller" type="text" class="input" style="width: 200px" value="{$IdSeller|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#WebKey#}</td>
              <td class="row_right"><input name="IdPayer" type="text" class="input" style="width: 200px" value="{$IdPayer|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Expires#}</td>
              <td class="row_right"><input name="Betreff" type="text" class="input" style="width: 50px" value="{$row->Betreff|sanitize}" maxlength="50" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Shop_payment_TM#} </td>
              <td class="row_right">
                <label><input type="radio" name="Testmodus" value="1" {if $row->Testmodus == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input type="radio" name="Testmodus" value="0" {if $row->Testmodus == 0}checked="checked"{/if} />{#No#}</label>
              </td>
            </tr>
	    <tr>
	      <td class="row_left">Для автоматического обновления статуса платежей, сообщите администрации EasyPay адрес для доставки online-уведомлений</td>
	      <td class="row_right" style="color:red;">{$baseurl}/index.php?&amp;p=shop&amp;payment=EP&amp;action=callback&amp;reply=result</td>
	    </tr>
          {else}
            <tr>
              <td class="row_left">{#Shop_payment_BZ#}</td>
              <td class="row_right"><input style="width: 200px" name="Betreff" type="text" class="input" value="{$row->Betreff|sanitize}" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Shop_payment_TM#}</td>
              <td class="row_right"><input style="width: 200px" name="Testmodus" type="text" class="input" value="{$row->Testmodus|sanitize}" /></td>
            </tr>
            <tr>
              <td class="row_left">{#Shop_payment_II#}</td>
              <td class="row_right"><input style="width: 200px" name="Install_Id" type="text" class="input" value="{$row->Install_Id|sanitize}" /></td>
            </tr>
          {/if}
        {/if}
      {/if}
    </table>
    <br />
    <input type="submit" name="button" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input name="lc" type="hidden" value="{$langcode}" />
  </form>
</div>
