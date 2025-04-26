$(document).ready(function () {
  // Add to cart functionality
  $(".btn-add-to-cart").click(function () {
    const productId = $(this).data("product-id");
    const button = $(this);

    // Show loading state
    button.html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...'
    );
    button.prop("disabled", true);

    $.ajax({
      url: "ajax/add_to_cart.php",
      method: "POST",
      data: { product_id: productId },
      success: function (response) {
        if (response.success) {
          // Update cart count
          $("#cart-count").text(response.cart_count);

          // Show success message
          showToast("Product added to cart!", "success");

          // Reset button state
          button.html("Add to Cart");
          button.prop("disabled", false);
        } else {
          showToast(
            response.message || "Error adding product to cart",
            "error"
          );
          button.html("Add to Cart");
          button.prop("disabled", false);
        }
      },
      error: function () {
        showToast("Error adding product to cart", "error");
        button.html("Add to Cart");
        button.prop("disabled", false);
      },
    });
  });

  // Update cart quantity
  $(".update-quantity-form").submit(function (e) {
    e.preventDefault();
    const form = $(this);
    const quantity = form.find('input[name="quantity"]').val();

    $.ajax({
      url: "ajax/update_cart.php",
      method: "POST",
      data: {
        cart_id: form.find('input[name="cart_id"]').val(),
        quantity: quantity,
      },
      success: function (response) {
        if (response.success) {
          // Reload the page to show updated cart
          location.reload();
        } else {
          showToast(response.message || "Error updating cart", "error");
        }
      },
      error: function () {
        showToast("Error updating cart", "error");
      },
    });
  });

  // Remove item from cart
  $(".remove-item-form").submit(function (e) {
    e.preventDefault();
    const form = $(this);

    if (confirm("Are you sure you want to remove this item?")) {
      $.ajax({
        url: "ajax/remove_from_cart.php",
        method: "POST",
        data: {
          cart_id: form.find('input[name="cart_id"]').val(),
        },
        success: function (response) {
          if (response.success) {
            // Remove the item from the DOM
            form.closest(".cart-item").fadeOut(300, function () {
              $(this).remove();
              // Update cart count
              $("#cart-count").text(response.cart_count);

              // If cart is empty, show message
              if ($(".cart-item").length === 0) {
                location.reload();
              }
            });
          } else {
            showToast(response.message || "Error removing item", "error");
          }
        },
        error: function () {
          showToast("Error removing item", "error");
        },
      });
    }
  });

  // Search functionality
  $(".search-form").submit(function (e) {
    e.preventDefault();
    const query = $(this).find('input[name="query"]').val();
    window.location.href = `products.php?search=${encodeURIComponent(query)}`;
  });

  // Toast notification function
  function showToast(message, type = "success") {
    const toast = $(`
            <div class="toast align-items-center text-white bg-${
              type === "success" ? "success" : "danger"
            } border-0" 
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);

    $(".toast-container").append(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after it's hidden
    toast.on("hidden.bs.toast", function () {
      $(this).remove();
    });
  }

  // Initialize tooltips
  $('[data-bs-toggle="tooltip"]').tooltip();

  // Handle responsive navigation
  $(".navbar-toggler").click(function () {
    $(".navbar-collapse").toggleClass("show");
  });

  // Close mobile menu when clicking outside
  $(document).click(function (e) {
    if (!$(e.target).closest(".navbar").length) {
      $(".navbar-collapse").removeClass("show");
    }
  });

  // Smooth scroll for anchor links
  $('a[href^="#"]').click(function (e) {
    e.preventDefault();
    const target = $($(this).attr("href"));
    if (target.length) {
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 70,
        },
        500
      );
    }
  });
});
