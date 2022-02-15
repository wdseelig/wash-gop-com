<?php

namespace Drupal\myvotersearch\Form;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;


/**
 * Provides a VoterSearch form.
 */
class VoterSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'votersearch_voter_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['LastName'] = [
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => 30,
      '#title' => $this->t('Last Name'),
    ];
    $form['FirstName'] = [
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => 30,
      '#title' => $this->t('First  Name'),
    ];
    $form['Address'] = [
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => 30,
      '#title' => $this->t('Address'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Click here to select the QVF+ voters who match your criteria'),
    ];
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $x = 1;
    $ln = $form_state->getValue('LastName') ? $form_state->getValue('LastName') : 'NULL';
    $fn = $form_state->getValue('FirstName') ? $form_state->getValue('FirstName') : 'NULL';
    $addr = $form_state->getValue('Address') ?  $form_state->getValue('Address') : 'NULL';
    $routeparams = ['fromform' => 'YES', 'lastname' => $ln, 'firstname' => $fn, 'address' => $addr];

  //  $form_state->setRedirect('votersearch.content', $routeparams);
    $ra = [
      '#type' => 'item',
      '#markup' => '<h2>This is text from the submit routine</h2>',
    ];
    return $ra;
  }
  public function ajaxCallback(&$form, FormStateInterface $form_state) {

    $myresponse = new AjaxResponse();
    $selector = '#block-claro-content';
    $content = '<h2>This is the replacement text</h2>';
    $text = 'This is my response from the submit routine';
    $myresponse->addCommand(new AfterCommand($selector,$content));
   return $myresponse;
  }
}
