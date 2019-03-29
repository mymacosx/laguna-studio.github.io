<div class="popbox">
  <form method="post" action="{$formaction}" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="4" cellspacing="0">
      <tr>
        <td class="row_left">{#ClosedTickets#}</td>
        <td class="row_right"><input name="Fertig" type="checkbox" id="Fertig" value="1" {if $item.Fertig == 1}checked="checked"{/if} /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Global_descr#}</td>
        <td class="row_right"><textarea name="Beschreibung" cols="90" rows="8">{$item.Beschreibung}</textarea></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Priority#}</td>
        <td class="row_right">
          <select name="pr">
            <option value="1" {if $item.pr == 1}selected="selected"{/if}>{#highest#}</option>
            <option value="2" {if $item.pr == 2}selected="selected"{/if}>{#high#}</option>
            <option value="3" {if $item.pr == 3}selected="selected"{/if}>{#normal#}</option>
            <option value="4" {if $item.pr == 4}selected="selected"{/if}>{#low#}</option>
            <option value="5" {if $item.pr == 5}selected="selected"{/if}>{#lowest#}</option>
          </select>
        </td>
      </tr>
      {if $smarty.request.sub != 'newticket'}
        <tr>
          <td width="200" class="row_left">{#Global_Author#}</td>
          <td class="row_right">
            <input style="width: 200px" name="Name" readonly="" type="text" value="{$item.Benutzer}" />
            <input style="width: 95px" name="Uid" type="text" value="{$item.Uid}" />
          </td>
        </tr>
        <tr>
          <td width="200" class="row_left">{#LastChange#}</td>
          <td class="row_right"> {$item.Datum|date_format: '%d-%m-%Y, %H:%M'} </td>
        </tr>
      {/if}
    </table>
    <br />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
    <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
  </form>
</div>
