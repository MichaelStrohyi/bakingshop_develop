search_str = ''
$(() ->
    $('.search-input').bind "input", () ->
        search_str = this.value
        $(".top-menu-search-result").html('').hide()
        if search_str.length >= 2
            $(".top-menu-search-image").hide()
            $(".top-menu-search-loading").show()
            $(".top-menu-search-result").hide()
            $.ajax {
                type: 'post',
                url: "/search", 
                data: {'search-ajax':search_str},
                response: 'text',
                error: () ->
                    $(".top-menu-search-loading").hide()
                    $(".top-menu-search-image").show()
                ,
                success: (data) ->
                    if data
                        $(".top-menu-search-result").html(data).fadeIn()
                        $(".top-menu-search-loading").hide()
                        $(".top-menu-search-image").show()
            }
        
    $(".top-menu-search-result").hover(() ->
        $(".search-input").blur()
    )
  
    $(".top-menu-search-result").on "mouseenter", "a", () ->
        s_res = $(this).text()
        if $(this).hasClass "search-result-more" then $(".search-input").val search_str else $(".search-input").val s_res

    $(".top-menu-search-form").on "mouseleave", () ->
        window.setTimeout(() ->
          $(".top-menu-search-result").fadeOut "fast"
        , 600)

    $(".top-menu-search-form").on "mouseenter", () ->
        if $.trim($(".top-menu-search-result").text()) != ''
          $(".top-menu-search-result").fadeIn "fast"
)