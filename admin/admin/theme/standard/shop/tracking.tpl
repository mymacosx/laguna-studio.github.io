<div class="header">{#Shop_Tracking#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="" name="kform">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="100" class="headers" nowrap="nowrap">{#Shop_TrackingType#}</td>
        <td class="headers" nowrap="nowrap">{#Shop_TrackingAdress#}</td>
        <td class="headers">&nbsp;</td>
      </tr>
      {foreach from=$Tracker item=tracker}
        <tr class="{cycle values='first,second'}">
          <td><input class="input" type="text" name="Name[{$tracker->Id}]" value="{$tracker->Name|sanitize}" /></td>
          <td><input name="Hyperlink[{$tracker->Id}]" type="text" class="input" value="{$tracker->Hyperlink|sanitize}" size="80" /></td>
          <td>
            {if $tracker->Id > 5}
              <label><input name="Del[{$tracker->Id}]" type="checkbox" id="Del[]" value="1" />{#Global_Delete#}</label>
              {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input type="submit" name="button" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<br />
<br />
<br />
<form method="post" action="">
  <fieldset>
    <legend>{#Shop_TrackingNew#}</legend>
    <div class="maintable">
      <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
        <tr>
          <td width="100" class="headers">{#Shop_TrackingType#}</td>
          <td class="headers" nowrap="nowrap">{#Shop_TrackingAdress#}</td>
        </tr>
        {section name=new loop=3}
          <tr class="{cycle values='second,first'}">
            <td><input class="input" type="text" name="Name[{$smarty.section.new.index+1}]" value="" /></td>
            <td nowrap="nowrap" class="row_spacer"><input name="Hyperlink[{$smarty.section.new.index+1}]" type="text" class="input" value="" size="80" /></td>
          </tr>
        {/section}
      </table>
    </div>
    <input class="button" type="submit" value="{#Save#}" />
    <input name="new" type="hidden" id="new" value="1" />
  </fieldset>
</form>
