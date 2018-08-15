// Generated by CoffeeScript 1.12.7
var cur_val;

cur_val = null;

window.removeLogo = function(e, link) {
  e.preventDefault();
  return link.closest('.form-group').remove();
};

window.setLinkForAll = function(e, link) {
  e.preventDefault();
  cur_val = $(link.closest('div')).children('.coupon-link').val();
  if (cur_val) {
    return $('.coupon-link').each(function(index) {
      return $(this).val(cur_val);
    });
  }
};

$(document).on("change", "input[name=stores_filter]", function() {
  var stores_filter;
  stores_filter = $('[name="stores_filter"]:checked').val();
  console.log(stores_filter);
  $('.store-type').show();
  if (stores_filter !== '') {
    return $('.store-type').not($('.' + stores_filter + '-store')).hide();
  }
});

$(document).ready(function() {
  return $("#edit-form").on('submit', function(event) {
    event.preventDefault();
    replaceSymbols($("#admin_store_name"));
    replaceSymbols($("#admin_store_keywords"));
    replaceSymbols($("#admin_store_description"));
    replaceSymbols($("#admin_store_metaDescription"));
    replaceSymbols($("#admin_store_metaKeywords"));
    replaceSymbols($("#admin_store_metatags"));
    return $(this).unbind('submit').submit();
  });
});
