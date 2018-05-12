jQuery(document).ready () ->
    # Get the ul that holds the collection of tags
    $collectionHolder = $('ul.mutable-items')

    # add function for updating position before submit
    $("#edit-items").on 'submit', (event) ->
        $collectionHolder.find("li.list-item .item-position").each (index) ->
            $(this).val index

$("ul.items-list").sortable()