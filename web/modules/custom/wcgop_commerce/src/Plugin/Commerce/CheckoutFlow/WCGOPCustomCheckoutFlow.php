<?php

namespace Drupal\wcgop_commerce\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @CommerceCheckoutFlow(
 *  id = "wcgop_custom_checkout_flow",
 *  label = @Translation("WCGOP Custommmm checkout flow"),
 * )
 */
class WCGOPCustomCheckoutFlow extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    return [
      'order_information' => [
        'label' => $this->t('OrderInfoooo'),
        'next_label' => $this->t('Review your Order'),
        'has_order_summary' => FALSE,
      ],
      'review' => [
        'label' => $this->t('Reviewwww'),
        'previous_label' => $this->t('Review Your Order'),
        'next_label' => $this->t('Review yourrrr Order'),
        'has_order_summary' => FALSE,
      ],
      'payment' => [
        'label' => $this->t('Payyyyment'),
        'next_label' => $this->t('Pay and or try to  complete purchase'),
        'has_order_summary' => FALSE,
      ],
      'complete' => [
        'label' => $this->t('Commmmplete'),
        'next_label' => $this->t('Pay andddd complete purchase'),
        'has_order_summary' => TRUE,
      ],
    ];
  }

}
