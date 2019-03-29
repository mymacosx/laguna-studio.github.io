<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#slidedown_top, #shownotes').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=shownotes&type=all&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
    $('#main_notes').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=shownotes&type=main&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
    $('#pub_notes').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=shownotes&type=pub&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
   $('#addnotes').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=addnotes&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>

<div id="slidedown_top"><img class="absmiddle" src="{$imgpath}/admin_mes.png" alt="{#Content_MultiAction_Open#}" border="0" /></div>
<div id="slidedown_content">
  <div class="content">
    <div class="outdiv">
      <table width="100%" border="0" cellspacing="5" cellpadding="0">
        <tr>
          <td width="90%" valign="top"> <div style="height: 330px;overflow: auto" id="outajax"> </div> </td>
          <td width="10%" valign="top">
            <input style="width: 130px" id="shownotes" type="button" onclick="javascript: void(0);" class="button" value="{#Global_All#}" /><br /><br />
            <input style="width: 130px" id="main_notes" type="button" onclick="javascript: void(0);" class="button" value="{#NotesMain#}" /><br /><br />
            <input style="width: 130px" id="pub_notes" type="button" onclick="javascript: void(0);" class="button" value="{#NotesPub#}" /><br /><br />
            <input style="width: 130px" id="addnotes" type="button" onclick="javascript: void(0);" class="button" value="{#Global_Add#}" /><br /><br />
          </td>
        </tr>
      </table>
    </div>
    <div class="clear"></div>
  </div>
</div>
