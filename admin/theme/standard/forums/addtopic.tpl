{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsform.tpl"}
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#f').validate({
        rules: {
            {if $smarty.request.action == "newtopic" || $smarty.request.p == "addtopic" || ($smarty.request.p == "newpost" && $smarty.request.action == "edit")}
            topic: { required: true },
            {/if}
            text: { required: true, maxlength: {$maxlength_post|default:$smarty.post.maxl|sanitize} }
        },
        messages: { },
        submitHandler: function() {
            document.forms['f'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>

{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong>{$top_title|default:$smarty.request.top_title|sanitize}</strong></div>
<div>
  {include file="$incpath/forums/tree.tpl"}
</div>
{if !empty($errors)}
  <div class="error_box">
    <ul>
      {foreach from=$errors item=error}
        <li>{$error}</li>
        {/foreach}
    </ul>
  </div>
{/if}
{if isset($smarty.request.preview) && $smarty.request.preview == 1}
  <table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
    <tr>
      <td class="forum_header">{#Forums_postpreview#}</td>
    </tr>
    <tr>
      <td class="forum_post_first">{$preview_text}</td>
    </tr>
  </table>
  <br />
{/if}
<form action="{$action|default:''}" enctype="multipart/form-data" method="post" name="f" id="f">
  <input type="hidden" name="maxl" value="{$maxlength_post|default:$smarty.post.maxl|sanitize}" />
  <input type="hidden" name="forum_id" value="{$forum_id|default:''}" />
  <input type="hidden" name="toid" value="{$topic_id|default:''}" />
  <input type="hidden" name="top_title" value="{$top_title|default:$smarty.request.top_title}" />
  <input name="s" type="hidden" id="s" value="{$smarty.request.s|default:''}" />
  {$topicform|default:''}
  {$threadform|default:''}
  <input name="f_id" type="hidden" id="f_id" value="{$f_id|default:''}" />
  <div class="forum_container">
    {if $permissions.19 == 1 || $permissions.13 == 1 || $permissions.18 == 1}
      <fieldset>
        <legend>{#Forums_AdminOptionsSelections#}</legend>
        {if $permissions.18 != 1 && $ugroup != 1}
          {if $permissions.13 == 1 && $aid == $userid}
            {assign var="close" value="1"}
          {/if}
        {else}
          {assign var="close" value="1"}
        {/if}
        <select class="input" id="sa" name="subaction">
          <option value=""></option>
          {if $close == 1}
            <option value="close">{#Forums_close_topic_after#}</option>
          {/if}
          {if $permissions.19 == 1}
            <option value="announce">{#Forums_announce_topic_after#}</option>
            <option value="attention">{#Forums_attention_topic_after#}</option>
          {/if}
        </select>
      </fieldset>
    {/if}
    <br />
    <input onclick="closeCodes(); document.getElementById('preview').value = 0;" class="button" id="submits" type="submit" value="{#ButtonSend#}" />&nbsp;
    <input onclick="closeCodes(); document.getElementById('preview').value = 1;" type="submit" class="button" value="{#Forums_postpreview#}" />&nbsp;
    <input name="preview" type="hidden" id="preview" value="" />
    <input onclick="closeCodes(); countComments({$maxlength_post|default:$smarty.post.maxl});" class="button" type="button" value="{#Forums_checklength#}" />
  </div>
</form>
<br />
<br />
{if empty($smarty.request.action) || $smarty.request.action != 'newtopic' && $smarty.request.action != 'edit'}
  <table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
    <tr>
      <td colspan="2" class="forum_header_bolder"><strong>{#Forums_LastPost#}</strong></td>
    </tr>
    {foreach from=$items item="lp"}
      <tr class="{cycle name="lastposts" values="forum_post_first,forum_post_second"}">
        <td width="20%" valign="top">
          <strong>{$lp->user}</strong><br />
          {#Date#} {$lp->datum|date_format: $lang.DateFormat}
        </td>
        <td>
          {if $lp->title}
            <strong>{$lp->title|escape}</strong>
            <br />
            <br />
          {/if}
          {$lp->message}
        </td>
      </tr>
    {/foreach}
  </table>
{/if}
{include file="$incpath/forums/forums_footer.tpl"}
