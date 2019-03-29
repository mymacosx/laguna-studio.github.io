{script file="$jspath/jcarousel.js" position='head'}
<script type="text/javascript">
<!-- //
function loadCallback(carousel) {
    if (!carousel.has(carousel.first, carousel.last)) {
        $.get('index.php?p=user&action=gallery&do=ajax&aj=1&id={$smarty.request.id}&width=80&key=' + Math.random(), {
            first: carousel.first, last: carousel.last
        },
        function(xml) {
            addCallback(carousel, carousel.first, carousel.last, xml);
        }, 'xml');
    }
}
function addCallback(carousel, first, last, xml) {
    carousel.size(parseInt($('total', xml).text()));
    $('image', xml).each(function(i) {
        carousel.add(first + i, $(this).text());
    });
}
function getIMG(id) {
    $.get('index.php?p=user&action=gallery&do=ajax_img&img=1&aj=1&id=' + id + '&key=' + Math.random(), function(data) {
        document.getElementById('gallery_image_thumb').src = data;
    });
}
function initImage() {
    image = document.getElementById('gallery_image_thumb');
    image.style.visibility = 'visible';
}
window.onload = function() {
    initImage();
};
$(document).ready(function() {
    $('#mycarousel').jcarousel({
        itemLoadCallback: loadCallback,
        start: 1
    });
});
//-->
</script>

<div align="center"><h4>{$item->Name|sanitize}</h4></div>
<div align="center">
  <div><img id="gallery_image_thumb" src="{$item->Link}" alt="{$item->Name|sanitize}" vspace="10" border="10" /></div>
  <div class="jcarousel_div">
    <div id="mycarousel" class="jcarousel-slider"><ul> </ul></div>
  </div>
  <br />
  <input type="button" class="button" onclick="self.close();" value="{#WinClose#}" />
</div>
