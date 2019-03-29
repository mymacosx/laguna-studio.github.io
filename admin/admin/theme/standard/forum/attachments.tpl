<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#q').suggest('index.php?do=forums&sub=searchattachments&key=' + Math.random());
});
//-->
</script>

<div class="header">{#Forums_Att_SAtt#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form name="attsearch" id="attsearch" method="post" action="">
    <table width="100%" border="0" cellpadding="4" cellspacing="1" class="tableborder">
      <tr>
        <td class="firstrow">
          <input class="input" name="q" type="text" id="q" size="30" style="width: 200px" value="{$smarty.request.q}" />
          <select style="width: 100px;" name="ext" id="select">
            <option value=""></option>
            {foreach from=$possibles item=p}
              <option style="width: 100px;" value="{$p}" {if $smarty.request.ext == $p}selected="selected" {/if}>{$p}</option>
            {/foreach}
          </select>
          <select class="input" name="pp" id="pp">
            {section name=pp loop=95 step=5}
              <option value="{$smarty.section.pp.index+10}" {if $smarty.request.pp == $smarty.section.pp.index+10}selected="selected"{/if}>{$smarty.section.pp.index+10} {#Global_EachPage#}</option>
            {/section}
          </select>
          <input type="submit" class="button" value="{#Forums_Att_SAtt#}" />
        </td>
      </tr>
    </table>
  </form>
</div>
<div class="header">{#Att_Att#}</div>
<form action="" method="post" enctype="multipart/form-data" name="kform" id="kform">
  <table width="100%" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td class="headers"><a href="?do=forums&amp;sub=showattachments&amp;sort=name&amp;ext={$smarty.request.ext}&amp;pp={$smarty.request.pp}">{#Global_Name#}</a></td>
      <td align="center" class="headers"><a href="?do=forums&amp;sub=showattachments&amp;sort=hits&amp;ext={$smarty.request.ext}&amp;pp={$smarty.request.pp}">{#Global_Hits#}</a></td>
      <td align="center" class="headers">{#GlobalSize#}</td>
      <td class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
    </tr>
    {foreach from=$attachments item="sm"}
      <tr style="font-weight: normal" class="{cycle values='second,first'}">
        <td width="20%" nowrap="nowrap"><a  href="?do=forums&amp;sub=showattachments&amp;dl=1&amp;id={$sm->id}"><strong>{$sm->orig_name}</strong></a></td>
        <td align="center" nowrap="nowrap"> {$sm->hits} </td>
        <td align="center">{$sm->sizes}</td>
        <td><label><input name="del[{$sm->filename}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label></td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Forums_Att_bDel#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
{if !empty($navi)}
  <div class="navi_div"> {$navi} </div>
{/if}
