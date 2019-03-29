<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#container-options').tabs({
      selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
});
//-->
</script>

<br />
<div id="container-options">
  <ul>
    <li><a href="#opt-1"><span>{#Global_Overview#}</span></a></li>
    <li><a href="#opt-2"><span>{#Newsletter_SourceNL#}</span></a></li>
    <li><a href="#opt-3"><span>{#Newsletter_Source#}</span></a></li>
    <li><a href="#opt-4"><span>{#Info#}</span></a></li>
  </ul>
  <div id="opt-1">
    <iframe frameborder="0" width="100%" height="500" style="width: 99%; height: 500px" src="index.php?do=newsletter&amp;sub=view&amp;id={$smarty.request.id}&amp;noframes=1&amp;noout=1"></iframe>
  </div>
  <div id="opt-2">
    <div class="subheaders">{#Newsletter_SourceNLInf#}</div>
    {$HtmlV}
  </div>
  <div id="opt-3">
    <div class="subheaders">{#Newsletter_SourceInf#}</div>
    <textarea cols="" rows="" name="x" style="width: 99%; height: 500px">{$res->Newsletter|sanitize}</textarea>
  </div>
  <div id="opt-4">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="150"><strong>{if $res->Typ == 'groups'}{#Newsletter_Inf_groups#}{else}{#Newsletter_Inf_abos#}{/if}</strong></td>
        <td>
          {foreach from=$names item=n name=x}
            {$n}{if !$smarty.foreach.x.last}, {/if}
          {/foreach}
        </td>
      </tr>
      <tr>
        <td><strong>{#Global_Date#}</strong></td>
        <td>{$res->Datum|date_format: $lang.DateFormat}</td>
      </tr>
      <tr>
        <td><strong>{#Att_Att#}</strong></td>
        <td>
          {if $att}
            {foreach from=$att item=a name=x}
              <a href="index.php?do=newsletter&amp;sub=getattachment&amp;att={$a}">{$a}</a>{if !$smarty.foreach.x.last}, {/if}
            {/foreach}
          {/if}
        </td>
      </tr>
    </table>
  </div>
</div>
<input type="button" class="button" value="{#Close#}" onclick="parent.location.href='index.php?do=newsletter&amp;sub=archive&amp;sys={$smarty.request.sys}';" />
