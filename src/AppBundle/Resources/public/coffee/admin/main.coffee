replaceSymbols = (el) ->
    value = el.val()
    pattern = new RegExp "[" + String.fromCharCode(171) + String.fromCharCode(187) + String.fromCharCode(8220) + String.fromCharCode(8221) + String.fromCharCode(8243) + String.fromCharCode(10077) + String.fromCharCode(10078) + "]", 'g'
    value = value.replace pattern, '"'
    pattern = new RegExp "[" + String.fromCharCode(8216) + String.fromCharCode(8217) + String.fromCharCode(8242) + String.fromCharCode(8249) + String.fromCharCode(8250) + "]", 'g'
    value = value.replace pattern, "'"
    pattern = new RegExp "[" + String.fromCharCode(8211) + String.fromCharCode(8212) + String.fromCharCode(8722) + "]", 'g'
    value = value.replace pattern, "-"
    el.val value