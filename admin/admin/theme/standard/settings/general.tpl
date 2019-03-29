<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['editforms'].submit();
    }
});
$(document).ready(function() {
    if ($('#js_off').prop('checked')) {
       $('.combjs').hide();
    }
    if ($('#css_off').prop('checked')) {
       $('.combcss').hide();
    }
    $('#js_off').on('click', function() {
        $('.combjs').hide();
    });
    $('#js_on').on('click', function() {
        $('.combjs').show();
    });
    $('#css_off').on('click', function() {
        $('.combcss').hide();
    });
    $('#css_on').on('click', function() {
        $('.combcss').show();
    });

    $('#editform').validate({
        rules: {
            Land: { required: true, minlength: 2 },
            Seitenname: { required: true, minlength: 2 },
            Seitenbetreiber: { required: true, minlength: 2 },
            Strasse: { required: true, minlength: 2 },
            Stadt: { required: true, minlength: 2 },
            Mail_Absender: { required: true, email: true },
            Mail_Name: { required: true, minlength: 5 },
            Mail_Header: { required: true },
            Mail_Fuss_HTML: { required: true },
            Mail_Fuss: { required: true },
            Spamwoerter: { required: true },
            SpamRegEx: { required: true },
            Kommentare_IconBreite: { required: true, range: [40, 120] },
            Kommentar_Laenge: { required: true, range: [100, 5000] },
            Kommentare_Seite: { required: true, range: [5, 35] },
            Loesch_Gruende: { required: true }
        },
        messages: {
            Land: {
                required: '{#SettingsCountry#}',
                minlength: '{#SettingsCountryLength#}'
            }
        },
        success: function(label) {
            label.html("&nbsp;").addClass("checked");
        }
    });

    $('#a_sett').accordion({
	autoHeight: false,
	icons: {
    	    header: 'ui-icon-circle-arrow-e',
   	    headerSelected: 'ui-icon-circle-arrow-s'
	}
    });
});

function set_method() {
    document.getElementById('Mail_Port').style.display = 'none';
    document.getElementById('Mail_Host').style.display = 'none';
    document.getElementById('Mail_Type_Auth').style.display = 'none';
    document.getElementById('Mail_Username').style.display = 'none';
    document.getElementById('Mail_Passwort').style.display = 'none';
    document.getElementById('Mail_Sendmailpfad').style.display = 'none';
    document.getElementById('Auth').style.display = 'none';
    if(document.getElementById('smtp').selected == true) {
        document.getElementById('Mail_Port').style.display = '';
        document.getElementById('Mail_Host').style.display = '';
        document.getElementById('Mail_Type_Auth').style.display = '';
        document.getElementById('Mail_Username').style.display = '';
        document.getElementById('Mail_Passwort').style.display = '';
        document.getElementById('Mail_Sendmailpfad').style.display = 'none';
        document.getElementById('Auth').style.display = '';
    }
    if(document.getElementById('sendmail').selected == true) {
        document.getElementById('Mail_Port').style.display = 'none';
        document.getElementById('Mail_Host').style.display = 'none';
        document.getElementById('Mail_Type_Auth').style.display = 'none';
        document.getElementById('Mail_Username').style.display = 'none';
        document.getElementById('Mail_Passwort').style.display = 'none';
        document.getElementById('Mail_Sendmailpfad').style.display = '';
        document.getElementById('Auth').style.display = 'none';
    }
}
function emailcheck() {
    var mailserver = document.getElementById('mah').value;
    var username = document.getElementById('mau').value;
    var password = document.getElementById('map').value;
    var method = document.getElementById('Mail_Typ').value;
    var mailabs = document.getElementById('mabs').value;
    var smpath = document.getElementById('smpath').value;
    var smport = document.getElementById('mailp').value;
    var auth = document.getElementById('auth').checked == true && document.getElementById('Mail_Typ').value == 'smtp' ? 1 : 0;
    window.open('index.php?do=settings&sub=emailcheck&noframes=1&f=' + mailabs + '&auth=' + auth + '&method=' + method + '&mailserver=' + mailserver + '&username=' + username + '&password=' + password + '&smpath=' + smpath + '&smport=' + smport + '', 'emc', 'top=0,left=0,width=700,height=400');
}
//-->
</script>

