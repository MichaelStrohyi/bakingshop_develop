slideSwitch = ->
    $active = $ '.slideshow img.active'
    $active = $ '.slideshow img:last' if $active.length == 0

    $next = $active.next()
    $next = $ '.slideshow img:first' if $next.length == 0

    $active.addClass 'last-active'
    $next.css {opacity: 0.0}
        .addClass 'active'
        .animate {opacity: 1.0}, 1000, ->
            $active.removeClass 'active last-active'

slideSetHeight = (slide) ->
    $slide = $ slide
    $img = $slide.find("img").first()
    $slide.height $img.innerHeight()

slideAllHeight = ->
    $(".slideshow").each -> slideSetHeight this

$(".slideshow img.active").load ->
    slideAllHeight()

$(window).resize ->
    slideAllHeight()

$ ->
    setInterval "slideSwitch()", 3000

    slideAllHeight()

    