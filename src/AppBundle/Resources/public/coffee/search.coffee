search_str = ''
globalTimeout = null
$(() ->
    $('.search-input').bind "input", () ->
        # set function to watch searh-input changes
        # if timeout from last change is not expired
        if globalTimeout != null
            # destroy task assigned to run after timeout
            clearTimeout globalTimeout
        el = this
        # exit if current value of search-input is equal to saved search_str
        if el.value == search_str
            return
        # set task to be run after timeout
        globalTimeout = setTimeout () ->
            # unset timeout flag
            globalTimeout = null;
            # save current value of search-input into search_str
            search_str = el.value
            # hide previous search results
            $(".top-menu-search-result").html('').hide()
            # if length of search-input value > 1
            if search_str.length >= 2
                # hide search image and show loading image
                $(".top-menu-search-image").hide()
                $(".top-menu-search-loading").show()
                # make ajax request
                $.ajax {
                    type: 'post',
                    url: "/search",
                    data: {'search-ajax':search_str},
                    response: 'text',
                    error: () ->
                        # hide loading image, show search image if ajax has returned error
                        $(".top-menu-search-loading").hide()
                        $(".top-menu-search-image").show()
                    ,
                    success: (data) ->
                        # if ajax request has been made successfully and server return none empty data
                        if data
                            # paste returned data into search-result
                            $(".top-menu-search-result").html(data).fadeIn()
                            # hide loading image, show search image
                            $(".top-menu-search-loading").hide()
                            $(".top-menu-search-image").show()
                }
        , 1000
    $(".top-menu-search-result").hover(() ->
        # remove focus from search-input if search-result is hovered
        $(".search-input").blur()
    )
  
    $(".top-menu-search-result").on "mouseenter", "a", () ->
        # set value of hovered result of search as search-input value and save this value into search_str var
        s_res = $(this).text()
        if $(this).hasClass "search-result-more"
            $(".search-input").val search_str
        else
            $(".search-input").val s_res
            search_str = s_res

    $(".top-menu-search-form").on "mouseleave", () ->
        # hide results of search on mouse leaves search-input area
        window.setTimeout(() ->
          $(".top-menu-search-result").fadeOut "fast"
        , 600)

    $(".top-menu-search-form").on "mouseenter", () ->
        # show results of search on mouse enters into search-input area if search-string is not empty
        if $.trim($(".top-menu-search-result").text()) != ''
          $(".top-menu-search-result").fadeIn "fast"
)