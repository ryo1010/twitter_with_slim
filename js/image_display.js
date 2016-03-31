$(function() {
  //type = file で選択されたとき
  //複数選択しても画像表示は一枚しかできない・・・
  $('#upfile').change(function(event) {
    var file = $(this).prop('files')[0];
    imagesDisplay(file);
  });

  $("#drug_image").on("drop",function(event){
    event.preventDefault();
    var files = event.originalEvent.dataTransfer.files;
    for (var i = 0 ; i<files.length; i++) {
      if (i < 4) {
        imagesDisplay(files[i]);
      } else {
        alert("画像は4つまで選択可能です。");
        break;
      }
    }
  });

  function imagesDisplay(file) {
    var reader = new FileReader();
    reader.onload = function() {
      var img_src = $('<img>').attr('src',reader.result).attr('width', 100).attr("id", "images").attr("name", "images");
      $('#drug_image').append(img_src);
    }
    reader.readAsDataURL(file);
  }

  $("#drug_image").on("dragover",function(event){
    event.preventDefault();
  });
});