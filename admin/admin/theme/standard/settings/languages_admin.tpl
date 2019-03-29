<div class="header">{#Settings_languages_a#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr class="firstrow">
        <td width="100" class="headers">{#LoginLang#}</td>
        <td width="100" class="headers">{#Locale#}</td>
        <th width="140" class="headers">{#Global_Active#}</th>
        <td class="headers">{#Global_Position#}</td>
      </tr>
      {foreach from=$languages item=c}
        <tr class="{cycle values='first,second'}">
          <td width="100">
            <input type="hidden" name="Sprache[{$c->Id}]" value="{$c->Sprachcode}" />
            <input type="hidden" name="Sprachcode[{$c->Id}]" value="{$c->Sprachcode}" />
            {foreach from=$folders item=f}
              {if $f->Name == $c->Sprachcode}
                {$f->Long}
              {/if}
            {/foreach}
          </td>
          <td width="30">
            {if $c->Exists == 1}
            <input class="input" name="Locale[{$c->Id}]" type="text" value="{$c->Locale}" size="5" />.UTF-8
            {/if}
          </td>
          <td width="140" align="center">
            {if $c->Id == 1}
              <input type="hidden" name="Aktiv[{$c->Id}]" value="1" />
              {#Yes#}
            {else}
              {if $c->Exists == 1}
                <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input type="radio" name="Aktiv[{$c->Id}]" value="2" {if $c->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
                {else}
                <input type="hidden" name="Aktiv[{$c->Id}]" value="0" />
                <em>{#NoInstall#}</em>
              {/if}
            {/if}
          </td>
          <td><input name="Posi[{$c->Id}]" type="text" class="input" id="Posi[{$c->Id}]" style="width: 40px" value="{$c->Posi}" maxlength="2" /></td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" value="{#Save#}" class="button" />
</form>
