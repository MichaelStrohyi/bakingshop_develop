search_str = ''
$(() ->
    $('.search-input').bind "change keyup input click", () ->
        search_str = this.value
        if search_str.length >= 2
            $.ajax {
                type: 'post',
                url: "/search", 
                data: {'search-string':search_str},
                response: 'text',
                error: () ->
                    $(".top-menu-search-result").hide()
                ,
                success: (data) ->
                    $(".top-menu-search-result").html(data).fadeIn()
                    if data == ''
                        $(".top-menu-search-result").hide()
            }
        else 
            $(".top-menu-search-result").html('').hide()
        
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