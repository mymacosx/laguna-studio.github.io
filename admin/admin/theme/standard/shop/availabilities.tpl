<div class="header">{#Shop_availabilities_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="" name="kform">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="120" class="headers">{#GlobalValue#}</td>
        <td width="100" class="headers">{$language.name.1}</td>
        <td width="100" class="headers">{$language.name.2}</td>
        <td class="headers">{$language.name.3}</td>
      </tr>
      {foreach from=$items item=sr}
        <tr class="{cycle values='first,second'}">
          <td>
            {if $sr.Id == 1}
              <img class="absmiddle stip" title="{$lang.Shop_shippingready_ava_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shippingready_ava#}
            {elseif $sr.Id == 2}
              <img class="absmiddle stip" title="{$lang.Shop_shippingready_nao_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shippingready_nao#}
            {elseif $sr.Id == 3}
              <img class="absmiddle stip" title="{$lang.Shop_shippingready_nav_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shippingready_nav#}
            {elseif $sr.Id == 5}
              <img class="absmiddle stip" title="{$lang.Shop_shippingready_trt_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shippingready_trt#}
            {else}
              <img class="absmiddle stip" title="{$lang.Shop_shippingready_ofy_inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shippingready_ofy#}
            {/if}
          </td>
          <td>
            <input name="Titel_1[{$sr.Id}]" type="text" class="input" style="width: 180px" value="{$sr.Titel_1|sanitize}" maxlength="100" />
            <br />
            <textarea name="Text_1[{$sr.Id}]" cols="30" rows="3" class="input" type="text" style="width: 180px">{$sr.Text_1|sanitize}</textarea>
          </td>
          <td>
            <input name="Titel_2[{$sr.Id}]" type="text" class="input" style="width: 180px" value="{$sr.Titel_2|sanitize}" maxlength="100" />
            <br />
            <textarea name="Text_2[{$sr.Id}]" cols="30" rows="3" class="input" type="text" style="width: 180px">{$sr.Text_2|sanitize}</textarea>
          </td>
          <td>
            <input name="Titel_3[{$sr.Id}]" type="text" class="input" style="width: 180px" value="{$sr.Titel_3|sanitize}" maxlength="100" />
            <br />
            <textarea name="Text_3[{$sr.Id}]" cols="30" rows="3" class="input" type="text" style="width: 180px">{$sr.Text_3|sanitize}</textarea>
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input type="submit" name="button" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
