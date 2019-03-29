<div class="box_innerhead">{#Content_passwordProtected#}</div>
<div class="box_content_login">
  <form name="form1" method="post" action="">
    <div class="h3">{#PassLogin#}</div>
    <br />
    <br />
    {#Content_LoginText#}
    <br />
    <br />
    <input class="input" type="text" name="Content_Kennwort_{$res.Id}" />&nbsp;
    <input class="button" type="submit" name="button" id="button" value="{#ButtonSend#}" />
  </form>
</div>
