log = (message) -> console.log message

ah_string = '1a2b3d4s5f0e7g8k'
site = 'uspromocodes.com'
pt = 90000
prepareL = null
ps = false
pw = false
pn = ''

window.onload = () ->
  if ps
    prepareShow()

ShowCode = (A, L) ->
  hc = A.getAttribute 'hc'
  if not hc
    return

  if not pw
    window.name = pageN()
    window.open window.location.pathname + '#' + pageI(A), pn, pageF(), false
  else
    pr = pageN()
    A.focus()
    A.blur()
    link = A.href
    if hc
#        s = document.createElement 'span'
#        s.innerHTML = 'Code: ' + check_index eval("'" + A.getAttribute('hc') + "'")
#        A.parentNode.appendChild s
      A.innerHTML = '<span>' + check_index(eval("'" + A.getAttribute('hc') + "'")) + '</span>'
      A.className += ' disabled yellow'
      A.target = pr
      A.onclick = null
#      A.parentNode.removeChild A
    if L != false
      window.open link.href, pr, pageF(), true
      window.setTimeout ( ->
        window.focus()
        ), 300
  return

pageN = () ->
  return 'parent_' + pn # return tmp_name of current page 'parent_{{store_name}}'

pageF = () -> # return parameters for new window, width is constant, height is calculated. Need to calculate width too!!!!
  h = 500
  if typeof window.innerWidth is 'number'
    h = window.innerHeight - 50
  else if document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)
    h = document.documentElement.clientHeight - 50
  else if document.body && (document.body.clientWidth || document.body.clientHeight)
    h = document.body.clientHeight - 50
  return "width=1000,height=" + h + ",menubar=1,resizable=1,toolbar=1,scrollbars=1,status=1,titlebar=1,addressbar=1,address=1,location=1"

pageI = (A) ->
  return atoh_str (new Date()).getTime() + ',' + A.id

atoh_str = (str) -> # encode given string into hash
  str_len = str.length
  res = ''

  for index in [0...str_len] by 1
    mod = Math.floor str.charCodeAt(index) / 16 ;
    rem = str.charCodeAt(index) % 16;
    res += ah_string.substring(mod, mod + 1) + ah_string.substring rem, rem + 1

  return res

htoa_str = (str) -> # encode given hash into string
  str_len = str.length
  res = ''
  index = 0
  while index < str_len
    pos1 = index++
    pos2 = index++
    mod = ah_string.indexOf str.substring(pos1, pos1 + 1)
    rem = ah_string.indexOf str.substring(pos2, pos2 + 1)
    ch = mod * 16 + rem
    res += String.fromCharCode ch

  return res

prepareShow = () ->
  l = prepareL
  ShowCode document.getElementById(l[1]), false

check_index = (index) ->
  index_len = index.length
  index_pos = 0
  hash_len = site.length
  hash_pos = -1
  index_checked = ''
  for index_pos in [0...index_len] by 1
    cd = index.charCodeAt index_pos
    if ++hash_pos >= hash_len
      hash_pos = 0
    cd_hash = site.charCodeAt hash_pos
    index_checked += String.fromCharCode cd ^ cd_hash

  return index_checked

(() ->
  wlp = window.location.pathname # get uri
  pn = wlp.split('/')[1].replace(/[-\.]/g, '_') # get store-name from uri
  l = prepareL = htoa_str(window.location.hash.substr(1)).split ','
  if l != '' && l[0] && l[0] != '' && (new Date()).getTime() - pt <= parseInt l[0]
    pw = true;
    ps = true;
)()