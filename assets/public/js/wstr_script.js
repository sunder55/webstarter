// scripts
jQuery(document).ready(function ($) {
  $(".ws_mega_menu").css("display", "none");
  $("h2").on("click", function () {
    $(".ws_mega_menu").toggle();
    $(this).toggleClass("active");
  });

  // getting selected currrency value
  $("#wstr-mulitcurrency").change(function () {
    var currency = $(this).val();
    $.ajax({
      type: "post",
      dataType: "json",
      url: cpmAjax.ajax_url,
      data: {
        action: "set_currency_session",
        currency: currency,
      },
      success: function (response) {
        if (response.data) {
          location.reload();
        }
      },
    });
  });


  // When the magnifying glass is clicked make the image whole screen single domain page  =================================
  $('.fa-magnifying-glass').on('click', function () {
    const imageSrc = $('.featured-image img').attr('src');

    // Set the image source in the modal
    $('#modalImage').attr('src', imageSrc);

    $('#imageModal').fadeIn();
  });

  $('.close').on('click', function () {
    $('#imageModal').fadeOut();
  });
  $(window).on('click', function (e) {
    if ($(e.target).is('#imageModal')) {
      $('#imageModal').fadeOut();
    }
  });

  // zoom feature on hover ====================================
  $(".img_producto_container")
    .on("mouseover", function () {
      $(this)
        .children(".img_producto")
        .css({ transform: "scale(" + $(this).attr("data-scale") + ")" });
    })
    .on("mouseout", function () {
      $(this)
        .children(".img_producto")
        .css({ transform: "scale(1)" });
    })
    .on("mousemove", function (e) {
      $(this)
        .children(".img_producto")
        .css({
          "transform-origin":
            ((e.pageX - $(this).offset().left) / $(this).width()) * 100 +
            "% " +
            ((e.pageY - $(this).offset().top) / $(this).height()) * 100 +
            "%"
        });
    });
});



jQuery('.swiper-wrapper').slick({

  centerMode: true,
  centerPadding: '100px',
  slidesToShow: 4,
  slidesToScroll: 1,
  infinite: true,
  arrows: false,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 3
      }
    },
    {
      breakpoint: 600,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
      }
    }
  ]
});



