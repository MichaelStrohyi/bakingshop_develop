itemType = "item"

addAllActionLinks = ($itemFormLi) ->
    addItemFormDeleteLink $itemFormLi

addItemFormDeleteLink = ($itemFormLi) ->
    $removeItemLink = $('<a href="#" class="btn btn-default">delete this item</a>')
    $removeItemLink.on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # remove the li for the tag form
        $itemFormLi.remove()
    $removeFormA = $('<div class="text-right"></div>').append $removeItemLink
    $itemFormLi.prepend $removeFormA

$(document).ready () ->
    # add function for replacing some special symbols in headers before submit
    $("#edit-items").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()

        # search and replace some special symbols in headers
        $('ul.mutable-items').find("li.list-item .item-title").each () ->
            replaceSymbols $(this)
        # submit form
        $(this).unbind('submit').submit()