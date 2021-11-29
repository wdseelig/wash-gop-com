<?php
/**
* @file
* Contains \Drupal\wcgop_commerce\Form\WCGOPFECForm.php.
*/

namespace Drupal\wcgop_commerce\Form;

use Drupal\Core\Form\Formbase;
use Drupal\Core\Form\FormStateInterface;

/**
* Provides an FEC Form for WCGOP use.
*/
class WCGOPFECForm extends FormBase
{
  /**
   * (@inheritdoc)
   */
  public function getFormId()
  {
    return 'wcgop_commerce_fecform';
  }

  /**
   * (@inheritdoc)
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['email'] = array(
      '#title' => t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t("We'll use this email address to communicate with you about the event."),
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Proceed to Payment'),
    );
    return $form;

  }
}
