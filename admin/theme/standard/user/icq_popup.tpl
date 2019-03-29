<div class="popup_header">
  <h2>{$title_html}</h2>
</div>
<div class="popup_content" align="center" style="padding: 5px;line-height: 1.5em">
  <div class="popup_box">
    <br />
    {#Profile_ICQ#}
    {if $icq}
      <br />
      <br />
      <div class="h2">{$icq|sanitize} <img src="http://status.icq.com/online.gif?icq={$icq}&amp;img=5" alt="" /></div>
      <br />
      <br />
      {#Arrow#} <a rel="nofollow" target="_blank" href="http://people.icq.com/people/cmd.php?uin={$icq}&amp;action=message">{#Profile_ICQContactWith#}</a><a href=""></a>
      <br />
      {#Arrow#} <a rel="nofollow" target="_blank" href="http://www.icq.com/people/webmsg.php?to={$icq}">{#Profile_ICQContactWithout#}</a>
      <br />
      {#Arrow#} <a rel="nofollow" target="_blank" href="http://people.icq.com/people/cmd.php?uin={$icq}&amp;action=add">{#Profile_ICQContactAdd#}</a><a href=""></a>
      <br />
      {#Arrow#} <a rel="nofollow" target="_blank" href="http://people.icq.com/people/about_me.php?uin={$icq}">{#Profile_ICQSHowProfile#}</a>
      <br />
      <br />
      <br />
      {#Profile_ICQInf#}
    </div>
  </div>
{/if}
<p align="center">
  <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
</p>
