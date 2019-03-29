<script type="text/javascript">
<!-- //
function userName(user) {
    parent.document.f.tofromname.value = user;
    closeWindow();
}
//-->
</script>

<div class="popup_header h2">{#PN_PeronalMessages#}</div>
<div class="popup_content" align="center" style="line-height: 1.5em">
  <div class="popup_box">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <fieldset class="fieldset">
            <legend><strong>{$searchuser}</strong></legend>
            <form style="display: inline" name="form1" method="post" action="?p=misc&amp;do=searchuser&amp;search=1">
              <input class="input" style="width: 250px" name="name" type="text" id="name" /> &nbsp;
              <input class="button" type="submit" name="Submit" value="{#StartSearch#}" />
            </form>
          </fieldset>
          <br />
          {if isset($userfound) && $userfound == 1}
            <fieldset class="fieldset">
              <legend><strong>{$userfound_t}</strong></legend>
              {$usererg}
            </fieldset>
          {/if}
        </td>
      </tr>
    </table>
  </div>
</div>
