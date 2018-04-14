window.removeLogo = (e, link) ->
    # open image in new window (window size = image size)
    e.preventDefault()
    link.closest('.form-group').remove()

jQuery(document).ready () ->
    # add function for replacing some special symbols in header and description before submit
    $("#edit-article").on 'submit', (event) ->
        # cancel form submit event
        event.preventDefault()
        # search and replace some special symbols in header and description
        descr = $("#admin_article_description").val()
        header = $("#admin_article_header").val()
        pattern = new RegExp "[" + String.fromCharCode(171) + String.fromCharCode(187) + String.fromCharCode(8220) + String.fromCharCode(8221) + String.fromCharCode(8243) + String.fromCharCode(10077) + String.fromCharCode(10078) + "]", 'g'
        descr = descr.replace pattern, '"'
        header = header.replace pattern, '"'
        pattern = new RegExp "[" + String.fromCharCode(8216) + String.fromCharCode(8217) + String.fromCharCode(8242) + String.fromCharCode(8249) + String.fromCharCode(8250) + "]", 'g'
        descr = descr.replace pattern, "'"
        header = header.replace pattern, "'"
        pattern = new RegExp "[" + String.fromCharCode(8211) + String.fromCharCode(8212) + String.fromCharCode(8722) + "]", 'g'
        descr = descr.replace pattern, "-"
        header = header.replace pattern, '-'
        $("#admin_article_description").val descr
        $("#admin_article_header").val header
        # submit form
        $(this).unbind('submit').submit()
