(function($) {
    $(document).ready(function() {
        var screenWidth = window.screen.width;
        // owl menu home
        $("#main.page-front .top .slider").owlCarousel({
            nav: false,
            loop: false,
            navRewind: false,
            center: true,
            items: 1,
        });
        $("#main.page-front .top .slider").addClass('owl-carousel');
        
        // owl expertos home
        $("#main.page-front .bottom .block-ngt-general-expert-home .content #box-slider-profiles .slider-expert .item-main").owlCarousel({
            nav: false,
            loop: false,
            navRewind: false,
            center: true,
            items: 1,
            stagePadding: 50,
            responsive: {
                768: {
                    items: 1,
                    stagePadding: 0
                },
                1180: {
                    items: 3,
                    center: false,
                    stagePadding: 0
                }
            }
        });
        $("#main.page-front .bottom .block-ngt-general-expert-home .content #box-slider-profiles .slider-expert .item-main").addClass('owl-carousel');

        // node curso cover
        $( "picture.cover-video.play" ).click(function() {
           $(this).removeClass('active');
           var vid = document.getElementById('presentation');
           vid.play();
        });
        

        // mobile
        if(screenWidth < 769){
            // scroll show menu
            $(window).scroll(function() {
                var scroll = $(window).scrollTop();
                if (scroll >= 197) {
                    $("nav#menu-fixed").addClass("scroll");
                } else {
                    $("nav#menu-fixed").removeClass("scroll");
                }
            });

            // slider node curso
            $(".main.node-course .right .other-course .more").owlCarousel({
                nav: false,
                loop: true,
                navRewind: false,
                center: true,
                items: 1.5,
            });
            $(".main.node-course .right .other-course .more").addClass('owl-carousel');

            $('#menu-live').click(function() {
                $(this).toggleClass('active');
                $('.izq.menu-live').toggleClass('active');
            });
            
        }
        $('#ngtModal').removeClass('none');

        // slider sección cursos
        $("#main.page-course .top .destacado .block-ngt-general-slider-course .content .slider").owlCarousel({
            nav: false,
            loop: false,
            navRewind: false,
            center: true,
            items: 1,
        });
        
        $("#main.page-course .top .destacado .block-ngt-general-slider-course .content .slider").addClass('owl-carousel');
        jQuery('.mobile #select-category').change(function(){
            var url = jQuery( ".mobile #select-category option:selected" ).attr('data-url');
            window.location.href = url;
        });

        $(window).scroll(function (event) {
            var scroll = $(window).scrollTop();
            if ($('#main.page-course').length) {
                if(scroll > 572) {
                    $("#main.page-course>.top .medio .left").addClass('scroll');
                }else{
                    $("#main.page-course>.top .medio .left").removeClass('scroll');
    
                }
            }
        });
    
    });
   
})(jQuery);