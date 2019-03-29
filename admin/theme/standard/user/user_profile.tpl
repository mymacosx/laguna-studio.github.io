<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "550px", width: "550px", iframe: true });
});
//-->
</script>

{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="round">
  <div class="box_innerhead_userprofile">{#MyAccount#} {$user.Benutzername|sanitize}</div>
</div>
{if $user.Profil_public == 1 || $smarty.session.benutzer_id == $smarty.request.id}
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="75%" valign="top">
        <div class="box_innerhead">{#Profile_GenInfo#}</div>
        <div class="infobox">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td width="35%" align="right" class="row_first">{#Autoritets#}: &nbsp;</td>
              <td class="row_second">
                <div style="width: 100%;border: 1px solid #665e58">
                  <div style="background-color: #F00;text-align: center;width: {$autoritet_bar}%"><strong>{$autoritet}%</strong></div>
                </div>
              </td>
            </tr>
            <tr>
              <td width="35%" align="right" class="row_first">{#Profile_Regged#}: &nbsp;</td>
              <td class="row_second">{$user.Regdatum|date_format: $lang.DateFormatExtended}</td>
            </tr>
            <tr>
              <td width="35%" align="right" class="row_first">{#Profile_CountProfile#}: &nbsp;</td>
              <td class="row_second">{$user.Profil_Hits}</td>
            </tr>
            {if $user.Rang}
              <tr>
                <td width="35%" align="right" class="row_first">{#Profile_Rank#}: &nbsp;</td>
                <td class="row_second">
                  {if $user.Team == 1}
                    {$user.TeamName|sanitize}
                  {else}
                    {$user.Rang|sanitize}
                  {/if}
                </td>
              </tr>
            {/if}
            <tr>
              <td width="35%" align="right" class="row_first">{#Profile_LastAction#}: &nbsp;</td>
              <td class="row_second">{if $user.Zuletzt_Aktiv > 1}{$user.Zuletzt_Aktiv|date_format: $lang.DateFormatExtended}{else}-{/if}</td>
            </tr>
            <tr>
              <td width="35%" align="right" class="row_first">{#Forums_Postings#}: &nbsp;</td>
              <td class="row_second">{if $user.Beitraege >= 1}<a href="index.php?p=forum&amp;action=print&amp;what=posting&amp;id={$user.Id}">{$user.Beitraege}</a>{else}-{/if}</td>
            </tr>
            {if isset($user_thanks.num_post) && $user_thanks.num_post >= 1}
              <tr>
                <td width="35%" align="right" class="row_first">{#ThanksMes#}: &nbsp;</td>
                <td class="row_second">{$user_thanks.num_post}</td>
              </tr>
              <tr>
                <td width="35%" align="right" class="row_first">{#ThanksAll#}: &nbsp;</td>
                <td class="row_second">{$user_thanks.num_thanks}</td>
              </tr>
              <tr>
                <td width="35%" align="right" class="row_first">{#ThanksUser#}: &nbsp;</td>
                <td class="row_second">{$user_thanks.num_user}</td>
              </tr>
            {/if}
          </table>
        </div>
        {if !empty($user)}
          <div class="box_innerhead">{#PersonalData#}</div>
          <div class="infobox">
            <table width="100%" cellpadding="0" cellspacing="0">
              {if !empty($user.Status)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Status#}: &nbsp;</td>
                  <td class="row_second">{$user.Status|sanitize}</td>
                </tr>
              {/if}
              {if $user.Geburtstag_public == 1 && $user.Geburtstag > 1}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Birth#}: &nbsp;</td>
                  <td class="row_second">{$user.Geburtstag|sanitize}</td>
                </tr>
              {/if}
              {if $user.Geschlecht != '-'}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Profile_Gender#}: &nbsp;</td>
                  <td class="row_second">
                    {if $user.Geschlecht == 'm'}
                      {#User_Male#}
                    {elseif $user.Geschlecht == 'f'}
                      {#User_Female#}
                    {else}
                      {#User_NoSettings#}
                    {/if}
                  </td>
                </tr>
              {/if}
              {if !empty($user.Ort) && $user.Ort_Public == 1}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Town#}: &nbsp;</td>
                  <td class="row_second"> {$user.Ort|sanitize} / <a href="http://maps.google.ru/maps?f=q&hl={$user.LandCode|sanitize}&q={$user.Postleitzahl|sanitize}+{$user.Ort|urlencode|sanitize}" target="_blank" rel="nofollow">{#Profile_ShowGoogleMaps#}</a></td>
                </tr>
              {/if}
              {if !empty($user.Beruf)}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Profile_Job#}: &nbsp;</td>
                  <td class="row_second">{$user.Beruf|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Interessen)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Int#}: &nbsp;</td>
                  <td class="row_second">{$user.Interessen|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Hobbys)}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Profile_Hobbys#}: &nbsp;</td>
                  <td class="row_second">{$user.Hobbys|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Essen)}
                <tr>
                  <td width="35%" align="right" class="row_first">{#Profile_Food#}: &nbsp;</td>
                  <td class="row_second">{$user.Essen|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Musik)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Music#}: &nbsp;</td>
                  <td class="row_second">{$user.Musik|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Films)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Films#}: &nbsp;</td>
                  <td class="row_second">{$user.Films|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Tele)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Tele#}: &nbsp;</td>
                  <td class="row_second">{$user.Tele|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Book)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Book#}: &nbsp;</td>
                  <td class="row_second">{$user.Book|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Game)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Game#}: &nbsp;</td>
                  <td class="row_second">{$user.Game|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Citat)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Citat#}: &nbsp;</td>
                  <td class="row_second">{$user.Citat|sanitize|nl2br}</td>
                </tr>
              {/if}
              {if !empty($user.Other)}
                <tr>
                  <td align="right" class="row_first">{#Profile_Other#}: &nbsp;</td>
                  <td class="row_second">{$user.Other|sanitize|nl2br}</td>
                </tr>
              {/if}
            </table>
          </div>
        {/if}
        {$user_activity}
        {$user_friends}
        {$user_visits}
        {$user_gallery}
        {$user_gallery_profile}
        {include file="$incpath/user/user_profile_guestbook.tpl"}
      </td>
      <td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td width="25%" valign="top">
        <div class="box_innerhead">{#Forums_avatar#}</div>
        <div class="infobox">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" class="row_second">{$user.Avatar}</td>
            </tr>
          </table>
        </div>
        <div class="box_innerhead">{#Imprint#}</div>
        <div class="infobox">
          <table width="100%" cellpadding="0" cellspacing="0">
            {if !empty($user.Webseite)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Web#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Webseite|sanitize}" target="_blank"> <img src="{$imgpath_forums}home.png" border="" alt="{$user.Webseite|sanitize}" /></a></td>
              </tr>
            {/if}
            {if $user.Email_User}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Profile_EmailContact#}&nbsp;</td>
                <td class="row_second">{$user.Email_User}&nbsp;</td>
              </tr>
            {/if}
            {if $user.Pn_User}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Profile_SendPN#}&nbsp;</td>
                <td class="row_second">{$user.Pn_User}</td>
              </tr>
            {/if}
            {if $user.Icq_User}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Profile_ICQ#}&nbsp;</td>
                <td class="row_second">{$user.Icq_User}</td>
              </tr>
            {/if}
            {if $user.Skype_User}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Profile_ScypeOpt#}&nbsp;</td>
                <td class="row_second">{$user.Skype_User}</td>
              </tr>
            {/if}
            {if !empty($user.msn)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Profile_MSN#}&nbsp;</td>
                <td class="row_second">{$user.msn|sanitize}</td>
              </tr>
            {/if}
            {if !empty($user.Vkontakte)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Vkontakte#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Vkontakte|sanitize}" target="_blank"> <img src="{$imgpath_forums}vkontakte.png" border="" alt="{$user.Vkontakte|sanitize}" /></a></td>
              </tr>
            {/if}
            {if !empty($user.Odnoklassniki)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Odnoklassniki#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Odnoklassniki|sanitize}" target="_blank"> <img src="{$imgpath_forums}odnoklassniki.png" border="" alt="{$user.Odnoklassniki|sanitize}" /></a></td>
              </tr>
            {/if}
            {if !empty($user.Mymail)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Mymail#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Mymail|sanitize}" target="_blank"> <img src="{$imgpath_forums}mymail.png" border="" alt="{$user.Mymail|sanitize}" /></a></td>
              </tr>
            {/if}
            {if !empty($user.Google)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Google#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Google|sanitize}" target="_blank"> <img src="{$imgpath_forums}google.png" border="" alt="{$user.Google|sanitize}" /></a></td>
              </tr>
            {/if}
            {if !empty($user.Facebook)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Facebook#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Facebook|sanitize}" target="_blank"> <img src="{$imgpath_forums}facebook.png" border="" alt="{$user.Facebook|sanitize}" /></a></td>
              </tr>
            {/if}
            {if !empty($user.Twitter)}
              {assign var=empty_profil value=1}
              <tr>
                <td width="60%" align="right" class="row_first">{#Twitter#}</td>
                <td class="row_second"><a rel="nofollow" href="{$user.Twitter|sanitize}" target="_blank"> <img src="{$imgpath_forums}twitter.png" border="" alt="{$user.Twitter|sanitize}" /></a></td>
              </tr>
            {/if}
            {if empty($empty_profil)}
              <tr>
                <td colspan="2" class="row_first">{#ProfilContactEmpty#}</a></td>
              </tr>
            {/if}
          </table>
        </div>
        {if get_active('user_videos') && $user_videos} <a name="uservideos"></a>
          <div class="box_innerhead">{#Forums_UserVideos#} {$user.Benutzername|sanitize}</div>
          <div class="infobox" style="text-align: center">
            {foreach from=$user_videos item=u}
              {if $u->Name}
                <div style="margin-top: 5px">
                  <h3>{$u->Name}</h3>
                </div>
              {/if}
              {$u->VideoData}
              <br />
              <br />
            {/foreach}
          </div>
        {/if}
      </td>
    </tr>
  </table>
{else}
  <br />
  <div class="h3">{#Profile_NotPublicThis#}</div>
  <br />
{/if}
