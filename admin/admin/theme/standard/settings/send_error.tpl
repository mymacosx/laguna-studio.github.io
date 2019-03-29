<form method="post" action="{$flink}" onsubmit="closeWindow();">
  <div class="header">{#SendError#}</div>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td>
        <fieldset>
          <legend><label for="error">{#TextError#}</label></legend>
          <textarea name="error" cols="" rows="3" readonly="readonly" style="width: 60%">{$items->Aktion|escape: 'html'}</textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="agent">{#AgentError#}</label></legend>
          <textarea name="agent" cols="" rows="2" readonly="readonly" style="width: 60%">{$items->Agent|escape: 'html'}</textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="text">{#ContactForms_ndef#}</label></legend>
          <textarea name="text" cols="" rows="4" style="width: 60%"></textarea>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="url">{#SendUrl#}</label></legend>
          <input name="url" type="text" class="input" style="width: 400px" value="{$baseurl}" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="name">{#SendName#}</label></legend>
          <input name="name" type="text" class="input" style="width: 400px" value="{$settings.Mail_Name}" />
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend><label for="mail">{#SendMail#}</label></legend>
          <input name="mail" type="text" class="input" style="width: 400px" value="{$settings.Mail_Absender}" />
        </fieldset>
      </td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Go_Button#}" />&nbsp;&nbsp;
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  <input type="hidden" name="id" value="{$items->Id}" />
  <input type="hidden" name="url2" value="{$baseurl}" />
  <input type="hidden" name="name2" value="{$settings.Mail_Name}" />
  <input type="hidden" name="mail2" value="{$settings.Mail_Absender}" />
</form>
