$(document).ready () ->
    # add function for replacing some special symbols in name before submit
    $("#edit-form").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in name
        replaceSymbols $("#admin_operator_name")
        # submit form
        $(this).unbind('submit').submit()