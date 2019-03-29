<script type="text/javascript">
<!-- //
function checkcategmove() {
    if(document.getElementById('movecateg').selected == true) {
	document.getElementById('movecateg_select').style.display = '';
    } else {
	document.getElementById('movecateg_select').style.display = 'none';
    }
}
//-->
</script>

<div class="header">{#Manufacturer#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=manufacturer&amp;sub=overview">
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
      <tr>
        <td width="100"><label for="gs">{#Search#}</label></td>
        <td><input style="width: 160px" class="input" type="text" name="q" id="gs" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
      </tr>
      <tr>
        <td><label for="dr">{#DataRecords#}</label></td>
        <td>
          <input type="text" id="dr" class="input" name="pp" style="width: 45px" value="{$limit}" />
          <input class="button" type="submit" value="{#Global_search_b#}" />
          <input name="startsearch" type="hidden" id="startsearch" value="1" />
        </td>
      </tr>
    </table>
  </form>
</div>
{if $manufacturer}
  <form method="post" action="" name="kform">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="{cycle values='first,second'}">
        <td width="150" class="headers"><a href="{$ordstr}{$name_s|default:'&amp;sort=name_asc'}">{#Global_Name#}</a></td>
        <td width="80" align="center" class="headers"><a href="{$ordstr}{$username_s|default:'&amp;sort=username_desc'}">{#Global_Author#}</a></td>
        <td width="50" align="center" class="headers"><a href="{$ordstr}{$date_s|default:'&amp;sort=date_desc'}">{#Global_Created#}</a></td>
        <td width="50" align="center" class="headers"><a href="{$ordstr}{$hits_s|default:'&amp;sort=hits_desc'}">{#Global_Hits#}</a></td>
          {if perm('products_del')}
          <td width="10" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
          {/if}
        <td class="headers">{#Global_Actions#}</td>
      </tr>
      {foreach from=$manufacturer item=n}
        <tr class="{cycle values='second,first'}">
          <td class="row_spacer">
            <a href="../index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$n->Id}&amp;name={$n->Name|translit}" target="_blank"><strong>{$n->Name|sanitize}</strong></a>
            <input type="hidden" name="nid[{$n->Id}]" value="{$n->Id}" />
          </td>
          <td align="center" nowrap="nowrap" class="row_spacer"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$n->Benutzer}&amp;noframes=1">{$n->User}</a></td>
          <td align="center" nowrap="nowrap" class="row_spacer">{$n->Datum|date_format: "%d.%m.%y"}</td>
          <td align="center" class="row_spacer"><input class="input" style="width: 25px" type="text" name="Hits[{$n->Id}]" value="{$n->Hits}" /></td>
            {if perm('manufacturer_del')}
            <td align="center" class="row_spacer"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$n->Id}]" type="checkbox" value="1" /></td>
            {/if}
          <td class="row_spacer" nowrap="nowrap">
            {if perm('manufacturer_newedit')}
              <a class="colorbox stip" title="{$lang.Manufacturer_edit|sanitize}" href="index.php?do=manufacturer&amp;sub=edit&amp;id={$n->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
              {/if}
          </td>
        </tr>
      {/foreach}
    </table>
    <input type="hidden" name="quicksave" value="1" />
    <input class="button" type="submit" value="{#Save#}" />
  </form>
{/if}
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
