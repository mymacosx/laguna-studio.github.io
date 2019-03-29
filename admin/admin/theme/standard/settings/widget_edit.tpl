<div class="header">{#WidgetEdit#} - {$widget.BName|sanitize}</div>
<form method="post" action="index.php?do=settings&amp;sub=widgetedit&amp;noframes=1">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td class="headers">{#GlobalValue#}</td>
      <td class="headers">{#GlobalParameter#}</td>
    </tr>
    {foreach from=$witget_settings item=b}
      <tr class="{cycle values='second,first'}">
        <td width="250">
          {if !empty($b.widget_inf)}
            <img class="absmiddle stip" title="{$b.widget_inf|sanitize}" src="{$imgpath}/help.png" alt="" />
          {else}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          {/if}
          <strong>{$b.widget}</strong>
        </td>
        <td><input type="text" class="input" style="width: 200px" name="witget_settings[{$b.key}]" value="{$b.value|sanitize}" /></td>
      </tr>
    {/foreach}
    <tr class="{cycle values='second,first'}">
      <td width="250">
        <img class="absmiddle stip" title="{$lang.WidgetNameInf|replace: '__ID__': $widget.Result}" src="{$imgpath}/help.png" alt="" />
        <strong>{#WidgetName#}</strong>
      </td>
      <td>
        <input type="text" class="input" style="width: 200px" name="Result" value="{$widget.Result|sanitize}" />
      </td>
    </tr>
    <tr class="{cycle values='second,first'}">
      <td width="250">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>{#Global_Active#}</strong></td>
      <td>
        <label><input type="radio" name="Aktiv" value="1" {if $widget.Aktiv == 1}checked="checked" {/if}/>{#Yes#}</label>
        <label><input type="radio" name="Aktiv" value="0" {if $widget.Aktiv == 0}checked="checked" {/if}/>{#No#}</label>
      </td>
    </tr>
  </table>
  <br />
  <input type="hidden" name="widget_id" value="{$widget.Id}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
</form>
