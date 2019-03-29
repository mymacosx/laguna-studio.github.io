<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<div class="popup_header">
  <h2><strong>{$title_html}</strong></h2>
</div>
<div class="popup_content" align="center" style="line-height: 1.5em">
  <div class="popup_box">
    {#Profile_Scype#}
    {if $skype}
      <br />
      <br />
      <div class="h2">{$skype|sanitize}</div>
      <br />
      <br />
      <img src="http://mystatus.skype.com/{$skype|sanitize}" alt="" />
      <br />
      <br />
      <table width="450" cellspacing="0" cellpadding="2">
        <tr>
          <td width="50%" valign="top">
            <img class="absmiddle" src="{$imgpath_forums}skype_voicemail.gif" alt="" /> <a href="skype: {$skype|sanitize}?voicemail" onclick="return skypeCheck();">{#Profile_ScypeVoicemail#}</a>
            <br />
            <img class="absmiddle" src="{$imgpath_forums}skype_info.gif" alt="" /> <a href="skype: {$skype|sanitize}?userinfo" onclick="return skypeCheck();">{#Profile_ScypeProfile#}</a><a href=""></a>
            <br />
            <img class="absmiddle" src="{$imgpath_forums}skype_callstart.gif" alt="" /> <a href="skype: {$skype|sanitize}?call" onclick="return skypeCheck();">{#Profile_ScypeCall#}</a><a href=""></a>
            <br />
          </td>
          <td width="50%" valign="top">
            <img class="absmiddle" src="{$imgpath_forums}skype_addcontact.gif" alt="" /> <a href="skype: {$skype|sanitize}?add" onclick="return skypeCheck();">{#Profile_ScypeAdd#}</a><a href=""></a>
            <br />
            <img class="absmiddle" src="{$imgpath_forums}skype_message.gif" alt="" /> <a href="skype: {$skype|sanitize}?chat" onclick="return skypeCheck();">{#SendEmail_Send#}</a>
            <br />
            <img class="absmiddle" src="{$imgpath_forums}skype_fileupload.gif" alt="" /> <a href="skype: {$skype|sanitize}?sendfile" onclick="return skypeCheck();">{#Profile_ScypeSendFile#}</a><a href=""></a>
            <br />
          </td>
        </tr>
      </table>
      <br />
      <br />
      {#Profile_ScypeInf#}
    </div>
  </div>
{/if}
<p align="center">
  <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
</p>
