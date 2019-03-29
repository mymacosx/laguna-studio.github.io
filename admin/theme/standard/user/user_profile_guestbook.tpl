{if $user.Gaestebuch == 1}
  <a name="eintraege"></a>
  <div class="box_innerhead"><strong>{#Profile_GuestbookUser#} {$user.Benutzername|sanitize} </strong></div>
  <div align="center">
    <a href="#eintrag_neu"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a>
    <a href="#eintrag_neu"><strong>{#Profile_Guestbook_EntryNow1#} {$user.Benutzername}</strong></a>
    {if $smarty.session.benutzer_id == $smarty.request.id && $eintrag}
      | <a onclick="return confirm('{#Profile_Guestbook_DeleteAllC#}');" href="index.php?p=user&amp;action=guestbook&amp;do=delete_all&amp;id={$smarty.request.id}&amp;area={$area}&amp;page={$smarty.request.page}">{#Profile_Guestbook_DeleteAll#}</a>
    {/if}
  </div>
  <br />
  {if $eintrag}
    {if $smarty.session.benutzer_id == $smarty.request.id}
<script type="text/javascript">
<!-- //
function edit_entry(ID) {
    {foreach from=$eintrag item=e}
    document.getElementById('eintrag_edit_' + {$e.Id}).style.display = 'none';
    document.getElementById('eintrag_' + {$e.Id}).style.display = '';
    {/foreach}
    document.getElementById('eintrag_' + ID).style.display = 'none';
    document.getElementById('eintrag_edit_' + ID).style.display = '';
}
//-->
</script>
    {/if}
    {foreach from=$eintrag item=e}
      <a name="{$e.Id}"></a>
      <div class="{cycle name='gb' values='comment_box,comment_box_second'}"{if $e.Aktiv != 1} style="border: 2px solid red; padding: 2px"{/if}>
        <div class="h4">{$e.Titel|sanitize|default:'-'}</div>
        <div class="user_guestbook_subheader">
          <strong>{#Date#}: </strong> {$e.Datum|date_format: $lang.DateFormatExtended}&nbsp;&nbsp;&nbsp;&nbsp;
          <strong>{#GlobalAutor#}: </strong> {$e.Autor|sanitize}&nbsp;&nbsp;&nbsp;&nbsp;
          {if $e.Autor_Web}
            <a rel="nofollow" target="_blank" href="{$e.Autor_Web|sanitize}"><img class="absmiddle" src="{$imgpath_forums}homepage.gif" alt="" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
            {/if}
            {if $e.Autor_Herkunft}
            <strong>{#Town#}: </strong> {$e.Autor_Herkunft|sanitize}
          {/if}
        </div>
        <div class="user_guestbook_text">
          <div id="eintrag_{$e.Id}">
            <!--START_NO_REWRITE-->
            {$e.Eintrag}
            <!--END_NO_REWRITE-->
          </div>
          {if $smarty.session.benutzer_id == $smarty.request.id}
            <div id="eintrag_edit_{$e.Id}" style="display: none">
              <form method="post" action="index.php?p=user&amp;action=guestbook">
                <table width="100%" cellpadding="0" cellspacing="1">
                  <tr>
                    <td width="160">{#GlobalName#}</td>
                    <td><input class="input" style="width: 200px" name="E_Autor" type="text" value="{$e.Autor|sanitize}" /></td>
                  </tr>
                  <tr>
                    <td width="160">{#Web#}</td>
                    <td><input class="input" style="width: 200px" name="E_Webseite" type="text" value="{$e.Autor_Web|sanitize}" /></td>
                  </tr>
                  <tr>
                    <td width="160">{#Town#}</td>
                    <td><input class="input" style="width: 200px" name="E_Herkunft" type="text" value="{$e.Autor_Herkunft|sanitize}" /></td>
                  </tr>
                  <tr>
                    <td width="160">{#Title#}</td>
                    <td><input class="input" style="width: 200px" name="E_Titel" type="text" value="{$e.Titel|sanitize}" /></td>
                  </tr>
                  <tr>
                    <td width="160">{#Comments#}</td>
                    <td><textarea id="GbComment_{$e.Id}" class="input" cols="2" rows="2" style="width: 99%; height: 150px" name="E_Eintrag">{$e.Eintrag_Raw|sanitize}</textarea></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>
                      <input type="hidden" name="id" value="{$smarty.request.id}" />
                      <input type="hidden" name="do" value="edit" />
                      <input type="hidden" name="gb_entry" value="{$e.Id}" />
                      <input type="hidden" name="page" value="{$smarty.request.page|default:1}" />
                      <input type="submit" class="button" value="{#Save#}" />
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <div align="right" style="padding: 4px; clear: both">
              <small>
                {if $e.Aktiv != 1}
                  <a href="index.php?p=user&amp;action=guestbook&amp;do=set_active&amp;id={$smarty.request.id}&amp;page={$smarty.request.page}&amp;gb_entry={$e.Id}&amp;area={$area}">{#Profile_Guestbook_SetActive#}</a> |
                {/if}
                <a onclick="return confirm('{#Profile_Guestbook_DeleteC#}');" href="index.php?p=user&amp;action=guestbook&amp;do=delete&amp;d=true&amp;id={$smarty.request.id}&amp;page={$smarty.request.page}&amp;gb_entry={$e.Id}&amp;area={$area}">{#Delete#}</a> |
                <a href="" onclick="edit_entry('{$e.Id}'); return false;">{#GlobalEdit#}</a>
              </small>
            </div>
          {/if}
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <p>{$pages}</p>
    {else}
      <br />
    {/if}
  {else}
    <div class="h3">{#NotMessages#}</div>
    <br />
  {/if}
  <a name="eintrag_neu"></a>
  <br />
  {if isset($KeineGaeste) && $KeineGaeste == 1}
    <p> {#Profile_Guestbook_NoGuests#} </p>
  {else}
    {if !empty($error)}
      <a name="error"></a><img src="{$imgpath}/page/pixel.gif" alt="" width="1" height="1" style="display: none" onload="location.href = '#error';" />
      <div class="error_box">
        <ul>
          {foreach from=$error item=err}
            <li>{$err}</li>
            {/foreach}
        </ul>
      </div>
    {/if}
    <div class="clear"></div>
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
{include file="$incpath/other/jsform.tpl"}
$(document).ready(function() {
    $('#fe').validate({
        rules: {
            Autor: { required: true },
            Titel: { required: true },
            text: { required: true, minlength: 10, maxlength: {$user.Gaestebuch_Zeichen} }
        },
        submitHandler: function() {
            document.forms['f'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
togglePanel('navpanel_usercomment', 'toggler', 30, '{$basepath}');
//-->
</script>

    <br />
    <form method="post" action="index.php?p=user" name="f" id="fe">
      <div class="round">
        <div class="opened" id="navpanel_usercomment" title="{#Profile_Guestbook_EntryNow1#} {$user.Benutzername}">
          <div class="comment_form">
            <label><input class="input" style="width: 200px" name="Autor" type="text" value="{$smarty.post.Autor|default:''|sanitize}" id="c_a" />&nbsp;{#Contact_myName#}</label>
            <br />
            <label><input class="input" style="width: 200px" name="Webseite" type="text" value="{$smarty.post.Webseite|default:''|sanitize}" id="c_w" />&nbsp;{#Web#}{#GlobalOption#}</label>
            <br />
            <label><input class="input" style="width: 200px" name="Herkunft" type="text" value="{$smarty.post.Herkunft|default:''|sanitize}" id="c_h" />&nbsp;{#Town#}{#GlobalOption#}</label>
            <br />
            <label><input class="input" style="width: 200px" name="Titel" type="text" value="{$smarty.post.Titel|default:''|sanitize}" />&nbsp;{#Title#}</label>
            <br />
            {if $settings.KommentarFormat == 1}
              {if $settings.SysCode_Smilies == 1}
                {$listemos}
              {/if}
              {include file="$incpath/comments/format.tpl"}
            {/if}
            <textarea name="text" cols="40" rows="5" class="input" id="msgform" style="width: 99%; height: 150px">{$smarty.post.text|default:''|escape: html}</textarea>
            {include file="$incpath/other/captcha.tpl"}
            <br />
            <input type="hidden" name="Redir" value="{page_link}" />
            <input type="hidden" name="Eintrag" value="1" />
            <input type="hidden" name="id" value="{$smarty.request.id}" />
            <input type="submit" class="button" onclick="closeCodes();" value="{#ButtonSend#}" />
          </div>
        </div>
      </div>
    </form>
  {/if}
{/if}
