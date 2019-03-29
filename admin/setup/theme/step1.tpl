<div class="content">
  <div class="headers">{#Step1#}</div>
  <form autocomplete="off" name="Form" id="Form"  method="post" action="{$setupdir}/setup.php">
    <div class="box">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td width="340"><span><strong>{#Load_php#}</strong></span></td>
          <td>
            {if $params.php == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
              <span style="font-style: italic;font-weight: bold;color: red"> {#SetSetup#} </span>
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} mbstring</strong></span></td>
          <td>
            {if $params.mbstring == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
              <span style="font-style: italic;font-weight: bold;color: red"> {#SetSetup#} </span>
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} spl</strong></span></td>
          <td>
            {if $params.spl == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
              <span style="font-style: italic;font-weight: bold;color: red"> {#SetSetup#} </span>
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} mysqli</strong></span></td>
          <td>
            {if $params.mysqli == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
              <span style="font-style: italic;font-weight: bold;color: red"> {#SetSetup#} </span>
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} iconv</strong></span></td>
          <td>
            {if $params.iconv == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} gd</strong></span></td>
          <td>
            {if $params.gd == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Ext#} zlib</strong></span></td>
          <td>
            {if $params.zlib == 1}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Param#} safe_mode</strong></span></td>
          <td>
            {if $params.safemode == 0}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Param#} magic_quotes_gpc</strong></span></td>
          <td>
            {if $params.magic_quotes_gpc == 0}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Param#} magic_quotes_runtime</strong></span></td>
          <td>
            {if $params.magic_quotes_runtime == 0}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Load_Param#} magic_quotes_sybase</strong></span></td>
          <td>
            {if $params.magic_quotes_sybase == 0}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
        <tr>
          <td width="340"><span><strong>{#Mem_limit#}</strong></span></td>
          <td>
            {if $params.memory_limit > 16}
              <img class="stip" title="{$lang.Test_Ok}" style="vertical-align: middle" src="{$setupdir}/images/ok.png" alt="" />
            {else}
              <img class="stip" title="{$lang.Test_Fail}" style="vertical-align: middle" src="{$setupdir}/images/fail.png" alt="" />
            {/if}
          </td>
        </tr>
      </table>
    </div>
    <div class="button_steps">
      {if $params.php == 1 && $params.mbstring == 1 && $params.spl == 1 && $params.mysqli == 1}
        <input type="hidden" name="step" value="2" />
        <input type="submit" value="{#Step_Button#}" />
      {else}
        <h2><span style="font-weight: bold;color: #fff">{#ErrorSetup#}</span></h2>
        {/if}
    </div>
  </form>
</div>
