<form method="post" action="">
  <div class="header">{#Navigation_docsedit#}</div>
  <div class="header_inf">
    <a class="colorbox" href="index.php?do=navigation&amp;sub=newnaviitem&amp;id={$smarty.request.id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Navigation_newdoc#}</a>&nbsp;&nbsp;&nbsp;
    <a href="index.php?do=navigation"><img class="absmiddle" src="{$imgpath}/arrow_left.png" alt="" border="" /> {#Global_BackOverview#}</a>&nbsp;&nbsp;&nbsp;
      {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td class="headers">{#Global_Name#}</td>
      <td class="headers">{#Navigation_doc#}</td>
      <td width="100" align="center" class="headers" nowrap="nowrap">{#NaviHrefTitle#}</td>
      <td width="50" align="center" class="headers">{#Global_Position#}</td>
      <td class="headers">{#Global_Active#}</td>
      <td class="headers">{#Global_Target#}</td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$items item=n}
      <tr class="second">
        <td width="175" class="spacer_rows"><input name="Titel_1[{$n->Id}]" class="input" style="width: 175px" type="text" id="textfield" value="{$n->Titel_1|sanitize}" /></td>
        <td width="100" class="spacer_rows"><input class="input" style="width: 215px" type="text" name="Dokument[{$n->Id}]" value="{$n->Dokument|sanitize}" /></td>
        <td width="100" align="center" class="spacer_rows"><input class="input" style="width: 100px" type="text" name="Link_Titel_1[{$n->Id}]" value="{$n->Link_Titel_1|sanitize}" /></td>
        <td width="50" align="center" class="spacer_rows"><input class="input" name="Position[{$n->Id}]" type="text" id="Position" value="{$n->Position|sanitize}" size="4" maxlength="3" /></td>
        <td width="50" class="spacer_rows">
          <select class="input" name="Aktiv[{$n->Id}]">
            <option value="1" {if $n->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
            <option value="0" {if $n->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
          </select>
        </td>
        <td width="100" class="spacer_rows"><select class="input" name="Ziel[{$n->Id}]">
            <option value="_self" {if $n->Ziel == '_self'}selected="selected"{/if}>{#Navigation_self#}</option>
            <option value="_new" {if $n->Ziel == '_new'}selected="selected"{/if}>{#Navigation_new#}</option>
          </select>
        </td>
        <td class="spacer_rows">
          {if perm('navigation_edit')}
            <a class="colorbox stip" title="{$lang.Navigation_docedit|sanitize}" href="index.php?do=navigation&amp;sub=editnavidoc&amp;id={$n->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
            <a class="stip" title="{$lang.Navigation_docdel|sanitize}" href="javascript: void(0);" onclick="if (confirm('{#Navigation_docdelc#}')) location.href = 'index.php?do=navigation&amp;sub=delete&amp;id={$n->Id}&amp;navi={$smarty.request.id}';"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
            {/if}
        </td>
      </tr>
      {foreach from=$n->sub1 item=s1 name=second}
        <tr class="first">
          <td width="175" class="{if $smarty.foreach.second.last && !$s1->sub2}tree4{else}tree1{/if}"><input name="Titel_1[{$s1->Id}]" class="input" style="margin-left: 25px; width: 150px" type="text" id="textfield" value="{$s1->Titel_1|sanitize}" /></td>
          <td><input class="input" style="width: 215px" type="text" name="Dokument[{$s1->Id}]" value="{$s1->Dokument|sanitize}" /></td>
          <td width="100" align="center"><input class="input" style="width: 100px" type="text" name="Link_Titel_1[{$s1->Id}]" value="{$s1->Link_Titel_1|sanitize}" /></td>
          <td width="50" align="center"><input class="input" name="Position[{$s1->Id}]" type="text" id="Position" value="{$s1->Position|sanitize}" size="4" maxlength="3" /></td>
          <td width="50" class="spacer_rows">
            <select class="input" name="Aktiv[{$s1->Id}]">
              <option value="1" {if $s1->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
              <option value="0" {if $s1->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
            </select>
          </td>
          <td>
            <select class="input" name="Ziel[{$s1->Id}]">
              <option value="_self" {if $s1->Ziel == '_self'}selected="selected"{/if}>{#Navigation_self#}</option>
              <option value="_new" {if $s1->Ziel == '_new'}selected="selected"{/if}>{#Navigation_new#}</option>
            </select>
          </td>
          <td>
            {if perm('navigation_edit')}
              <a class="colorbox stip" title="{$lang.Navigation_docedit|sanitize}" href="index.php?do=navigation&amp;sub=editnavidoc&amp;id={$s1->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
              <a class="stip" title="{$lang.Navigation_docdel|sanitize}" href="javascript: void(0);" onclick="if (confirm('{#Navigation_docdelc#}')) location.href = 'index.php?do=navigation&amp;sub=delete&amp;id={$s1->Id}&amp;navi={$smarty.request.id}';"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
              {/if}
          </td>
        </tr>
        {foreach from=$s1->sub2 item=s2 name=third}
          <tr class="first">
            <td width="175" class="{if $smarty.foreach.third.last}tree3{else}tree2{/if}"><input name="Titel_1[{$s2->Id}]" class="input" style="margin-left: 50px; width: 125px" type="text" id="textfield" value="{$s2->Titel_1|sanitize}" /></td>
            <td><input class="input" style="width: 215px" type="text" name="Dokument[{$s2->Id}]" value="{$s2->Dokument|sanitize}" /></td>
            <td width="100" align="center"><input class="input" style="width: 100px" type="text" name="Link_Titel_1[{$s2->Id}]" value="{$s2->Link_Titel_1|sanitize}" /></td>
            <td width="50" align="center"><input class="input" name="Position[{$s2->Id}]" type="text" id="Position" value="{$s2->Position|sanitize}" size="4" maxlength="3" /></td>
            <td width="50" class="spacer_rows">
              <select class="input" name="Aktiv[{$s2->Id}]">
                <option value="1" {if $s2->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
                <option value="0" {if $s2->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
              </select>
            </td>
            <td>
              <select class="input" name="Ziel[{$s2->Id}]">
                <option value="_self" {if $s2->Ziel == '_self'}selected="selected"{/if}>{#Navigation_self#}</option>
                <option value="_new" {if $s2->Ziel == '_new'}selected="selected"{/if}>{#Navigation_new#}</option>
              </select>
            </td>
            <td>
              {if perm('navigation_edit')}
                <a class="colorbox stip" title="{$lang.Navigation_docedit|sanitize}" href="index.php?do=navigation&amp;sub=editnavidoc&amp;id={$s2->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
                <a class="stip" title="{$lang.Navigation_docdel|sanitize}" href="javascript: void(0);" onclick="if (confirm('{#Navigation_docdelc#}')) location.href = 'index.php?do=navigation&amp;sub=delete&amp;id={$s2->Id}&amp;navi={$smarty.request.id}';"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="" /></a>
                {/if}
            </td>
          </tr>
        {/foreach}
      {/foreach}
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
