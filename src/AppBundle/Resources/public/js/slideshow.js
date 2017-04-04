function slideSwitch() {
    var $active = $('.slideshow img.active');

    if ( $active.length === 0 ) $active = $('.slideshow img:last');

    var $next =  $active.next().length ? $active.next()
        : $('.slideshow img:first');

    $active.addClass('last-active');
        
    $next.css({opacity: 0.0})
        .addClass('active')
        .animate({opacity: 1.0}, 1000, function() {
            $active.removeClass('active last-active');
        });
}

function slideSetHeight(slide) {
    var $slide = $(slide);
    var $img = $slide.find("img").first();
    $slide.height($img.innerHeight());
}

function slideAllHeight() {
    $(".slideshow").each(function() {
        slideSetHeight(this);
    });
}

$(function() {
    setInterval( "slideSwitch()", 3000 );

    $(".slideshow img.active").on('load', function() {
        slideAllHeight();
    });

    $(window).resize(function() {
        slideAllHeight();
    });
});