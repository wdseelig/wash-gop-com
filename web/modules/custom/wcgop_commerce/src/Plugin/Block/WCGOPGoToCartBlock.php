<?php

namespace Drupal\wcgop_commerce\Plugin\Block;

use Drupal\commerce_cart\CartProviderInterface;
use Drupal\Core\Block\BlockBase;
use UncacheableDependencyTrait;

/**
 * Provides a WCGOP Commerce Block that return a url to
 * shopping link
 * @Block(
 *   id = "wcgop_commerce_gotocartblock",
 *   admin_label = @Translation("WCGOP Commerce Go To Cart  Block"),
 *   )
 */

  class WCGOPGoToCartBlock extends BlockBase {
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
     $build = [];
     // Make the block non-cacheable
     \Drupal::service('page_cache_kill_switch')->trigger();
     $build['#cache']['max-age'] = 0;
     $build['#cache']['contexts'] = [];
     $build['#cache']['tags'] = [];
     // Get the number of items in the cart
     /* @var CurrentStoreInterface $cs */
     $cs = \Drupal::service('commerce_store.current_store');
     /* @var CartProviderInterface $cpi */
     $cpi = \Drupal::service('commerce_cart.cart_provider');
     $cart = $cpi->getCart('default', $cs->getStore());
     $cartItems = $cart ? count($cart->getItems()) : 0;
     $gotocarturl = "<a href=/cart>" .  "<b class='wcgop-cart-link'>Go To Cart</b></a>";
     $build ['content'] = [ '#markup' => $gotocarturl];
     return $build;
     }
   }
