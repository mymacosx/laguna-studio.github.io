<div class="header">{#Gaming_plattforms#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if $plattforms}
  <form method="post" action="" name="kform">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="{cycle values='first,second'}">
        <td width="200" class="headers">{#Global_Name#}</td>
        {if perm('plattforms')}
          <td width="10" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
          <td align="center" class="headers">&nbsp;</td>
        {/if}
      </tr>
      {foreach from=$plattforms item=n}
        <tr class="{cycle values='second,first'}">
          <td class="row_spacer"><input class="input" type="text" style="width: 200px" name="Name[{$n->Id}]" value="{$n->Name|sanitize}" /></td>
            {if perm('plattforms')}
            <td align="center" class="row_spacer"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$n->Id}]" type="checkbox" value="1" /></td>
            <td align="center" class="row_spacer">&nbsp;</td>
          {/if}
        </tr>
      {/foreach}
    </table>
    <input name="save" type="hidden" id="save" value="1" />
    <input class="button" type="submit" value="{#Save#}" />
  </form>
{else}
{/if}
<br />
<form method="post" action="" name="kform">
  <fieldset>
    <legend>{#Gaming_plattforms_new#}</legend>
    {section name=pf loop=5}
      #{$smarty.section.pf.index+1}
      <input class="input" type="text" style="width: 200px" name="Name[]" value="" />
      <br />
    {/section}
    <input name="new" type="hidden" id="new" value="1" />
    <input class="button" type="submit" value="{#Save#}" />
  </fieldset>
</form>
