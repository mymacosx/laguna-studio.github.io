<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#sysform').validate({
        rules: {
	    Max_Groesse: { required: true, range: [15,16384] }
        },
        messages: {
	    Url: { url: '{#InserURL#}' }
        },
        submitHandler: function() {
            document.forms['sysform'].submit();
        }
    });
});
//-->
</script>

<div class="header">{#SettingsModule#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" name="sysform" id="sysform" method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="row_left" width="350">{#ForumNofollow#}</td>
      <td class="row_right">
        <label><input type="radio" name="nofollow" value="1" {if $res.nofollow == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="nofollow" value="0" {if $res.nofollow == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#GlobalImageSize#}</td>
      <td class="row_right"><input class="input" name="size" type="text" style="width: 40px" value="{$res.size}" maxlength="3" /> px</td>
    </tr>
    <tr>
      <td class="row_left">{#GlobalImageCompres#}</td>
      <td class="row_right"><input class="input" name="compres" type="text" style="width: 40px" value="{$res.compres}" maxlength="2" /> %</td>
    </tr>
    {if perm('forum_attachments')}
    <tr>
      <td class="row_left">{#Forums_attMax#}</td>
      <td class="row_right"><input class="input" name="Max_Groesse" type="text" style="width: 40px" value="{$res.Max_Groesse}" maxlength="6" /> (1024 梳 = 1 提)</td>
    </tr>
    <tr>
      <td class="row_left">{#Forums_attInf#}</td>
      <td class="row_right">
        <select style="width: 100px;" name="Typen[]" size="15" multiple="multiple" id="select">
          {foreach from=$possibles item=p}
            <option style="width: 100px;" value="{$p}" {if in_array($p, $res.possibles)}selected="selected" {/if}>{$p}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    {/if}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
