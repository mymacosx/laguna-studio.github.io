<div class="header">{#Content_Categs#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="200" class="headers">{#Global_Name#}</td>
      <td width="150" class="headers">{#Content_Categs_Tpl#}</td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$categs item=g}
      <tr class="{cycle values='second,first'}">
        <td><input class="input" style="width: 200px" type="text" name="Name[{$g->Id}]" value="{$g->Name|sanitize}" /></td>
        <td>
          <select class="input" style="width: 150px" name="Tpl_Extra[{$g->Id}]">
            <option value="">{#Content_Categs_TplPrfb#}</option>
            {foreach from=$g->templates item=tp}
              <option value="{$tp->Name}" {if $tp->Name == $g->Tpl_Extra}selected="selected"{/if}>{$tp->Name}</option>
            {/foreach}
          </select>
        </td>
        <td>
          {if $g->Id != 1}
            <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$g->Name|jsspecialchars}');" href="index.php?do=content&amp;sub=delcateg&amp;id={$g->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
            {/if}
        </td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input class="button" type="submit" value="{#Save#}" />
</form>
<form method="post" action="">
  <fieldset>
    <legend>{#GlobalAddCateg#}</legend>
    <input class="input" style="width: 145px" type="text" name="Name" id="NN" value="" />
    <label for="NN">{#Global_Name#}</label>
    <br />
    <select class="input" style="width: 150px" name="Tpl_Extra" id="TN">
      <option value="">{#Content_Categs_TplPrfb#}</option>
      {foreach from=$g->templates item=tp}
        <option value="{$tp->Name}">{$tp->Name}</option>
      {/foreach}
    </select>
    <label for="TN">{#Content_Categs_Tpl#}</label>
    <br />
    <br />
    <input class="button" type="submit" value="{#Save#}" />
    <input type="hidden" name="new" value="1" />
  </fieldset>
</form>
