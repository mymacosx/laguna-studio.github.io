<div class="header">{#Notes#}</div>
{foreach from=$notes item=note}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#delnotes_{$note->Id}').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=delnotes&notid={$note->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
    $('#editnotes_{$note->Id}').on('click', function() {
        var options = {
            target: '#outajax',
            url: 'index.php?do=notes&sub=editnotes&notid={$note->Id}&edit=1',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>

<div style="border: 1px solid red;padding: 3px;margin: 2px">
  <div style="border-bottom: 1px solid #ddd">
    <strong>{#Global_Date#}: </strong> {$note->Datum|date_format: $lang.DateFormat}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <strong>{#Global_Author#}: </strong> {$note->Autor}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <strong>{#Global_Type#}: </strong> {if $note->Type == 'main'}{#NotesMain#}{else}{#NotesPub#}{/if}
    {if $note->UserId == $smarty.session.benutzer_id || $smarty.session.benutzer_id == 1}
      &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a id="editnotes_{$note->Id}" href="javascript: void(0);">{#Edit#}</a>
      &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a id="delnotes_{$note->Id}" href="javascript: void(0);">{#Global_Delete#}</a>
    {/if}
  </div>
  <div>{$note->Text|nl2br}</div>
</div>
{/foreach}
