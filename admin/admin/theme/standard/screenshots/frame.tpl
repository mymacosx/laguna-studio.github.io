<body style="background: #fff; padding: 0px">
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#open_screen').on('click', function() {
         $('#add_screen').slideToggle();
         $('#content_screen').slideToggle();
    });
});
parent.document.getElementById('screenshots').value = '{$thearray}';

function check(){
    if( document.kform.titel.value == "") {
	alert("{#Inline_upNoT#}");
	document.kform.titel.focus();
	return false;
    }
    if( document.kform.text.value == "") {
	alert("{#Inline_upNoD#}");
	document.kform.text.focus();
	return false;
    }
    if( document.kform.shot.value == "") {
	alert("{#Inline_upNoI#}");
	return false;
    }
}
//-->
</script>

{if perm('screenshot_upload')}
  <div class="subheaders">
    {#Global_InlineInf#}
    <br />
    <br />
    <span id="open_screen" style="cursor: pointer; font-weight: bold"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Global_InlineNew#}</span>
  </div>
  <div id="add_screen" style="display: none">
    <form name="kform" action="?do=screenshots&amp;action=add&amp;noframes=1&amp;langcode={$smarty.request.langcode}&amp;fieldname={$smarty.request.fieldname}" method="post" enctype="multipart/form-data" onSubmit="return check();">
      <input type="hidden" name="code" value="{$thearray}" />
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td align="center" class="headers">{#Global_Name#}</td>
          <td class="headers">{#Global_descr#}</td>
          <td align="center" class="headers">{#Global_Actions#}</td>
        </tr>
        <tr class="second">
          <td align="center" valign="top" width="250"><input class="input" type="text" name="titel" style="width: 95%;" /></td>
          <td>
            <textarea class="input" name="text" cols="40" rows="4" style="width: 98%; height: 50px;"></textarea>
          </td>
          <td align="center" width="300"  valign="top">
            <input style="width: 200px" type="file" name="shot" />
            <input type="submit" class="button" value="{#UploadButton#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
<div id="content_screen">
  <table width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td align="center" class="headers"><span class="stip" title="{$lang.Global_InlineInf2|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_InlineCode#} </td>
      <td align="center" class="headers">{#Global_Name#}</td>
      <td class="headers">{#Global_descr#}</td>
      <td align="center" class="headers">{#Image#}</td>
      <td align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {section name=hw loop=$hiddenvalue}
      <form action="?do=screenshots&amp;action=choice&amp;noframes=1&amp;langcode={$smarty.request.langcode}&amp;fieldname={$smarty.request.fieldname}" method="post">
        <input type="hidden" name="code" value="{$hiddenvalue[hw].hiddencode}" />
        <input type="hidden" name="id" value="{$hiddenvalue[hw].hiddenid}" />
        <tr class="{cycle values='second,first'}">
          <td align="center" valign="top" width="80" class="c1"><input class="input" style="width: 90%" type="text" value="[SCREEN:{$hiddenvalue[hw].hiddenid}]" readonly="readonly" /></td>
          <td align="center" valign="top" width="160" class="c1"><input class="input" type="text" name="titel" style="width: 90%" value="{$hiddenvalue[hw].titel|sanitize}" /></td>
          <td valign="top"><textarea class="input" name="text" cols="40" rows="4" style="width: 97%; height: 60px;">{$hiddenvalue[hw].text|sanitize}</textarea></td>
          <td width="90" align="center">
            <div id="UpInf_{$hiddenvalue[hw].hiddenid}"><img src="../lib/image.php?action=screenshots&amp;width=80&amp;image={$hiddenvalue[hw].image}" border="0" alt="" /></div>
            <input id="newFile_{$hiddenvalue[hw].hiddenid}" name="image" type="hidden" value="{$hiddenvalue[hw].image}" />
          </td>
          <td width="160" align="center">
            {if perm('screenshot_delete')}
              <label><input type="radio" name="submit" value="edit" checked="checked" />{#Save#}</label>
              <label><input type="radio" name="submit" value="delete" />{#Global_Delete#}</label>
              <br />
              <input type="submit" class="button" style="width: 140px" value="{#Forums_delT_submit#}">
              <br />
            {/if}
            {if perm('mediapool')}
              <input type="button" class="button" style="width: 140px" onclick="uploadBrowser('image', 'screenshots', {$hiddenvalue[hw].hiddenid});" value="{#Global_ImgSel#}" />
            {/if}
          </td>
        </tr>
      </form>
    {/section}
  </table>
</div>
