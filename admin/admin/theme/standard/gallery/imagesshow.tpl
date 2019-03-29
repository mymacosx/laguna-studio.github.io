<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['ppform'].submit();
    }
});

$(document).ready(function() {
    $('#ppform').validate({
	rules: {
	    pp: { required: true,range: [2,250] }
	},
	messages: { }
    });
});
//-->
</script>

<div class="popbox">
  <div class="header">{#Gallery_ImagesView#} - {$res->Name_1|sanitize}</div>
  {if $images}
    <form  method="post" action="" name="kform" id="kform">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td width="50" class="headers">&nbsp;</td>
          <td align="center" class="headers">&nbsp;</td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$name1sort|default:'name1_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Name#} ({$language.name.1})</a></td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$name2sort|default:'name2_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Name#} ({$language.name.2})</a></td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$name3sort|default:'name3_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Name#} ({$language.name.3})</a></td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$autorsort|default:'autor_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Author#}</a></td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$datesort|default:'date_asc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Date#}</a></td>
          <td align="center" class="headers"><a href="index.php?do=gallery&amp;sub=editimages&amp;id={$smarty.request.id}&amp;noframes=1&amp;sort={$hitssort|default:'hits_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}">{#Global_Hits#}</a></td>
          <td align="center" class="headers"><img class="absmiddle" src="{$imgpath}/message.png" alt="" /></td>
          <td align="center" class="headers"><input class="stip" title="{$lang.Global_SelAll|sanitize}" name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></td>
        </tr>
        {foreach from=$images item=im}
          <tr class="{cycle values='second,first'}">
            <td class="row_spacer"><a target="_blank" href="../index.php?p=gallery&amp;action=showimage&amp;id={$im->Id}&amp;galid={$res->Id}&amp;ascdesc=desc&amp;categ={$res->Kategorie}">{$im->Thumb}</a></td>
            <td align="center" class="row_spacer"><a class="colorbox stip" title="{$lang.GalleryEdit|sanitize}" href="index.php?do=gallery&amp;sub=editimage&amp;id={$im->Id}&amp;noframes=1"><img src="{$imgpath}/image_edit.png" alt="" border="0" class="absmiddle" /></a></td>
            <td align="center" class="row_spacer">
              <input type="hidden" name="Id[{$im->Id}]" value="{$im->Id}" />
              <input class="input" style="width: 150px" type="text" name="Name_1[{$im->Id}]" value="{$im->Name_1|sanitize}" />
              <br />
              <textarea cols="" rows=""  class="input" style="width: 150px" name="Beschreibung_1[{$im->Id}]">{$im->Beschreibung_1|sanitize}</textarea></td>
            <td align="center" class="row_spacer">
              <input class="input" style="width: 150px" type="text" name="Name_2[{$im->Id}]" value="{$im->Name_2|sanitize}" />
              <br />
              <textarea cols="" rows=""  class="input" style="width: 150px" name="Beschreibung_2[{$im->Id}]">{$im->Beschreibung_2|sanitize}</textarea></td>
            <td align="center" class="row_spacer">
              <input class="input" style="width: 150px" type="text" name="Name_3[{$im->Id}]" value="{$im->Name_3|sanitize}" />
              <br />
              <textarea cols="" rows=""  class="input" style="width: 150px" name="Beschreibung_3[{$im->Id}]">{$im->Beschreibung_3|sanitize}</textarea></td>
            <td class="row_spacer" align="center"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$im->Autor}&amp;noframes=1">{$im->User}</a></td>
            <td class="row_spacer" align="center">{$im->Datum|date_format: '%d.%m.%y'}</td>
            <td class="row_spacer" align="center">{$im->Klicks}</td>
            <td align="center" class="row_spacer">
              {if $im->Comments}
                <a title="{#Global_Comments#}" class="colorbox" href="index.php?do=comments&amp;where=galerie&amp;object={$im->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/message.png" alt="" border="0" /></a>
                {else}
                <img class="absmiddle" src="{$imgpath}/message_no.png" alt="" border="0" />
              {/if}
            </td>
            <td class="row_spacer" align="center"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$im->Id}]" type="checkbox" value="1" /></td>
          </tr>
        {/foreach}
      </table>
      <br />
      <input type="submit" class="button" value="{#Save#}" />
      <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
      <input name="save" type="hidden" id="save" value="1" />
    </form>
    <br />
    {if !empty($Navi)}
      <div class="navi_div"> {$Navi} </div>
    {/if}
    <br />
    <form name="ppform" id="ppform" method="post" action="{$ppformaction}">
      {#Gallery_pp#}:
      <input class="input" style="width: 50px" type="text" name="pp" value="{$limit}" />
      <input type="submit" class="button" value="{#Global_Show#}" />
    </form>
  {else}
    <p align="center">
    <h2>{#Gallery_noImages#}</h2>
    <br />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  </p>
{/if}
</div>
