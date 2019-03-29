{assign var='langcode' value=$smarty.request.langcode|default:1}
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
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        if (langcode === 1) {
            location.href='index.php?do=shop&sub=edit_categ&id={$smarty.request.id}&parent={$smarty.request.parent}&noframes=1&langcode=1';
        } else {
            location.href='index.php?do=shop&sub=name_text&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
        }
    } else {
        document.getElementById('l_{$langcode}').selected = true;
    }
}
//-->
</script>

<div class="popbox">
  <div class="header_inf">
    <form method="post" action="">
      <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
        <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
      </select>
      <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
    </form>
  </div>
  <form name="form" action="" method="post" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" class="table_back">
      <tr>
        <td nowrap="nowrap" class="row_left">{#Global_CategParent#}</td>
        <td class="row_right">
          <select name="Parent" class="input" style="width: 330px; padding: 0px" >
            {foreach from=$shop_search_small_categs item=scs}
              {if $scs->area}
                <option class="shop_selector_back" value="{$scs->area}_0"{if $row->Sektion == $scs->area} selected="selected"{/if}> ----------- {#LoginSection#} {$scs->area} ----------- </option>
              {else}
                {if $scs->Subcount < 28 && $row->Id != $scs->catid && $scs->Parent_Id != $row->Id}
                  <option class="shop_selector_subs" value="{$scs->Sektion}_{$scs->catid}"{if $row->Parent_Id == $scs->catid} selected="selected"{/if}>{$scs->visible_title|specialchars}</option>
                {/if}
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="160" nowrap="nowrap" class="row_left">{#Global_name#} ({$language.name.$langcode})</td>
        <td class="row_right"><input name="Name_1" type="text" class="input" size="60" value="{$row->Name|sanitize}" /></td>
      </tr>
      <tr>
        <td width="160" nowrap="nowrap" class="row_left">{#Global_descr#} ({$language.name.$langcode})</td>
        <td class="row_right"></td>
      </tr>
    </table>
    {$Editor}
    <table width="100%" border="0" cellspacing="0" cellpadding="4" class="table_back">
      {if $row->Bild_Kategorie}
        <tr>
          <td width="250" nowrap="nowrap" class="row_left"><span class="stip" title="{$lang.Shop_cat_catimageinf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Shop_cat_catimage#} </td>
          <td class="row_right"> {$row->Bild_Kategorie} </td>
        </tr>
      {/if}
      <tr>
        <td width="250" nowrap="nowrap" class="row_left"><span class="stip" title="{$lang.Shop_cat_newcatimage_inf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Shop_cat_newcatimage#} </td>
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
      {if $row->Bild_Kategorie}
        <tr>
          <td width="250" nowrap="nowrap" class="row_left">{#Global_ImgDel#}</td>
          <td class="row_right"><input name="noImg_1" type="checkbox" id="noImg_1" value="1" /></td>
        </tr>
      {/if}
      {if $row->Bild_Navi && $smarty.request.parent == 0}
        <tr>
          <td width="250" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_catnavimageinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_cat_catnavimage#} </td>
          <td class="row_right">{$row->Bild_Navi}</td>
        </tr>
      {/if}
      {if $smarty.request.parent == 0}
        <tr>
          <td width="250" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_catnavimage_newinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_cat_catnavimage_new#} </td>
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
      {/if}
      {if $row->Bild_Navi}
        <tr>
          <td width="250" nowrap="nowrap" class="row_left">{#Shop_cat_catnavimagedel#}</td>
          <td class="row_right"><input name="noImg_2" type="checkbox" id="noImg_2" value="1" /></td>
        </tr>
      {/if}
      <tr>
        <td width="160" nowrap="nowrap" class="row_left">
          <label><input type="checkbox" class="absmiddle" name="AlleGruppen" value="1" {if empty($row->Gruppen)}checked="checked"{/if}/><strong>{#All_Grupp#}</strong></label>
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
        <td width="250" nowrap="nowrap" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_cat_ustidinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_countries_taxable#} </td>
        <td class="row_right">
          <select class="input" name="UstId" id="UstId">
            {foreach from=$ust_elements item=ust}
              <option value="{$ust->Id}"{if $ust->Id == $row->UstId} selected="selected"{/if}>{$ust->Name} ({$ust->Wert}%)</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="row_left">
          <input class="button" type="submit" id="s" value="{#Save#}" />
          <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
          <input type="hidden" name="id" value="{$smarty.request.id}" />
          <input type="hidden" name="categ_del_img" value="{$row->Bild_Kategorie_Del}" />
          <input type="hidden" name="navi_del_img" value="{$row->Bild_Navi_Del}" />
          <input name="save" type="hidden" id="save" value="1" />
        </td>
      </tr>
    </table>
  </form>
</div>
