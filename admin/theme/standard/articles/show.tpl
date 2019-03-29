{script file="$jspath/jrating.js" position='head'}
<div class="box_innerhead">
  <h2>{$row.Titel|sanitize}</h2>
  <!-- {#Gaming_articles#} ({$row.TypName}) -->
</div>
<div class="news_content">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        {if $row.Intro}
          <div class="news_intro">{$row.Intro}</div>
        {/if}
        <div style="text-align: justify"> {$row.News} </div>
        <br />
        <table cellpadding="2" cellspacing="0">
          <tr>
            <td>{#GlobalAutor#}: <a href="index.php?p=user&amp;id={$row.Autor}&amp;area={$area}">{$row.User}</a> {#Global_artdate#} {$row.ZeitStart|date_format: $lang.DateFormatSimple}</td>
          </tr>
          {if $row.Bewertung == 1}
            <tr>
              <td>
                <table cellspacing="0" cellpadding="0">
                  <tr>
                    <td>{#Rating_Rating#}: &nbsp;</td>
                    <td>
                      <input name="starrate_res" type="radio" value="1" class="star" disabled="disabled" {if $row.Wertung == 1}checked="checked"{/if} />
                      <input name="starrate_res" type="radio" value="2" class="star" disabled="disabled" {if $row.Wertung == 2}checked="checked"{/if} />
                      <input name="starrate_res" type="radio" value="3" class="star" disabled="disabled" {if $row.Wertung == 3}checked="checked"{/if} />
                      <input name="starrate_res" type="radio" value="4" class="star" disabled="disabled" {if $row.Wertung == 4}checked="checked"{/if} />
                      <input name="starrate_res" type="radio" value="5" class="star" disabled="disabled" {if $row.Wertung == 5}checked="checked"{/if} />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          {/if}
        </table>
        <br />
        <br style="clear: both" />
        <br />
        {if !empty($article_pages)}
          {$article_pages}
        {/if}
        <br />
        <br />
      </td>
      {if $row.Typ == 'special' && empty($row.Bild) && empty($IncludedGalleries)}
        <td width="15" valign="top">&nbsp;</td>
      {else}
        <td width="25" valign="top">&nbsp;&nbsp;</td>
        <td width="{if $row.Typ == 'review' || $row.Typ == 'preview'}200{else}150{/if}" valign="top">
          {if !empty($row.Bild) && $smarty.request.artpage<2}
            <div style="text-align: center"><img src="uploads/articles/{$row.Bild}" alt="" /></div>
            {/if}
            {if $row.Typ == 'review' || $row.Typ == 'preview'}
            <div class="download_link_infbox">
              <table width="100%" cellspacing="0" cellpadding="2">
                {if $row.ManLink}
                  <tr>
                    <td width="70">{#Manufacturer#}: </td>
                    <td width="92" align="right">{$row.ManLink}</td>
                  </tr>
                {/if}
                {if $row.PubLink}
                  <tr>
                    <td>{#Products_publisher#}: </td>
                    <td width="100" align="right">{$row.PubLink}</td>
                  </tr>
                {/if}
                {if !empty($row.Preis)}
                  <tr>
                    <td>{#Products_price#}: </td>
                    <td width="100" align="right">{$row.Preis|sanitize}</td>
                  </tr>
                {/if}
                {if !empty($row.Veroeffentlichung)}
                  <tr>
                    <td>{#Gaming_articles_release#}: </td>
                    <td align="right">{$row.Veroeffentlichung|sanitize}</td>
                  </tr>
                {/if}
                {if !empty($row.Genre)}
                  <tr>
                    <td>{#Global_Categ#}: </td>
                    <td width="100" align="right">{$row.Genre}</td>
                  </tr>
                {/if}
                {if !empty($row.Plattform)}
                  <tr>
                    <td>{#Gaming_cheats_plattform#}: </td>
                    <td width="100" align="right">{$row.Plattform}</td>
                  </tr>
                {/if}
                {if !empty($row.Shop) && empty($row.ShopArtikel)}
                  <tr>
                    <td>{#Products_buyat#}: </td>
                    <td width="100" align="right"><a class="stip" title="{$row.Shop|tooltip}" target="_blank" href="{$row.Shop|sanitize}">{#Gaming_articles_buyatName#}</a></td>
                  </tr>
                {/if}
                {if !empty($row.ShopArtikel)}
                  <tr>
                    <td>{#Products_buyat#}: </td>
                    <td width="100" align="right">{$row.ShopArtikel}</td>
                  </tr>
                {/if}
                {if $LinksExtern}
                  <tr>
                    <td valign="top">{#Links#}: </td>
                    <td width="100" align="right" valign="top" nowrap="nowrap">
                      {foreach from=$LinksExtern item=a}
                        <a href="{$a->Link}" target="_blank">{$a->Name}</a>
                        <br />
                      {/foreach}
                    </td>
                  </tr>
                {/if}
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </table>
            </div>
            {if $DataVal && $row.Typ == 'review'}
              <div class="download_link_infbox">
                {foreach from=$DataVal item=dv}
                  <table width="100%" cellpadding="0" cellspacing="1">
                    <tr>
                      <td width="95">{$dv->Name}: </td>
                      <td align="right" nowrap="nowrap">
                        <input name="starrate_x_{$dv->Id}" type="radio" value="" class="star" disabled="disabled" {if $dv->Wert == 1}checked="checked"{/if} />
                        <input name="starrate_x_{$dv->Id}" type="radio" value="" class="star" disabled="disabled" {if $dv->Wert == 2}checked="checked"{/if} />
                        <input name="starrate_x_{$dv->Id}" type="radio" value="" class="star" disabled="disabled" {if $dv->Wert == 3}checked="checked"{/if} />
                        <input name="starrate_x_{$dv->Id}" type="radio" value="" class="star" disabled="disabled" {if $dv->Wert == 4}checked="checked"{/if} />
                        <input name="starrate_x_{$dv->Id}" type="radio" value="" class="star" disabled="disabled" {if $dv->Wert == 5}checked="checked"{/if} />
                      </td>
                    </tr>
                  {/foreach}
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><strong>{#Rating_Rating#}: </strong></td>
                    <td width="92" align="right" nowrap="nowrap"><h3>{$Ges|numf} %</h3></td>
                  </tr>
                </table>
              </div>
            {/if}
            {if !empty($row.Top)}
              <div class="round" style="margin: 10px 0 10px 0">
                <div class="tops">
                  <div class="h3">{#Gaming_articles_top#}</div>
                  <ul>
                    {foreach from=$row.Top item=top}
                      <li>{$top|sanitize}</li>
                      {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            {if !empty($row.Flop)}
              <div class="round" style="margin: 10px 0 10px 0">
                <div class="flops">
                  <div class="h3">{#Gaming_articles_flop#}</div>
                  <ul>
                    {foreach from=$row.Flop item=Flop}
                      <li>{$Flop|sanitize}</li>
                      {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            {if $row.Minimum || $row.Optimum}
              <div class="round" style="margin: 10px 0 10px 0">
                <div class="system">
                  {if $row.Minimum}
                    <div class="h3">{#Gaming_articles_sysmin#}</div>
                    <ul>
                      {foreach from=$row.Minimum item=minsys}
                        <li>{$minsys|sanitize}</li>
                        {/foreach}
                    </ul>
                  {/if}
                  {if $row.Optimum}
                    <div class="h3">{#Gaming_articles_sysopt#}</div>
                    <ul>
                      {foreach from=$row.Optimum item=optsys}
                        <li>{$optsys|sanitize}</li>
                        {/foreach}
                    </ul>
                  {/if}
                </div>
              </div>
            {/if}
          {/if}
          {$IncludedGalleries|default:''}
        </td>
      {/if}
    </tr>
  </table>
    {if $row.Bewertung == 1}
      {$RatingForm|default:''}
    {/if}
    {$IncludedArticles|default:''}
    {$IncludedNews|default:''}
    {$IncludedContent|default:''}
    {$GetComments|default:''}
    <br />
</div>
