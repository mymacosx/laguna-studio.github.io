<div class="header">{#Forums_URank_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form name="rf" id="rf" action="" method="post">
  <input name="save" type="hidden" id="save" value="1" />
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tableborder">
    <tr class="firstrow">
      <td width="20%" class="headers"> {#Global_Name#} </td>
      <td class="headers"> {#Forums_URank_posts#} </td>
    </tr>
    {foreach from=$ranks item=rank}
      <tr class="{cycle values='second,first'}">
        <td width="20%"><input class="input" type="text" name="title[{$rank->id}]" id="title_{$rank->id}" value="{$rank->title|escape: 'html'|sslash}" size="50" maxlength="100" /></td>
        <td>
          <input class="input" type="text" name="count[{$rank->id}]" value="{$rank->count}" size="6" maxlength="7" />
          <a href="index.php?do=forums&amp;sub=userrankings&amp;del=1&amp;id={$rank->id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
        </td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
</form>
<br />
<br />
<form action="" method="post">
  <input type="hidden" name="new" value="1" />
  <div class="header">{#Forums_URank_rNew#}</div>
  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableborder">
    <tr class="firstrow">
      <td class="headers"> {#Global_Name#} </td>
      <td class="headers"> {#Forums_URank_posts#} </td>
    </tr>
    <tr>
      <td width="20%" class="second"><input class="input" type="text" name="title" value="" size="50" maxlength="100" /></td>
      <td class="second"><input class="input" type="text" name="count" value="" size="6" maxlength="7" /></td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Save#}" />
</form>
