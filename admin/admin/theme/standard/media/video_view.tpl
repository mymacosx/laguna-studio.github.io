<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $f('player', '../lib/flowplayer.swf', {
	clip: {
          fullscreen: false,
          autoPlay: false,
          autoBuffering: true
        },
        play: {
          replayLabel: 'Старт...',
          label: 'Старт...',
          fadeSpeed: 3000
        },
	plugins: {
	    controls: {
                fullscreen: true,
                backgroundGradient: 'none',
                buttonColor: '#999999',
                backgroundColor: '#cccccc',
                progressColor: '#999999',
                sliderGradient: 'none',
                bufferColor: '#333333',
                borderRadius: '0',
                buttonOverColor: '#333333',
                progressGradient: 'none',
                durationColor: '#ffffff',
                sliderColor: '#333333',
                bufferGradient: 'none',
                timeColor: '#000000',
                opacity: 1.0
	    }
	}
    });
});
//-->
</script>

<h3>{$res->Name|sanitize}</h3>
<div class="popheaders">
  <span style="font-weight: normal">{#GlobalTeg#} </span>
  <input class="input" disabled="disabled" type="text" value="[VIDEO:{$res->Id}]" />
</div>
<script type="text/javascript" src="{$jspath}/flowplayer.js"></script>
<div style="margin: 12px 0 12px 0; text-align: center">
  <a id="player" href="../uploads/videos/{$res->Video}" style="display: block; text-align: center;width: {$res->Breite|default:'100%'}; height: {$res->Hoehe|default:'400px'}"></a>
</div>
<input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
