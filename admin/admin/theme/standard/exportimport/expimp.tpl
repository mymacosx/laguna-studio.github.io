{if $action == 'start' && perm('settings')}
  <div class="header">{#Start_DbDump#}</div>
  <div class="sysinfos">
    <form onsubmit="" id="dbopt" method="post" action="index.php?do=main&sub=db">
      <select class="stip" title="{$lang.Sys_db_dumpInf|sanitize}" style="width: 99%" size="10" name="ta[]" multiple="multiple">
        {$db_fields}
      </select>
      <input name="what" type="hidden" value="dump" />
      <br />
      <br />
      <input class="button" type="submit" value="{#Export_button#}" />
    </form>
  </div>
  <br />
  <div class="header">{#Export_user#}</div>
  <div class="sysinfos">
    <form method="post" action="index.php?do=expimp&amp;action=userexp">
      <table width="100%" border="0" cellpadding="4" cellspacing="1">
        <tr>
          <td width="250">{#Export_usergroup#}&nbsp;</td>
          <td>
            <select name="groups[]" size="5" multiple="multiple" id="select">
              {section name=customer loop=$groups}
                <option value="{$groups[customer].Id}" selected="selected">{$groups[customer].Name}</option>
              {/section}
            </select>
          </td>
        </tr>
        <tr>
          <td width="250">{#Export_type#}&nbsp;</td>
          <td>
            <input name="format" type="radio" value="csv" checked="checked" />&nbsp;csv&nbsp;&nbsp;&nbsp;
            <input name="format" type="radio" value="txt" />&nbsp;txt
          </td>
        </tr>
        <tr>
          <td>{#Export_rpole#}&nbsp;</td>
          <td><input name="separator" type="text" id="separator" value=";" size="5" /></td>
        </tr>
        <tr>
          <td>{#Export_opole#}&nbsp;</td>
          <td><input name="enclosed" type="text" id="enclosed" value="&quot;" size="5" /></td>
        </tr>
        <tr>
          <td>{#Export_rstr#}&nbsp;</td>
          <td><input name="cutter" type="text" id="cutter" value="\r\n" size="5" /></td>
        </tr>
        <tr>
          <td width="250">{#Export_zag#}&nbsp;</td>
          <td><input name="showcsvnames" type="checkbox" id="showcsvnames" value="yes" checked="checked" /></td>
        </tr>
        <tr>
          <td colspan="2"><input name="submit" type="submit" class="button" value="{#Export_button#}" /></td>
        </tr>
      </table>
    </form>
  </div>
  <br />
{/if}
<div class="header">{#Imp_user#}</div>
{if $action == 'start' || $action == 'error'}
  <div class="sysinfos">
    <form method="post" action="index.php?do=expimp&amp;action=importcsv" enctype="multipart/form-data">
      <table width="100%" border="0" cellpadding="4" cellspacing="1">
        <tr>
          <td colspan="2" class="headers">{#Imp_user1#}</td>
        </tr>
        <tr>
          <td colspan="2">
            {if $error}
              <div class="error_box">{$error}</div>
            {/if}
          </td>
        </tr>
        <tr>
          <td width="100">{#select_file#}&nbsp;</td>
          <td><input name="csvfile" type="file" id="csvfile" size="40" /></td>
        </tr>
        <tr>
          <td colspan="2">
            <input name="send" type="hidden" value="1" />
            <input class="button" type="submit" value="{#Imp_dalee#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
{if $action == 'importcsv'}
  <div class="sysinfos">
    <form action="index.php?do=expimp&action=importcsv2" method="post">
      <table width="100%" border="0" cellpadding="3" cellspacing="1">
        <tr>
          <td colspan="3" class="headers">{#Imp_user2#}</td>
        </tr>
        <tr>
          <td width="20%"><strong>{#Imp_file#}&nbsp;</strong></td>
          <td><strong>&nbsp;&raquo;&nbsp;</strong></td>
          <td><strong>{#Imp_base#}</strong></td>
        </tr>
        {foreach from=$field_table item=item}
          {if !empty($item.csv_field)}
          <tr>
            <td width="20%">{$item.csv_field}</td>
            <td width="1%"><strong>&nbsp;&raquo;&nbsp;</strong></td>
            <td>
              <select name="field_{$item.id}">
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
          <td width="20%">{#Nav_Other#}&nbsp;</td>
          <td colspan="2">
            <input name="existing" type="radio" value="replace" checked="checked" /> {#Imp_zamena#}
            <br />
            <input name="existing" type="radio" value="ignore" /> {#Imp_nezamena#}
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <input name="fileid" type="hidden" id="fileid" value="{$fileid}" />
            <input name="types" type="hidden" id="types" value="{$types}" />
            <input class="button" type="submit" value="{#Imp_dalee#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
