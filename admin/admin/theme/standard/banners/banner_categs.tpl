<div class="header">{#BannersCategs#}</div>
<div class="subheaders">{#BannersCodeInf#}</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="150" class="headers">{#Global_Name#}&nbsp;</td>
      <td width="100" class="headers"> {#Global_descr#}&nbsp;</td>
      <td width="100" class="headers">{#BannersCode#}&nbsp;</td>
      <td class="headers">{#Global_Actions#}&nbsp;</td>
    </tr>
    {foreach from=$banner_categs item=b}
      <tr class="{cycle values='second,first'}">
        <td><input type="text" class="input" name="Name[{$b->Id}]" style="width: 150px" value="{$b->Name|sanitize}" /></td>
        <td nowrap="nowrap"><input type="text" class="input" name="Beschreibung[{$b->Id}]" style="width: 250px" value="{$b->Beschreibung|sanitize}" /></td>
        <td nowrap="nowrap"><strong>&#123;banner categ={$b->Id}&#125;</strong></td>
        <td><a class="stip" title="{$lang.BannersDelete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$b->Name|jsspecialchars}');" href="index.php?do=banners&amp;sub=delcateg&amp;id={$b->Id}&amp;name={$b->Name|sanitize}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
<br />
<br />
<fieldset>
  <legend>{#Global_NewCateg#}</legend>
  <form method="post" action="">
    <label><strong>{#Global_Name#}</strong><input type="text" class="input" name="Name" style="width: 150px" value="" /></label>&nbsp;
    <label><strong>{#Global_descr#}</strong><input type="text" class="input" name="Beschreibung" style="width: 150px" value="" /></label>
    <input type="hidden" name="new" value="1"/>
    <input type="submit" class="button" value="{#Save#}" />
  </form>
</fieldset>
