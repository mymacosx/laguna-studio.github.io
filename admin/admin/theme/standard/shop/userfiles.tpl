<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#new').validate({
	rules: {
            DownloadBis: { required: true
            }
        },
	messages: { },
	submitHandler: function() {
            document.forms['new'].submit();
        }
    });

    $('#save').validate( {
	rules: {
            {foreach from=$userfiles item=uf name=ufi}
	    'DownloadBis[{$uf->Id}]': { required: true }
	    {if !$smarty.foreach.ufi.last},{/if}
            {/foreach}
	},
	messages: {
            {foreach from=$userfiles item=uf name=ufi}
	    'DownloadBis[{$uf->Id}]': { required: '' }
	    {if !$smarty.foreach.ufi.last},{/if}
            {/foreach}
	},
	submitHandler: function() {
            document.forms['form'].submit();}
    });

    $('#dlbis').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    {foreach from=$userfiles item=uf}
    $('#DownloadBis_{$uf->Id}').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    {/foreach}
});
//-->
</script>

<div class="popbox">
  {if $userfiles}
    <form method="post" action="index.php?do=shop&amp;sub=user_downloads&amp;save=1&amp;user={$smarty.request.user}&amp;noframes=1" id="form" name="form">
      <table width="100%" border="0" cellpadding="2" cellspacing="0" class="tableborder">
        <tr>
          <td class="headers">{#Shop_downloads_product#}</td>
          <td align="center" class="headers">{#Shop_downloads_dltill#}</td>
          <td align="center" class="headers">{#Shop_downloads_lic#}</td>
          <td align="center" class="headers">{#Shop_downloads_urli#}</td>
          <td align="center" class="headers">{#Shop_downloads_acomment#}</td>
          <td align="center" class="headers">{#Shop_downloads_ucomment#}</td>
          <td align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
        </tr>
        {foreach from=$userfiles item=uf}
          <tr>
            <td class="subheaders" style="padding: 6px" colspan="7"><h3>{$uf->article|sanitize}</h3></td>
          </tr>
          <tr class="{cycle values='second,first'}">
            <td valign="top" class="row_spacer">
              <div><input style="width: 180px" class="input" name="GesperrtGrund[{$uf->Id}]" type="text" value="{$uf->GesperrtGrund|sanitize|default:$lang.Shop_downloads_deflocked}" /></div>
              <input type="hidden" name="Userfile[{$uf->Id}]" value="{$uf->Id}" /> {#Shop_downloads_flocked#}
              <label><input type="radio" name="Gesperrt[{$uf->Id}]" value="1" {if $uf->Gesperrt == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="Gesperrt[{$uf->Id}]" value="0" {if $uf->Gesperrt != 1}checked="checked"{/if} />{#No#}</label>
            </td>
            <td align="center" valign="top" class="row_spacer"><input style="width: 60px" class="input" name="DownloadBis[{$uf->Id}]" type="text" id="DownloadBis_{$uf->Id}" value="{$uf->DownloadBis|date_format: '%d.%m.%Y'}" readonly="readonly" /></td>
            <td align="center" valign="top" class="row_spacer"><input style="width: 110px" class="input" name="Lizenz[{$uf->Id}]" type="text" id="Lizenz[{$uf->Id}]" value="{$uf->Lizenz}" readonly="readonly" /></td>
            <td align="center" valign="top" class="row_spacer">
              <input class="input" name="UrlLizenz[{$uf->Id}]" type="text" id="url_{$uf->Id}" value="{$uf->UrlLizenz}" />
              <br />
              {#Shop_downloads_fmust#}
              <label><input type="radio" name="UrlLizenz_Pflicht[{$uf->Id}]" value="1" {if $uf->UrlLizenz_Pflicht == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="UrlLizenz_Pflicht[{$uf->Id}]" value="0" {if $uf->UrlLizenz_Pflicht != 1}checked="checked"{/if} />{#No#}</label>
            </td>
            <td align="center" class="row_spacer"><textarea cols="" rows="" class="input" name="KommentarAdmin[{$uf->Id}]" id="comment_admin[{$uf->Id}]" style="width: 150px;height: 50px" onclick="focusArea(this, 200);">{$uf->KommentarAdmin|sanitize}</textarea></td>
            <td align="center" class="row_spacer"><textarea cols="" rows="" class="input" name="KommentarBenutzer[{$uf->Id}]" id="comment_user[{$uf->Id}]" style="width: 150px;height: 50px" onclick="focusArea(this, 200);">{$uf->KommentarBenutzer|sanitize}</textarea></td>
            <td class="row_spacer"><input class="stip" title="{$lang.Global_Delete|sanitize}" name="del[{$uf->Id}]" type="checkbox" id="del[{$uf->Id}]" value="1" /></td>
          </tr>
        {/foreach}
      </table>
      <br />
      <input type="submit" class="button" value="{#Save#}" />
      <input name="save" type="hidden" id="save" value="1" />
      <input name="name" type="hidden" id="name" value="{$smarty.request.name|sanitize}" />
      <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    </form>
    <br />
    <br />
  {/if}
  <div class="header">{#Shop_downloads_newufi#}</div>
  <form method="post" action="index.php?do=shop&amp;sub=user_downloads&amp;newfile=1&amp;user={$smarty.request.user}&amp;noframes=1" id="new" name="new">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="20%" class="row_left">{#Shop_downloads_product#}</td>
        <td class="row_right">
          <select class="input" name="ArtikelId">
            {foreach from=$produkte item=files}
              <option value="{$files->Id}||{$files->Titel_1}">{$files->Titel_1}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="20%" class="row_left">{#Shop_downloads_dltill#}</td>
        <td class="row_right"><input class="input" readonly="readonly" type="text" name="DownloadBis" id="dlbis" value="" /></td>
      </tr>
      <tr>
        <td colspan="2" class="thirdrow">
          <input type="submit" class="button" value="{#Save#}" />
          <input name="Lizenz" type="hidden" value="{$lizenz}" />
          <input name="newfile" type="hidden" value="1" />
          <input name="name" type="hidden" id="name" value="{$smarty.request.name|sanitize}" />
        </td>
      </tr>
    </table>
  </form>
</div>
