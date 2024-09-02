jQuery(document).ready(function ($) {
  // for taxonomy logo starts
  var mediaUploader;
  $("#taxonomy-image-upload-button").click(function (e) {
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    });
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#taxonomy-image-id").val(attachment.id);
      $("#taxonomy-image-wrapper").html(
        '<img src="' + attachment.url + '" style="max-width:100%;"/>'
      );
    });
    mediaUploader.open();
  });

  $("#taxonomy-image-remove-button").click(function () {
    $("#taxonomy-image-id").val("");
    $("#taxonomy-image-wrapper").html("");
  });

  // for taxonomy logo ends

  // product logo and audion starts
  function wp_media_uploader(button, inputId, isImage) {
    var frame = wp.media({
      title: button.data("title"),
      button: { text: button.data("button") },
      multiple: false,
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      $(inputId).val(attachment.id); // Save the ID instead of the URL

      var description = "";

      if (isImage) {
        description =
          '<img src="' +
          attachment.url +
          '" style="max-width: 150px; height: auto;" />';
      } else if (attachment.type === "audio") {
        description = '<audio controls src="' + attachment.url + '"></audio>';
      } else {
        description =
          '<a href="' + attachment.url + '">' + attachment.url + "</a>";
      }

      $(inputId).siblings("p.description").html(description);
    });

    frame.open();
  }

  $("#upload_pronounce_audio").click(function (e) {
    e.preventDefault();
    wp_media_uploader($(this), "#pronounce_audio_url", false);
  });

  $("#remove_pronounce_audio").click(function (e) {
    e.preventDefault();
    $("#pronounce_audio_url").val("");
    $(this).siblings("p.description").html('<?php _e("No file selected"); ?>');
  });

  $("#upload_logo_image").click(function (e) {
    e.preventDefault();
    wp_media_uploader($(this), "#logo_image_url", true);
  });

  $("#remove_logo_image").click(function (e) {
    e.preventDefault();
    $("#logo_image_url").val("");
    $(this).siblings("p.description").html('<?php _e("No image selected"); ?>');
  });

  // product logo and audion ends

  // for displaying error msg when sale price is greater than regular price
  $(".domainSalePrice input").on("keyup", function () {
    regularPrice = parseFloat($(".domainRegularPrice input").val());
    salePrice = parseFloat($(".domainSalePrice input").val());
    console.log(salePrice);
    if (salePrice > regularPrice) {
      $(".wstr-error-msg").show();
      $(".wstr-error-msg").text(
        "Please enter the value less than regular price"
      );
    } else {
      $(".wstr-error-msg").hide();
    }
  });

  // for displaying error message if rating is greater that 5.
  $(".domainSeo input").on("keyup", function () {
    if ($(this).val() > 5) {
      $(".wstr-error-msg").show();
      $(".wstr-error-msg").text("Rating cannot be greater than 5.");
    } else {
      $(".wstr-error-msg").hide();
    }
  });

 
});
