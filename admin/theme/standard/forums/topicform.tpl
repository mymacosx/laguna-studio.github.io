<div class="forum_container">
  <fieldset>
    <legend>{#GlobalTheme#}</legend>
    <input class="input" type="text" name="topic" value="{$topic|default:''|escape: 'html'|sslash}" maxlength="200" size="50" />
  </fieldset>
  <br />
  <fieldset>
    <legend>{#Forums_PostIconTitle#}</legend>
    {foreach from=$posticons item=posticon}
      {$posticon}
    {/foreach}
  </fieldset>
  <br />
</div>
