jQuery(document).ready(function($){

console.log("hello from tap34556");
let touchstartArray = []
let holdtype = "short"
$(document).on('touchstart', 'body', function(e) {
    //console.log(e.originalEvent.touches[0].pageX);
    touchstartArray.push({taptype: "short", coordinate_x: e.originalEvent.touches[0].pageX, coordinate_y: e.originalEvent.touches[0].pageY})
});

function changeHoldType(){
  holdtype = "long"
}

let touchmoveArray = []
$(document).on('touchmove', 'body', function(e) {
    //console.log(e.originalEvent.touches[0].pageX);
    touchmoveArray.push({coordinate_x: e.originalEvent.touches[0].pageX, coordinate_y: e.originalEvent.touches[0].pageY})
});
let touchendArray = []
$(document).on('touchend', 'body', function(e){
  //console.log(e.originalEvent.changedTouches[0].pageX);

})

$(window).bind("beforeunload", function(e) {
  const first_coor = touchmoveArray[0]
  const last_coor = touchmoveArray[touchmoveArray.length - 1]
  $.ajax({
    type: 'GET',
    url: myAjax.ajaxurl,
    contentType: "json",
    data: {
      action: 'tap_action',
      touchstart: touchstartArray,
      touchmove: [first_coor, last_coor],
      session_id: myAjax.session_id,

    },
    success: function(data, textStatus, XMLHttpRequest){
      console.log(textStatus)
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus)
          }
  })
  touchmoveArray = []
  //$.post(myAjax.adminurl, {action: "mouse_action", mousedown: mousedownArray}, function(res){console.log(res)})
  //$.post(myAjax.ajaxurl, {action: "my_test", php_test: "hello"}, function(res){console.log(res)})

})
})
