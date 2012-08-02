jQuery(document).ready(function($) {


 $('.BoxCategories h4').append(" [+ -]");
 $('.BoxCategories').click(function(){
    if($('.BoxCategories ul').is(':visible')) {
      $('.BoxCategories ul').slideUp('200');
    } else {
      $('.BoxCategories ul').slideDown('200');
    }
  });

  $('.BoxBookmarks h4').append(" [+ -]");
    $('.BoxBookMarks').click(function(){
    if($('.BoxBookmarks ul').is(':visible')) { 
      $('.BoxBookmarks ul').slideUp('200');
    } else {
      $('.BoxBookmarks ul').slideDown('200');
    }
  });
 
 
 $("h4:contains('In this Discussion')").append(" [+ -]").addClass("InDis");
  $('.InDis+ul').slideUp('200');
  var $this = $(this);
  $this.click(function(){
    if($('.InDis+ul').is(':visible')) {
      $('.InDis+ul').slideUp('200');
    } else {
      $('.InDis+ul').slideDown('200');
    }
  });
 
 

});




