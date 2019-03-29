<html>
<head>
<title>{#MpTitle#}</title>
{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.stip').tooltip();
});

function selfile(src, prv) {
    if (src === undefined) {
        src = '';
    }
    if (prv === 1) {
	parent.frames['right'].location.href = 'index.php?do=browser&sub=right&target={$smarty.request.target}&typ={$smarty.request.typ}&dir={$dir}&image=' + src;
    }
    parent.document.dat.fn.value = src;
}
function delfile(src) {
    if (window.confirm('{#FileDelC#} ' + src)) {
        parent.frames['left'].location.href = 'index.php?do=browser&sub=delfile&target={$smarty.request.target}&typ={$smarty.request.typ}&dir={$dir}&file=' + src;
    }
    parent.document.dat.fn.value = '';
}
function copyfile(src) {
    var newfile = window.prompt('{#MyvYtPeV#}', '');
    if (newfile) {
        parent.frames['left'].location.href = 'index.php?do=browser&sub=copy&target={$smarty.request.target}&typ={$smarty.request.typ}&dir={$dir}&file=' + src + '&newfile=' + newfile;
    }
}
function renamefile(src) {
    var newfile = window.prompt('{#MyvYtPeV#}', '');
    if (newfile) {
        parent.frames['left'].location.href = 'index.php?do=browser&sub=rename&target={$smarty.request.target}&typ={$smarty.request.typ}&dir={$dir}&file=' + src + '&newfile=' + newfile;
    }
}
parent.document.dat.dateiname.value='{$dir}';
parent.document.getElementById('select').innerHTML='{$dir}';
//-->
</script>
</head>
<body topmargin="0" leftmargin="0" id="mediapool" oncontextmenu="return false">
  <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tableborder">
    <tr>
      <td class="header">&nbsp;</td>
      <td class="header">{#MpFiles#}</td>
      <td align="center" class="header">{#GlobalSize#}</td>
      <td align="center" class="header">{#MpCreated#}</td>
      <td align="center" class="header"><img src="{$imgpath}/edit.png" alt="" border="0" /></td>
      <td align="center" class="header"><img src="{$imgpath}/copy.png" alt="" border="0" /></td>
      <td align="center" class="header"><img src="{$imgpath}/delete.png" alt="" border="0" /></td>
    </tr>
    {if isset($dirup) && $dirup == 1}
      <form method="post" action="index.php?noout=1&amp;do=browser&amp;sub=left&amp;target={$smarty.request.target}&amp;typ={$smarty.request.typ}&amp;dir={$dir}../" name="updir"></form>
      <tr class="second" style="cursor: pointer" onclick="document.forms['updir'].submit();">
        <td width="5%"><div align="center"><a href="javascript: void(0);" onclick="document.forms['updir'].submit();"><img src="{$imgpath}/folder_up.gif" alt="" border="0" /></a></div></td>
        <td width="45%">&nbsp;<a href="javascript: void(0);" onclick="document.forms['updir'].submit();">..</a>&nbsp;&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    {/if}
    {foreach from=$bfiles item=file}
      {assign var=id value=$id+1}
      <tr class="{cycle values='first,second'}" style="cursor: pointer" onclick="document.forms['go_folder_{$id}'].submit();">
        <td width="5%" style="padding: 5px">
          <form method="post" action="index.php?noout=1&amp;do=browser&amp;target={$smarty.request.target}&amp;typ={$file->open}" name="go_folder_{$id}"></form>
          <div align="center"><img src="{$imgpath}/folder.gif" alt="" border="0" /></div></td>
        <td width="45%">&nbsp;<a href="javascript: void(0);" onclick="selfile(); document.forms['go_folder_{$id}'].submit();"><strong>{$file->val}</strong></a>&nbsp;&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    {/foreach}
    {foreach from=$dats item=dat}
      <tr class="{cycle values='second,first'}">
        <td width="5%"><div align="center"><img src="{$imgpath}/mediapool/{$dat->ext}.gif" alt="" border="0" /></div></td>
        <td width="45%">
          {if $dat->ext != 'php'}
            <a href="javascript: void(0);" onclick="
               {if $dat->ext == 'gif' || $dat->ext == 'png' || $dat->ext == 'jpg' || $dat->ext == 'jpeg'}
                selfile('{$dat->val}', {if $smarty.request.typ == 'image'}1{/if});
               {else}
                selfile('{$dat->val}');
               {/if}
               "> {if $dat->ext == 'gif' || $dat->ext == 'png' || $dat->ext == 'jpg' || $dat->ext == 'jpeg'}
              {$dat->image}
            {else}
              {$dat->val}
            {/if}
          </a>
          {else}
            {$dat->val}
            {/if}
            </td>
            <td align="center">{$dat->size}</td>
            <td align="center">{$dat->date}</td>
            <td align="center">
              {if perm('mediapool_del')}
                <a class="stip" title="{#Rename#}" href="javascript: void(0);" onclick="renamefile('{$dat->val}');"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
                {/if}
            </td>
            <td align="center">
              {if perm('mediapool_del')}
                <a class="stip" title="{#Copy#}" href="javascript: void(0);" onclick="copyfile('{$dat->val}');"><img src="{$imgpath}/copy.png" alt="" border="0" /></a>
                {/if}
            </td>
            <td align="center">
              {if perm('mediapool_del')}
                <a class="stip" title="{#Global_Delete#}" href="javascript: void(0);" onclick="delfile('{$dat->val}');"><img src="{$imgpath}/delete.png" alt="" border="0" /></a>
                {/if}
            </td>
          </tr>
          {/foreach}
          </table>
        </body>
      </html>
