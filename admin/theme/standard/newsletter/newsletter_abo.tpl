<div class="box_innerhead">{#Newsletter#}</div>
{if !empty($email_error)}
  <div class="error_box"> <strong>{#Newsletter_e_inf#}</strong>
    <ul>
      {foreach from=$email_error item=e}
        <li>{$e}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<div class="newsletter_box">
  {if isset($Entry_Ok) && $Entry_Ok == 1}
    <div class="h2">{#Newsletter_okT#}</div>
    <br />
    {#Newsletter_ok#}
    <br />
    <br />
  {else}
    {#Newsletter_info#}
    <br />
    <br />
    <form method="post" action="index.php?p=newsletter&amp;area={$area}">
      <table width="100%" cellspacing="0" cellpadding="3">
        <tr>
          <td width="90" class="row_left"><label for="lnl_email"><strong>{#Email#}&nbsp;</strong></label></td>
          <td class="row_right"><input name="nl_email" id="lnl_email" type="text" class="input" style="width: 250px" value="{$smarty.post.nl_email|default:''|sanitize}" /></td>
        </tr>
        {if $Nl_Count>1}
          <tr>
            <td valign="top" class="row_left"><strong>{#Newsletter_sections#}&nbsp;</strong></td>
            <td class="row_right">
              {foreach from=$nl_items item=nli}
                <label class="stip" title="{$nli->Info|tooltip}"><input type="checkbox" name="nl_welche[{$nli->Id}]" value="1" />{$nli->Name|sanitize}</label>
                <br />
              {/foreach}
            </td>
          </tr>
        {/if}
        <tr>
          <td class="row_left"><strong>{#Newsletter_format#}&nbsp;</strong></td>
          <td class="row_right">
            {if $Nl_Count < "2"}
              {foreach from=$nl_items item=nli}
                <input type="hidden" name="nl_welche[{$nli->Id}]" value="1" />
              {/foreach}
            {/if}
            <select name="nl_format" class="input">
              <option value="html">{#GlobalHTML#}</option>
              <option value="text">{#GlobalText#}</option>
            </select>&nbsp;
            <input type="submit" class="button" value="{#Newsletter_aboButton#}" />
            <input type="hidden" name="action" value="abonew" />
          </td>
        </tr>
      </table>
    </form>
  {/if}
</div>
