# setup an "add a tag" link
$addItemLink = $("""<a href="#" class="add_item_link btn btn-default col-sm-offset-2">Add #{itemType}</a>""");
$collectionHolder = null

jQuery(document).ready () ->
    # Get the ul that holds the collection of tags
    $collectionHolder = $('ul.mutable-items')

    # add the "add a tag" anchor and li to the tags ul
    $collectionHolder.after $addItemLink

    # count the current form inputs we have (e.g. 2), use that as the new
    # index when inserting a new item (e.g. 2)
    $collectionHolder.data 'index', $collectionHolder.find(".list-item").length

    $addItemLink.on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()

        # add a new tag form (see next code block)
        addItemLink $collectionHolder

    # add actions links to all of the existing tag form li elements
    $collectionHolder.find('li.list-item').each () ->
        addAllActionLinks $(this)

    # add function for updating position before submit
    $("#edit-#{itemType}s").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # recalculate position for all items
        $collectionHolder.find("li.list-item .#{itemType}-position").each (index) ->
            $(this).val index
        # submit form
        $(this).unbind('submit').submit()

addItemLink = ($collectionHolder) ->
    # Get the data-prototype explained earlier
    prototype = $collectionHolder.data 'prototype'

    # get the new index
    index = $collectionHolder.data 'index'

    # Replace '__name__' in the prototype's HTML to
    # instead be a number based on how many items we have
    newForm = prototype.replace /__name__/g, index

    # increase the index with one for the next item
    $collectionHolder.data 'index', index + 1

    # Display the form in the page in an li, before the "Add a tag" link li
    $newFormLi = $('<li class="list-item new-item"></li>').append newForm
    $collectionHolder.append($newFormLi);

    # add actions links to the new form
    addAllActionLinks $newFormLi