function render_code_tr($, code, price, symbol) {
  const render_tr = `
                    <tr class="woocommerce-cart-form__cart-item cart_item nswp-coupon-code-tr product-coupun-${code}">
                        <td class="coupon-product-remove">
                            <a class="remove_coupon_code" href="#" aria-label="coupun code" coupon_code="${code}" data-product_sku="">&times;</a>
                        </td>
                        <td class="product-name" data-title="Product">Code:&nbsp;${code}</td>
                <td></td>
                        <td class="product-price" data-title="Prijs">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">-${symbol}</span>&nbsp;${price}</bdi></span>
                        </td>
                        <td class="product-subtotal" data-title="Subtotaal">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">-${symbol}</span>&nbsp;${price}</bdi></span>
                        </td>
                    </tr>
                `;

  $("#nswp-wc-cart-coupon .woocommerce-cart-form tbody tr:has(td.actions)")
    .first()
    .before(render_tr);
}

function nswp_display_message($, isSuccess, coupon) {
  const message = isSuccess
    ? `The coupon code ${coupon} has been applied`
    : "The coupon code you entered does not work, please try again.";

  if ($("td.nswp_shown_message")) {
    $("td.nswp_shown_message").remove();
  }

  const render_html = `
      <tr>
          <td colspan="6" class="nswp_shown_message">
              <div class="nswp_msg_box ${
                isSuccess ? "success_msg" : "error_msg"
              }" >
                  <p>${message}</p>
              </div>
          </td>
      </tr>
      `;

  $("#nswp-wc-cart-coupon .woocommerce-cart-form tbody tr:has(td.actions)")
    .first()
    .after(render_html);
}

function nswp_coupon_apply($) {
  // Use the localized data
  var apiUrl = nswpRestApi.url + "/verify-coupon";
  var apiNonce = nswpRestApi.nonce;

  $("#nswp-wc-cart-coupon button[name='apply_coupon']").on(
    "click",
    function (e) {
      if (e.preventDefault) e.preventDefault();
      var form = $("#nswp-wc-cart-coupon .woocommerce-cart-form");
      var couponCode = form.find('input[name="coupon_code"]').val();

      if (!couponCode) return;

      // Perform an AJAX call to verify the coupon before applying
      $.ajax({
        url: apiUrl,
        method: "POST",
        data: {
          coupon_code: couponCode,
          nonce: apiNonce,
        },
        success: function (response) {
          if (response.amount) {
            const { code, amount, currency_symbol } = response;
            render_code_tr($, code, amount, currency_symbol);
            nswp_display_message($, true, code);
          } else {
            console.warn("Warning: ", response);
          }
        },
        error: function (response) {
          // Show an error message
          console.warn("There was an error verifying the coupon.");
          nswp_display_message($, false, response.code);
        },
      });
    }
  );
}

function render_coupon_code_fields($) {
  let apiUrl = nswpRestApi.url + "/cart-coupons";

  $.ajax({
    url: apiUrl,
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response) {
        for (let data of response) {
          const { code, amount, currency_symbol } = data;

          render_code_tr($, code, amount, currency_symbol);
        }
      } else {
        console.warn("Warning: No data found");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}

function nswp_remove_coupon($) {
  $(document).on(
    "click",
    ".nswp-coupon-code-tr .coupon-product-remove .remove_coupon_code",
    function (e) {
      if (e.preventDefault) {
        e.preventDefault();
      } else {
        e.returnValue = true;
      }
      let couponCode = $(this).attr("coupon_code");

      let apiUrl = nswpRestApi.url + "/remove-coupon";

      $.ajax({
        url: apiUrl,
        method: "POST",
        data: {
          coupon_code: couponCode,
        },
        success: function (response) {
          if (response.success) {
            $(`.product-coupun-${couponCode}`).remove();
            if ($("td.nswp_shown_message")) {
              $("td.nswp_shown_message").remove();
            }
          } else {
            console.warn("Error: Something went wrong");
          }
        },
        error: function (response) {
          // Show an error message
          console.warn("There was an error removing the coupon.");
        },
      });
    }
  );
}

function render_cart_field_amount($) {
  $("#nswp-wc-cart-coupon thead th.product-name").after(
    '<th class="product-amount">Amount</th>'
  );

  $("#nswp-wc-cart-coupon tbody td.product-name").each(function () {
    var parentTr = $(this).closest("tr.cart_item");
    var product_id = $(parentTr)
      .find(".product-remove a.remove[data-product_id]")
      .attr("data-product_id");
    var product_quantity = $(parentTr)
      .find('.product-quantity input[type="number"]')
      .val();

    $(this).after(`
        <td class="product-amount-field">
            <div data-product_iid="${product_id}" class="field-amount-data" name="product_increment">${product_quantity}</div>
            <div class="amount_actions">
               <span action='plus' class="amount_up arrow-btn">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="5" stroke="currentColor" class="size-6">
  			   <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
		     </svg>
               </span>
               <span action='minus' class="amount_dec arrow-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="5" stroke="currentColor" class="size-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
                </span> 
           </div>
        </td>`);
  });
}

function cart_amount_action_func() {
  $(document).on("click", ".product-amount-field .arrow-btn", function () {
    var action = $(this).attr("action");
    if (!action) return;

    const productId = $(this)
      .closest(".product-amount-field")
      .find(".field-amount-data")
      .attr("data-product_iid");

    if (action === "plus") {
      updateQuantity($, productId, "increment", 1);
    } else if (action === "minus") {
      updateQuantity($, productId, "decrement", 1);
    }
  });
}

function updateQuantity($, productId, action, quantity) {
  $.ajax({
    url: nswpRestApi.url + "/cart" + "/update_quantity",
    method: "POST",
    data: {
      action: action,
      quantity: quantity,
      product_id: productId,
    },
    success: function (response) {
      location.reload();
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function cartConfigCoreFunc($) {
  nswp_coupon_apply($);
  render_coupon_code_fields($);
  nswp_remove_coupon($);

  render_cart_field_amount($);
  cart_amount_action_func($);
}

jQuery(document).ready(function ($) {
  cartConfigCoreFunc($);
});

jQuery(document.body).on("updated_cart_totals wc_cart_emptied", function (e) {
  if (e.type === "wc_cart_emptied") {
    window.location.href = window.location.host + "/packages";
  } else if (e.type === "updated_cart_totals") {
    window.location.reload();
  }
});
