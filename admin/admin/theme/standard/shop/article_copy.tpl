<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['cf'].submit();
    }
});

$(document).ready(function() {
    $('#copyform').validate( {
	rules: {
	    Artikelnummer: { required: true, minlength: 2 },
	    Titel_1: { required: true, minlength: 2 }
        },
	 messages: { }
    });
});
//-->
</script>

<h4>{$row->Titel_1}</h4>
{if $error}
  <div class="error_box">
    {foreach from=$error item=e}
      <strong>{$e}</strong>
      <br />
    {/foreach}
  </div>
{/if}
<p>
<form id="copyform" name="cf" method="post" action="">
  <table>
    <tr>
      <td><label for="Artikelnummer">{#Shop_articles_numberNew#}</label></td>
      <td><input class="input" type="text" name="Artikelnummer" id="Artikelnummer" value="{$smarty.post.Artikelnummer}" /></td>
    </tr>
    <tr>
      <td><label for="Titel_1">{#Shop_articles_name#}</label></td>
      <td><input class="input" type="text" name="Titel_1" id="Titel_1" value="{$smarty.post.Titel_1}" /></td>
    </tr>
    <tr>
      <td>{#Shop_articles_copyEAfter#}</td>
      <td><input name="edit_new" type="checkbox" id="edit_new" value="1" checked="checked" /></td>
    </tr>
    <tr>
      <td><input name="copy" type="hidden" id="copy" value="1" /></td>
      <td><input class="button" type="submit" value="{#Save#}" onclick="" /></td>
    </tr>
  </table>
</form>
</p>
