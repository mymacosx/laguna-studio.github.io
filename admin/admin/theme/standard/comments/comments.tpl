<div class="popbox">
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  {if $smarty.request.where == 'all'}
    <div class="header">{#CommentsShowUnpublic#}</div>
  {/if}
  <form method="post" action="" name="kform">
    <div class="maintable">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="headers">
          <td width="90" class="headers">{#Info#}</td>
          <td width="110" class="headers"> {#Global_Comments#}</td>
          <td align="center" class="headers">{#Bereich#}</td>
          <td width="120" align="center" class="headers">{#Publicated#}</td>
          <td width="30" align="center" class="headers"><label><input class="stip" title="{$lang.Global_SelAll|sanitize}" name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></label></td>
        </tr>
        {foreach from=$comments item=c}
          <tr class="{cycle values='second,first'}">
            <td valign="top" class="row_spacer">
              <table width="100%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td>{#User_first#}</td>
                  <td>
                    <input type="hidden" name="Cid_k[{$c->Id}]" value="{$c->Id}" />
                    <input class="input" name="Autor[{$c->Id}]" value="{$c->Autor|sanitize}" />
                  </td>
                </tr>
                <tr>
                  <td>{#User_town#}</td>
                  <td><input class="input" name="Autor_Herkunft[{$c->Id}]" value="{$c->Autor_Herkunft|sanitize}" /></td>
                </tr>
                <tr>
                  <td>{#Comments_web#}</td>
                  <td><input class="input" name="Autor_Web[{$c->Id}]" value="{$c->Autor_Web|sanitize}" /></td>
                </tr>
                <tr>
                  <td>{#Global_Email#}</td>
                  <td><input class="input" name="Autor_Email[{$c->Id}]" value="{$c->Autor_Email|sanitize}" /></td>
                </tr>
              </table>
              <input type="hidden" name="Id[{$c->Id}]" value="{$c->Id}"/>
            </td>
            <td valign="top" nowrap="nowrap" class="row_spacer">
              <textarea cols="" rows=""  style="{if !empty($smarty.request.id)}height: 200px;{else}height: 100px;{/if}width: 380px;" name="Eintrag[{$c->Id}]">{$c->Eintrag|sanitize}</textarea>
              <br />
              <strong>{#Global_Date#}: </strong> {$c->Datum|date_format: $lang.DateFormat} | <strong>{#Comments_ip#}: </strong> {$c->Autor_Ip} </td>
            <td align="center" valign="top" class="row_spacer">
              <br />
              <a class="stip" href="javascript: void(0);" onclick="parent.location.href = 'index.php?{$c->navi_modul}';">{#CommentsLinkAdm#} - {$c->title_modul}</a><br /><br />
              {if !empty($c->navi_link)}
                <a class="colorbox stip" href="{$baseurl}/{$c->navi_link}&amp;noframes=1">{#CommentsLinkId#}</a>
              {/if}
            </td>
            <td width="120" align="center" valign="top" class="row_spacer">
              <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
              <label><input type="radio" name="Aktiv[{$c->Id}]" value="0" {if $c->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
            </td>
            <td width="30" align="center" valign="top"><label><input class="stip" title="{$lang.Global_Delete|sanitize}" name="del[{$c->Id}]" type="checkbox" id="d" value="1" /></label></td>
          </tr>
        {/foreach}
      </table>
    </div>
    <br />
    {if !empty($Navi)}
      <div class="navi_div"> {$Navi} </div>
    {/if}
    {if !empty($smarty.request.id)}
      <input type="hidden" name="close" value="1" />
    {/if}
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
    {if $smarty.request.where != 'all' && isset($smarty.request.noframes) && $smarty.request.noframes == 1}
      <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    {/if}
  </form>
</div>
