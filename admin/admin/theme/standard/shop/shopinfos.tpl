<div class="header">{#Global_Shop#} - {#Shop_Infos#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !empty($error)}
  <div class="error_box">
    <ul>
      {foreach from=$error item=e}
        <li>{$e}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<form onsubmit="" name="shopinfos" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="250" class="row_left">{#Shop_Settings_CompanyAdress#}</td>
      <td class="row_right">{$ShopAdresse}</td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#FSK18_Shop#}</td>
      <td class="row_right">{$Fsk18}</td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_Settings_ShopAgb#}</td>
      <td class="row_right">{$ShopAGB}</td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_Settings_DataInf#}</td>
      <td class="row_right">{$ShopDatenschutz}</td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_Settings_ShopCancelInf#}</td>
      <td class="row_right">{$Widerruf}</td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Shop_Settings_ShippingInf#}</td>
      <td class="row_right">{$VersandInfo_Footer}</td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Global_savesettings#}" />
</form>
