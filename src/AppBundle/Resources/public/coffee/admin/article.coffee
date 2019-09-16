$(document).on "change", "input[name=pages_filter]", () ->
            pages_filter = $('[name="pages_filter"]:checked').val()
            $('.page-type').show()
            if pages_filter isnt ''
                $('.page-type').not($('.' + pages_filter + '-page')).hide()