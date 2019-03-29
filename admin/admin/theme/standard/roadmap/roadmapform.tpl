<div class="popbox">
  {if $smarty.request.sub == 'newroadmap'}
    <div class="header">{#NewRoadmap#}</div>
  {/if}
  <form method="post" action="{$formaction}" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="4" cellspacing="0">
      {if $smarty.request.sub != 'newroadmap'}
        <tr>
          <td class="row_left">{#Sys_on#}</td>
          <td class="row_right"><input name="Aktiv" type="checkbox" id="Aktiv" value="1" {if $item.Aktiv == 1}checked="checked"{/if} /></td>
        </tr>
      {else}
        <tr>
          <td><input name="active" value="1" type="hidden" /></td>
        </tr>
      {/if}
      <tr>
        <td width="200" class="row_left">{#Global_Name#}</td>
        <td class="row_right"><input style="width: 300px" name="Name" type="text" value="{$item.Name}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Global_descr#}</td>
        <td class="row_right"><textarea name="Beschreibung" cols="90" rows="8">{$item.Beschreibung}</textarea></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Global_Position#}</td>
        <td class="row_right"><input style="width: 95px" name="Pos" type="text" value="{$item.Pos}" /></td>
      </tr>
    </table>
    <br />
    <input name="save" type="hidden" id="save" value="1" />
    <input name="submit" type="submit" class="button" value="{#Save#}" />
  </form>
</div>
