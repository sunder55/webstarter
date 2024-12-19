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
  $(".fa-magnifying-glass").on("click", function () {
    const imageSrc = $(".featured-image img").attr("src");

    // Set the image source in the modal
    $("#modalImage").attr("src", imageSrc);

    $("#imageModal").fadeIn();
  });

  $(".close").on("click", function () {
    $("#imageModal").fadeOut();
  });
  $(window).on("click", function (e) {
    if ($(e.target).is("#imageModal")) {
      $("#imageModal").fadeOut();
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
      $(this).children(".img_producto").css({ transform: "scale(1)" });
    })
    .on("mousemove", function (e) {
      $(this)
        .children(".img_producto")
        .css({
          "transform-origin":
            ((e.pageX - $(this).offset().left) / $(this).width()) * 100 +
            "% " +
            ((e.pageY - $(this).offset().top) / $(this).height()) * 100 +
            "%",
        });
    });

  // ============================ resend otp starts
  $(".resend_otp").on("click", function () {
    const user_id = $(this).attr("id");
    $("#resend_error").text("");
    $("#resend_success").text("");
    $.ajax({
      type: "post",
      dataType: "json",
      url: cpmAjax.ajax_url,
      data: {
        action: "wstr_resend_otp",
        userId: user_id,
      },
      success: function (response) {
        console.log("responose", response);
        if (response.data) {
          console.log(response.data);
          if (response.data.type == "failed") {
            $("#resend_error").text(response.data.message);
          }
          if (response.data.type == "success") {
            $("#resend_success").text(response.data.message);
          }
          // location.reload();
        }
      },
    });
  });
  // otp inputs
  const inputs = document.getElementById("otp_inputs");

  inputs.addEventListener("input", function (e) {
    const target = e.target;
    const val = target.value;

    if (isNaN(val)) {
      target.value = "";
      return;
    }

    if (val != "") {
      const next = target.nextElementSibling;
      if (next) {
        next.focus();
      }
    }
  });

  inputs.addEventListener("keyup", function (e) {
    const target = e.target;
    const key = e.key.toLowerCase();

    if (key == "backspace" || key == "delete") {
      target.value = "";
      const prev = target.previousElementSibling;
      if (prev) {
        prev.focus();
      }
      return;
    }
  });

  // pasting mulitple numbers starts
  var $inputs = $(".input");
  var intRegex = /^\d+$/;

  // Prevents user from manually entering non-digits.
  $inputs.on("input.fromManual", function () {
    if (!intRegex.test($(this).val())) {
      $(this).val("");
    }
  });

  // Prevents pasting non-digits and if value is 6 characters long will parse each character into an individual box.
  $inputs.on("paste", function () {
    var $this = $(this);
    var originalValue = $this.val();

    $this.val("");

    $this.one("input.fromPaste", function () {
      $currentInputBox = $(this);

      var pastedValue = $currentInputBox.val();

      if (pastedValue.length == 6 && intRegex.test(pastedValue)) {
        pasteValues(pastedValue);
      } else {
        $this.val(originalValue);
      }

      $inputs.attr("maxlength", 1);
    });

    $inputs.attr("maxlength", 6);
  });

  // Parses the individual digits into the individual boxes.
  function pasteValues(element) {
    var values = element.split("");

    $(values).each(function (index) {
      var $inputBox = $('.input[name="otp_input' + (index + 1) + '"]');
      $inputBox.val(values[index]);
    });
  }

  // pasting mulitple numbers ends

  // ============================ resend otp ends
});

jQuery(".swiper-wrapper").slick({
  centerMode: true,
  centerPadding: "100px",
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
        centerPadding: "40px",
        slidesToShow: 3,
      },
    },
    {
      breakpoint: 600,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "40px",
        slidesToShow: 1,
      },
    },
  ],
});

// trending cards aniamtion effect

