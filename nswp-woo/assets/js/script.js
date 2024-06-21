"use strict";

(function ($, document, window) {
  $(".nswp-btn-wrapper .pricing-btn-switcher").on("click", function () {
    $(".pricing-btn-switcher").removeClass("active-btn");

    var action = $(this).attr("data-cat");

    $(this).addClass("active-btn");

    $.ajax({
      url: ns_ajax_url.url,
      type: "POST",
      data: {
        action: "pricing_table_switcher",
        category: action,
      },

      success: function (response) {
        const { products } = response;
        let renderHtml = "";

        if (!products?.length) {
          return false;
        }

        for (let product of products) {
          const { id, short_name, price, currency_symbol, desc, name } =
            product;

          let image_url = "/wp-content/uploads/2024/04/House.png";

          if (short_name !== "Basic") {
            image_url =
              short_name === "Standard"
                ? "/wp-content/uploads/2024/04/Villa.png"
                : "/wp-content/uploads/2024/04/Castle.png";
          }

          renderHtml += `<div class="pricing-item-wrapper">
          <img src='${image_url}' alt="${name}" />
                <h3>${short_name}</h3>
                <p class="nswp-price-label">
                    <sup>${currency_symbol}</sup> 
                    <span class="animate-digits">${price}</span>
                    <sub>/ month</sub>
                </p>
                <p class="font-sm">${price * 12} /year</p>

                <hr />
                ${desc}

                <button class='ns-addToCart-btn' product-id='${id}'>Add to cart</button>
            </div>`;
        }

        $(".nswp-pricing-container").html(renderHtml);
      },
    });
  });

  $(document).on("click", ".cart-btns .cart-continue", function () {
    window.location.href = "/cart";
  });

  $(document).on("click", ".cart-btns .cart-close-modal", function () {
    $(".cart-added-modal-container").remove();
    $("body").css("overflow", "auto");
  });

  $(document).on("click", ".ns-addToCart-btn", function () {
    let productId = $(this).attr("product-id");
    let package_name = $(this)
      .closest(".pricing-item-wrapper")
      .find("h3")
      .text();

    $.ajax({
      url: ns_ajax_url.url,
      type: "POST",
      data: {
        action: "pricing-add-to-cart",
        product_id: productId,
      },

      success: function () {
        $("body").css("overflow", "hidden");
        let time_plan = $(".pricing-btn-switcher.active-btn").text();

        $(".site--header").append(
          `<div class="cart-added-modal-container">
            <div class="cart-added-modal">
            <p><strong>${package_name}; ${time_plan} plan</strong></p>
            <p>has been added to your cart.</p>

            <div class="cart-btns">
              <button class="cart-continue">Continue to cart</button>
              <button class="cart-close-modal">Continue browsing</button>
            </div>
          </div>
        </div>`
        );
      },
    });
  });
})(jQuery, document, window);
