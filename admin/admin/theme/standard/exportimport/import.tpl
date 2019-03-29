{if $next != 1}
  <div class="header">{#Imp_tov1#}</div>
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <form method="post" action="?do=shopimport&amp;action=importcsv" enctype="multipart/form-data">
    {if $error}
      <div class="error_box">{$error}</div>
    {/if}
    <fieldset>
      <legend>{#select_file#}</legend>
      <input name="csvfile" type="file" id="csvfile" size="40" />
    </fieldset>
    <input name="send" type="hidden" value="1" />
    <input class="button" type="submit" value="{#Imp_dalee#}" />
  </form>
{/if}
{if $next == 1}
  <div class="header">{#Imp_tov2#}</div>
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <form action="?do=shopimport&amp;action=importcsv2" method="post">
    <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tableborder">
      <tr>
        <td width="10%" nowrap="nowrap" class="headers"><strong>{#Imp_file#}&nbsp;</strong></td>
        <td nowrap="nowrap" class="headers"><strong>&nbsp; &raquo;&nbsp; </strong></td>
        <td class="headers"><strong>{#Imp_base#}</strong></td>
      </tr>
      {foreach from=$field_table item=item}
        {if !empty($item.csv_field)}
          <tr class="{cycle values='second,first'}">
            <td width="10%" nowrap="nowrap"> {$item.csv_field}&nbsp; </td>
            <td width="1%" nowrap="nowrap"><strong>&nbsp; &raquo;&nbsp; </strong></td>
            <td class="secondrow">
              <select class="input" name="field_{$item.id}">
                <option value=""></option>
                {foreach from=$available_fields item=field key=key}
                  <option value="{$key}"{if $key == $item.my_field || $key == $item.csv_field} selected="selected"{/if}>
                    {$field}
                  </option>
                {/foreach}
              </select>
            </td>
          </tr>
        {/if}
      {/foreach}
      <tr>
        <td width="10%" class="secondrow">{#Nav_Other#}</td>
        <td colspan="2" class="secondrow">
          <input name="existing" type="radio" value="replace" checked="checked" />{#Imp_zamena#}
          <br />
          <input name="existing" type="radio" value="ignore" />{#Imp_nezamena#}
        </td>
      </tr>
      {if $method == 'shop'}
        <tr>
          <td class="secondrow">{#ShopPrais#}&nbsp;</td>
          <td colspan="2" class="secondrow">
            <input name="netto_to_brutto" type="checkbox" id="netto_to_brutto" value="1" /> {#Sys_on#} &nbsp;&nbsp;&nbsp;&nbsp;
            <select class="input" name="operand">
              <option value="+" selected="selected">{#Shop_payment_ZZ#}</option>
              <option value="-">{#Shop_payment_AZ#}</option>
            </select>
            <input name="mpli" type="text" id="mpli" value="10" size="3" maxlength="2" /> %
          </td>
        </tr>
      {/if}
      <tr>
        <td width="10%" class="thirdrow">&nbsp;</td>
        <td colspan="2" class="thirdrow">
          <input name="fileid" type="hidden" id="fileid" value="{$fileid}" />
          <input name="types" type="hidden" id="types" value="{$types}" />
          <input class="button" type="submit" value="{#Imp_button#}" />
        </td>
      </tr>
    </table>
  </form>
{/if}
