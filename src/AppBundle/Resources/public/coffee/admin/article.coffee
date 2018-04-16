$(document).on "change", "input[name=pages_filter]", () ->
    pages_filter = $('[name="pages_filter"]:checked').val()
    $('.page-type').show()
    if pages_filter isnt ''
        $('.page-type').not($('.' + pages_filter + '-page')).hide()

$(document).ready () ->
    # add function for replacing some special symbols in header, author and description before submit
    $("#edit-form").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in header, author and description
        replaceSymbols $("#admin_article_description")
        replaceSymbols $("#admin_article_header")
        replaceSymbols $("#admin_article_author")
        # submit form
        $(this).unbind('submit').submit()
