<div class="popup_header h2">{#Forums_AddNewAttachments#}</div>
<div class="popup_content padding5">
  <div class="popup_box">
    {if !empty($UpError)}
      <strong>{#Error#}</strong>
      <div class="error_box">
        {foreach from=$UpError item=f}
          {$f}
          <br />
        {/foreach}
      </div>
    {else}
      {if empty($smarty.request.left_f)}
        {assign var=acount value=$smarty.request.left|default:''}
      {elseif !empty($smarty.request.left_f)}
        {assign var=acount value=$smarty.request.left_f-1|default:''}
      {else}
        {assign var=acount value=$maxattachment}
      {/if}
      <form name="formular" enctype="multipart/form-data" action="index.php?p=misc&amp;do=attachment&amp;action=upload" method="post">
        <input id="count" type="hidden" name="count" value="{$maxattachment}" />
        <input id="count" type="hidden" name="fid" value="{$smarty.request.fid|default:''}" />
        {section name="file" loop=$acount}
          <input class="input" name="attachment[]" type="file" size="40" />
          <br />
        {/section}
        <br />
        <input class="button" type="submit" value="{#Forums_ButtonUpload#}" />
        <input name="left_f" type="hidden" id="left_f" value="{$smarty.request.left_f-1|default:''}" />
        {if !empty($smarty.request.toid)}
          <input name="toid" type="hidden" id="toid" value="{$smarty.request.toid|default:''}" />
        {else}
          <input name="fid" type="hidden" id="fid" value="{$smarty.request.fid|default:''}" />
        {/if}
        {if !empty($files)}
          {foreach from=$files item=file}
            {if !$file.forbidden}
<script type="text/javascript">
<!-- //
var hiddenField = document.createElement("input");
hiddenField.type = "hidden";
hiddenField.name = "attachment[]";
hiddenField.value = "{$file.id}";
var fileCount = opener.document.getElementById("hidden_count");
if (fileCount.value < {$maxattachment}) {
    fileCount.value++;
    opener.document.getElementById('attachments_fieldset').style.display = '';
    opener.document.getElementById("attachments").innerHTML += "<div id='delatt_{$file.id}'><input id='files' type='hidden' name='attach_hidden[]' value='{$file.id}' />" + "<a href=\"index.php?p=forum&amp;action=getfile&amp;id={$file.id}&amp;f_id={$file.fid}&amp;t_id={$smarty.request.toid|sanitize}\">{$file.orig_name}</a><a href='index.php?da=1&p=misc&do=delattach&id={$file.id}&file={$file.file_name}' target='attachment_frame' onclick=\"document.getElementById('hidden_count').value=document.getElementById('hidden_count').value-1; document.getElementById('delatt_{$file.id}').innerHTML='';\"><img src=\"{$imgpath_forums}delete_small.png\" alt=\"\" border=\"0\" class=\"absmiddle\" /></a></div>";
}
//-->
</script>
            {/if}
          {/foreach}
        {/if}
        {if isset($smarty.request.action) && $smarty.request.action == 'upload'}
<script type="text/javascript">
<!-- //
window.close();
//-->
</script>
        {/if}
        <br />
        <br />
        <strong>{#Forums_UploadExtensions#}: </strong>
        {foreach from=$allowed item=af name=aff}
          {$af}{if !$smarty.foreach.aff.last}, {/if}
        {/foreach}
        <br />
        <strong>{#Forums_UploadSize#}: </strong> {$res.Max_Groesse} ??
        <br />
        <br />
      </form>
    {/if}
  </div>
  <br />
  <p align="center">
    <input type="button" class="button" value="{#WinClose#}" onclick="window.close();" />
  </p>
</div>
