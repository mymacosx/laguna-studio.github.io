<script type="text/javascript">
<!-- //
function check_languages() {
    var lang_1 = document.getElementById('Sprache_1').value;
    var lang_2 = document.getElementById('Sprache_2').value;
    var lang_3 = document.getElementById('Sprache_3').value;
    if(lang_1 == lang_2 || lang_1 == lang_3 || lang_2 == lang_3) {
        alert('{#Settings_language_jse#}');
        return false;
    }
}
//-->
</script>

<form action="" method="post" onsubmit="return check_languages();">
  <div class="header">{#Settings_languages#}</div>
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr class="firstrow">
        <td width="100" class="headers">{#LoginLang#}</td>
        <td width="100" class="headers">{#Global_Name#}</td>
        <td width="100" class="headers">{#Locale#}</td>
        <td width="140" class="headers">{#Global_Active#}</td>
        <td class="headers">{#Global_Position#}</td>
      </tr>
      {foreach from=$languages item=c}
        <tr class="{cycle values='first,second'}">
          <td width="100">
            <select style="width: 100px" onchange="document.getElementById('Bez_{$c->Id}').value = this.options[this.selectedIndex].id;" class="input" name="Sprachcode[{$c->Id}]" id="Sprache_{$c->Id}">
              {foreach from=$folders item=f}
                {if $f->Exists == 1}
                  <option id="{$f->Long}" value="{$f->Name}" {if $f->Name == $c->Sprachcode}selected="selected" {/if}>{$f->Long}</option>
                {/if}
              {/foreach}
            </select>
          </td>
          <td width="100"><input id="Bez_{$c->Id}" class="input" name="Sprache[{$c->Id}]" type="text" value="{$c->Sprache}" size="25" /></td>
          <td width="30"><input class="input" name="Locale[{$c->Id}]" type="text" value="{$c->Locale}" size="5" />.UTF-8</td>
          <td>
            {if $c->Id == 1}
              <label><input disabled="disabled" type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input disabled="disabled" type="radio" name="Aktiv[{$c->Id}]" value="2" {if $c->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
              <input type="hidden" name="Aktiv[{$c->Id}]" value="1" />
            {else}
              {if $c->Exists == 1}
                <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
                <label><input type="radio" name="Aktiv[{$c->Id}]" value="2" {if $c->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
                {else}
                <input type="hidden" name="Aktiv[{$c->Id}]" value="0" />
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
