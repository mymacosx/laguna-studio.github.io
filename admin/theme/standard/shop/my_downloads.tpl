<div class="box_innerhead">{#LoginExternVd#}</div>
{if $smarty.get.sub != 'showfile'}
  <div class="infobox">{#Shop_go_mydownloadsInf#}</div>
{/if}
{if isset($smarty.get.sub) && $smarty.get.sub == 'showfile'}
  <div class="infobox">{#Shop_go_mydownloadsInf2#}</div>
{/if}
{if $error}
  <div class="error_box">
    <div class="h3">{#Error#}</div>
    <ul>
      {foreach from=$error item=e}
        <li>{$e}</li>
        {/foreach}
    </ul>
    <a style="text-decoration: none" href="javascript: history.go(-1);"><img class="absmiddle" src="{$imgpath_page}arrow_left_small.png" alt="" /> {#GlobalBack#}</a> </div>
    {/if}
    {if isset($smarty.get.sub) && $smarty.get.sub == 'showfile'}
  <form method="post" action="index.php">
    <table width="100%" cellspacing="1" cellpadding="3">
      <tr>
        <td width="200" class="shop_mydownloads_rows">{#Shop_mydownloads_filename#}&nbsp;</td>
        <td class="shop_dl_second">{$file->Titel|sanitize}</td>
      </tr>
      <tr>
        <td width="200" class="shop_mydownloads_rows">{#Shop_mydownloads_filename#}&nbsp;</td>
        <td class="shop_dl_second">{$file->Datei}</td>
      </tr>
      {if $file->Beschreibung}
        <tr>
          <td width="200" class="shop_mydownloads_rows">{#Description#}&nbsp;</td>
          <td class="shop_dl_second">{$file->Beschreibung}&nbsp;</td>
        </tr>
      {/if}
      {if $file->Gesperrt == 1}
        <tr>
          <td width="200" class="shop_mydownloads_rows">{#Shop_mydownloads_filehint#}&nbsp;</td>
          <td class="shop_dl_second">
            {#Shop_mydownloads_loecked#}
            <p><em>{$file->GesperrtGrund}</em></p>
          </td>
        </tr>
      {else}
        {if !empty($file->KommentarAdmin)}
          <tr>
            <td width="200" class="shop_mydownloads_rows">{#Global_Comment#}&nbsp;</td>
            <td class="shop_dl_second">{$file->KommentarAdmin|default:'-'}&nbsp;</td>
          </tr>
        {/if}
        <tr>
          <td width="200" class="shop_mydownloads_rows">{#Shop_mydownloads_filecommntuser#}&nbsp;</td>
          <td class="shop_dl_second"><textarea name="KommentarBenutzer" cols="" rows="" class="input" id="KommentarBenutzer" style="width: 98%; height: 100px">{$file->KommentarBenutzer|sanitize}</textarea></td>
        </tr>
        {if $file->UrlLizenz_Pflicht == 1}
          <tr>
            <td width="200" class="shop_mydownloads_rows"> {#Shop_mydownloads_fileurl#} <br />
              <small>{#Shop_mydownloads_fileurl_hint#}</small></td>
            <td class="shop_dl_second"><input class="input" style="width: 98%" type="text" name="UrlLizenz" id="UrlLizenz" value="{$file->UrlLizenz|sanitize}" /></td>
          </tr>
        {/if}
        <tr>
          <td width="200" class="shop_mydownloads_rows">&nbsp;</td>
          <td class="shop_dl_second"><input id="agbok" name="agb_ok" type="checkbox" value="1" /> <a href="index.php?p=shop&amp;action=agb">{#Shop_mydownloads_fileagbok#}</a></td>
        </tr>
        <tr>
          <td width="200" class="shop_mydownloads_rows">&nbsp;</td>
          <td class="shop_dl_second"><input type="submit" class="button" value="{#Shop_mydownloads_filedownload#}" /></td>
        </tr>
      {/if}
    </table>
    <input type="hidden" name="p" value="shop" />
    <input type="hidden" name="action" value="mydownloads" />
    <input type="hidden" name="sub" value="getfile" />
    <input type="hidden" name="Id" value="{$smarty.get.Id}" />
    <input type="hidden" name="FileId" value="{$smarty.get.FileId}" />
    <input type="hidden" name="getId" value="{$smarty.get.getId}" />
    <input type="hidden" name="FileName" value="{$file->Titel|sanitize}" />
  </form>
{else}
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">

<script type="text/javascript">
<!-- //
function print_container(id, act) {
    var tag = 'body';
    var html = document.getElementById(id).innerHTML;
    html = html.replace(/&lt;/gi, '<');
    html = html.replace(/&gt;/gi, '>');
    var act = act == 'preform' ? '<pre>' : '';
    var pFenster = window.open('', null, 'height=600,width=780,toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
    var HTML = '<html><' + tag + ' style="font-family: arial,verdana;font-size: 12px" onload="window.print()">' + act + html + '</' + tag + '></html>';
    pFenster.document.write(HTML);
    pFenster.document.close();
}
function request(id) {
    var text = '{#OrderRequestText#}';
    text = text.replace(/__ORDER__/gi, id);
    text = text.replace(/__USER__/gi, '{$smarty.session.cp_uname}');
    document.getElementById('request').style.display = '';
    document.getElementById('request_subject').value = '{#OrdersRequestSubject#} ' + id;
    document.getElementById('request_text').value = text;
}
//-->
</script>

        <br />
        {foreach from=$downloads item=dl name=d}
          <div class="shop_headers">
            <span style="" id="so_{$dl->Id}">
              <a href="javascript: void(0);" onclick="document.getElementById('{$dl->Id}').style.display = ''; document.getElementById('sc_{$dl->Id}').style.display = '';document.getElementById('so_{$dl->Id}').style.display = 'none';">[+]&nbsp;{$dl->ArtName} </a></span> <span style="display: none" id="sc_{$dl->Id}">
              <a href="javascript: void(0);" onclick="document.getElementById('{$dl->Id}').style.display = 'none';document.getElementById('so_{$dl->Id}').style.display = '';document.getElementById('sc_{$dl->Id}').style.display = 'none';">[-]&nbsp;{$dl->ArtName} </a>
            </span>
          </div>
          <div id="{$dl->Id}" style="display: none">
            {if $dl->Lizenz}
              <div class="infobox h2">{#Shop_yLicData#}: {$dl->Lizenz}</div>
            {/if}
            <table width="100%" cellspacing="1" cellpadding="3">
              <tr>
                <td valign="top"><strong>{#Shop_mydownloads_filename#}</strong></td>
                <td valign="top"><strong>{#Shop_mydownloads_downloadable#}</strong></td>
                <td valign="top"><strong>{#GlobalSize#}</strong></td>
              </tr>
              {if $dl->DataFiles}
                <tr>
                  <td colspan="3" class="shop_mydownloads_categs">{#Shop_mydownloads_full#}</td>
                </tr>
              {/if}
              {foreach from=$dl->DataFiles item=df}
                <tr>
                  <td valign="top" class="shop_mydownloads_rows">
                    {if $df->Abgelaufen == 1}
                      {$df->Titel}
                    {else}
                      <a class="stip" title="{$df->Beschreibung|tooltip}" href="index.php?p=shop&amp;action=mydownloads&amp;sub=showfile&amp;Id={$df->Id}&amp;FileId={$dl->ArtikelId}&amp;getId={$df->Id}">{$df->Titel}</a>
                    {/if}
                  </td>
                  <td class="shop_mydownloads_rows"> {$dl->DownloadBis|date_format: '%d.%m.%Y'} </td>
                  <td class="shop_mydownloads_rows">{$df->size} ??</td>
                </tr>
              {/foreach}
              {if $dl->DataFilesUpdates}
                <tr>
                  <td colspan="3" class="shop_mydownloads_categs">{#Shop_mydownloads_update#}</td>
                </tr>
              {/if}
              {foreach from=$dl->DataFilesUpdates item=df}
                <tr>
                  <td valign="top" class="shop_mydownloads_rows">
                    {if $df->Abgelaufen == 1}
                      {$df->Titel}
                    {else}
                      <a class="stip" title="{$df->Beschreibung|tooltip}"  href="index.php?p=shop&amp;action=mydownloads&amp;sub=showfile&amp;Id={$df->Id}&amp;FileId={$dl->ArtikelId}&amp;getId={$df->Id}">{$df->Titel}</a>
                    {/if}
                  </td>
                  <td class="shop_mydownloads_rows"> {$dl->DownloadBis|date_format: '%d.%m.%Y'} </td>
                  <td class="shop_mydownloads_rows">{$df->size} ??</td>
                </tr>
              {/foreach}
              {if $dl->DataFilesBugfixes}
                <tr>
                  <td colspan="3" class="shop_mydownloads_categs">{#Shop_mydownloads_bugfix#}</td>
                </tr>
              {/if}
              {foreach from=$dl->DataFilesBugfixes item=df}
                <tr>
                  <td valign="top" class="shop_mydownloads_rows"><a class="stip" title="{$df->Beschreibung|tooltip}"  href="index.php?p=shop&amp;action=mydownloads&amp;sub=showfile&amp;Id={$df->Id}&amp;FileId={$dl->ArtikelId}&amp;getId={$df->Id}">{$df->Titel}</a></td>
                  <td class="shop_mydownloads_rows">{$dl->DownloadBis|date_format: $lang.DateFormatEsdTill}</td>
                  <td class="shop_mydownloads_rows">{$df->size} ??</td>
                </tr>
              {/foreach}
              {if $dl->DataFilesOther}
                <tr>
                  <td colspan="3" class="shop_mydownloads_categs">{#ActionOther#}</td>
                </tr>
              {/if}
              {foreach from=$dl->DataFilesOther item=df}
                <tr>
                  <td valign="top" class="shop_mydownloads_rows"><a class="absmiddle stip" title="{$df->Beschreibung|tooltip}" href="index.php?p=shop&amp;action=mydownloads&amp;sub=showfile&amp;Id={$df->Id}&amp;FileId={$dl->ArtikelId}&amp;getId={$df->Id}">{$df->Titel}</a></td>
                  <td class="shop_mydownloads_rows">{#Shop_mydownloads_infi#}</td>
                  <td class="shop_mydownloads_rows">{$df->size} ??</td>
                </tr>
              {/foreach}
            </table>
          </div>
          <br />
        {/foreach}
        </div>
      </td>
    </tr>
  </table>
{/if}
<br />
