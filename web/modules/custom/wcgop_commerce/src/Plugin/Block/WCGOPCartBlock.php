<?php

namespace Drupal\wcgop_commerce\Plugin\Block;

use Drupal\commerce_cart\CartProviderInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a WCGOP Commerce Block that return a url to
 * shopping link
 * @Block(
 *   id = "wcgop_commerce_block",
 *   admin_label = @Translation("WCGOP Commerce Block"),
 *   )
 */

  class WCGOPCartBlock extends BlockBase {
  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
  protected $cartProvider;
  /**
   * Constructs a new wcgop_commerce block object.
   *
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.

    public function __construct(CartProviderInterface $cart_provider) {
      $this->cartProvider = $cart_provider;
    }

    /**
     * {@inheritdoc}

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('commerce_cart.cart_provider')
      );
    }
    */
   public function build() {
     $x = 1;
     // Make the block non-cacheable
     \Drupal::service('page_cache_kill_switch')->trigger();
     // This is an attempt to get the number of items in the cart (for use in the block Go To Cart)
     /* @var CurrentStoreInterface $cs */
     $cs = \Drupal::service('commerce_store.current_store');
     /* @var CartProviderInterface $cpi */
     $cpi = \Drupal::service('commerce_cart.cart_provider');
     $cart = $cpi->getCart('default', $cs->getStore());
     $nbItemsInCart = $cart ? count($cart->getItems()) : 0;
     $session = \Drupal::request()->getSession();
     $shoppingurl = $session->get('shoppingurl', 'default');
     $shoppingurl = "<a href=" . $shoppingurl . ">" .  "<b>Continue Shopping</b>" . "</a>";

     return array("#markup" => $shoppingurl);
     }
   }
