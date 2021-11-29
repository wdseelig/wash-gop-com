(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.cartitemCount = {
    attach: function (context, drupalSettings) {

      $.ajax({
        url: "/wcgop_commerce/cartcount",
        method: "GET",
        headers: {
          "Content-Type": "application/hal+json"
        },
        success: function(data, status, xhr) {
         drupalSettings.cartCount = data;

          $('.wcgop-cart-link').each(function(i, field){
            this.innerHTML='Go To Cart (' + drupalSettings.cartCount + ")";
          });
        }
      })
    }
  };
})(jQuery, Drupal, drupalSettings);
