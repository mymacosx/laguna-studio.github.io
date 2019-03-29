<div class="popbox">
  <div class="header">{#Shop_prodvotes_vfor#} {$smarty.request.name|sanitize}</div>
  <div class="header_inf">{#Shop_prodvotes_inf#}</div>
  <form name="kform" method="post" action="">
    <table id="tablesorter" class="tablesorter" width="100%" border="0" cellspacing="0" cellpadding="2">
      <thead>
        <tr class="jheader">
          <th width="80" class="headers">{#Global_Date#}</th>
          <th width="100" class="headers">{#Shop_prodvotes_text#}</th>
          <th width="100" class="headers" align="center">{#Shop_prodvotes_points#}</th>
          <th width="100" class="headers" align="center">{#Global_User#}</th>
          <th width="100" class="headers" align="center">{#Publicated#}</th>
          <td class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
        </tr>
      </thead>
      <tbody>
        {foreach from=$votes item=v}
          <tr class="{cycle values='first,second'}">
            <td class="row_spacer" align="center"><span style="display: none">{$v->Datum}</span> {$v->Datum|date_format: "%d.%m.%Y"} <br /></td>
            <td class="row_spacer" width="100"><textarea cols="" rows="" class="input" style="width: 350px; height: 100px" name="Bewertung[{$v->Id}]">{$v->Bewertung|sanitize}</textarea></td>
            <td class="row_spacer" width="100" align="center">
              <span style="display: none"> {$v->Bewertung_Punkte} </span>
              <select class="input" name="Bewertung_Punkte[{$v->Id}]">
                <option value="5" {if $v->Bewertung_Punkte == 5}selected="selected"{/if}>5</option>
                <option value="4" {if $v->Bewertung_Punkte == 4}selected="selected"{/if}>4</option>
                <option value="3" {if $v->Bewertung_Punkte == 3}selected="selected"{/if}>3</option>
                <option value="2" {if $v->Bewertung_Punkte == 2}selected="selected"{/if}>2</option>
                <option value="1" {if $v->Bewertung_Punkte == 1}selected="selected"{/if}>1</option>
                <option value="0" {if $v->Bewertung_Punkte == 0}selected="selected"{/if}>0</option>
              </select>
            </td>
            <td width="100" align="center" class="row_spacer"><a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$v->Benutzer}&amp;noframes=1">{$v->BenutzerName}</a></td>
            <td align="center" class="row_spacer">
              <label><input type="radio" name="Offen[{$v->Id}]" value="1" {if $v->Offen == 1}checked="checked"{/if} />{#Yes#}</label>
              <label><input type="radio" name="Offen[{$v->Id}]" value="0" {if $v->Offen == 0}checked="checked"{/if} />{#No#} </label>
            </td>
            <td><label><input name="Del[{$v->Id}]" type="checkbox" id="Del[]" value="1" />{#Global_Delete#}</label></td>
          </tr>
        {/foreach}
      </tbody>
    </table>
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
  </form>
  <div class="navi_div"><strong>{#GoPagesSimple#}</strong>
    <form method="get" action="index.php">
      <input type="text" class="input" style="width: 25px; text-align: center" name="page" value="{$smarty.request.page|default:'1'}" />
      <input type="hidden" name="do" value="shop" />
      <input type="hidden" name="sub" value="prodvotes" />
      <input type="hidden" name="id" value="{$smarty.request.id}" />
      <input type="hidden" name="name" value="{$smarty.request.name}" />
      <input type="hidden" name="noframes" value="1" />
      <input type="hidden" name="limit" value="{$limit|default:'15'}" />
      <input type="submit" class="button" value="{#GoPagesButton#}" />
    </form>
    &nbsp;&nbsp;
    {if !empty($pages)}
      <strong>{#GoPages#}</strong>
      {$pages}
    {/if}
  </div>
</div>
