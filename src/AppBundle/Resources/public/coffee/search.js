// Generated by CoffeeScript 1.12.7
var search_str;

search_str = '';

$(function() {
  $('.search-input').bind("change keyup input click", function() {
    search_str = this.value;
    if (search_str.length >= 2) {
      return $.ajax({
        type: 'post',
        url: "/search",
        data: {
          'search-string': search_str
        },
        response: 'text',
        error: function() {
          return $(".top-menu-search-result").hide();
        },
        success: function(data) {
          $(".top-menu-search-result").html(data).fadeIn();
          if (data === '') {
            return $(".top-menu-search-result").hide();
          }
        }
      });
    } else {
      return $(".top-menu-search-result").html('').hide();
    }
  });
  $(".top-menu-search-result").hover(function() {
    return $(".search-input").blur();
  });
  $(".top-menu-search-result").on("mouseenter", "a", function() {
    var s_res;
    s_res = $(this).text();
    if ($(this).hasClass("search-result-more")) {
      return $(".search-input").val(search_str);
    } else {
      return $(".search-input").val(s_res);
    }
  });
  $(".top-menu-search-form").on("mouseleave", function() {
    return window.setTimeout(function() {
      return $(".top-menu-search-result").fadeOut("fast");
    }, 600);
  });
  return $(".top-menu-search-form").on("mouseenter", function() {
    if ($.trim($(".top-menu-search-result").text()) !== '') {
      return $(".top-menu-search-result").fadeIn("fast");
    }
  });
});
