<div class="popup_header">
  <h2>{$payment_inf->Name}</h2>
</div>
<div class="popup_content" style="padding: 5px">
  <div class="popup_box">
    {if $payment_inf->Icon}
      <div align="center"><img src="uploads/shop/payment_icons/{$payment_inf->Icon}" alt="" /></div>
      <br />
    {/if}
    {$payment_inf->Beschreibung}
    <br />
    {$payment_inf->BeschreibungLang}
  </div>
</div>
<div style="padding: 10px; text-align: center">
  <input class="button" onclick="window.print();" type="button" value="{#PrintNow#}" />&nbsp;
  <input class="button" onclick="closeWindow();" type="button" value="{#WinClose#}" />
</div>
