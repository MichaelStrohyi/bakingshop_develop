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