<div class="header">{#Settings_general#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{$iframe_index|default:''}
<form class="adminform" method="post" action="" autocomplete="off" name="editforms" id="editform">
  {if $shop_aktiv != 1}
    <input type="hidden" name="ShopStart" value="0" />
  {/if}
  <div id="a_sett">
    <div><a href="#">{#Settings_websitesettings#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RwEHtacces_Inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_Settings_seos#}</td>
          <td class="row_right">
            {if !empty($rewrite_error1) || !empty($rewrite_error2)}
              <span style="color: red">{$rewrite_error1}{$rewrite_error2}</span>
              <input type="hidden" name="use_seo" value="0" />
            {else}
              <label><input type="radio" name="use_seo" value="1" {if $row.use_seo == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="use_seo" value="0" {if $row.use_seo == 0}checked="checked"{/if} />{#RwEHtacces_dym#}</label>
              {/if}
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.LogsTInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#LogsT#}</td>
          <td class="row_right">
            <label><input type="radio" name="Logging" value="1" {if $row.Logging == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Logging" value="0" {if $row.Logging == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Editor_text|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Admin_Editor#}</td>
          <td class="row_right">
            <label><input type="radio" name="SiteEditor" value="1" {if $row.SiteEditor == 1}checked="checked"{/if} />{#EditorCKE#}</label>
            <label><input type="radio" name="SiteEditor" value="0" {if $row.SiteEditor == 0}checked="checked"{/if} />{#EditorText#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.TimeZone_Inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#TimeZone#}</td>
          <td class="row_right">
            <select class="input" name="timezone">
              {foreach key=key item=tz from=$timezone}
                <option value="{$key}" {if $row.timezone == $key}selected="selected"{/if}> {$tz|sanitize} </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_CountryInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_shopCountry#}</td>
          <td class="row_right"><input class="input" name="Land" type="text" style="width: 50px" value="{$row.Land|upper}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AktivDomains#}</td>
          <td class="row_right">
            <label><input type="radio" name="Domains" value="1" {if $row.Domains == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Domains" value="0" {if $row.Domains == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AktivYandex#}</td>
          <td class="row_right">
            <label><input type="radio" name="meta_yandex" value="1" {if $row.meta_yandex == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="meta_yandex" value="0" {if $row.meta_yandex == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#CodeYandex#}</td>
          <td class="row_right"><input class="input" name="code_yandex" type="text" style="width: 160px" value="{$row.code_yandex|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AktivGoogle#}</td>
          <td class="row_right">
            <label><input type="radio" name="meta_google" value="1" {if $row.meta_google == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="meta_google" value="0" {if $row.meta_google == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#CodeGoogle#}</td>
          <td class="row_right"><input class="input" name="code_google" type="text" style="width: 160px" value="{$row.code_google|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AktivAnalytics#}</td>
          <td class="row_right">
            <label><input type="radio" name="analytics" value="1" {if $row.analytics == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="analytics" value="0" {if $row.analytics == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#CodeAnalytics#}</td>
          <td class="row_right"><input class="input" name="analytics_code" type="text" style="width: 160px" value="{$row.analytics_code|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AutoMailBirthdays#}</td>
          <td class="row_right">
            <label><input type="radio" name="birthdays_mail" value="1" {if $row.birthdays_mail == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="birthdays_mail" value="0" {if $row.birthdays_mail == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#SendErrorEmail#}</td>
          <td class="row_right">
            <label><input type="radio" name="Error_Email" value="1" {if $row.Error_Email == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Error_Email" value="0" {if $row.Error_Email == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#AllowedHtml#}</td>
          <td class="row_right"><input class="input" name="allowed" style="width: 160px" type="text" style="width: 160px" value="{$row.allowed}" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_Rekvizit#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td  width="350" class="row_left">{#Settings_websitename#}</td>
          <td class="row_right"><input class="input" name="Seitenname" id="sname" type="text" style="width: 160px" value="{$row.Seitenname|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_company#}</td>
          <td class="row_right"><input class="input" name="Firma" type="text" style="width: 160px" value="{$row.Firma|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Inn#}</td>
          <td class="row_right"><input class="input" name="Inn" type="text" style="width: 160px" value="{$row.Inn|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Kpp#}</td>
          <td class="row_right"><input class="input" name="Kpp" type="text" style="width: 160px" value="{$row.Kpp|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Bik#}</td>
          <td class="row_right"><input class="input" name="Bik" type="text" style="width: 160px" value="{$row.Bik|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Bank#}</td>
          <td class="row_right"><input class="input" name="Bank" type="text" style="width: 160px" value="{$row.Bank|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Kschet#}</td>
          <td class="row_right"><input class="input" name="Kschet" type="text" style="width: 160px" value="{$row.Kschet|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Rschet#}</td>
          <td class="row_right"><input class="input" name="Rschet" type="text" style="width: 160px" value="{$row.Rschet|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Zip#}</td>
          <td class="row_right"><input class="input" name="Zip" type="text" style="width: 160px" value="{$row.Zip|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#User_town#}</td>
          <td class="row_right"><input class="input" name="Stadt" type="text" style="width: 160px" value="{$row.Stadt|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_streetnumber#}</td>
          <td class="row_right"><input class="input" name="Strasse" type="text" style="width: 160px" value="{$row.Strasse|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Telefon#}</td>
          <td class="row_right"><input class="input" name="Telefon" type="text" style="width: 160px" value="{$row.Telefon|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Fax#}</td>
          <td class="row_right"><input class="input" name="Fax" type="text" style="width: 160px" value="{$row.Fax|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_yname#}</td>
          <td class="row_right"><input class="input" name="Seitenbetreiber" type="text" style="width: 160px" value="{$row.Seitenbetreiber|sanitize}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Buh#}</td>
          <td class="row_right"><input class="input" name="Buh" type="text" style="width: 160px" value="{$row.Buh|sanitize}" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#SystemOptimize#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left">{#MaxCount#} &lt;title&gt;</td>
          <td class="row_right"><input class="input" name="CountTitle" type="text" value="{$row.CountTitle}" size="4" maxlength="3" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#MaxCount#} &lt;keywords&gt</td>
          <td class="row_right"><input class="input" name="CountKeywords" type="text" value="{$row.CountKeywords}" size="4" maxlength="3" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#MaxCount#} &lt;description&gt;</td>
          <td class="row_right"><input class="input" name="CountDescription" type="text" value="{$row.CountDescription}" size="4" maxlength="3" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendNo|sanitize}" src="{$imgpath}/help.png" alt="" /> {#AutoCheckFile#}</td>
          <td class="row_right">
            <label><input type="radio" name="cleanup" value="1" {if $row.cleanup == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="cleanup" value="0" {if $row.cleanup == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#PageOptimize#}</td>
          <td class="row_right">
            <label><input type="radio" name="min_page" value="1" {if $row.min_page == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="min_page" value="0" {if $row.min_page == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#PageGzip#}</td>
          <td class="row_right">
            <label><input type="radio" name="gzip_page" value="1" {if $row.gzip_page == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="gzip_page" value="0" {if $row.gzip_page == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#JsCombine#}</td>
          <td class="row_right">
            <label><input id="js_on" type="radio" name="comb_js" value="1" {if $row.comb_js == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input id="js_off" type="radio" name="comb_js" value="0" {if $row.comb_js == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combjs">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#JsOptimize#}</td>
          <td class="row_right">
            <label><input type="radio" name="min_js" value="1" {if $row.min_js == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="min_js" value="0" {if $row.min_js == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combjs">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#JsGzip#}</td>
          <td class="row_right">
            <label><input type="radio" name="gzip_js" value="1" {if $row.gzip_js == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="gzip_js" value="0" {if $row.gzip_js == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combjs">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#JsCache#}</td>
          <td class="row_right">
            <label><input type="radio" name="expires_js" value="1" {if $row.expires_js == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="expires_js" value="0" {if $row.expires_js == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#CssCombine#}</td>
          <td class="row_right">
            <label><input id="css_on" type="radio" name="comb_css" value="1" {if $row.comb_css == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input id="css_off" type="radio" name="comb_css" value="0" {if $row.comb_css == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combcss">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#CssOptimize#}</td>
          <td class="row_right">
            <label><input type="radio" name="min_css" value="1" {if $row.min_css == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="min_css" value="0" {if $row.min_css == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combcss">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#CssGzip#}</td>
          <td class="row_right">
            <label><input type="radio" name="gzip_css" value="1" {if $row.gzip_css == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="gzip_css" value="0" {if $row.gzip_css == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr class="combcss">
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.RecomendYes|sanitize}" src="{$imgpath}/help.png" alt="" /> {#CssCache#}</td>
          <td class="row_right">
            <label><input type="radio" name="expires_css" value="1" {if $row.expires_css == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="expires_css" value="0" {if $row.expires_css == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.NoFileOptimizeInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#NoFileOptimize#} </td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 400px; height: 50px" onclick="focusArea(this, 120);" name="ignore_list">{$row.ignore_list|sanitize}</textarea></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_companyImp#}&nbsp;&nbsp;<img class="absmiddle stip" title="{$lang.Settings_companyImpInf|sanitize}" src="{$imgpath}/help.png" alt="" /></a></div>
    <div> {$Impressum} </div>
    <div><a href="#">{#Settings_agb#}&nbsp;&nbsp;<img class="absmiddle stip" title="{$lang.Settings_agbInf|sanitize}" src="{$imgpath}/help.png" alt="" /></a></div>
    <div> {$Reg_Agb} </div>
    {if $shop_aktiv == 1}
      <div><a href="#">{#Settings_startsettings#}</a></div>
      <div>
        <table width="100%" border="0" cellpadding="4" cellspacing="0">
          <tr>
            <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Setting_shopstart_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_shopasstartpage#} </td>
            <td class="row_right">
              <label><input type="radio" name="shop_is_startpage" value="1" {if $row.shop_is_startpage == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="shop_is_startpage" value="0" {if $row.shop_is_startpage == 0}checked="checked"{/if} />{#No#}</label>
            </td>
          </tr>
        </table>
      </div>
    {/if}
    <div><a href="#">{#Settings_regsettings#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_userreg_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_userregdone#}</td>
          <td class="row_right">
            <label><input type="radio" name="Reg_Typ" value="norm" {if $row.Reg_Typ == 'norm'}checked="checked"{/if} />{#ConfirmRegNoMail#}</label>
            <label><input type="radio" name="Reg_Typ" value="email" {if $row.Reg_Typ == 'email'}checked="checked"{/if} />{#ConfirmRegMail#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_Reg_Pass_Inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_Reg_Pass#}</td>
          <td class="row_right">
            <label><input type="radio" name="Reg_Pass" value="1" {if $row.Reg_Pass == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Pass" value="0" {if $row.Reg_Pass == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_mailserversettings#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_emailm_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailmethod#}</td>
          <td class="row_right">
            <select class="input" onchange="set_method();" id="Mail_Typ" name="Mail_Typ">
              <option value="mail" {if $row.Mail_Typ == 'mail'}selected="selected"{/if}>MAIL</option>
              <option id="smtp" value="smtp" {if $row.Mail_Typ == 'smtp'}selected="selected"{/if}>SMTP</option>
              <option id="sendmail" value="sendmail" {if $row.Mail_Typ == 'sendmail'}selected="selected"{/if}>SENDMAIL</option>
            </select>
          </td>
        </tr>
        <tr id="Mail_Port">
          <td width="350" class="row_left">{#Settings_smtp_port#}</td>
          <td class="row_right"><input class="input" name="Mail_Port" id="mailp" type="text" style="width: 45px" value="{$row.Mail_Port}" size="5" maxlength="5" /></td>
        </tr>
        <tr id="Auth">
          <td width="350" class="row_left">{#Settings_smtp_autor#}</td>
          <td class="row_right"><input type="checkbox" id="auth" name="Mail_Auth" value="1" {if $row.Mail_Auth == 1}checked="checked"{/if} /></td>
        </tr>
        <tr id="Mail_Type_Auth">
          <td width="350" class="row_left">{#Settings_smtp_type_auth#}</td>
          <td class="row_right">
            <select class="input" name="Mail_Type_Auth">
              <option value="not" {if $row.Mail_Type_Auth == 'not'}selected="selected"{/if}>{#No#}</option>
              <option value="ssl" {if $row.Mail_Type_Auth == 'ssl'}selected="selected"{/if}>SSL</option>
              <option value="tls" {if $row.Mail_Type_Auth == 'tls'}selected="selected"{/if}>TLS</option>
            </select>
          </td>
        </tr>
        <tr id="Mail_Host">
          <td width="350" class="row_left">{#Settings_smtp_server#}</td>
          <td class="row_right"><input class="input" id="mah" name="Mail_Host" type="text" style="width: 160px" value="{$row.Mail_Host}" /></td>
        </tr>
        <tr id="Mail_Username">
          <td width="350" class="row_left">{#Settings_sm_user#}</td>
          <td class="row_right"><input id="mau" class="input" name="Mail_Username" type="text" style="width: 160px" value="{$row.Mail_Username}" /></td>
        </tr>
        <tr id="Mail_Passwort">
          <td width="350" class="row_left">{#Settings_sm_pass#}</td>
          <td class="row_right"><input id="map" class="input" name="Mail_Passwort"type="text" style="width: 160px" value="{$row.Mail_Passwort}" /></td>
        </tr>
        <tr id="Mail_Sendmailpfad">
          <td width="350" class="row_left">{#Settings_sm_path#}</td>
          <td class="row_right"><input class="input" id="smpath" name="Mail_Sendmailpfad" type="text" style="width: 160px" value="{$row.Mail_Sendmailpfad}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Test_email#}</td>
          <td class="row_right"><input class="button" type="button" value="{#Test#}" onclick="emailcheck();" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_mailsettings#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_emaila_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailemail#}</td>
          <td class="row_right"><input class="input" id="mabs" name="Mail_Absender" type="text" style="width: 160px" value="{$row.Mail_Absender}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_emailab_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailsender#}</td>
          <td class="row_right"><input class="input" name="Mail_Name" type="text" style="width: 160px" value="{$row.Mail_Name}" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_htmlh_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailheader#} </td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 98%; height: 120px" onclick="focusArea(this, 200);" name="Mail_Header">{$row.Mail_Header|sanitize}</textarea></td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_htmlf_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailfooter_html#}
            <div class="leftinf">
              <strong>{#Settings_rp#}</strong> <br />
              %%COMPANY%% = {#User_company#} <br />
              %%TOWN%% = {#User_town#} <br />
              %%ZIP%% = {#User_zip#} <br />
              %%STREET%% = {#User_street#} <br />
              %%ADRESS%% = {#User_street#} <br />
              %%MAIL%% = {#Global_Email#} <br />
              %%TELEFON%% = {#User_phone#} <br />
              %%FAX%% = {#User_fax#} <br />
              %%HTTP%% = {#Comments_web#} <br />
              %%INN%% = {#Settings_Inn#} <br />
              %%KPP%% = {#Settings_Kpp#} <br />
              %%BIK%% = {#Settings_Bik#} <br />
              %%BANK%% = {#Settings_Bank#} <br />
              %%KSCHET%% = {#Settings_Kschet#} <br />
              %%RSCHET%% = {#Settings_Rschet#} <br />
              %%OWNER%% = {#User_first#} <br />
              %%DIREKTOR%% = {#Settings_yname#} <br />
              %%BUH%% = {#Settings_Buh#} <br />
            </div>
          </td>
          <td class="row_right"><textarea cols="" rows="" id="xi" class="input" style="width: 98%; height: 350px" name="Mail_Fuss_HTML">{$row.Mail_Fuss_HTML|sanitize}</textarea></td>
        </tr>
        <tr>
          <td width="350" class="row_left">
            <img class="absmiddle stip" title="{$lang.Settings_textf_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_mailfooter_text#}
            <div class="leftinf">
              <strong>{#Settings_rp#}</strong> <br />
              %%COMPANY%% = {#User_company#} <br />
              %%TOWN%% = {#User_town#} <br />
              %%ZIP%% = {#User_zip#} <br />
              %%STREET%% = {#User_street#} <br />
              %%ADRESS%% = {#User_street#} <br />
              %%MAIL%% = {#Global_Email#} <br />
              %%TELEFON%% = {#User_phone#} <br />
              %%FAX%% = {#User_fax#} <br />
              %%HTTP%% = {#Comments_web#} <br />
              %%INN%% = {#Settings_Inn#} <br />
              %%KPP%% = {#Settings_Kpp#} <br />
              %%BIK%% = {#Settings_Bik#} <br />
              %%BANK%% = {#Settings_Bank#} <br />
              %%KSCHET%% = {#Settings_Kschet#} <br />
              %%RSCHET%% = {#Settings_Rschet#} <br />
              %%OWNER%% = {#User_first#} <br />
              %%DIREKTOR%% = {#Settings_yname#} <br />
              %%BUH%% = {#Settings_Buh#} <br />
            </div>
          </td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 98%; height: 350px" name="Mail_Fuss">{$row.Mail_Fuss|sanitize}</textarea></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_forbidden#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_delReasons_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_delReasons#}</td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 400px; height: 70px" name="Loesch_Gruende">{$row.Loesch_Gruende|sanitize}</textarea></td>
        </tr>
        <tr>
          <td width="350" class="row_left"><img class="absmiddle stip" title="{$lang.Settings_spam_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Settings_fwords#} </td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 400px; height: 70px" onclick="focusArea(this, 140);" name="Spamwoerter">{$row.Spamwoerter|sanitize}</textarea></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_spamrp#}</td>
          <td class="row_right"><input class="input" name="SpamRegEx" type="text" style="width: 160px" value="{$row.SpamRegEx}" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_kcoptions#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left">{#Settings_kcactive#}</td>
          <td class="row_right">
            <label><input type="radio" name="SysCode_Aktiv" value="1" {if $row.SysCode_Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SysCode_Aktiv" value="0" {if $row.SysCode_Aktiv == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_kcformat#}</td>
          <td class="row_right">
            <label><input type="radio" name="KommentarFormat" value="1" {if $row.KommentarFormat == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="KommentarFormat" value="0" {if $row.KommentarFormat == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_kcsmilies#}</td>
          <td class="row_right">
            <label><input type="radio" name="SysCode_Smilies" value="1" {if $row.SysCode_Smilies == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SysCode_Smilies" value="0" {if $row.SysCode_Smilies == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_kcimages#}</td>
          <td class="row_right">
            <label><input type="radio" name="SysCode_Bild" value="1" {if $row.SysCode_Bild == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SysCode_Bild" value="0" {if $row.SysCode_Bild == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_kclinkcode#}</td>
          <td class="row_right">
            <label><input type="radio" name="SysCode_Links" value="1" {if $row.SysCode_Links == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SysCode_Links" value="0" {if $row.SysCode_Links == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_kcemailcode#}</td>
          <td class="row_right">
            <label><input type="radio" name="SysCode_Email" value="1" {if $row.SysCode_Email == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SysCode_Email" value="0" {if $row.SysCode_Email == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_commentsmoderated#}</td>
          <td class="row_right">
            <label><input type="radio" name="Kommentar_Moderiert" value="1" {if $row.Kommentar_Moderiert == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Kommentar_Moderiert" value="0" {if $row.Kommentar_Moderiert == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_commentsiconsuse#}</td>
          <td class="row_right">
            <label><input type="radio" name="Kommentare_Icon" value="1" {if $row.Kommentare_Icon == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Kommentare_Icon" value="0" {if $row.Kommentare_Icon == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_commenticonsw#}</td>
          <td class="row_right"><input class="input" name="Kommentare_IconBreite" type="text" style="width: 50px" value="{$row.Kommentare_IconBreite}" size="5" maxlength="3" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_commentslength#}</td>
          <td class="row_right"><input class="input" name="Kommentar_Laenge" type="text" style="width: 50px" value="{$row.Kommentar_Laenge}" size="5" maxlength="4" /></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_commentspage#}</td>
          <td class="row_right"><input class="input" name="Kommentare_Seite" type="text" style="width: 50px" value="{$row.Kommentare_Seite}" size="5" maxlength="3" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Settings_fieldsettings#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td width="350" class="row_left">{#Settings_NSO#}</td>
          <td width="100" class="row_right">
            <label><input type="radio" name="Reg_DataPflicht" value="1" {if $row.Reg_DataPflicht == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_DataPflicht" value="0" {if $row.Reg_DataPflicht == 0}checked="checked"{/if} />{#No#}</label>
          </td>
          <td class="row_left"><label><input type="checkbox" class="absmiddle" name="Reg_DataPflichtFill" value="1" {if $row.Reg_DataPflichtFill == 1}checked="checked"{/if}/>{#Settings_InputFill#}</label></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_Address#}</td>
          <td width="100" class="row_right">
            <label><input type="radio" name="Reg_Address" value="1" {if $row.Reg_Address == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Address" value="0" {if $row.Reg_Address == 0}checked="checked"{/if} />{#No#}</label>
          </td>
          <td class="row_left"><label><input type="checkbox" class="absmiddle" name="Reg_AddressFill" value="1" {if $row.Reg_AddressFill == 1}checked="checked"{/if}/>{#Settings_InputFill#}</label></td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_agbInf2#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_AgbPflicht" value="1" {if $row.Reg_AgbPflicht == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_AgbPflicht" value="0" {if $row.Reg_AgbPflicht == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_f_company#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Firma" value="1" {if $row.Reg_Firma == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Firma" value="0" {if $row.Reg_Firma == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_f_ustid#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Ust" value="1" {if $row.Reg_Ust == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Ust" value="0" {if $row.Reg_Ust == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_bank#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Bank" value="1" {if $row.Reg_Bank == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Bank" value="0" {if $row.Reg_Bank == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_f_fon#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Fon" value="1" {if $row.Reg_Fon == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Fon" value="0" {if $row.Reg_Fon == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_f_fax#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Fax" value="1" {if $row.Reg_Fax == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Fax" value="0" {if $row.Reg_Fax == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td width="350" class="row_left">{#Settings_f_birth#}</td>
          <td colspan="3" class="row_right">
            <label><input type="radio" name="Reg_Birth" value="1" {if $row.Reg_Birth == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Reg_Birth" value="0" {if $row.Reg_Birth == 0}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" value="{#Save#}" class="button" />
</form>
<script type="text/javascript">
<!-- //
 set_method();
//-->
</script>
