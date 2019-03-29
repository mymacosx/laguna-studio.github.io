{script file="$jspath/flowplayer.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
$f("player-{$video_id}", "{$baseurl}/lib/flowplayer.swf", {
    clip: { fullscreen: false, autoPlay: false,autoBuffering: true },
    play: { replayLabel: 'Старт...', label: 'Старт...', fadeSpeed: 3000 },
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
                    timeColor: '#000000', opacity: 1.0 }
            }
        });
    });
//-->
</script>

<div style="margin: 12px 0 12px 0; text-align: center">
  <a id="player-{$video_id}" href="{$baseurl}/uploads/videos/{$video_Video}" style="display: block; text-align: center; width: {$video_Breite|default:'100%'}; height: {$video_Hoehe|default:'400px'}"></a>
</div>

