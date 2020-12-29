jQuery(document).ready(function($){

let mousedownArray = []

$('body').mousedown(function(e) {
  switch (e.which) {
        case 1:
            mousedownArray.push({button: "Left", coordinate_x: e.pageX, coordinate_y: e.pageY})
            //console.log('Left Mouse button pressed.');
            break;
        case 2:
            mousedownArray.push({button: "Middle", coordinate_x: e.pageX, coordinate_y: e.pageY})
            //console.log('Middle Mouse button pressed.');
            break;
        case 3:
            mousedownArray.push({button: "Right", coordinate_x: e.pageX, coordinate_y: e.pageY})
            //console.log('Right Mouse button pressed.');
            break;
        default:
            //console.log('You have a strange Mouse!');
            mousedownArray.push({button: "Not Recognizeable", coordinate_x: e.pageX, coordinate_y: e.pageY})
    }
})

let mousemoveArray = []
$("body").mousemove(function(e){
  mousemoveArray.push({coordinate_x: e.pageX, coordinate_y: e.pageY})
})

$(window).bind("beforeunload", function(e) {
  const first_coor = mousemoveArray[0]
  const last_coor = mousemoveArray[mousemoveArray.length - 1]
  e.preventDefault();
  $.ajax({
    type: 'GET',
    url: myAjax.ajaxurl,
    contentType: "json",
    data: {
      action: 'mouse_action',
      mousedown: mousedownArray,
      mousemove: [first_coor, last_coor],
      session_id: myAjax.session_id
    },
    success: function(data, textStatus, XMLHttpRequest){
      console.log(textStatus)
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus)
          }
  })

  //$.post(myAjax.adminurl, {action: "mouse_action", mousedown: mousedownArray}, function(res){console.log(res)})
  //$.post(myAjax.ajaxurl, {action: "my_test", php_test: "hello"}, function(res){console.log(res)})

})


});
