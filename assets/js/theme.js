/*$( document ).ready(function( $ ) {
		$( '#slide-1' ).sliderPro({
            width: 400,
			height: 400,
			orientation: 'horizontal',
			loop: false,
            fade: true,
			arrows: true,
			buttons: false,
            startSlide: 0,
            imageScaleMode: 'contain',
            //autoScaleLayers: false
			thumbnailsPosition: 'left',
			thumbnailPointer: false,
            //aspectRatio: 1,
			//visibleSize: 100,
			//forceSize: 'fullWidth'
            //fullScreen: true,
            //fadeFullScreen: true,
            //shuffle: false,
            //smallSize: 500,
			//mediumSize: 800,
			//largeSize: 1200,
			thumbnailArrows: false,
			autoplay: false,
			thumbnailWidth: 60,
            thumbnailHeight: 60,
			breakpoints: {
				800: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
                    //fullScreen: true,
                    imageScaleMode: 'contain',
					thumbnailWidth: 50,
					thumbnailHeight: 50
				},
				500: {
                    width: 300,
			        height: 300,
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
                    fade: false,
                    //fullScreen: true,
                    imageScaleMode: 'cover',
					thumbnailWidth: 50,
					thumbnailHeight: 50
				},
                320: {
                    width: 300,
			        height: 300,
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
                    fade: false,
                    //fullScreen: true,
                    imageScaleMode: 'cover',
					thumbnailWidth: 50,
					thumbnailHeight: 50
				}
			}
		});
	});*/
$( document ).ready(function( $ ) {
		$( '#slide-1' ).sliderPro({
			width: 400,
			height: 400,
			orientation: 'vertical',
			loop: false,
			arrows: true,
			buttons: false,
            fullScreen: true,
			thumbnailsPosition: 'left',
			thumbnailPointer: true,
			thumbnailWidth: 290,
			breakpoints: {
				800: {
					thumbnailsPosition: 'bottom',
					thumbnailWidth: 270,
					thumbnailHeight: 100
				},
				500: {
					thumbnailsPosition: 'bottom',
					thumbnailWidth: 120,
					thumbnailHeight: 50
				}
			}
		});
	});



