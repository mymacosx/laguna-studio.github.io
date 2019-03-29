<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#getnew').submit(function() {
        var options = {
            url: 'index.php?action=getnew&p=pwlost',
            target: '#msggetnew',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });

    $('#activate').submit(function() {
        var options = {
            url: 'index.php?action=activate&p=pwlost',
            target: '#msgactivate',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });
});
//-->
</script>

<div class="box_innerhead">{#PassLost#}</div>
<div class="box_data"> {#PassLostKodeText#}
  <br />
  <div id="msggetnew"></div>
  <form method="post" name="getnew" id="getnew">
    <fieldset>
      <legend>{#Email#}</legend>
      <input type="text" class="input" name="mail" style="width: 200px" />&nbsp;
      <input type="submit" class="button" style="width: 180px" value="{#SendKod#}" />
    </fieldset>
  </form>
</div>
<br />
<div class="box_innerhead">{#ZagolovokKod#}</div>
<div class="box_data"> {#EnterKodText#}
  <br />
  <div id="msgactivate"></div>
  <form method="post" name="activate" id="activate">
    <fieldset>
      <legend>{#Email#}</legend>
      <input type="text" class="input" name="mail" value="{$smarty.request.email|sanitize}" style="width: 200px" />
    </fieldset>
    <fieldset>
      <legend>{#PassKod#}</legend>
      <input type="text" class="input" name="pass" value="{$smarty.request.pass|sanitize}" style="width: 200px" />&nbsp;
      <input type="submit" class="button" style="width: 180px" value="{#SendPassKod#}" />
    </fieldset>
  </form>
</div>
