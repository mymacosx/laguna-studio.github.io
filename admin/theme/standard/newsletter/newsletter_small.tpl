{if $nl_items}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_newsletter', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_newsletter" title="{#Newsletter#}">
    <div class="boxes_body">
      <form method="post" action="index.php?p=newsletter&amp;area={$area}">
        <strong>{#Email#}</strong>
        <br />
        <input name="nl_email" type="text" class="input" style="width: 160px"/>
        {if $Nl_Count>1}
          <br />
          <strong>{#Newsletter_sections#}</strong>
          <br />
          {foreach from=$nl_items item=nli}
            <label title="{$nli->Info|sanitize}"><input type="checkbox" name="nl_welche[{$nli->Id}]" value="1" />{$nli->Name|sanitize}</label>
            <br />
          {/foreach}
          <br />
        {else}
          {foreach from=$nl_items item=nli}
            <input type="hidden" name="nl_welche[{$nli->Id}]" value="1" />
          {/foreach}
        {/if}
        <strong>{#Newsletter_format#}</strong>
        <br />
        <select name="nl_format" class="input">
          <option value="html">{#GlobalHTML#}</option>
          <option value="text">{#GlobalText#}</option>
        </select>
        <input type="submit" class="button" value="{#Newsletter_aboButton#}" style="width: 95px; margin-left: 2px" />
        <input type="hidden" name="action" value="abonew" />
      </form>
    </div>
  </div>
</div>
{/if}
