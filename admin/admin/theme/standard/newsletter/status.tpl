{if isset($ok) && $ok == 1}
  <div align="center">
    <br />
    <br />
    <br />
    <br />
    <h3>{#NewsletterJobOk#}</h3>
    <br />
    <br />
    <input type="button" class="button" onclick="closeWindow();" value="{#Close#}" />
  </div>
{else}
  {if $done < 100}
    <div class="subheaders"><strong>{#Newsletter_Sending#}</strong></div>
    <table width="100%" border="0" cellpadding="4" cellspacing="1" class="tableborder">
      <tr>
        <td style="padding: 10px">
          <div class="nl_statusbar1">
            <div class="nl_statusbar2" style="width: {$done_nl}%">{$done_nl}%</div>
          </div>
        </td>
      </tr>
    </table>
    {$forms}
    {$jsdata}
  {else}
    <table width="100%" border="0" cellpadding="4" cellspacing="1" class="tableborder">
      <tr>
        <td style="padding: 10px">
          <div align="center">
            {if isset($not_send) && $not_send == 1}
              <h3>{#Newsletter_notSend#}</h3>
              <br />
              <br />
              <input type="button" class="button" onclick="location.href = 'index.php?do=newsletter&sub=new&to={$smarty.request.to}&area={$smarty.request.area}&noframes=1';" value="{#GoBack#}" />
            {else}
              <strong><font size="+1">{#Newsletter_SendOk#}</font></strong>
              <br />
              <br />
              <input type="button" class="button" onclick="closeWindow();" value="{#Close#}" />
            </div>
          {/if}
        </td>
      </tr>
    </table>
  {/if}
{/if}
