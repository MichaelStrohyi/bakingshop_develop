$collectionHolder = null

jQuery(document).ready () ->
    # Get the ul that holds the collection of tags
    $collectionHolder = $('ul.mutable-items')
    # add actions links to all of the existing tag form li elements
    $collectionHolder.find('li.list-item').each () ->
        addAllActionLinks $(this)


addAllActionLinks = ($itemFormLi) ->
    addItemFormDeleteLink $itemFormLi
    addItemFormCheckboxAction $itemFormLi

addItemFormDeleteLink = ($itemFormLi) ->
    $removeItemLink = $('<a href="#" class="btn btn-default">delete comment</a>')
    $removeItemLink.on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # remove the li for the tag form
        $itemFormLi.remove()
    $removeFormA = $('<div class="text-right"></div>').append $removeItemLink
    $itemFormLi.prepend $removeFormA

addItemFormCheckboxAction = ($itemFormLi) ->
    $activateFormA = $itemFormLi.find "input:checkbox"

    $activateFormA.on 'change', (e) ->  
        # change coupon's class to change background color
        $itemFormLi.toggleClass "deactivated"

$(document).ready () ->
    # add function for replacing some special symbols in labels and names before submit
    $("#edit-commens").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in labels
        $('ul.mutable-items').find("li.list-item .comment-label").each () ->
            replaceSymbols $(this)
        # search and replace some special symbols in names
        $('ul.mutable-items').find("li.list-item .comment-author").each () ->
            replaceSymbols $(this)
        # submit form
        $(this).unbind('submit').submit()