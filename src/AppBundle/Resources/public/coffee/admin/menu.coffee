$(document).ready () ->
    # add function for replacing some special symbols in header before submit
    $("#edit-form").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in header
        replaceSymbols $("#admin_menu_header")
        # submit form
        $(this).unbind('submit').submit()