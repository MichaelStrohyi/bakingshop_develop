// Generated by CoffeeScript 1.12.7
var ShowCode, ah_string, atoh_str, check_index, ci, fa, getUrlParams, htoa_str, init, log, pageI, pageN, pageQ, pn, prepareL, prepareShow, ps, pt, pw, site, urlParams;

log = function(message) {
  return console.log(message);
};

ah_string = '1a2b3d4s5f0e7g8k';

site = 'uspromocodes.com';

pt = 90000;

prepareL = pn = urlParams = null;

ps = pw = fa = ci = false;

window.onload = function() {
  return prepareShow();
};

ShowCode = function(A, O) {
  var hc, nw, pr, wo;
  hc = A.getAttribute('hc');
  if (!hc) {
    return;
  }
  if (!pw) {
    wo = window.opener;
    if (O && wo) {
      window.opener.name = pageN();
      window.opener.location = A.getAttribute('href');
      window.location = window.location.pathname + pageQ() + '#' + pageI(A);
      init();
      prepareShow();
      return;
    }
    window.name = pageN();
    nw = window.open(window.location.pathname + pageQ() + '#' + pageI(A));
    if (!nw) {
      alert("Please, allow pop-ups from Bakingshop.com to have ability copy and paste Coupons OR you will need to keep in mind this Coupon Code: " + check_index(eval("'" + A.getAttribute('hc') + "'")));
    }
  } else {
    pr = pageN();
    A.focus();
    A.blur();
    if (hc) {
      A.innerHTML = '<span>' + check_index(eval("'" + A.getAttribute('hc') + "'")) + '</span>';
      A.className += ' disabled yellow';
      A.target = pr;
      A.onclick = null;
    }
  }
};

pageN = function() {
  return 'parent_' + pn;
};

pageI = function(A) {
  return atoh_str((new Date()).getTime() + ',' + A.id);
};

pageQ = function() {
  if (urlParams['a'] && urlParams['a'] !== '') {
    return '?a=' + urlParams['a'];
  }
  return '';
};

atoh_str = function(str) {
  var i, index, mod, ref, rem, res, str_len;
  str_len = str.length;
  res = '';
  for (index = i = 0, ref = str_len; i < ref; index = i += 1) {
    mod = Math.floor(str.charCodeAt(index) / 16);
    rem = str.charCodeAt(index) % 16;
    res += ah_string.substring(mod, mod + 1) + ah_string.substring(rem, rem + 1);
  }
  return res;
};

htoa_str = function(str) {
  var ch, index, mod, pos1, pos2, rem, res, str_len;
  str_len = str.length;
  res = '';
  index = 0;
  while (index < str_len) {
    pos1 = index++;
    pos2 = index++;
    mod = ah_string.indexOf(str.substring(pos1, pos1 + 1));
    rem = ah_string.indexOf(str.substring(pos2, pos2 + 1));
    ch = mod * 16 + rem;
    res += String.fromCharCode(ch);
  }
  return res;
};

prepareShow = function() {
  var l;
  if (!ps) {
    return;
  }
  if (ci) {
    ShowCode(document.getElementById(urlParams['i']), fa);
    return;
  }
  l = prepareL;
  return ShowCode(document.getElementById(l[1]), fa);
};

check_index = function(index) {
  var cd, cd_hash, hash_len, hash_pos, i, index_checked, index_len, index_pos, ref;
  index_len = index.length;
  index_pos = 0;
  hash_len = site.length;
  hash_pos = -1;
  index_checked = '';
  for (index_pos = i = 0, ref = index_len; i < ref; index_pos = i += 1) {
    cd = index.charCodeAt(index_pos);
    if (++hash_pos >= hash_len) {
      hash_pos = 0;
    }
    cd_hash = site.charCodeAt(hash_pos);
    index_checked += String.fromCharCode(cd ^ cd_hash);
  }
  return index_checked;
};

getUrlParams = function() {
  var decode, match, params, pl, query, search;
  params = {};
  pl = /\+/g;
  search = /([^&=]+)=?([^&]*)/g;
  decode = function(s) {
    return decodeURIComponent(s.replace(pl, " "));
  };
  query = window.location.search.substring(1);
  while (match = search.exec(query)) {
    params[decode(match[1])] = decode(match[2]);
  }
  return params;
};

init = function() {
  var l, wlp;
  pw = ps = fa = ci = false;
  wlp = window.location.pathname;
  urlParams = getUrlParams();
  pn = wlp.split('/')[1].replace(/[-\.]/g, '_');
  l = prepareL = htoa_str(window.location.hash.substr(1)).split(',');
  if (l !== '' && l[0] && l[0] !== '' && (new Date()).getTime() - pt <= parseInt(l[0])) {
    pw = true;
    ps = true;
  }
  if (urlParams['a'] && urlParams['a'] !== '') {
    fa = true;
    ps = true;
  }
  if (urlParams['i'] && urlParams['i'] !== '') {
    ci = true;
    ps = true;
  }
};

init();