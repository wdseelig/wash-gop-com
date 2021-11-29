<?php

namespace Drupal\wcgop_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\Core\Ajax\AjaxResponse;

/**
 * Returns responses for wcgop_commerce routes.
 */
class WcgopCommerceController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  public function CartCount(){
    // Get the number of items in the cart
    /* @var CurrentStoreInterface $cs */
    $cs = \Drupal::service('commerce_store.current_store');
    /* @var CartProviderInterface $cpi */
    $cpi = \Drupal::service('commerce_cart.cart_provider');
    $cart = $cpi->getCart('default', $cs->getStore());
    $cartItems = $cart ? count($cart->getItems()) : 0;
    $response = new AjaxResponse();
    $response->setContent(json_encode($cartItems));
    return $response;
  }


}
