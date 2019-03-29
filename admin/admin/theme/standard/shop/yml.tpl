<div class="header">{#YaMarket#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if $aktive == 1}
  <div style="padding: 5px; margin: 5px; border: 1px solid #ccc; height: 350px; overflow: auto;"> {$output} </div>
  <br />
  <br />
  <div class="headers">{#Info#}</div>
  <table width="100%" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td class="row_left" width="300">{#Content_Link#}</td>
      <td class="row_right"><a href="{$link_yml}.gz">{$link_yml}.gz</a></td>
    </tr>
    <tr>
      <td class="row_left" width="300">{#Content_Link#}</td>
      <td class="row_right"><a class="colorbox" href="{$link_yml}">{$link_yml}</a></td>
    </tr>
  </table>
{else}
  <form method="post" action="index.php?do=shop&amp;sub=yamarket">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="second">
        <td class="row_spacer" width="100"><strong>bid: </strong></td>
        <td class="row_spacer"><input type="text" class="input" style="width: 50px" name="bid" value="" /> {#Option#}</td>
      </tr>
      <tr class="first">
        <td class="row_spacer" width="100"><strong>cbid: </strong></td>
        <td class="row_spacer"><input type="text" class="input" style="width: 50px" name="cbid" value="" /> {#Option#}</td>
      </tr>
      <tr class="second">
        <td class="row_spacer" width="100"><strong>delivery: </strong></td>
        <td class="row_spacer">
          <input type="radio" name="delivery" value="true" checked="checked" /> {#Yes#}
          <input type="radio" name="delivery" value="false" /> {#No#}
        </td>
      </tr>
      <tr class="first">
        <td class="row_spacer" width="100"><strong>local_delivery_cost: </strong></td>
        <td class="row_spacer"><input type="text" class="input" style="width: 50px" name="local_delivery_cost" value="300" /> </td>
      </tr>
    </table>
    <br />
    <input type="hidden" name="generate" value="1" />
    <input type="submit" class="button" value="{#YaMarketGen#}" />
  </form>
{/if}
