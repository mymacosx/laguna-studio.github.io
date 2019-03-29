<div class="box_innerhead"> &nbsp;&nbsp;{#Info#}</div>
{foreach from=$banned item=bn}
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td colspan="2" class="links_list">&nbsp;</td>
    </tr>
    {if $bn->reson}
      <tr>
        <td class="links_list" width="200"><strong>{#BanReson#}: </strong></td>
        <td class="links_list">{$bn->Reson|sanitize}</td>
      </tr>
    {/if}
    {if $bn->TimeStart}
      <tr>
        <td class="links_list" width="200"><strong>{#BanDate#}: </strong></td>
        <td class="links_list">{$bn->TimeStart|date_format: '%d-%m-%Y, %H:%M:%S'}</td>
      </tr>
    {/if}
    {if $bn->TimeEnd}
      <tr>
        <td class="links_list" width="200"><strong>{#BanDateEnd#}: </strong></td>
        <td class="links_list">{$bn->TimeEnd|date_format: '%d-%m-%Y, %H:%M:%S'}</td>
      </tr>
    {/if}
    {if $bn->Type}
      <tr>
        <td class="links_list" width="200"><strong>{#BanType#}: </strong></td>
        <td class="links_list">{if $bn->Type == 'autobann'}{#BanAuto#}{else}{#BanAdmin#}{/if}</td>
      </tr>
    {/if}
    {if $bn->Aktiv}
      <tr>
        <td class="links_list" width="200"><strong>{#BanStatus#}: </strong></td>
        <td class="links_list">{if $bn->Aktiv == 1}{#BanAkt#}{else}{#BanNoAkt#}{/if}</td>
      </tr>
    {/if}
    {if $bn->User_id}
      <tr>
        <td class="links_list" width="200"><strong>{#BanIdUser#}: </strong></td>
        <td class="links_list">{$bn->User_id}</td>
      </tr>
    {/if}
    {if $bn->Name}
      <tr>
        <td class="links_list" width="200"><strong>{#Username#}: </strong></td>
        <td class="links_list">{$bn->Name|sanitize}</td>
      </tr>
    {/if}
    {if $bn->Email}
      <tr>
        <td class="links_list" width="200"><strong>{#Email#}: </strong></td>
        <td class="links_list">{$bn->Email}</td>
      </tr>
    {/if}
    {if $bn->Ip}
      <tr>
        <td class="links_list" width="200"><strong>{#BanIp#}: </strong></td>
        <td class="links_list">{$bn->Ip}</td>
      </tr>
    {/if}
  </table>
{/foreach}
