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
});
