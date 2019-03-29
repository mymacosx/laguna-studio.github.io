<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>{$pagetitle}</title>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta name="keywords" content="{$keywords}" />
<meta name="description" content="{$description}" />
<meta name="robots" content="{$robots}" />
<meta name="publisher" content="{$settings.Seitenbetreiber}" />
<meta name="generator" content="{#meta_generator#}" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
{if $settings.meta_google == 1 && !empty($settings.code_google)}
<meta name="google-site-verification" content="{$settings.code_google}" />
{/if}
{if $settings.meta_yandex == 1 && !empty($settings.code_yandex)}
<meta name="yandex-verification" content="{$settings.code_yandex}" />
{/if}
<link rel="shortcut icon" href="{$baseurl}/favicon.ico" />
{if !empty($canonical)}
<link rel="canonical" href="{$baseurl}/{$canonical}" />
{/if}
<link type="application/atom+xml" rel="alternate" title="{$settings.Seitenname|sanitize}" href="{$baseurl}/index.php?p=rss&amp;area={$area}" />
{if get_active('News')}
<link type="application/atom+xml" rel="alternate" title="{#Newsarchive#}" href="{$baseurl}/index.php?p=rss&amp;area={$area}&amp;action=news" />
{/if}
{if get_active('articles')}
<link type="application/atom+xml" rel="alternate" title="{#Gaming_articles#}" href="{$baseurl}/index.php?p=rss&amp;area={$area}&amp;action=articles" />
{/if}
{if get_active('forums')}
<link type="application/atom+xml" rel="alternate" title="{#Forums_Title#}" href="{$baseurl}/index.php?p=rss&amp;area={$area}&amp;action=forum" />
{/if}
{style file="$csspath/main.css" position='head' priority='1000'}
{style file="$csspath/navi.css" position='head' priority='1000'}
{if get_active('shop')}
{style file="$csspath/shop.css" position='head' priority='1000'}
{/if}
{if get_active('forums')}
{style file="$csspath/forum.css" position='head' priority='1000'}
{/if}
{if $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
{style file="$csspath/ie.css" position='head' priority='1000'}
{/if}
{style file="$csspath/colorbox.css" position='head' priority='800'}

{result type='style' format='file' position='head'} {* вывод файлов стилей *}
{result type='style' format='code' position='head'} {* вывод кода стилей *}
