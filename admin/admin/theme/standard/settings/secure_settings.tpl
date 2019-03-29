<div class="header">{#SecureSettings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="350" class="row_left">{#SecureActive#} </td>
      <td class="row_right">
        <label><input type="radio" name="active" value="1" {if $row.active == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="active" value="0" {if $row.active == 0}checked="checked"{/if} />{#No#}</label>
        <label><input type="radio" name="active" value="2" {if $row.active == 2}checked="checked"{/if} />{#SecureGuest#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#SecureGd#}</td>
      <td class="row_right">
        <label><input type="radio" name="gd" value="1" {if $row.gd == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="gd" value="0" {if $row.gd == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#SecureFont#}</td>
      <td class="row_right">
        <label><input type="radio" name="ttf_font" value="1" {if $row.ttf_font == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="ttf_font" value="0" {if $row.ttf_font == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#SecureMaxCalc1#}</td>
      <td class="row_right"><input class="input" name="max_calc1" type="text" style="width: 40px" value="{$row.max_calc1}" maxlength="2" /></td>
    </tr>
    <tr>
      <td class="row_left">{#SecureMaxCalc2#}</td>
      <td class="row_right"><input class="input" name="max_calc2" type="text" style="width: 40px" value="{$row.max_calc2}" maxlength="2" /></td>
    </tr>
    <tr>
      <td class="row_left">{#SecureMinText#}</td>
      <td class="row_right"><input class="input" name="min_text" type="text" style="width: 40px" value="{$row.min_text}" maxlength="2" /></td>
    </tr>
    <tr>
      <td class="row_left">{#SecureMaxText#}</td>
      <td class="row_right"><input class="input" name="max_text" type="text" style="width: 40px" value="{$row.max_text}" maxlength="2" /></td>
    </tr>
    <tr>
      <td class="row_left">{#SecureType#}</td>
      <td class="row_right">
        <select class="input" name="type">
          <option value="auto" {if $row.type == 'auto'}selected="selected"{/if}>{#Automatically#}</option>
          <option value="text" {if $row.type == 'text'}selected="selected"{/if}>{#SecureRand#}</option>
          <option value="calc" {if $row.type == 'calc'}selected="selected"{/if}>{#SecureCalc#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#SecureText#}</td>
      <td class="row_right"><input class="input" name="text" type="text" style="width: 340px" value="{$row.text}" /></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
