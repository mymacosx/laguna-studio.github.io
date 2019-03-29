<div class="header">{#MoneySite#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=settings&amp;sub=money">
  <table width="100%" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td width="250" colspan="2" class="row_left"><a class="stip" title="Покупка - продажа ссылок и статей" href="http://www.sape.ru/r.biCzvbhteN.php" target="_blank" >{#MoneyReg#} Sape</a></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneySystem#} Sape: </td>
      <td class="row_right">
        <label><input type="radio" name="sape" value="1" {if $row.sape == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="sape" value="0" {if $row.sape == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneyCode#} Sape: </td>
      <td class="row_right"><input class="input" name="code_sape" type="text" style="width: 250px" value="{$row.code_sape|sanitize}" /></td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left">&nbsp;</td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left"><a class="stip" title="Покупка - продажа ссылок" href="http://www.linkfeed.ru/reg/58410" target="_blank" >{#MoneyReg#} Linkfeed</a></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneySystem#} Linkfeed: </td>
      <td class="row_right">
        <label><input type="radio" name="linkfeed" value="1" {if $row.linkfeed == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="linkfeed" value="0" {if $row.linkfeed == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneyCode#} Linkfeed: </td>
      <td class="row_right"><input class="input" name="code_linkfeed" type="text" style="width: 250px" value="{$row.code_linkfeed|sanitize}" /></td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left">&nbsp;</td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left"><a class="stip" title="Покупка - продажа ссылок" href="http://www.mainlink.ru/?partnerid=80447" target="_blank" >{#MoneyReg#} Mainlink</a></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneySystem#} Mainlink: </td>
      <td class="row_right">
        <label><input type="radio" name="mainlink" value="1" {if $row.mainlink == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="mainlink" value="0" {if $row.mainlink == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneyCode#} Mainlink: </td>
      <td class="row_right"><input class="input" name="code_mainlink" type="text" style="width: 250px" value="{$row.code_mainlink|sanitize}" /></td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left">&nbsp;</td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left"><a class="stip" title="Покупка - продажа ссылок" href="http://trustlink.ru/registration/142626" target="_blank" >{#MoneyReg#} Trustlink</a></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneySystem#} Trustlink: </td>
      <td class="row_right">
        <label><input type="radio" name="trustlink" value="1" {if $row.trustlink == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="trustlink" value="0" {if $row.trustlink == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneyCode#} Trustlink: </td>
      <td class="row_right"><input class="input" name="code_trustlink" type="text" style="width: 250px" value="{$row.code_trustlink|sanitize}" /></td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left">&nbsp;</td>
    </tr>
    <tr>
      <td width="250" colspan="2" class="row_left"><a class="stip" title="Покупка - продажа ссылок" href="http://www.setlinks.ru/?pid=94341" target="_blank" >{#MoneyReg#} Setlinks</a></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneySystem#} Setlinks: </td>
      <td class="row_right">
        <label><input type="radio" name="setlinks" value="1" {if $row.setlinks == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="setlinks" value="0" {if $row.setlinks == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#MoneyCode#} Setlinks: </td>
      <td class="row_right"><input class="input" name="code_setlinks" type="text" style="width: 250px" value="{$row.code_setlinks|sanitize}" /></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" value="{#Save#}" class="button" />
</form>
