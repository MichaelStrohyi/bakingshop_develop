window.removeLogo = (e, link) ->
    # open image in new window (window size = image size)
    e.preventDefault()
    link.closest('.form-group').remove()