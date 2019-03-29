<div class="popup_header h2">{#PN_PeronalMessages#}</div>
<div class="popup_content" style="padding: 5px">
  <div class="popup_box">
    {if $cancel_popup == 1}
      <div class="forum_header_bolder h2">{#PN_PeronalMessages#}</div>
      <br />
      {#PN_NewMessagePopFalse#}
      <div class="padding5">
        <br />
        <div align="center"><a href="javascript: closeWindow();">{#WinClose#}</a>
          <br />
        </div>
      </div>
    {else}
      <div class="forum_header_bolder h2">{#PN_NewMessageI#}</div>
      <br />
      {#PN_NewMessage#}
      <br />
      <br />
      <div align="center">
        <input class="button" type="button" onclick="parent.location.href = 'index.php?p=pn';closeWindow();" value="{#PN_GotoInbox#}" />
      </div>
      <br />
      <div align="center"><a href="javascript: closeWindow();">{#WinClose#}</a>
        <br />
        <a href="index.php?p=misc&amp;do=cancel_popup">{#PN_Shownomore#}</a> </div>
      {/if}
  </div>
</div>
<br />
