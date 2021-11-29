<?php

namespace Drupal\wcgop_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use \Drupal\Core\Routing;


/**
 * Provides a custom wcgop checkout pane.
 *
 * @CommerceCheckoutPane(
 *   id = "wcgop_commerce_checkout_pane",
 *   label = @Translation("WCGOP commerce checkout pane"),
 * )
 */
class WCGOPCheckoutPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $current_uri = \Drupal::request()->getRequestUri();
    $pane_form['body'] = array(
      '#markup' => "<p>Federal and State law require us to collect this information.  Please fill in the form
      below and proceed to the payment process by clicking on the 'Proceed to Payment' button at the
      bottom of the form</p>",
      '#weight' => 0
    );
    $pane_form['customer_info'] = [
      '#type' => 'address',
      '#used_fields' => [
        AddressField::GIVEN_NAME,
        AddressField::FAMILY_NAME,
        AddressField::ADDRESS_LINE1,
        AddressField::ADDRESS_LINE2,
        AddressField::ADMINISTRATIVE_AREA,
        AddressField::LOCALITY,
        AddressField::POSTAL_CODE,
      ],
      '#default_value' => ['country_code' => 'US'],
   //   '#available_countries' => ['US'],
    ];
    $pane_form['moreinfo'] = array(
      '#type' => 'fieldset',
    );
    $pane_form['moreinfo']['email'] = array(
      '#title' => t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t("We'll use this email address to communicate with you about the event."),
      '#required' => TRUE,
    );
    $pane_form['moreinfo']['occupation'] = array(
      '#title' => t('Occupation'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t("Enter 'Retired' if you are no longer working"),
      '#required' => TRUE,
    );
    $pane_form['body2'] = array(
      '#markup' => "<p>If you are retired, self-employed or do not have an employer, please select NO and go on
       to the next checkout step.  If you do have an employer, choose YES and fill in the additional
       information.</p>",
      '#weight' => 4
    );
    $pane_form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Proceed to Payment'),
      '#weight' => 10
    );
    return $pane_form;
  }
  public function submitPaneForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state, &$complete_form) {
    $current_uri = \Drupal::request()->getRequestUri();
    /* @var $order \Drupal\commerce_order\Entity\OrderInterface */
    $order = \Drupal::routeMatch()->getParameter('commerce_order');
    $ordernumber = $order->get('order_id')->getValue();
    // Retrieve values from the WCGOP form; use them to create a profile; attach that profile to the order
   $values = $form_state->getValues();
   /* @var $profile \Drupal\profile\Entity\Profile */
    // Note that this is an invocation of a static method ultimately found in EntityBase
    $profile = \Drupal\profile\Entity\Profile::create(['type' => 'wcgop_customer']);
   $profile->set('address', $values['wcgop_commerce_checkout_pane']['customer_info']);
   // @TODO Need to get the rest of the FEC information here
   $profile->save();
   // @TODO Now attach this profile to the order and dispatch to payment
    $order->setBillingProfile($profile);
    $order->save();
    $orderid = $order->get('order_id')->getValue();
    $intorderid = $orderid[0]["value"];
    $routeparameters = ['commerce_order' => $intorderid, 'step' => 'order_information'];
    $form_state->setRedirect('wcgop_commerce_gotopayment',$routeparameters);
   return;
  }
}
