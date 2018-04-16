itemType = "coupon"

addAllActionLinks = ($itemFormLi) ->
    addItemFormDeleteLink $itemFormLi
    addItemFormActivateLink $itemFormLi
    addItemFormMoveLinks $itemFormLi

addItemFormDeleteLink = ($itemFormLi) ->
    $removeFormA = $itemFormLi.find 'a.removeLink'

    $removeFormA.on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # remove the li for the tag form
        $itemFormLi.remove()

addItemFormActivateLink = ($itemFormLi) ->
    $activateFormA = $itemFormLi.find 'a.activateLink'

    $activateFormA.on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()

        $inputActivity = $itemFormLi.find 'input.coupon-activity'   
        # change coupon's class to change background color
        $itemFormLi.toggleClass "deactivated"
        # if coupon is active
        if ($inputActivity.val() is '1')
            # set coupon activity into 0
            $inputActivity.val '0'
            # change link text to "Activate"
            $itemFormLi.find('a.activateLink').text 'Activate'

            return;

        # set coupon activity into 1
        $inputActivity.val '1'
        # change link text to "Deactivate"
        $itemFormLi.find('a.activateLink').text 'Deactivate'

addItemFormMoveLinks = ($itemFormLi) ->
    $itemFormLi.find('a.upLink').on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # change current li position
        $itemFormLi.insertBefore $itemFormLi.prev()
        # scroll page if new li position came out of screen
        if ($(document).scrollTop() > $itemFormLi.position().top)
            $(document).scrollTop $itemFormLi.position().top

    $itemFormLi.find('a.downLink').on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # change current li position
        $itemFormLi.insertAfter $itemFormLi.next()
        # scroll page if new li position came out of screen
        if ($(document).scrollTop() + $(window).height() - $itemFormLi.outerHeight() < $itemFormLi.position().top)
            $(document).scrollTop $itemFormLi.position().top - $(window).height() + $itemFormLi.outerHeight()

    $itemFormLi.find('a.firstLink').on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # change current li position
        $itemFormLi.insertBefore $('li.list-item').first()
        # scroll page if new li position came out of screen
        if ($(document).scrollTop() > $itemFormLi.position().top)
            $(document).scrollTop $itemFormLi.position().top

    $itemFormLi.find('a.lastLink').on 'click', (e) ->
        # prevent the link from creating a "#" on the URL
        e.preventDefault()
        # change current li position
        $itemFormLi.insertAfter $('li.list-item').last()
        # scroll page if new li position came out of screen
        if ($(document).scrollTop() + $(window).height() - $itemFormLi.outerHeight() < $itemFormLi.position().top)
            $(document).scrollTop $itemFormLi.position().top - $(window).height() + $itemFormLi.outerHeight()

$(document).ready () ->
    # add function for replacing some special symbols in labels and codes before submit
    $("#edit-coupons").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in labels
        $('ul.mutable-items').find("li.list-item .coupon-label").each () ->
            replaceSymbols $(this)
        # search and replace some special symbols in codes
        $('ul.mutable-items').find("li.list-item .coupon-code").each () ->
            replaceSymbols $(this)
        # submit form
        $(this).unbind('submit').submit()