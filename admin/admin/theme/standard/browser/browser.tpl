<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>{#MpTitle#} {$upload}</title>
{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
<script type="text/javascript">
  <!-- //
function submitForm() {
    if (document.dat.fn.value == '') {
        alert('{#MpNoFile#}');
    } else {
        {if $smarty.request.mode == 'editor'}
        window.opener.CKEDITOR.tools.callFunction({$funcnum}, '{$upload}' + document.dat.dateiname.value + document.dat.fn.value);
        {elseif $smarty.request.mode == 'system'}
        window.opener.document.getElementById('newFile_' + {$smarty.request.elemid}).value = document.dat.fn.value;
        {if $smarty.request.typ == 'image'}
        {if $smarty.request.target == 'screenshots'}
        window.opener.document.getElementById('UpInf_' + {$smarty.request.elemid}).innerHTML = '<img border="0" src="index.php?do=browser&sub=thumb&width=80&noout=1&target=screenshots&dir=' + document.dat.dateiname.value + '&image=' + document.dat.fn.value + '">';
        {else}
        window.opener.document.getElementById('UpInf_' + {$smarty.request.elemid}).innerHTML = '<img border="0" src="../uploads/{$folder}/' + document.dat.fn.value + '">';
        {/if}
        {else}
        window.opener.document.getElementById('UpInf_' + {$smarty.request.elemid}).innerHTML = document.dat.fn.value;
        {/if}
        {/if}
        setTimeout('self.close()', 100);
    }
}
function newFolder() {
    var dname = window.prompt('{#NewFolderInf#}', '');
    if (dname) {
        parent.frames['left'].location.href = 'index.php?noout=1&do=browser&sub=left&target={$smarty.request.target}&typ={$smarty.request.typ}&dir=' + document.dat.dateiname.value + '&newdir=' + dname;
    }
}
function fileUpload() {
    var url = 'index.php?noout=1&do=browser&sub=upload&pfad=' + document.dat.dateiname.value + '&target={$smarty.request.target}&typ={$smarty.request.typ}';
    var winWidth = 500;
    var winHeight = 400;
    var w = (screen.width - winWidth) / 2;
    var h = (screen.height - winHeight) / 2 - 60;
    var features = 'scrollbars=no,width=' + winWidth + ',height=' + winHeight + ',top=' + h + ',left=' + w;
    window.open(url, 'upload2mp', features);
}
//-->
</script>
</head>
<body class="mediapool_body" oncontextmenu="return false">
  <form style="display: inline;" name="dat" onsubmit="return false;">
    <input type="hidden" name="dateiname" />
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
      <tr valign="top">
        <td width="65%">
          <div class="mediapool_leftframe">
            <iframe frameborder="0" name="left" id="left" width="100%" style="height: 470px" src="index.php?noout=1&amp;do=browser&amp;sub=left&amp;target={$smarty.request.target}&amp;typ={$smarty.request.typ}"></iframe>
          </div>
        </td>
        {if $smarty.request.typ == 'image'}
          <td width="35%">
            <div class="mediapool_rightframe">
              <iframe frameborder="0" name="right" id="right" width="100%" style="height: 470px" src="index.php?noout=1&amp;do=browser&amp;sub=right"></iframe>
            </div>
          </td>
        {/if}
      </tr>
    </table>
    <div style="padding: 5px">
      {if !empty($smarty.request.typ)}
        <span id="select" style="display: none"></span>
        <input type="text" name="fn" style="width: 350px" readonly="readonly" disabled="disabled" class="mediapool_selected" />&nbsp;&nbsp;&nbsp;
        <input type="button" class="button" onclick="submitForm();" value="{#MpGetFile#}" />
        {if perm('mediapool_folder')}
          {if $create == 1}
            <input type="button" class="button" onclick="newFolder();" value="{#NewFolder#}" />
          {/if}
          <input type="button" class="button" onclick="fileUpload();" value="{#FileUpload#}" />
        {/if}
      </div>
    {/if}
  </form>
</body>
</html>