jQuery(document).ready(function ($) {
  var $container = $(".ws_home_trending_cards .ws-cards-container-wrapper");
  var $contents = $container.html();

  $container.html('<div class="scrolling">' + $contents + "</div>");

  var $scrolling = $container.find(".scrolling");

  // Clone the contents for seamless scrolling (optional)
  for (let i = 0; i < 1; i++) {
    $scrolling.append($scrolling.children().clone());
  }

  var isPaused = false; // Track the paused state

  $(".toggleMarquee").on("click", function () {
    isPaused = !isPaused;

    // Update button icon based on the paused state
    if (isPaused) {
      $(this).html('<i class="fa-solid fa-circle-play"></i>');
      $scrolling.css("animation-play-state", "paused");
    } else {
      $(this).html('<i class="fa-regular fa-circle-pause"></i>');
      $scrolling.css("animation-play-state", "running");
    }
  });

  // Add mouse grab scrolling functionality
  let isDragging = false;
  let startX;
  let scrollLeft;

  // Mouse down event to initiate dragging
  $container.on("mousedown", function (e) {
    isDragging = true;
    startX = e.pageX - $container.offset().left; // Get the mouse position relative to the container
    scrollLeft = $container.scrollLeft(); // Get the current scroll position
    $container.css("cursor", "grabbing"); // Change cursor to grabbing
  });

  // Mouse leave event to stop dragging
  $container.on("mouseleave", function () {
    isDragging = false;
    $container.css("cursor", "grab"); // Reset cursor
  });

  // Mouse up event to stop dragging
  $container.on("mouseup", function () {
    isDragging = false;
    $container.css("cursor", "grab"); // Reset cursor
  });

  // Mouse move event to handle the dragging
  $container.on("mousemove", function (e) {
    if (!isDragging) return; // Do nothing if not dragging
    e.preventDefault(); // Prevent default text selection
    const x = e.pageX - $container.offset().left; // Get the current mouse position
    const walk = (x - startX) * 2; // Calculate distance to scroll
    $container.scrollLeft(scrollLeft - walk); // Scroll the container
  });

  $scrolling.addClass("scrolling-animation");

  // var $container = $('.ws_home_trending_cards .ws-cards-container-wrapper');
  // var $contents = $container.html();

  // $container.html('<div class="scrolling">' + $contents + '</div>');

  // var $scrolling = $container.find('.scrolling');

  // for (let i = 0; i < 1; i++) {
  //   $scrolling.append($scrolling.children().clone());
  // }

  // var isPaused = false; // Track the paused state

  // $('.toggleMarquee').on('click', function () {
  //   isPaused = !isPaused;

  //   // Update button icon based on the paused state
  //   if (isPaused) {
  //     $(this).html('<i class="fa-solid fa-circle-play"></i>');
  //     $scrolling.css('animation-play-state', 'paused');
  //   } else {
  //     $(this).html('<i class="fa-regular fa-circle-pause"></i>');
  //     $scrolling.css('animation-play-state', 'running');
  //   }

  // });

  // $scrolling.addClass('scrolling-animation');

  // favourite section ===========================
  $(".ws-card-likes i").on("click", function () {
    var $this = $(this);
    var domainId = $(this).closest(".ws-card-likes").attr("id");

    // Get the current count from the span (handle both number and 'K' format)
    var countText = $this.closest(".ws-card-likes").find("span").text().trim();
    var count = 0;

    // Check if the count is in the 'K' format and convert to a number
    if (countText.includes("K")) {
      count = parseFloat(countText.replace("K", "")) * 1000;
    } else {
      count = parseInt(countText);
    }

    $.ajax({
      type: "post",
      dataType: "json",
      url: cpmAjax.ajax_url,
      data: {
        action: "wstr_favourite",
        domain_id: domainId,
      },
      success: function (response) {
        if (response.success == true) {
          // console.log(response.data);
          if (response.data.count == "deduct") {
            count = Math.max(0, count - 1); // Prevent negative count
          } else {
            // Add to the count
            count++;
          }

          // Update the displayed count (convert back to 'K' format if necessary)
          if (count >= 1000) {
            $this
              .closest(".ws-card-likes")
              .find("span")
              .text((count / 1000).toFixed(1) + "K");
          } else {
            $this.closest(".ws-card-likes").find("span").text(count);
          }
        }
      },
    });
  });

  // // password validation
  $("#wstr_signup").on("submit", function (e) {
    var password = $("#password").val();
    var confirmPassword = $("#confirm-password").val();
    var errorMessage = "";

    // Password validation
    if (password.length < 8) {
      errorMessage = "Password must be at least 8 characters long.";
    } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
      errorMessage = "Password must contain at least one special character.";
    } else if (password !== confirmPassword) {
      errorMessage = "Passwords do not match.";
    }

    if (errorMessage !== "") {
      $("#error-msg").text(errorMessage);
      e.preventDefault();
    } else {
      $("#error-msg").text("");
      // Submit the form if validation passes
      // this.submit();
    }
  });
});
