cur_val = null

window.removeLogo = (e, link) ->
    # open image in new window (window size = image size)
    e.preventDefault()
    link.closest('.form-group').remove()

window.setLinkForAll = (e, link) ->
    # open image in new window (window size = image size)
    e.preventDefault()
    cur_val = $(link.closest('div')).children('.coupon-link').val()
    if (cur_val)
        $('.coupon-link').each (index) ->
            $(this).val cur_val

$(document).on "change", "input[name=stores_filter]", () ->
    stores_filter = $('[name="stores_filter"]:checked').val()
    console.log stores_filter
    $('.store-type').show()
    if stores_filter isnt ''
        $('.store-type').not($('.' + stores_filter + '-store')).hide()

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