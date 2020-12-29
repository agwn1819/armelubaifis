jQuery(document).ready(function($) {

$(document).ready(function() {
    needToConfirm = false;
    console.log()
    window.addEventListener("close", function(e){
       e.preventDefault();
    })
});

function askConfirm() {
    let example = '<?php $wpdb; echo "hello";?>'
    return example;
}

$("#first_name").change(function() {
    needToConfirm = true;
});

 })