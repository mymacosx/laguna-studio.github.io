<script type="text/javascript">
<!-- //
function attachment_window() {
    var attcount = document.getElementById('hidden_count').value;
    var left = {$maxattachment} - attcount;
    if (attcount >= {$maxattachment}) {
	alert('{#Forums_EDelattFirst#}');
    } else {
	{if !empty($smarty.request.toid)}
	openWindow('index.php?p=misc&do=attachment&toid={$smarty.request.toid}&fid={$smarty.request.fid}&left='+ left +'&p_id={if !empty($smarty.request.pid)}{$smarty.request.pid}{else}-1{/if}', 'moo', '400','400','0');
	{else}
	openWindow('index.php?p=misc&do=attachment&fid={$smarty.request.fid}&left='+ left +'&p_id={if !empty($smarty.request.pid)}{$smarty.request.pid}{else}-1{/if}', 'moo', '400','400','0');
	{/if}
    }
}
//-->
</script>

<input type="hidden" name="p_id" value="{$smarty.request.p_id|default:$smarty.request.pid}" />
<input type="hidden" name="action" value="{$smarty.request.action|default:''}" />
<input type="hidden" name="num_pages" value="{$smarty.request.num_pages|default:''}" />
<div class="forum_container">
  <fieldset>
    <strong>{#GlobalTheme#}</strong>
    <br />
    <input class="input" type="text" name="subject" value="{$message->title|default:$smarty.post.subject}" maxlength="200" size="50" />
    <br />
    {if $bbcodes}
      <br />
      {if $smilie == 1}
        {$listemos}
      {/if}
      {include file="$incpath/forums/format.tpl"}
      <br />
    {/if}
    <div style="height: 425px"><textarea name="text" cols="" rows="25" class="input" id="msgform" style="width: 98%; height: 400px; font-size: 110%">{if !empty($message->message)}{$message->message|default:$preview_text_form|escape: "html"}{else}{$message->text|default:$preview_text_form|escape: "html"}{/if}</textarea></div>
    <br />
    <strong>{#Forums_Field_options#}</strong>
    <br />
    <label><input class="noborder" type="checkbox" name="parseurl" value="1" checked="checked" /> {#Forums_Label_urlconvert#}</label>
    <br />
    {if $loggedin}
      <label><input class="noborder" type="checkbox" name="notification" value="1" {if (isset($notification) && $notification == 1) || (isset($smarty.request.notification) && $smarty.request.notification == 1)}checked="checked"{/if} /> {#Forums_Label_notification#}</label>
      <br />
    {/if}
    <label><input class="noborder" type="checkbox" name="disablebb" value="1" {if isset($smarty.request.disablebb) && $smarty.request.disablebb == 1}checked="checked"{/if} /> {#Forums_Label_disablebbcode#}</label>
    <br />
    <label><input class="noborder" type="checkbox" name="disablesmileys" value="1" {if isset($smarty.request.disablesmileys) && $smarty.request.disablesmileys == 1}checked="checked"{/if} /> {#Forums_Label_disablesmilies#}</label>
    <br />
    {if $loggedin}
      <label><input class="noborder" type="checkbox" name="usesig" value="1" checked="checked" /> {#Forums_Label_usesig#}</label>
      <br />
    {/if}
    {if $permissions.8 == 1}
      <br />
      {if (isset($smarty.request.action) && $smarty.request.action == 'edit') || (isset($pre_error) && $pre_error == 1) || (isset($nooon) && $nooon != 1) || (isset($smarty.request.p) && $smarty.request.p == 'addtopic')}
        {if !empty($h_attachments_only_show)}
          <fieldset>
            <legend>{#Forums_ThisAttachments#}</legend>
            {assign var="counter" value=0}
            {foreach name=attachments from=$h_attachments_only_show item=at}
              {assign var="counter" value=$counter+1}
              {$at}
            {/foreach}
          </fieldset>
        {/if}
      {/if}
      {assign var=fileCount value=$smarty.foreach.attachments.total|default:''}
      <input type="hidden" id="hidden_count" value="{$counter|default:''}" />
      <input type="hidden" id="fileCount" name="fileCount" value="{$fileCount}" />
      <fieldset id="attachments_fieldset" style="display: none">
        <legend>{#Forums_ThisAttachments#}</legend>
        <div id="attachments"></div>
      </fieldset>
      <br />
      <input class="button" type="button" onclick="return attachment_window();" value="{#Forums_AddNewAttachments#}" />
      <iframe style="width: 1px; height: 1px; display: none" name="attachment_frame"></iframe>
    {/if}
  </fieldset>
</div>
