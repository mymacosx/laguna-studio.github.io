<div class="header">{#Forums_Help#}</div>
<div class="subheaders">
  <a class="colorbox stip" title="{$lang.Global_NewCateg|sanitize}" href="?do=forums&amp;sub=forumshelpnewcateg&amp;new=1&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Global_NewCateg#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
    <tr>
      <td class="header">{#Forums_Help_categt#}</td>
      <th class="header">{#Global_Position#}</th>
      <th width="100" class="header">{#Global_Active#}</th>
      <td class="header">{#Global_Actions#}</td>
    </tr>
    {foreach from=$topics item=t}
      <tr>
        <td class="headers">{$t->Name_1|sanitize}</td>
        <td width="100" class="headers" align="center">
          <input type="hidden" name="Fid[{$t->Id}]" value="{$t->Id}" />
          <input name="Position[{$t->Id}]" type="text" class="input" style="width: 40px" value="{$t->Position}" maxlength="3" />
        </td>
        <td align="center" nowrap="nowrap" class="headers">
          <label><input type="radio" name="Aktiv[{$t->Id}]" value="1" {if $t->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$t->Id}]" value="0" {if $t->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
        </td>
        <td class="headers">
          <a class="colorbox_small stip" title="{$lang.Global_CategEdit|sanitize}" href="?do=forums&amp;sub=forumshelpedit&amp;id={$t->Id}&amp;n={$t->Name_1|sanitize}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Forums_Help_addhelp|sanitize}" href="?do=forums&amp;sub=forumshelpnew&amp;categ={$t->Id}&amp;n={$t->Name_1|sanitize}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a>
          <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$t->Name_1|jsspecialchars}');" href="index.php?do=forums&amp;sub=delhelpcateg&amp;categ={$t->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
        </td>
      </tr>
      {if !$t->subtopics}
        <tr>
          <td colspan="4" style="padding: 5px"><span style="font-weight: bold">{#Forums_Help_nopage#}&nbsp;</span><a class="colorbox stip" title="{$lang.Forums_Help_addhelp|sanitize}" href="?do=forums&amp;sub=forumshelpnew&amp;categ={$t->Id}&amp;n={$t->Name_1|sanitize}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a></td>
        </tr>
      {/if}
      {foreach from=$t->subtopics item=st}
        <tr class="{cycle name=$t->Id values='first,second'}">
          <td><input type="hidden" name="SFid[{$st->Id}]" value="{$st->Id}" />{$st->Name_1|sanitize} </td>
          <td align="center"><input name="SubPosition[{$st->Id}]" type="text" class="input" style="width: 40px" value="{$st->Position}" maxlength="3" /></td>
          <td align="center">
            <label><input type="radio" name="SubAktiv[{$st->Id}]" value="1" {if $st->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="SubAktiv[{$st->Id}]" value="0" {if $st->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
          </td>
          <td>
            <a class="colorbox stip" title="{$lang.Forums_Help_edithelp_i|sanitize}" href="?do=forums&amp;sub=forumshelppageedit&amp;id={$st->Id}&amp;n={$st->Name_1|sanitize}&amp;edit=1&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
            <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$st->Name_1|jsspecialchars}');" href="index.php?do=forums&amp;sub=delhelppage&amp;id={$st->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          </td>
        </tr>
      {/foreach}
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" value="{#Save#}" class="button" />
</form>
