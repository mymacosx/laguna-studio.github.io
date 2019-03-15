// Custome theme code

if ($('.clean-gallery').length > 0) {
   baguetteBox.run('.clean-gallery', { animation: 'slideIn'});
}

if ($('.clean-product').length > 0) {
    $(window).on("load",function() {
        $('.sp-wrap').smoothproducts();
    });
}

$(document).ready(function(){
  $('.slider').bxSlider({
        maxSlides: 1,
        slideWidth: 420,
        //mode: 'fade',
        captions: true,
        touchEnabled: true,
        infiniteLoop: false,
        hideControlOnEnd: false,
        pager: false
  });
});

$(document).ready(function() {
    $('.rs-slider').rs-slider();
});
$(document).ready(function() {
    $('.pgwSlider').pgwSlider();
});


