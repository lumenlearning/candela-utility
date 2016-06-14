jQuery( document ).ready( function() {
  var pressed = false;
  jQuery( "#citation-header-" + thePost.id ).click(function() {
    pressed = !pressed;
    jQuery( "#citation-list-" + thePost.id ).slideToggle();
    jQuery( "#citation-header-" + thePost.id ).toggleClass('expanded collapsed');
    jQuery( "#citation-header-" + thePost.id ).attr('aria-pressed', pressed);
  });
});
