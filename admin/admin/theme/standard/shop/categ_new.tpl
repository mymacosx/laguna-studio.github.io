<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
function fileUpload(sub ,divid) {
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
function checkForm() {
    if(document.getElementById('NameCateg').value == '') {
        alert('{#Shop_cat_JSnoName#}');
        document.getElementById('NameCateg').focus();
        return false;
    }
}
//-->
</script>

{if $error}
  {foreach from=$error name=ee item=e}
    {$e}
  {/foreach}
{/if}
<form name="form" action="" method="post" onsubmit="return checkForm();" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td nowrap="nowrap" class="row_left">{#Global_CategParent#}</td>
      <td class="row_right">
        <select name="Parent" class="input" style="width: 200px">
          <option value="0">{#Global_noParent#}</option>
          {foreach from=$shop_search_small_categs item=scs}
            {if $scs->Subcount<28}
              <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if $smarty.request.id == $scs->catid}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
            {/if}
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="160" nowrap="nowrap" class="row_left">{#Global_name#}</td>
      <td class="row_right"><input name="Name_1" id="NameCateg" type="text" class="input" size="40" style="width: 200px;" value="{$row->Name|sanitize}" /></td>
    </tr>
    <tr>
      <td width="160" nowrap="nowrap" class="row_left">{#Global_descr#}</td>
      <td class="row_right">&nbsp;</td>
    </tr>
  </table>
  {$Editor}
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    {if $row->Bild_Kategorie}
      <tr>
        <td width="160" nowrap="nowrap" class="row_left"><span class="stip" title="{$lang.Shop_cat_catimageinf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Shop_cat_catimage#} </td>
        <td class="row_right"> {$row->Bild_Kategorie} </td>
      </tr>
    {/if}
    <tr>
      <td width="160" nowrap="nowrap" class="row_left"><span class="stip" title="{$lang.Shop_cat_newcatimage_inf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Shop_cat_newcatimage#} </td>
      <td class="row_right">
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('icons_categs', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'shop/icons_categs', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </td>
    </tr>
    {if $row->Bild_Navi}
      <tr>
        <td width="160" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_catnavimageinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_cat_catnavimage#} </td>
        <td class="row_right">{$row->Bild_Navi}</td>
      </tr>
    {/if}
    <tr>
      <td width="160" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_catnavimage_newinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_cat_catnavimage_new#} </td>
      <td class="row_right">
        <div id="UpInf_2"></div>
        <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_2" type="text" size="3" name="resizeUpload_2" class="input" value="25" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_2" type="file" size="45" name="fileToUpload_2" class="input" />
        <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('navi_categs', 2);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'shop/navi_categs', 2);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_2" id="newFile_2" />
      </td>
    </tr>
    <tr>
      <td width="160" nowrap="nowrap" class="row_left">
        <label><input type="checkbox" class="absmiddle" name="AlleGruppen" value="1" checked="checked" /><strong>{#All_Grupp#}</strong></label>
        <br />
        <br />
        {#Shop_allowed_select#}
      </td>
      <td class="row_right">
        <select name="Gruppen[]" size="6" multiple="multiple" class="input" style="width: 250px">
          {foreach from=$UserGroups item=group}
            <option value="{$group->Id}" {if in_array($group->Id, $groups)}selected="selected" {/if}>{$group->Name_Intern}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="160" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_ustidinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_countries_taxable#} </td>
      <td class="row_right">
        <select name="UstId" id="UstId">
          {foreach from=$ust_elements item=ust}
            <option value="{$ust->Id}"{if $ust->Id == $row->UstId} selected="selected"{/if}>{$ust->Name} ({$ust->Wert}%)</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Global_PosiNavi#}</td>
      <td class="row_right"><input name="Position" type="text" class="input" id="posi" value="1" size="3" maxlength="2" /></td>
    </tr>
    <tr>
      <td width="160" class="row_left">&nbsp;</td>
      <td class="row_right"><input class="button" type="submit" value="{#Save#}" />
        <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
        <input name="save" type="hidden" id="save" value="1" />
      </td>
    </tr>
  </table>
</form>
