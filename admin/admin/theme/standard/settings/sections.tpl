<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    var tcs = document.getElementById('Template').options[document.getElementById('Template').selectedIndex].value;
    $('#editform').validate({
	rules: {
            Name: { required: true },
            LimitNews: { required: true, range: [2, 25] },
            LimitNewsArchive: { required: true, range: [5, 50] },
            LimitNewlinks: { required: true, range: [1, 25] },
            LimitNewDownloads: { required: true, range: [1, 25] },
            LimitNewProducts: { required: true, range: [1, 25] },
            LimitNewCheats: { required: true, range: [1, 25] },
            LimitNewGalleries: { required: true, range: [1, 25] },
            LimitLastPosts: { required: true, range: [1, 25] },
            LimitTopArticles: { required: true, range: [1, 25] },
            LimitTopcontent: { required: true, range: [1, 25] },
            CSS_Theme: {
                required: true,
                minlength: 3,
                remote: 'index.php?do=settings&sub=checkcsspath&tcs=' + tcs
            }
	},
	messages: {
	    CSS_Theme: {
                remote: $.validator.format("{#Validate_wrongCSSPath#}")
            }
	},
        submitHandler: function() {
	    document.forms['editform'].submit();
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

function caa() {
    if(document.getElementById('sa').checked == true) {
        document.getElementById('catr').style.display = '';
    } else {
	document.getElementById('catr').style.display = 'none';
    }
}
//-->
</script>

<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post" name="editform" id="editform">
  <div id="a_sett">
    <div><a href="#">{#SettingsGen#}</a></div>
    <div>
      <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
        <tr>
          <td width="200" class="row_left">{#Sections_spez#}</td>
          <td class="row_right"><input class="input" style="width: 150px" name="Name" type="text" id="Name" value="{$res->Name|sanitize}" /></td>
        </tr>
        {if $settings.Domains == 1}
          <tr>
            <td class="row_left"><img class="absmiddle stip" title="{$lang.DomainsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Domains#}</td>
            <td class="row_right"><input class="input" style="width: 150px" name="Domains" type="text" value="{$res->Domains|sanitize}" /></td>
          </tr>
        {/if}
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Sections_ThemeInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Sections_theme#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Template" id="Template">
              {foreach from=$folders item=tp}
                <option value="{$tp->Name}" {if $tp->Name == $res->Template}selected="selected"{/if}>{$tp->Name}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Sections_CssInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Sections_Css#}</td>
          <td class="row_right"><input class="input" style="width: 150px" name="CSS_Theme" type="text" value="{$res->CSS_Theme|sanitize}" /></td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Sections_InActiveInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Sections_active#}</td>
          <td class="row_right">
            <label><input onclick="caa();" type="radio" name="Aktiv" value="1" {if $res->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input onclick="caa();" type="radio" name="Aktiv" id="sa" value="0" {if $res->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
          </td>
          </td>
        </tr>
        <tr id="catr">
          <td class="row_left">{#Sections_msginactive#}</td>
          <td class="row_right"><textarea cols="" rows="" class="input" style="width: 350px; height: 100px" name="Meldung" id="Meldung">{$res->Meldung|sanitize}</textarea></td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Sections_OpenPassInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#LoginPass#}</td>
          <td class="row_right"><input type="text" class="input" style="width: 150px" name="Passwort" id="pw" value="{$res->Passwort}" />
            <input class="button" onclick="openWindow('../index.php?area={$res->Id}&pass=' + document.getElementById('pw').value, '', '980', '800', 1);" type="button" value="{#Sections_OpenPass#}" /></td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_newslimit#}</td>
          <td class="row_right"><input class="input" name="LimitNews" type="text" id="LimitNews" value="{$res->LimitNews}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_newslimit_arc#}</td>
          <td class="row_right"><input class="input" name="LimitNewsArchive" type="text" id="LimitNewsArchive" value="{$res->LimitNewsArchive}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitNewlinks#}</td>
          <td class="row_right"><input class="input" name="LimitNewlinks" type="text" value="{$res->LimitNewlinks}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitNewDownloads#}</td>
          <td class="row_right"><input class="input" name="LimitNewDownloads" type="text" value="{$res->LimitNewDownloads}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitNewProducts#}</td>
          <td class="row_right"><input class="input" name="LimitNewProducts" type="text" value="{$res->LimitNewProducts}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitNewCheats#}</td>
          <td class="row_right"><input class="input" name="LimitNewCheats" type="text" value="{$res->LimitNewCheats}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitNewGalleries#}</td>
          <td class="row_right"><input class="input" name="LimitNewGalleries" type="text" value="{$res->LimitNewGalleries}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitLastPosts#}</td>
          <td class="row_right"><input class="input" name="LimitLastPosts" type="text" value="{$res->LimitLastPosts}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitLastThreads#}</td>
          <td class="row_right"><input class="input" name="LimitLastThreads" type="text" value="{$res->LimitLastThreads}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitTopArticles#}</td>
          <td class="row_right"><input class="input" name="LimitTopArticles" type="text" value="{$res->LimitTopArticles}" size="4" maxlength="2" /></td>
        </tr>
        <tr>
          <td class="row_left">{#LimitTopcontent#}</td>
          <td class="row_right"><input class="input" name="LimitTopcontent" type="text" value="{$res->LimitTopcontent}" size="4" maxlength="2" /></td>
        </tr>
      </table>
    </div>
    <div><a href="#">{#Sections_Messg#}</a></div>
    <div>
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td width="200" class="row_left">{#Sections_MessgShow#}</td>
          <td class="row_right">
            <label><input type="radio" name="ZeigeStartText" value="1" {if $res->ZeigeStartText == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input  type="radio" name="ZeigeStartText" value="0" {if $res->ZeigeStartText != 1}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_MessgShowOnly#}</td>
          <td class="row_right">
            <label><input type="radio" name="ZeigeStartTextNur" value="1" {if $res->ZeigeStartTextNur == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input  type="radio" name="ZeigeStartTextNur" value="0" {if $res->ZeigeStartTextNur != 1}checked="checked"{/if} />{#No#}</label>
          </td>
        </tr>
      </table>
      {$StartText} </div>
    <div><a href="#">{#Sections_tTemplates#}</a></div>
    <div>
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td width="200" class="row_left">{#Sections_start#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_index">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_index == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_shop#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_shop">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_shop == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_content#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_content">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_content == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_news#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_news">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_news == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_newsarc#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_newsarchive">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_newsarchive == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_newsletter#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_newsletter">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_newsletter == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_sitemap#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_sitemap">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_sitemap == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_useractions#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_useraction">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_useraction == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_calendar#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_calendar">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_calendar == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_faq#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_faq">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_faq == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_gallery#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_gallery">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_gallery == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_articles#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_articles">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_articles == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_products#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_products">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_products == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_downloads#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_downloads">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_downloads == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_links#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_links">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_links == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_signup#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_register">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_register == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_forums#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_forums">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_forums == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_userlist#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_members">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_members == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_pn#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_pn">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_pn == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_passlost#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_pwlost">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_pwlost == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_manufacturer#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_manufacturer">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_manufacturer == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_cheats#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_cheats">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_cheats == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_polls#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_polls">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_poll == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_guestbook#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_guestbook">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_guestbook == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_misc#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_misc">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_misc == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Sections_imprint#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_imprint">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_imprint == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left">{#Search#}</td>
          <td class="row_right">
            <select class="input" style="width: 150px" name="Tpl_search">
              {foreach from=$templates item=t}
                <option value="{$t}" {if $res->Tpl_search == $t}selected="selected"{/if}>{$t}</option>
              {/foreach}
            </select>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <br />
  <input name="save" type="hidden" id="save" value="1" />
  <input name="section" type="hidden" id="section" value="{$smarty.request.section|default:1}" />
  <input type="submit" value="{#Save#}" class="button" />
  <input type="button" class="button_second" value="{#Close#}" onclick="closeWindow(true);" />
</form>
<script type="text/javascript">
<!-- //
caa();
//-->
</script>
