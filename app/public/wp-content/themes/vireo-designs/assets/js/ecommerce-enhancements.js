jQuery(document).ready(function($) {
    // Update cart count after add to cart
    $(document.body).on("added_to_cart", function() {
        updateCartCount();
    });
    
    // Update cart count function
    function updateCartCount() {
        $.ajax({
            url: vireo_ajax.ajax_url,
            type: "POST",
            data: {
                action: "update_cart_count",
                nonce: vireo_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $(".cart-count").text(response.data.count);
                    if (response.data.count > 0) {
                        $(".cart-count").show();
                    } else {
                        $(".cart-count").hide();
                    }
                }
            }
        });
    }
    
    // Initial cart count update
    updateCartCount();
    
    // Cart icon animation on hover
    $(".cart-link").hover(
        function() {
            $(this).find("i").addClass("fa-bounce");
        },
        function() {
            $(this).find("i").removeClass("fa-bounce");
        }
    );
    
    // Smooth scroll to top after add to cart
    $(document.body).on("added_to_cart", function() {
        $("html, body").animate({
            scrollTop: 0
        }, 800);
        
        // Show temporary success message
        if ($(".cart-success-message").length === 0) {
            $("body").prepend("<div class=\"cart-success-message\">✅ Item added to cart! <a href=\"" + vireo_ajax.cart_url + "\">View Cart</a></div>");
            setTimeout(function() {
                $(".cart-success-message").fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
});

// Add success message styles
var successMessageCSS = `
.cart-success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    font-weight: 600;
    animation: slideInRight 0.5s ease;
}

.cart-success-message a {
    color: white;
    text-decoration: underline;
    margin-left: 10px;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
`;

// Inject CSS into head
if ($("#cart-success-css").length === 0) {
    $("head").append("<style id=\"cart-success-css\">" + successMessageCSS + "</style>");
}