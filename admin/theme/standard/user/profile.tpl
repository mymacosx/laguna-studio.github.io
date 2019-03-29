{script file="$jspath/jaccordion.js" position='head'}
{if permission('own_avatar')}
{script file="$jspath/jupload.js" position='head'}
{/if}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#pform').validate({
        rules: {
            {if permission('username')}
            reg_username: { required: true },
            {/if}
            {if $settings.Reg_DataPflichtFill == 1}
            Vorname: { required: true },
            Nachname: { required: true },
            {/if}
            {if $settings.Reg_AddressFill == 1}
            Strasse_Nr: { required: true },
            Postleitzahl: { required: true, number: true },
            Ort: { required: true },
            {/if}
            send: { }
        },
        messages: { },
        submitHandler: function() {
            document.forms['pform'].submit();
        },
        success: function(label) { }
    });
    $('#usetts-ac').accordion({ autoheight: false });
});
{if permission('own_avatar')}
function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    $.ajaxFileUpload({
        url: 'index.php?action=' + sub + '&p=useraction&divid=' + divid,
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
{/if}
//-->
</script>

{if $error}
  <div class="error_box"> {#ErrorOcured#}
    <ul>
      {foreach from=$error item=reg_error}
        <li>{$reg_error}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<div id="ok" style="margin-top: 10px">
  <form id="pform" name="pform" action="index.php?p=useraction&amp;action=profile" method="post" enctype="multipart/form-data">
    <div class="accordion" id="usetts-ac">
      <a><img src="{$imgpath}/profile/cdata.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_Cdata#}</a>
      <div>
        <div class="box_data" style="margin-top: 10px">
          <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
            <tr>
              <td class="row_first" width="40%" align="right"><label for="l_Benutzername">{#Username#}&nbsp;</label></td>
              <td class="row_second">
                {if permission('username')}
                  <input name="reg_username" type="text" class="input" id="l_Benutzername" style="width: 150px" value="{$smarty.post.reg_username|default:$data.Benutzername|sanitize}" maxlength="20" />
                {else}
                  {$smarty.session.user_name}
                {/if}
              </td>
            </tr>
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_Email#}&nbsp;</td>
              <td class="row_second">{$smarty.session.login_email}</td>
            </tr>
            {if permission('email_change')}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_newmail">{#Profile_EmailNew#}&nbsp;</label></td>
                <td class="row_second"><input name="newmail" type="text" class="input" id="l_newmail" style="width: 150px" value="{$smarty.post.newmail|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_newmail2">{#Profile_EmailNew2#}&nbsp;</label></td>
                <td class="row_second"><input name="newmail2" type="text" class="input" id="l_newmail2" style="width: 150px" value="{$smarty.post.newmail2|sanitize}" /></td>
              </tr>
            {/if}
          </table>
        </div>
      </div>
      <a><img src="{$imgpath}/profile/contact.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#PersonalData#}</a>
      <div>
        <div class="box_data" style="margin-top: 10px">
          <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
            {if $settings.Reg_DataPflicht == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_nachname">{#LastName#}&nbsp;</label></td>
                <td width="5">{if $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Nachname" type="text" id="l_nachname" value="{$smarty.post.Nachname|default:$data.Nachname|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_vorname">{#GlobalName#}&nbsp;</label></td>
                <td width="5">{if $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Vorname" type="text" id="l_vorname" value="{$smarty.post.Vorname|default:$data.Vorname|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_middlename">{#Profile_MiddleName#}&nbsp;</label></td>
                <td width="5">&nbsp;</td>
                <td class="row_second"><input class="input" style="width: 150px" name="MiddleName" type="text" id="l_middlename" value="{$smarty.post.MiddleName|default:$data.MiddleName|sanitize}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Fon == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_fon">{#Phone#}&nbsp;</label></td>
                <td width="5">&nbsp;</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Telefon" type="text" id="l_fon" value="{$smarty.post.Telefon|default:$data.Telefon|sanitize}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Fax == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_fax">{#Fax#}&nbsp;</label></td>
                <td width="5">&nbsp;</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Telefax" type="text" id="l_fax" value="{$smarty.post.Telefax|default:$data.Telefax|sanitize}" /></td>
              </tr>
            {/if}
            <tr>
              <td class="row_first" width="40%" align="right"><label for="l_reg_country">{#Country#}&nbsp;</label></td>
              <td width="5">&nbsp;</td>
              <td class="row_second">
                <select class="input" name="country" id="l_reg_country" style="width: 160px">
                  {foreach from=$countries item=c}
                    <option value="{$c.Code}" {if $smarty.request.send != 1}{if $data.LandCode|upper == $c.Code}selected="selected"{/if}{else}{if $smarty.post.country == $c.Code}selected="selected"{/if}{/if}>{$c.Name}</option>
                  {/foreach}
                </select>
              </td>
            </tr>
            {if $settings.Reg_Address == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_zip">{#Profile_Zip#}&nbsp;</label></td>
                <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Postleitzahl" type="text" id="l_zip" value="{$smarty.post.Postleitzahl|default:$data.Postleitzahl|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_ort">{#Town#}&nbsp;</label></td>
                <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Ort" type="text" id="l_ort" value="{$smarty.post.Ort|default:$data.Ort|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_strassenr">{#Profile_Street#}&nbsp;</label></td>
                <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Strasse_Nr" type="text" id="l_strassenr" value="{$smarty.post.Strasse_Nr|default:$data.Strasse_Nr|sanitize}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Firma == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_firma">{#Profile_company#}&nbsp;</label></td>
                <td width="5">&nbsp;</td>
                <td class="row_second"><input class="input" style="width: 150px" name="Firma" type="text" id="l_firma" value="{$smarty.post.Firma|default:$data.Firma|sanitize}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Ust == 1}
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_ustid">{#Profile_vatnum#}&nbsp;</label></td>
                <td width="5">&nbsp;</td>
                <td class="row_second"><input class="input" style="width: 150px" name="UStId" type="text" id="l_ustid" value="{$smarty.post.UStId|default:$data.UStId|sanitize}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Bank == 1}
              <tr>
                <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_bank">{#Profile_Bank#}</label>&nbsp;</td>
                <td width="5">&nbsp;</td>
                <td nowrap="nowrap" class="row_second"><textarea name="BankName" cols="" rows="" class="input" id="l_reg_bank" style="width: 280px; height: 160px">{$smarty.post.BankName|default:$data.BankName|sanitize}</textarea></td>
              </tr>
            {/if}
            <tr>
              <td width="180" class="row_first">&nbsp;</td>
              <td width="5">&nbsp;</td>
              <td nowrap="nowrap" class="row_second">{#Profile_RequiredInf#}</td>
            </tr>
          </table>
        </div>
      </div>
      {if get_active('optional_data')}
        <a><img src="{$imgpath}/profile/opt.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_Optional#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td class="row_first" align="right"><label for="l_Geschlecht">{#Profile_Gender#}&nbsp;</label></td>
                <td class="row_second">
                  <select name="Geschlecht" class="input" id="l_Geschlecht">
                    <option value="-" {if $data.Geschlecht == '-'}selected="selected" {/if}>{#User_NoSettings#}</option>
                    <option value="m" {if $data.Geschlecht == 'm'}selected="selected" {/if}>{#User_Male#}</option>
                    <option value="f" {if $data.Geschlecht == 'f'}selected="selected" {/if}>{#User_Female#}</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_reg_birth">{#Birth#}&nbsp;</label></td>
                <td class="row_second"><input class="input" name="birth" type="text" id="l_reg_birth" style="width: 150px" value="{$smarty.post.birth|default:$data.Geburtstag|sanitize}" maxlength="10" />&nbsp;&nbsp;{#BirthFormat#}</td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_icq">{#Profile_ICQ#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 150px" name="icq" type="text" id="l_icq" value="{$smarty.post.icq|default:$data.icq|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_skype">{#Profile_Scype#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 150px" name="skype" type="text" id="l_skype" value="{$smarty.post.skype|default:$data.skype|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_msn">{#Profile_MSN#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 150px" name="msn" type="text" id="l_msn" value="{$smarty.post.msn|default:$data.msn|sanitize}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_webseite">{#Web#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 150px" name="Webseite" type="text" id="l_webseite" value="{$smarty.post.Webseite|default:$data.Webseite|sanitize}" /></td>
              </tr>
              {if $signatur_erlaubt == 1}
                <tr>
                  <td class="row_first" width="40%" align="right"><label for="l_signatur">{#Profile_Sig#}&nbsp;</label>
                    <br />
                    <br />
                    {#Profile_SigLength#}{$signatur_laenge}
                    {if $signatur_syscode == 1}
                      <br />
                      {#Profile_SysCode#} {#Profile_IsAllowed#}
                    {/if}
                  </td>
                  <td class="row_second"><textarea name="Signatur" cols="" rows="" class="input" id="l_signatur" style="width: 98%;height: 100px">{$smarty.post.Signatur|default:$data.Signatur|escape: html}</textarea></td>
                </tr>
              {/if}
              <tr>
                <td class="row_first" align="right"><label for="l_status">{#Profile_Status#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Status" cols="" rows="" class="input" id="l_status" style="width: 98%;height: 40px">{$smarty.post.Status|default:$data.Status|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_beruf">{#Profile_Job#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Beruf" cols="" rows="" class="input" id="l_beruf" style="width: 98%;height: 40px">{$smarty.post.Beruf|default:$data.Beruf|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_interessen">{#Profile_Int#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Interessen" cols="" rows="" class="input" id="l_interessen" style="width: 98%;height: 40px">{$smarty.post.Interessen|default:$data.Interessen|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="l_hobbys">{#Profile_Hobbys#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Hobbys" cols="" rows="" class="input" id="l_hobbys" style="width: 98%;height: 40px">{$smarty.post.Hobbys|default:$data.Hobbys|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_essen">{#Profile_Food#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Essen" cols="" rows="" class="input" id="l_essen" style="width: 98%;height: 40px">{$smarty.post.Essen|default:$data.Essen|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_musik">{#Profile_Music#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Musik" cols="" rows="" class="input" id="l_musik" style="width: 98%;height: 40px">{$smarty.post.Musik|default:$data.Musik|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_films">{#Profile_Films#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Films" cols="" rows="" class="input" id="l_films" style="width: 98%;height: 40px">{$smarty.post.Films|default:$data.Films|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_tele">{#Profile_Tele#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Tele" cols="" rows="" class="input" id="l_tele" style="width: 98%;height: 40px">{$smarty.post.Tele|default:$data.Tele|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_book">{#Profile_Book#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Book" cols="" rows="" class="input" id="l_book" style="width: 98%;height: 40px">{$smarty.post.Book|default:$data.Book|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_game">{#Profile_Game#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Game" cols="" rows="" class="input" id="l_game" style="width: 98%;height: 40px">{$smarty.post.Game|default:$data.Game|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_citat">{#Profile_Citat#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Citat" cols="" rows="" class="input" id="l_citat" style="width: 98%;height: 40px">{$smarty.post.Citat|default:$data.Citat|sanitize}</textarea></td>
              </tr>
              <tr>
                <td class="row_first" align="right"><label for="l_other">{#Profile_Other#}&nbsp;</label></td>
                <td class="row_second"><textarea name="Other" cols="" rows="" class="input" id="l_other" style="width: 98%;height: 40px">{$smarty.post.Other|default:$data.Other|sanitize}</textarea></td>
              </tr>
            </table>
          </div>
        </div>
        <a><img src="{$imgpath}/profile/social.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#SocialNetworks#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Vkontakte">{#Vkontakte#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Vkontakte" type="text" id="Vkontakte" value="{$smarty.post.Vkontakte|default:$data.Vkontakte|escape: html}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Odnoklassniki">{#Odnoklassniki#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Odnoklassniki" type="text" id="Odnoklassniki" value="{$smarty.post.Odnoklassniki|default:$data.Odnoklassniki|escape: html}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Mymail">{#Mymail#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Mymail" type="text" id="Mymail" value="{$smarty.post.Mymail|default:$data.Mymail|escape: html}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Google">{#Google#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Google" type="text" id="Google" value="{$smarty.post.Google|default:$data.Google|escape: html}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Facebook">{#Facebook#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Facebook" type="text" id="Facebook" value="{$smarty.post.Facebook|default:$data.Facebook|escape: html}" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right"><label for="Twitter">{#Twitter#}&nbsp;</label></td>
                <td class="row_second"><input class="input" style="width: 250px" name="Twitter" type="text" id="Twitter" value="{$smarty.post.Twitter|default:$data.Twitter|escape: html}" /></td>
              </tr>
            </table>
          </div>
        </div>
      {/if}
      {if get_active('user_videos')} <a><img src="{$imgpath}/profile/videos.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Forums_UserVideosMy#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            {#Forums_UserVideosMyInf#}
            <br />
            <br />
            {if $myvideos}
              <div class="infobox">
                <div class="h3">{#Forums_UserVideosCurr#}</div>
                <table width="100%" cellpadding="2" cellspacing="0" class="profile_tableborder">
                  <tr>
                    <td width="100"><strong>{#Forums_UserVideosVideoId#}</strong></td>
                    <td width="100"><strong>{#GlobalTitle#}</strong></td>
                    <td width="50"><strong>{#Forums_UserVideosVideoSrc#}</strong></td>
                    <td width="10"><strong>{#Forums_UserVideosVideoPos#}</strong></td>
                    <td><strong>{#Delete#}</strong></td>
                  </tr>
                  {foreach from=$myvideos item=u}
                    <tr>
                      <td><input name="Video[{$u->Id}]" type="text" class="input" style="width: 100px" value="{$u->Video}" maxlength="20" /></td>
                      <td><input name="Name[{$u->Id}]" type="text" class="input" style="width: 150px" value="{$u->Name|sanitize}" maxlength="100" /></td>
                      <td>
                        <select name="VideoSource[{$u->Id}]" class="input">
                          <option value="youtube" {if $u->VideoSource == 'youtube'}selected="selected"{/if}>{#YouTube#}</option>
                        </select>
                      </td>
                      <td><input name="Position[{$u->Id}]" type="text" class="input" style="width: 30px" value="{$u->Position}" maxlength="2" /></td>
                      <td><input name="DelVideo[{$u->Id}]" type="checkbox" value="1" /></td>
                    </tr>
                  {/foreach}
                </table>
              </div>
            {/if}
            {if $myvideosCount<'4'}
              <div class="infobox">
                <div class="h4">{#Forums_UserVideosNew#}</div>
                <table width="100%" cellpadding="2" cellspacing="0" class="profile_tableborder">
                  <tr>
                    <td width="100"><strong>{#Forums_UserVideosVideoId#}</strong></td>
                    <td width="100"><strong>{#GlobalTitle#}</strong></td>
                    <td width="50"><strong>{#Forums_UserVideosVideoSrc#}</strong></td>
                    <td><strong>{#Forums_UserVideosVideoPos#}</strong></td>
                  </tr>
                  {section name=new loop=$myvideosCountLoop}
                    <tr>
                      <td><input name="VideoNeu[]" type="text" class="input" style="width: 100px" value="" maxlength="20" /></td>
                      <td><input name="NameNeu[]" type="text" class="input" style="width: 150px" value="" maxlength="100" /></td>
                      <td>
                        <select name="VideoSourceNeu[]" class="input">
                          <option value="youtube">{#YouTube#}</option>
                        </select>
                      </td>
                      <td><input name="PositionNeu[]" type="text" class="input" style="width: 30px" value="1" maxlength="2" /></td>
                    </tr>
                  {/section}
                </table>
              </div>
            {/if}
          </div>
        </div>
      {/if}
      {if get_active('forums')}
        <a><img src="{$imgpath}/profile/avatar.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Forums_avatar#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td width="40%" align="right" class="row_first">{#Gravatar#}&nbsp;</td>
                <td class="row_second">
                  <label><input type="radio" name="Gravatar" value="1" {if $data.Gravatar == 1}checked="checked" {/if}/> {#Yes#}</label>
                  <label><input type="radio" name="Gravatar" value="0" {if $data.Gravatar == 0}checked="checked" {/if}/> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td width="40%" align="right" class="row_first">{#Profile_AvatarStandard#}&nbsp;</td>
                <td class="row_second">
                  <label><input type="radio" name="Avatar_Default" value="1" {if $data.Avatar_Default == 1}checked="checked" {/if}/> {#Yes#}</label>
                  <label><input type="radio" name="Avatar_Default" value="0" {if $data.Avatar_Default == 0}checked="checked" {/if}/> {#No#}</label>
                </td>
              </tr>
              {if $avatar && permission('own_avatar')}
                <tr>
                  <td width="40%" align="right" class="row_first">{#Profile_AvatarDelete#}&nbsp;</td>
                  <td class="row_second">
                    <input type="radio" name="Avatar_Del" value="1" /> {#Yes#}
                    <input name="Avatar_Del" type="radio" value="0" checked="checked" /> {#No#}
                  </td>
                </tr>
              {/if}
              <tr>
                <td width="40%" align="right" class="row_first">{#Profile_AvatarImage#}&nbsp;</td>
                <td class="row_second">
                  {if $avatar}
                    {$avatar}
                  {else}
                    {#Profile_AvatarNone#}
                  {/if}
                </td>
              </tr>
              {if permission('own_avatar')}
                <tr>
                  <td width="40%" align="right" class="row_first">{#Profile_AvatarNew#}&nbsp;</td>
                  <td nowrap="nowrap" class="profile_second">
                    <div id="UpInf_1"></div>
                    <div id="loading_1" style="display: none;"><img src="{$imgpath_page}ajaxbar.gif" alt="" /></div>
                    <input id="fileToUpload_1" type="file" size="20" name="fileToUpload_1" class="input" />&nbsp;
                    <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('avatarupload', 1);" value="{#UploadButton#}" />
                    <input type="hidden" name="newAvatar" id="newFile_1" />
                  </td>
                </tr>
              {/if}
            </table>
          </div>
        </div>
      {/if} <a><img src="{$imgpath}/profile/shield.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_Profile#}</a>
      <div>
        <div class="box_data" style="margin-top: 10px">
          <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_Public#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Profil_public" type="radio" value="1" {if $data.Profil_public == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Profil_public" type="radio" value="0" {if $data.Profil_public == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" align="right">{#Profile_PublicAll#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Profil_Alle" type="radio" value="1" {if $data.Profil_Alle == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Profil_Alle" type="radio" value="0" {if $data.Profil_Alle == 0}checked="checked"{/if} /> {#Profile_PublicAll2#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" align="right">{#Profile_TownPublic#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Ort_Public" type="radio" value="1" {if $data.Ort_Public == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Ort_Public" type="radio" value="0" {if $data.Ort_Public == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_SBirth#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Geburtstag_public" type="radio" value="1" {if $data.Geburtstag_public == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Geburtstag_public" type="radio" value="0" {if $data.Geburtstag_public == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_BInv#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Unsichtbar" type="radio" value="1" {if $data.Unsichtbar == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Unsichtbar" type="radio" value="0" {if $data.Unsichtbar == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_RNl#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Newsletter" type="radio" value="1" {if $data.Newsletter == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Newsletter" type="radio" value="0" {if $data.Newsletter == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
            <tr>
              <td class="row_first" width="40%" align="right">{#Profile_REm#}&nbsp;</td>
              <td class="row_second">
                <label><input name="Emailempfang" type="radio" value="1" {if $data.Emailempfang == 1}checked="checked"{/if} /> {#Yes#}</label>
                <label><input name="Emailempfang" type="radio" value="0" {if $data.Emailempfang == 0}checked="checked"{/if} /> {#No#}</label>
              </td>
            </tr>
          </table>
        </div>
      </div>
      {if get_active('forums')}
        <a><img src="{$imgpath}/profile/fsettings.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_ForumsSettings#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td class="row_first" width="40%" align="right">{#Profile_ForumsSettingsThreads#}&nbsp;</td>
                <td class="row_second"><input name="Forum_Themen_Limit" type="text" class="input" value="{$smarty.post.Forum_Themen_Limit|default:$data.Forum_Themen_Limit|sanitize}" size="5" maxlength="2" /></td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right">{#Profile_ForumsSettingsPosts#}&nbsp;</td>
                <td class="row_second"><input name="Forum_Beitraege_Limit" type="text" class="input" value="{$smarty.post.Forum_Beitraege_Limit|default:$data.Forum_Beitraege_Limit|sanitize}" size="5" maxlength="2" /></td>
              </tr>
            </table>
          </div>
        </div>
      {/if}
      {if get_active('pn')}
        <a><img src="{$imgpath}/profile/pnsettings.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_Guestbook_PrivateMessages#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td class="row_first" width="40%" align="right">{#Profile_RPn#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Pnempfang" type="radio" value="1" {if $data.Pnempfang == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Pnempfang" type="radio" value="0" {if $data.Pnempfang == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" width="40%" align="right">{#Profile_REPn#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="PnEmail" type="radio" value="1" {if $data.PnEmail == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="PnEmail" type="radio" value="0" {if $data.PnEmail == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_PopUpPn#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="PnPopup" type="radio" value="1" {if $data.PnPopup == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="PnPopup" type="radio" value="0" {if $data.PnPopup == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
            </table>
          </div>
        </div>
      {/if}
      {if get_active('user_guestbook')}
        <a><img src="{$imgpath}/profile/gbsettings.png" alt="" border="0" class="absmiddle" />&nbsp;&nbsp;&nbsp;{#Profile_GuestbookUser#}</a>
        <div>
          <div class="box_data" style="margin-top: 10px">
            <table width="100%" cellpadding="0" cellspacing="0" class="profile_tableborder">
              <tr>
                <td class="row_first" width="40%" align="right">{#Profile_AGb#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch" type="radio" value="1" {if $data.Gaestebuch == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch" type="radio" value="0" {if $data.Gaestebuch == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_OptNoGuests#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch_KeineGaeste" type="radio" value="0" {if $data.Gaestebuch_KeineGaeste == 0}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch_KeineGaeste" type="radio" value="1" {if $data.Gaestebuch_KeineGaeste == 1}checked="checked"{/if} /> {#Profile_Guestbook_OptNoGuests2#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_Moderated#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch_Moderiert" type="radio" value="0" {if $data.Gaestebuch_Moderiert == 0}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch_Moderiert" type="radio" value="1" {if $data.Gaestebuch_Moderiert == 1}checked="checked"{/if} /> {#Profile_Guestbook_Moderated2#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_MaxChars1#}&nbsp;</td>
                <td class="row_second"><input name="Gaestebuch_Zeichen" type="text" class="input" id="Gaestebuch_Zeichen" value="{$data.Gaestebuch_Zeichen}" size="5" maxlength="4" />&nbsp;{#Profile_Guestbook_MaxChars2#}</td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_AllowSmilies#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch_smilies" type="radio" value="1" {if $data.Gaestebuch_smilies == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch_smilies" type="radio" value="0" {if $data.Gaestebuch_smilies == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_AllowBBcode#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch_bbcode" type="radio" value="1" {if $data.Gaestebuch_bbcode == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch_bbcode" type="radio" value="0" {if $data.Gaestebuch_bbcode == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
              <tr>
                <td class="row_first" align="right">{#Profile_Guestbook_AllowImgCode#}&nbsp;</td>
                <td class="row_second">
                  <label><input name="Gaestebuch_imgcode" type="radio" value="1" {if $data.Gaestebuch_imgcode == 1}checked="checked"{/if} /> {#Yes#}</label>
                  <label><input name="Gaestebuch_imgcode" type="radio" value="0" {if $data.Gaestebuch_imgcode == 0}checked="checked"{/if} /> {#No#}</label>
                </td>
              </tr>
            </table>
          </div>
        </div>
      {/if}
    </div>
    <p>
      <input type="submit" value="{#SaveProfile#}" class="button" accesskey="s" />
      <input name="area" type="hidden" value="{$area}" />
      <input name="lang" type="hidden" value="{$langcode}" />
      <input name="send" type="hidden" value="1" />
    </p>
  </form>
</div>
