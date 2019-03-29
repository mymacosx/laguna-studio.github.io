<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['new'].submit();
    }
});

$(document).ready(function() {
    $('#new').validate({
	rules: {
	    Name_1: { required: true }
	},
	messages: { }
    });
});
//-->
</script>

<div class="popbox">
  <form name="new" id="new" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td class="row_left" width="150"><label for="Name_1">{#Global_Name#}</label></td>
        <td class="row_right"><input class="input" style="width: 200px" type="text" name="Name_1" id="Name_1" /></td>
      </tr>
      <tr>
        <td class="row_left" valign="top"><label for="Beschreibung_1">{#Global_descr#}</label></td>
        <td class="row_right">{$Beschreibung_1}</td>
      </tr>
      <tr>
        <td class="row_left" valign="top">{#Gallery_addGal#}</td>
        <td class="row_right">
          <select class="input" style="width: 200px" name="thegal">
            <option value="0">{#Gallery_addNoParent#}</option>
            {foreach from=$gallery item=item}
              {if $item->Parent_Id == 0}
                <option value="{$item->Id}" {if $smarty.request.subg == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {else}
                <option value="{$item->Id}" {if $smarty.request.subg == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="row_left"><span class="stip" title="{$lang.GalleryTagHelp|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </td>
        <td class="row_right"><input name="Tags" type="text" class="input" value="{$res->Tags|sanitize}" style="width: 200px" maxlength="255" /></td>
      </tr>
    </table>
    <input name="save" type="hidden" id="new" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  </form>
</div>