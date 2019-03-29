{foreach from=$newsitems item=n}
  <strong>{$n.Titel|escape: html}</strong>
  <br />
  <small>{$n.Zeit|date_format: $lang_settings.Zeitformat}</small>
  <p> {$n.News|truncate: 200} <br />
    <a href="index.php?area={$n.Sektion}&amp;lang={$langcode}&amp;p=news&amp;newsid={$n.Id}&amp;t={$n.LinkTitle}">{#ReadAll#}</a>
  </p>
{/foreach}
