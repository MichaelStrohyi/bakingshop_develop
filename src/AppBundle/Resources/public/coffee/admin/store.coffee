window.removeLogo = (e, link) ->
    # open image in new window (window size = image size)
    e.preventDefault()
    link.closest('.form-group').remove()

$(document).ready () ->
    # add function for replacing some special symbols in name, keywords and description before submit
    $("#edit-form").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in name, keywords and description
        replaceSymbols $("#admin_store_name")
        replaceSymbols $("#admin_store_keywords")
        replaceSymbols $("#admin_store_description")
        # submit form
        $(this).unbind('submit').submit()