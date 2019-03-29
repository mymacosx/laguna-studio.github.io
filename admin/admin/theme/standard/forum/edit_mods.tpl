<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#q').suggest('index.php?do=forums&sub=searchmod&key=' + Math.random());
});
//-->
</script>

<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
  {foreach from=$mods item=mod}
    <form action="" method="post">
      <input type="hidden" name="delete" value="1" />
      <input type="hidden" name="user_id" value="{$mod->Id}" />
      <input type="hidden" name="id" value="{$smarty.get.id}" />
      <tr>
        <td width="20%" class="row_left">{$mod->Benutzername}</td>
        <td class="row_right"><input type="image" src="{$imgpath}/delete.png" alt="" border="" /></td>
      </tr>
    </form>
  {/foreach}
</table>
<br />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableborder">
  <tr>
    <td >
      <fieldset>
        <legend>{#Forums_ModsAdd#}</legend>
        {if $error == 1}
          <div class="error_box"> {#Forums_ModsAddErr#} </div>
        {/if}
        <small>{#Forums_ModsAddInf#}</small>
        <br />
        <form action="" method="post">
          <input type="hidden" name="new" value="1" />
          <input type="hidden" name="id" value="{$smarty.get.id}" />
          <input class="input" type="text" name="q" size="30" style="width: 200px" id="q" maxlength="100" />
          <input class="button" type="submit" value="{#Forums_ModsAdd#}" />
          <input class="button" type="button" value="{#Close#}" onclick="closeWindow();" />
          {foreach from=$mods item=mod}
            <input type="hidden" name="mods[]" value="{$mod->Benutzername}" />
          {/foreach}
        </form>
      </fieldset>
    </td>
  </tr>
</table>
