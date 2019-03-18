$( document ).ready(function( $ ) {
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
            fullScreen: true,
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
                    fullScreen: true,
					thumbnailWidth: 50,
					thumbnailHeight: 50
				},
				500: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
                    fade: false,
                    fullScreen: true,
					thumbnailWidth: 50,
					thumbnailHeight: 50
				},
                300: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
                    fade: false,
                    fullScreen: true,
					thumbnailWidth: 50,
					thumbnailHeight: 50
				}
			}
		});
	});


