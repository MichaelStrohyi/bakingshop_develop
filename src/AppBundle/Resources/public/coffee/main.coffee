log = (message) -> console.log message
# set strings for hash xor
ah_string = '1a2b3d4s5f0e7g8k'
site = 'uspromocodes.com'
# set time for coupon link to be valid
pt = 90000
prepareL = pn = urlParams =  null
ps = pw = fa = ci = false

window.onload = () ->
    # run function to prepare page for show
    prepareShow()

ShowCode = (A, O) ->
  # get attribute 'hc' of clicked <a>
  hc = A.getAttribute 'hc'
  # exit if attribute 'hc' not exists
  if not hc
    return
  # if parent-window flag is not set
  if not pw
    # get window-opener, from wich current window was opened
    wo = window.opener
    # if window-opener exists and from-amp flaf is set
    if O && wo
      # set store-name as name of window-opener
      window.opener.name = pageN()
      # redirect window-opener into clicked <a> link
      window.opener.location = A.getAttribute 'href'
      # redirect current window into the same address but with hash for current clicked <a>
      window.location = window.location.pathname + pageQ() + '#' + pageI(A)
      # run init and prepareSho functions on new location
      init()
      prepareShow()
      return
    # set store-name as name of current window
    window.name = pageN();
    # open new window  with the same address but with hash for current clicked <a>
    nw = window.open window.location.pathname + pageQ() + '#' + pageI(A)
    # show error message if new window has been blocked
    if not nw
      alert "Please, allow pop-ups from Bakingshop.com to have ability copy and paste Coupons OR you will need to keep in mind this Coupon Code: " + check_index(eval "'" + A.getAttribute('hc') + "'" )
  # if parent-window flag is set
  else
    # get store-name
    pr = pageN()
    # set focus into clicked <a>
    A.focus()
    # remove focus
    A.blur()
    # if 'hc' attribute is set
    if hc
      # write coupon-code instead of clicked <a>
      A.innerHTML = '<span>' + check_index(eval("'" + A.getAttribute('hc') + "'")) + '</span>'
      # set new class for clicked <a>
      A.className += ' disabled yellow'
      # set window with name 'store-name' as target window for clicked <a>
      A.target = pr
      # clear onclick function of clicked <a>
      A.onclick = null
  return

pageN = () ->
  # return name for page of current store 'parent_{{store_name}}'
  return 'parent_' + pn 

pageI = (A) ->
  # create hash for clicked <a>: current timestamp plus id of clicked <a>
  return atoh_str (new Date()).getTime() + ',' + A.id

pageQ = () ->
  # return 'a' parameter as uri parameter-string if 'a' exists
  if urlParams['a'] && urlParams['a'] != ''
    return '?a=' + urlParams['a']

  return ''

atoh_str = (str) -> 
  # encode given string into hash using xor
  str_len = str.length
  res = ''

  for index in [0...str_len] by 1
    mod = Math.floor str.charCodeAt(index) / 16 ;
    rem = str.charCodeAt(index) % 16;
    res += ah_string.substring(mod, mod + 1) + ah_string.substring rem, rem + 1

  return res

htoa_str = (str) -> 
  # decode given hash into string
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
  # exit if prepare-show flag is not set
  if not ps
    return
  # run ShowCode function and exit if coupon-id flag is set
  if ci
    ShowCode document.getElementById(urlParams['i']), fa
    return
  # run ShowCode function
  l = prepareL
  ShowCode document.getElementById(l[1]), fa

check_index = (index) ->
  # decode 'hc value into coupon code'
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

getUrlParams = () ->
  # return uri parameters as object
  params = {}
  # Regex for replacing addition symbol with a space
  pl = /\+/g 
  search = /([^&=]+)=?([^&]*)/g
  decode = (s) -> return decodeURIComponent s.replace(pl, " ")
  query = window.location.search.substring(1)

  while match = search.exec query
     params[decode match[1]] = decode match[2]

  return params

init = () ->
  pw = ps = fa = ci = false
  # get page uri
  wlp = window.location.pathname 
  # get parameters from uri
  urlParams = getUrlParams()
  # get store-name from uri
  pn = wlp.split('/')[1].replace(/[-\.]/g, '_') 
  # get parameters from hash-string in uri
  l = prepareL = htoa_str(window.location.hash.substr(1)).split ','
  # if hash-string is not empty and date of it's creation is actal (pt - time for page to expire)
  if l != '' && l[0] && l[0] != '' && (new Date()).getTime() - pt <= parseInt l[0]
    # set parent-exists and prepare-show flags
    pw = true;
    ps = true;
  # if parameter 'a' exists in parameters and is not empty
  if urlParams['a'] && urlParams['a'] != ''
    # set from-amp and prepare-show flags
    fa = true
    ps = true
  # if parameter 'i' exists in parameters and is not empty
  if urlParams['i'] && urlParams['i'] != ''
    # set coupon-id and prepare-show flags
    ci = true
    ps = true
  return
# run init function
init()