{if !empty($SiteNavigation)}
  <div class="page_navibox">
    <div class="page_navibox_header">{$navi_title|default:$lang.Title_Navi}</div>
    <ul>
      {foreach from=$SiteNavigation item=navi}
        <li><a title="{$navi->AltTitle|sanitize}" target="{$navi->target|default:'_self'}" href="{$navi->document|escape: "html"}" class="{if $navi->document == $document || !empty($navi->active)}navi_first_active{else}navi_first{/if}">{$navi->title|sanitize}</a>
          {if empty($navi->sub_navi) || !count($navi->sub_navi)}</li>{/if}
          {if !empty($navi->sub_navi) && count($navi->sub_navi)}
          <ul>
            {foreach from=$navi->sub_navi item=sub_navi}
              <li><a title="{$sub_navi->AltTitle|sanitize}" target="{$sub_navi->target|default:'_self'}" href="{$sub_navi->document|escape: "html"}" class="{if $sub_navi->document == $document || !empty($sub_navi->active)}navi_second_active{else}navi_second{/if}">{$sub_navi->title|sanitize}</a>
                {if empty($sub_navi->sub_navi) || !count($sub_navi->sub_navi)}</li>{/if}
                {if !empty($sub_navi->sub_navi) && count($sub_navi->sub_navi)}
                <ul>
                  {foreach from=$sub_navi->sub_navi item=sub_sub_navi}
                    <li><a title="{$sub_sub_navi->AltTitle|sanitize}" target="{$sub_sub_navi->target|default:'_self'}" href="{$sub_sub_navi->document|escape: "html"}" class="{if $sub_sub_navi->document == $document || !empty($sub_sub_navi->active)}navi_third_active{else}navi_third{/if}">{$sub_sub_navi->title|sanitize}</a></li>
                    {/foreach}
                </ul>
                </li>
              {/if}
            {/foreach}
          </ul>
          </li>
        {/if}
      {/foreach}
    </ul>
  </div>
{/if}
