{if $not_logged == 1}
  <p> {#NotLoggedInPass#} </p>
{else}
  <div class="box_innerhead">{#ChangePassTitle#}</div>
  <div class="infobox"> {#ChangePass_Inf#} </div>
  {if $register_ok == 1}
    <p> {#ChangePass_Ok#} </p>
  {else}
    {if $error}
      <div class="error_box">
        <div class="h3">{#Error#}</div>
        <ul>
          {foreach from=$error item=reg_error}
            <li>{$reg_error}</li>
            {/foreach}
        </ul>
      </div>
    {/if}
    <form method="post" action="{page_link}">
      <table width="100%" cellspacing="0" cellpadding="3">
        <tr>
          <td width="200" class="row_first"><label for="l_oldpass">{#ChangePass_Ap#}&nbsp;</label></td>
          <td class="row_second"><input class="input" name="oldpass" type="password" id="l_oldpass" value="{$smarty.post.oldpass|sanitize}" /></td>
        </tr>
        <tr>
          <td width="200" class="row_first"><label for="l_newpass">{#ChangePass_Np#}&nbsp;</label></td>
          <td class="row_second"><input class="input" name="newpass" type="password" id="l_newpass" value="{$smarty.post.newpass|sanitize}" /></td>
        </tr>
        <tr>
          <td class="row_first"><label for="l_newpass2">{#ChangePass_Np2#}&nbsp;</label></td>
          <td class="row_second"><input class="input" name="newpass2" type="password" id="l_newpass2" value="{$smarty.post.newpass2|sanitize}" /></td>
        </tr>
      </table>
      <p>
        <input type="hidden" name="send" value="1" />
        <input type="submit" class="button" value="{#LoginExternCp#}" />
      </p>
    </form>
  {/if}
{/if}
