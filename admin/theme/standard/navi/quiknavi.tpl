{*
<div id="quicknavi" class="quicknavi">
  <ul>
    {foreach from=$quicknavi item=quick name=qn}
    <li><a {if $quick->IsActive == 1}class="active"{/if} href="{$quick->Dokument|escape: "html"}" target="{$quick->Ziel}">{$quick->Name|sanitize}</a></li>
    {/foreach}
  </ul>
</div>
*}
<div id="quicknavimenu">
  <ul>
    {foreach from=$quicknavi item=quick name=qn}
      <li {if isset($quick->IsActive) && $quick->IsActive == 1}class="current"{/if} style="margin-left: 1px"><a href="{$quick->Dokument|escape: "html"}" target="{$quick->Ziel}">{$quick->Name|sanitize}</a></li>
    {/foreach}
  </ul>
</div>
