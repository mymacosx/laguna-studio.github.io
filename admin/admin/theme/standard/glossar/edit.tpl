<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['sysform'].submit();
    }
});
$(document).ready(function() {
    $('#sysform').validate({
	rules: {
	    Wort: { required: true }
	},
	messages: { }
    });
});
//-->
</script>

<div class="header">
  {if $smarty.request.type == 1}
    {#Hiden_Link#}
  {elseif $smarty.request.type == 2}
    {#Links_Link#}
  {else}
    {#Glossar#}
  {/if}
</div>
<form method="post" action="" name="sysform" id="sysform">
  <fieldset>
    <legend>{#Global_Name#}</legend>
    <input type="text" class="input" style="width: 200px" value="{$res->Wort|default:''|sanitize}" name="Wort" />
  </fieldset>
  <fieldset>
    {if $smarty.request.type == 1}
      <legend>{#Links_Link#}</legend>
      <input type="text" class="input" name="Beschreibung" style="width: 500px" value="{$res->Beschreibung|default:''|sanitize}" />
      <input type="hidden" name="Typ" value="1" />
    {elseif $smarty.request.type == 2}
      <legend>{#Links_Link#}</legend>
      <input type="text" class="input" name="Beschreibung" style="width: 500px" value="{$res->Beschreibung|default:''|sanitize}" />
      <input type="hidden" name="Typ" value="2" />
    {else}
      <legend>{#Global_descr#}</legend>
      {$Beschreibung}
      <input type="hidden" name="Typ" value="0" />
    {/if}
  </fieldset>
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
