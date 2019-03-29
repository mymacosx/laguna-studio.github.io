<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var id = '#{$types}notes';
    $(id).submit(function() {
        var options = {
            target: '#outajax',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });
});
//-->
</script>

<div class="header">{#Notes#}</div>
{if $types == 'add'}
  <form method="post" action="index.php?do=notes&sub=addnotes" id="addnotes">
    <textarea cols="" rows="" style="width: 99%; height: 200px; margin-bottom: 5px" name="text_notes"></textarea>
    <br />
    <strong>{#Global_Type#}: </strong>
    <input type="radio" name="type" value="main" checked="checked" />{#NotesMain#}
    <input type="radio" name="type" value="pub" />{#NotesPub#}
    <br />
    <br />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
{/if}
{if $types == 'edit'}
  <form method="post" action="index.php?do=notes&sub=editnotes" id="editnotes">
    <input name="save" type="hidden" id="save" value="1" />
    <textarea cols="" rows="" style="width: 99%; height: 200px; margin-bottom: 5px" name="text_notes">{$enotes->Text|sanitize}</textarea>
    <br />
    <strong>{#Global_Type#}: </strong>
    <input type="radio" name="type" value="main" {if $enotes->Type == 'main'}checked="checked"{/if} />{#NotesMain#}
    <input type="radio" name="type" value="pub" {if $enotes->Type == 'pub'}checked="checked"{/if} />{#NotesPub#}
    <br />
    <br />
    <input type="hidden" name="notid" value="{$enotes->Id}" />
    <input type="hidden" name="edit" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
{/if}
