{if $error}
  <div class="error_box">
    <strong>{#Newsletter_e_inf#}</strong>
    <ul>
      {foreach from=$error item=e}
        <li>{$e}</li>
        {/foreach}
    </ul>
  </div>
{/if}
{if empty($error)}
  <div class="box_data">
    <div class="h2">{#Newsletter_unsubscribe#}</div>
    <br />
    {#Newsletter_unsubscribe_ok#}
    <br />
    <br />
  </div>
{/if}
