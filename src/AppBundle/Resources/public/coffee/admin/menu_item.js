// Generated by CoffeeScript 1.12.7
var addAllActionLinks, addItemFormDeleteLink, itemType;

itemType = "item";

addAllActionLinks = function($itemFormLi) {
  return addItemFormDeleteLink($itemFormLi);
};

addItemFormDeleteLink = function($itemFormLi) {
  var $removeFormA, $removeItemLink;
  $removeItemLink = $('<a href="#" class="btn btn-default">delete this item</a>');
  $removeItemLink.on('click', function(e) {
    e.preventDefault();
    return $itemFormLi.remove();
  });
  $removeFormA = $('<div class="text-right"></div>').append($removeItemLink);
  return $itemFormLi.prepend($removeFormA);
};

$(document).ready(function() {
  return $("#edit-items").on('submit', function(event) {
    event.preventDefault();
    $('ul.mutable-items').find("li.list-item .item-title").each(function() {
      return replaceSymbols($(this));
    });
    return $(this).unbind('submit').submit();
  });
});