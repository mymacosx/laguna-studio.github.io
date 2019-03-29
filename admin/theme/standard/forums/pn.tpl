{include file="$incpath/forums/user_panel_forums.tpl"}
<script type="text/javascript">
<!-- //
function togglePn(im, id) {
    if ($('#' + id).css('display') === 'none') {
        $('#' + id).show();
        $(im).attr('src', '{$imgpath_page}spoiler_open.png');
        $.cookie(id, null, { path: '{$basepath}', expires: 0 });
    } else {
        $('#' + id).hide();
        $(im).attr('src', '{$imgpath_page}spoiler_close.png');
        $.cookie(id, 1, { path: '{$basepath}', expires: 365 });
    }
}
//-->
</script>

<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_info_main"><strong>{#PN_PeronalMessages#}: </strong>&nbsp; <a href="index.php?p=pn&amp;goto=inbox">{#PN_inbox#}</a> | <a href="index.php?p=pn&amp;goto=outbox">{#PN_outbox#}</a> | <a href="index.php?p=pn&amp;action=new">{#NewMessage#}</a><a href="index.php?p=pn&amp;action=new"></a></td>
  </tr>
</table>
{if empty($table_data) && $smarty.request.action != 'message' && $outin == 1 || (isset($nomessages) && $nomessages == 1)}
  <table width="100%" cellspacing="1" cellpadding="4">
    <tr>
      <td align="center"> {#NotMessages#} </td>
    </tr>
  </table>
{/if}
{if $smarty.request.action != 'new'}
  <div class="forum_header_bolder" style="margin-top: 5px;margin-bottom: 5px">
    {if $smarty.request.goto == 'outbox'}
      <div class="h3">{#PN_outbox#}</div>
    {else}
      <div class="h3">{#PN_inbox#}</div>
    {/if}
  </div>
{/if}
{if $outin == 1}
  <table width="100%" cellpadding="0" cellspacing="1" class="forum_tableborder">
    <tr>
      <td valign="top">
        <table width="100%" align="center" cellpadding="4" cellspacing="0">
          <tr>
            <td width="1%" class="forum_form_left"><table width="15%" cellpadding="2" cellspacing="0" class="">
                <tr>
                  <td nowrap="nowrap"><div align="center">0 % </div></td>
                </tr>
              </table></td>
            <td width="70%" align="left" class="forum_form_right"><table width="{$inoutwidth|numf}%" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="background: url({$imgpath_forums}pn_bar.gif) repeat-x"><img src="{$imgpath_forums}pn_bar.gif" alt="" /></td>
                </tr>
              </table></td>
            <td width="1%" class="forum_form_left"><table width="15%" cellpadding="2" cellspacing="0" class="">
                <tr>
                  <td nowrap="nowrap"><div align="center">100 % </div></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
      <td width="35%" align="right" valign="middle" class="forum_info_main"> {$pnioutnall} ({$inoutpercent}%) {$pnmax}<span class="error">! {$warningpnfull}</span></td>
    </tr>
  </table>
  <br />
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
          <tr>
            <td colspan="4" align="center" class="forum_header">
              <form  method="post" action="index.php?p=pn" name="psel" id="psel">
                <input type="hidden" name="goto" value="{$goto}" />
                <input type="hidden" name="page" value="1" />
                <select name="pp" class="input" id="pp"> {$pp_l} </select>&nbsp;
                <select name="porder" class="input" id="porder"> {$sel_topic_read_unread} </select>&nbsp;
                <select name="sort" class="input" id="sort">
                  <option value="DESC" {$sel1|default:''}>{#desc_t#} </option>
                  <option value="ASC" {$sel2|default:''}>{#asc_t#}</option>
                </select>&nbsp;
                <input type="submit" class="button" value="{#GlobalShow#}" />
                <input name="page" type="hidden" id="page" value="{$page}" />
              </form>
            </td>
          </tr>
          <form  method="post" action="index.php?del=yes&amp;p=pn" name="kform" id="kform" onsubmit="return confirm('{$pndel_confirm}');">
            <tr class="forum_form_right">
              <td colspan="4">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></td>
                    <td align="right"><a href="{$normmodus_link}"> {#PN_ListNorm#}</a> | <a href="{$katmodus_link}"> {#PN_ListCateg#} </a></td>
                  </tr>
                </table>
              </td>
            </tr>
            {foreach from=$table_data item=data}
              {if isset($data.header) && $data.header == 1}
                <tr>
                  <td class="forum_header" colspan="4">
                    <img src="{$imgpath_page}{$data.image}" hspace="2" class="absmiddle" border="0" style="cursor:pointer" onclick="togglePn(this, 'pn_{$data.key}');" alt="" /> {$data.time} {$data.date}
                  </td>
                </tr>
                <tbody id="pn_{$data.key}" style="display:{$data.display}">
                {/if}
                <tr>
                  <td width="20" align="center" class="forum_info_meta"><input name="pn_{$data.pnid}" type="checkbox" id="d" value="1" /></td>
                  <td class="forum_info_meta" nowrap="nowrap"><div align="center"> {$data.icon} </div></td>
                  <td class="forum_info_main" width="100%" nowrap="nowrap">
                    <a class="forum_links" href="{$data.mlink}">{$data.title|sanitize}</a>
                    <br />
                    <a class="forum_links_small" href="{$data.toid}">{$data.von}</a>
                  </td>
                  <td align="right" nowrap="nowrap" class="forum_info_meta"> {$data.pntime|date_format:$lang.DateFormat} </td>
                </tr>
                {if isset($data.end) && $data.end == 1}
                </tbody>
              {/if}
            {/foreach}
            <tr>
              <td colspan="4" align="center" nowrap="nowrap" class="forum_info_meta">
                <input name="goto" type="hidden" id="goto" value="{$goto}" />
                <input type="submit" class="button" value="{#PN_delmarked#}" />
              </td>
            </tr>
          </form>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td>
              {if !empty($nav)}
                <p> {$nav} </p>
              {/if}
            </td>
            <td align="right">{$dlpnas}&nbsp;<a href="{$pndl_text}">{$pndl_text_link}</a>&nbsp;|&nbsp;<a href="{$pndl_html}">{$pndl_html_link}</a>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
{/if}
{if $neu == 1}
<br />
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsform.tpl"}
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#f').validate({
        rules: {
            tofromname: { required: true },
            title: { required: true, minlength: 4 },
            text: { required: true, minlength: 4, maxlength: {$maxlength_post} }
        },
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

  {if isset($preview) && $preview == 1}
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
            <tr>
              <td class="forum_header">{#Forums_postpreview#}</td>
            </tr>
            <tr>
              <td class="forum_post_first">{$preview_text|sslash}</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br />
  {/if}
  <form  method="post" action="index.php?p=pn&amp;action=new" name="f" id="f">
    <div class="box_innerhead">
      {if isset($smarty.request.forward) && $smarty.request.forward == 2}
        <strong>{#GlobalReply#}</strong>
      {else}
        {#NewMessage#}
      {/if}
    </div>
    <br />
    {if isset($iserror) && $iserror == 1}
      <div class="error_box">
        {$title_error}
        <ul>
          {$error}
        </ul>
      </div>
      <br />
    {/if}
    <fieldset>
      <label><input type="text" class="input" name="tofromname" value="{$tofromname|default:''}" size="40" />&nbsp;{#Recipient#}&nbsp;</label>
      <input onclick="newWindow('{$baseurl}/index.php?p=misc&do=searchuser', 400, 450);" type="button" class="button" value="{#PN_SearchUser#}" />
      <br />
      <label><input name="title" type="text" class="input" value="{$title|default:''}" size="40" />&nbsp;{#GlobalTheme#}</label>
      <br />
      <br />
    {if $smilie == 1}{$listemos}{/if}
    {include file="$incpath/comments/format.tpl"}
    <br />
    <div style="height: 275px">
      <textarea name="text" cols="" rows="15" class="input" id="msgform" style="width: 99%; height: 250px">{$text|default:''|sslash}</textarea>
    </div>
    <label><input name="use_smilies" type="checkbox" id="use_smilies" value="yes" checked="checked" /> {#PN_UseSmilies#}</label>
    <br />
    <label><input name="parseurl" type="checkbox" id="parseurl" value="yes" checked="checked" /> {#PN_URLParse#}</label>
    <br />
    <label><input name="savecopy" type="checkbox" id="savecopy" value="yes" checked="checked" /> {#PN_SaveCopy#}</label>
    <br />
    <label><input type="radio" name="send" value="2" checked="checked" /> {#SendEmail_Send#}</label>
    <br />
    <label><input type="radio" name="send" value="1" /> {#Forums_postpreview#}</label>
    <br />
    <br />
    <br />
    <input type="submit" class="button" value="{#SendEmail_Send#}" />
  </fieldset>
</form>
{/if} <br />
{if isset($showmessage) && $showmessage == 1}
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
          <tr>
            <td colspan="2" class="forum_header"><strong>{$pntitle}</strong></td>
          </tr>
          <tr>
            <td width="150" class="forum_form_left"><a class="forum_links" href="{$tofromname_link}"> {$tofromname} </a></td>
            <td class="forum_form_right">{#send_dt#}: <span class="time"> {$pntime|date_format: $lang.DateFormat} </span></td>
          </tr>
          <tr valign="top">
            <td width="150" height="120" class="forum_form_left">
              {$posts}:
              {$posts_num}
              <br />
              {$membersince}:
              {$membersince_date|date_format: $lang.DateFormatSimple}
            </td>
            <td class="forum_form_right">{$message}</td>
          </tr>
          <tr valign="top">
            <td class="forum_form_left">&nbsp;</td>
            <td class="forum_form_right">
              <form  name="answer_f" method="post" action="index.php">
                <input type="hidden" name="p" value="pn" />
                <input type="hidden" name="action" value="new" />
                <input type="hidden" name="forward" value="2" />
                <input type="hidden" name="id" value="{$pn_id}" />
                <input type="hidden" name="subject" value="{$pn_subject}" />
                <input type="hidden" name="aut" value="{$pn_aut}" />
                <input type="hidden" name="goto" value="{$pn_goto}" />
                <input type="hidden" name="date" value="{$pn_date}" />
                <input type="hidden" name="text" value="{$pn_text|escape: html}" />
              </form>
              <form  name="forward_f" method="post" action="index.php">
                <input type="hidden" name="p" value="pn" />
                <input type="hidden" name="action" value="new" />
                <input type="hidden" name="forward" value="1" />
                <input type="hidden" name="id" value="{$pn_id}" />
                <input type="hidden" name="subject" value="{$pn_subject}" />
                <input type="hidden" name="aut" value="{$pn_aut}" />
                <input type="hidden" name="goto" value="{$pn_goto}" />
                <input type="hidden" name="date" value="{$pn_date}" />
                <input type="hidden" name="text" value="{$pn_text|escape: html}" />
              </form>
              {if $answerok == 1}
                <div class="forum_buttons_small"><a href="javascript: document.forms['answer_f'].submit();"><img src="{$imgpath_forums}reply_small.png" alt="" />{#GlobalReply#}</a></div>
                  {/if}
              <div class="forum_buttons_small"><a href="javascript: document.forms['forward_f'].submit();" target="_self"><img src="{$imgpath_forums}reply_small.png" alt="" />{#PN_forward#}</a></div>
              <div class="forum_buttons_small"><a href="{$delpn}" target="_self" onclick="return confirm('{#delpn_t#}');"><img src="{$imgpath_forums}delete_small.png" alt="" />{#PN_delete#}</a></div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
{/if}
{include file="$incpath/forums/forums_footer.tpl"}
