$(function() {
  $('input[type=file]').change(function() {
    var file = $(this).prop('file')[0];

  var reader = new FileReader();
  reader.onload = function() {
    var img_src = $('<img>').attr('src', reader.result).attr('width', 100).attr("id", "images");
    $('#drug_image').append(img_src);
  }
  reader.readAsDataURL(file);
  });

  $("#drug_image").on("drop",function(event){
    event.preventDefault();
    var files = event.originalEvent.dataTransfer.files;
    for (var i = 0 ; i<files.length; i++) {
      imagesDisplay(files[i]);
    }
  });

  function imagesDisplay(file) {
    var reader = new FileReader();
    reader.onload = function() {
      var img_src = $('<img>').attr('src',reader.result).attr('width', 100).attr("id", "images");
      $('#drug_image').append(img_src);
    }
    reader.readAsDataURL(file);
  }

  $("#drug_image").on("dragover",function(event){
    event.preventDefault();
  });
});