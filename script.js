jQuery(document).ready(function ($) {
  // for getting domain age from the post title
  $("#title").on("change", function () {
    var domainName = jQuery("#title").val();
    /**
     * for getting domain age
     */
    $.ajax({
      type: "POST",
      url: cpmAjax.ajax_url,
      data: {
        action: "get_domain_age",
        domain_name: domainName,
      },
      success: function (response) {
        console.log(response);
        jQuery("#domainAge").val(response.data);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: ", textStatus, errorThrown);
      },
    });

    /**
     * for getting da/pa ranking
     */
    $.ajax({
      type: "POST",
      url: cpmAjax.ajax_url,
      data: {
        action: "get_domain_da_pa",
        domain_name: domainName,
      },
      success: function (response) {
        var daPaRanking = response.data.split("/");
        var da = daPaRanking[0];
        var pa = daPaRanking[1];
        jQuery("#domainDaPa").val(da + " / " + pa);
      },
    });
    getTLD(domainName);

    var domainLength = getDomainLength(domainName);
    if (domainLength) {
      $("#domainLength").val(domainLength);
    }
  });

  /**
   * function for displaying tld automatically for domain name
   * @param  url -> getting full domain url
   */
  function getTLD(url) {
    // Backend logic with jQuery
    var productTld = jQuery("#domainTld").find("select");
    var options_values = [];

    // Use jQuery .find("option") to get options
    productTld.find("option").each(function () {
      options_values.push(jQuery(this).val()); // getting all tld options values
    });

    // Use jQuery to iterate over options
    var domainParts = url.split(".");
    var tld = "." + domainParts[domainParts.length - 1];

    var tld_from_url = options_values.includes(tld);
    if (tld_from_url) {
      productTld.val(tld); // Use val() to set the value in jQuery
      // Trigger the change event
      productTld.trigger("change");
    }
  }

  /**
   * function for getting domain length
   */
  function getDomainLength(domain) {
    // Split the domain by the dot
    var parts = domain.split(".");

    // If there are more than two parts, we assume the last part is the TLD
    if (parts.length > 1) {
      // Remove the last part (TLD)
      parts.pop();
    }

    // Join the remaining parts and get the length
    var domainWithoutTLD = parts.join(".");
    return domainWithoutTLD.length;
  }

  // single domain tabs
  jQuery("ul.tabs li").on("click", function ($) {
    // get the data attribute
    var tab_id = jQuery(this).attr("data-tab");
    // remove the default classes
    jQuery("ul.tabs li").removeClass("current");
    jQuery(".tab-content").removeClass("current");
    // add new classes on mouse click
    jQuery(this).addClass("current");
    jQuery("#" + tab_id).addClass("current");
  });

  /** not pushed yet */
  /** select 2 js  */
  $("#industry").select2({
    placeholder: "Any",
  });
  $("#style").select2({
    placeholder: "Any",
  });

  $(
    "#industry, #style, #sort-by, #domain-type, #price-range-min, #price-range-max, #length-slider, #sort-by-price, #sort-by-list"
  ).on("change", function () {
    // console.log("changed", $(this).attr("id"));
    const targetedId = $(this).attr("id");
    loadDomains(1, targetedId); // Load the first page initially
  });

  function getQueryParam(param) {}
  // Handle pagination clicks
  $(document).on("click", ".pagination a", function (e) {
    e.preventDefault();
    // var paged = $(this).data("page"); // Get the page number from the link
    // Get the URL from the 'href' attribute
    var pagedUrl = $(this).attr("href");

    // Construct the full URL by combining with the current location
    const fullUrl = new URL(
      pagedUrl,
      window.location.origin + window.location.pathname
    );
    console.log(fullUrl);
    // Use URLSearchParams to extract the 'paged' parameter
    const urlParams = new URLSearchParams(fullUrl.search);
    var pageNumber = urlParams.get("paged");

    loadDomains(pageNumber);
  });

  function loadDomains(paged, targetedId) {
    const industry = $("#industry").val();
    const style = $("#style").val();
    const domainType = $("#domain-type").val();
    const lengthSlider = $("#length-slider").val();
    const sortBy = $("#sort-by").val();
    const minPrice = $("#price-range-min").val();
    const maxPrice = $("#price-range-max").val();
    const sortByPrice = $("#sort-by-price").val();
    const sortByList = $("#sort-by-list").val();

    $.ajax({
      method: "POST",
      url: cpmAjax.ajax_url,
      data: {
        action: "wstr_domain_filter",
        industry: industry,
        style: style,
        tld: domainType,
        length: lengthSlider,
        sortBy: sortBy,
        min_price: minPrice,
        max_price: maxPrice,
        sort_by_price: sortByPrice,
        sort_by_list: sortByList,
        targeted_id: targetedId,
        paged: paged, // Send the current page
      },
      beforeSend: function () {
        $("#buy-domain-main").html("<p>Loading...</p>");
      },
      success: function (response) {
        if (response.success) {
          if (response.data) {
            $("#buy-domain-main").html(response.data); // Update the domain list
          } else {
            $("#buy-domain-main").html("<p>No domains found.</p>");
          }
        } else {
          $("#buy-domain-main").html("<p>No domains found.</p>");
        }
      },
      error: function () {
        $("#buy-domain-main").html(
          "<p>Something went wrong. Please try again.</p>"
        );
      },
    });
  }

  function updateMaxOptions() {
    let minValue = parseInt($("#price-range-min").val()) || 0;
    $("#price-range-max option").each(function () {
      $(this).prop("disabled", parseInt($(this).val()) <= minValue);
    });
  }

  function updateMinOptions() {
    let maxValue = parseInt($("#price-range-max").val()) || Infinity;
    $("#price-range-min option").each(function () {
      $(this).prop("disabled", parseInt($(this).val()) >= maxValue);
    });
  }

  $("#price-range-min").on("change", function () {
    updateMaxOptions();
  });

  $("#price-range-max").on("change", function () {
    updateMinOptions();
  });

  function addToSelect2($select, term_id) {
    // Check if the term_id is already selected
    if (!$select.find(`option[value="${term_id}"]`).is(":selected")) {
      // Get current values or initialize as empty array
      var currentValues = $select.val() || [];

      // If currentValues is null or a single value, convert it to an array
      if (!Array.isArray(currentValues)) {
        currentValues = [currentValues];
      }

      // Add the new term_id to the array
      currentValues.push(term_id);

      // Set the new values for Select2
      $select.val(currentValues).trigger("change");
    }
  }

  $(".popular-searched-item").on("click", function () {
    let term_id = $(this).attr("id");
    let taxonomy = $(this).data("taxonomy");

    switch (taxonomy) {
      case "domain_cat":
        addToSelect2($("#style"), term_id);
        break;
      case "domain_industry":
        addToSelect2($("#industry"), term_id);
        break;
      default:
        console.log(`Unknown taxonomy: ${taxonomy}`);
    }
  });

  function getUrlParameter(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\#&]" + name + "=([^&#]*)");
    var results = regex.exec(document.location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

  // // Function to initialize Select2 and populate with URL parameter
  // function initAndPopulateSelect2($select, paramName) {
  //   // Initialize Select2 if it hasn't been initialized yet
  //   if (!$select.hasClass("select2")) {
  //     $select.select2({
  //       multiple: true,
  //       placeholder: "Select " + paramName.toLowerCase(),
  //       allowClear: true,
  //       // Add other options as needed
  //     });
  //   }

  //   // Get the parameter value
  //   var paramValue = getUrlParameter(paramName);

  //   // Check if the parameter exists and is valid
  //   if (
  //     paramValue &&
  //     $select.find(`option[value="${paramValue}"]`).length > 0
  //   ) {
  //     // Add the parameter value to the selection
  //     var currentValues = $select.val() || [];

  //     if (!Array.isArray(currentValues)) {
  //       currentValues = [currentValues];
  //     }

  //     currentValues.push(paramValue);
  //     $select.val(currentValues).trigger("change.select2");
  //   }
  // }

  // // Find the select elements
  // var $styleSelect = $("#style");
  // var $industrySelect = $("#industry");

  // // Check if the select elements exist and initialize/populate if needed
  // if ($styleSelect.length > 0 && $industrySelect.length > 0) {
  //   initAndPopulateSelect2($styleSelect, "style");
  //   initAndPopulateSelect2($industrySelect, "industry");

  //   console.log("URL parameters processed successfully.");
  // } else {
  //   console.error("Elements with ids 'style' and/or 'industry' not found");
  // }
});
