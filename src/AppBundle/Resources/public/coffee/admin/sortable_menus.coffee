jQuery(document).ready () ->
    # Get the ul that holds the collection of tags
    $collectionHolder = $('ul.mutable-items')

    # add function for updating position before submit
    $("#edit-items").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # recalculate position for all items
        $collectionHolder.find("li.list-item .item-position").each (index) ->
            $(this).val index
        # submit form
        $(this).unbind('submit').submit()

$("ul.items-list").sortable()