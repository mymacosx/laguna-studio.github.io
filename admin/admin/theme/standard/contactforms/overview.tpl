<div class="header">{#ContactForms#}</div>
<div class="subheaders">
  <a class="colorbox" title="{#ContactForms_new#}" href="index.php?do=contactforms&amp;sub=new&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#ContactForms_new#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=contactforms&amp;sub=save">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="220" class="headers">{#Global_Name#}</td>
      <td width="120" class="headers"> {#CodeTpl#} <span class="stip" title="{$lang.CodeTplInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td width="120" class="headers"> {#ContactForms_ccode#} <span class="stip" title="{$lang.ContactForms_ccodeInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td width="120" align="center" class="headers">{#Global_Active#}</td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$cforms item=c}
        <tr class="{cycle values='second,first'}">
          <td><strong>{$c->Titel1|sanitize}</strong></td>
          <td><input class="input" style="width: 100px" readonly="readonly" type="text" name="textfield" id="textfield" value="{ldelim}contact id={$c->Id}{rdelim}" /></td>
          <td><input class="input" style="width: 100px" readonly="readonly" type="text" name="textfield" id="textfield" value="[CONTACT:{$c->Id}]" /></td>
          <td align="center">
            <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
            <label><input type="radio" name="Aktiv[{$c->Id}]" value="0" {if $c->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
          </td>
          <td>
            <a class="colorbox stip" title="{$lang.ContactFormEdit|sanitize}" href="index.php?do=contactforms&amp;sub=edit&amp;id={$c->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
            <a class="stip" title="{$lang.ContactForms_copy|sanitize}" href="index.php?do=contactforms&amp;sub=copy&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/copy.png" alt="" border="0" /></a>
            <a class="stip" title="{$lang.ContactForms_del|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$c->Titel1|jsspecialchars}');" href="index.php?do=contactforms&amp;sub=delete&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          </td>
        </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
