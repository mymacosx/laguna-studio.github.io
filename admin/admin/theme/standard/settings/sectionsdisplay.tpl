<div class="header">{#Sections#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="maintable">
  <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
    <tr>
      <td width="20" class="headers">{#Global_Id#}</td>
      <td width="200" class="headers">{#Global_Name#}</td>
      <td width="50" align="center" class="headers">{#Global_Status#}</td>
      <td class="headers">{#Global_Actions#}</td>
      <td class="headers">&nbsp;</td>
    </tr>
    {foreach from=$sections item=s}
      <tr class="{cycle values='second,first'}">
        <td>{$s->Id}</td>
        <td><a href="../index.php?area={$s->Id}{if !empty($s->Passwort) && $s->Aktiv != 1}&pass={$s->Passwort}{/if}" target="_blank"><strong>{$s->Name|sanitize}</strong></a></td>
        <td align="center">
          {if $s->Aktiv == 1}
            <img class="absmiddle stip" title="{$lang.Sections_active|sanitize}" src="{$imgpath}/opened.png" alt="" border="" />
          {else}
            <img class="absmiddle stip" title="{$lang.Sections_Open|sanitize}" src="{$imgpath}/closed.png" alt="" border="" />
          {/if}
        </td>
        <td>
          <a class="colorbox stip" title="{$lang.Sections_Edit|sanitize}" href="index.php?do=settings&amp;sub=sectionsdisplay&amp;secaction=edit&amp;section={$s->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="stip" title="{$lang.Sections_OpenPass|sanitize}" href="../index.php?area={$s->Id}{if !empty($s->Passwort) && $s->Aktiv != 1}&pass={$s->Passwort}{/if}" target="_blank"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
            {if $s->Id != 1}
            <a onclick="return confirm('{#Sections_DeleteC#}');" class="stip" title="{$lang.Sections_Delete|sanitize}" href="index.php?do=settings&amp;sub=delsection&amp;id={$s->Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
            {/if}
        </td>
        <td>&nbsp;</td>
      </tr>
    {/foreach}
  </table>
</div>
<br />
<form method="post" action="index.php?do=settings&amp;sub=sectionnew">
  <fieldset>
    <legend>{#Sections_New#}</legend>
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="60"><label for="Name">{#Global_Name#}</label></td>
        <td>
          <input type="text" name="Name" id="Name" class="input" />
          <input class="button" type="submit" value="{#Save#}" />
        </td>
      </tr>
    </table>
  </fieldset>
</form>
