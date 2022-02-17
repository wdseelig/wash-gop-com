<?php

namespace Drupal\admincontacts\Form;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;



/**
 * Provides a form to search for voters' information.
 */
class VoterSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admincontacts_voter_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (!\Drupal::currentUser()->hasPermission('access admincontacts page'))
    {
      /*
       * Only logged in users can see the admincontacts search form
       */
      $build['content'] = [
        '#type' => 'item',
        '#markup' => $this->t('Only authorized users are allowed to access this information'),
      ];
      return $build;
    }
    $form['Header'] = [
      '#type' => 'item',
      '#prefix' => '<div class=contactsearch>',
      '#markup' => '<h3>Contact Selection Criteria</h3>',
      '#suffix' => '</div>',
    ];
    $form['LastName'] = [
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 30,
      '#title' => $this->t('Last Name'),
    ];
    $form['FirstName'] = [
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 30,
      '#title' => $this->t('First  Name'),
    ];
    $form['Address'] = [
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 30,
      '#title' => $this->t('Address'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#ajax' => [
        'callback' => '::searchajaxSubmit'
      ],
      '#value' => $this->t('Click here to select the QVF+ voters who match your criteria'),
    ];
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $ln = $form_state->getValue('LastName') ? $form_state->getValue('LastName') : 'NULL';
    $fn = $form_state->getValue('FirstName') ? $form_state->getValue('FirstName') : 'NULL';
    $addr = $form_state->getValue('Address') ? $form_state->getValue('Address') : 'NULL';
  }
  public function searchajaxSubmit(&$form, FormStateInterface $form_state) {
    $ln = $form_state->getValue('LastName');
    $fn = $form_state->getValue('FirstName');
    $addr = $form_state->getValue('Address');
    $contactdata_storage = \Drupal::entityTypeManager()->getStorage('contactdata');
    $result = \Drupal::entityQuery('contactdata', 'cd');
    $andGroup = $result->andConditionGroup()
      ->condition('field_firstname', '%' . $fn . '%', 'LIKE')
      ->condition('field_lastname', '%' . $ln . '%', 'LIKE')
      ->condition('field_primaryaddress1', '%' . $addr . '%' , 'LIKE');
    $result->condition($andGroup);
    $cidresult = $result->execute();
    $cids = $contactdata_storage->loadMultiple($cidresult);

    $rows = [];
    foreach ($cids as $value) {
      $url = Url::fromRoute('entity.contactdata.canonical',
        array( 'contactdata' => $value->get('id')->value));
      $url->setRouteParameter("acmain", "1");
      $url->setOptions(['attributes' => ['class' => ['c-link']]]);
      $link = Link::fromTextAndUrl($value->get('field_lastname')->value . ', ' .
        $value->get('field_firstname')->value, $url);
      $link = $link->toString();
      /*TODO  Add additional info to $row*/
      $rows[] = [Markup::create($link), $value->get('field_primaryaddress1')->value,
        $value->get('field_precinctname')->value];
    }
    /*TODO This just searches contacts; need to add search of info and merge tables   */
    $header = [
      'Name (Last, First)' => t('Name (Last, First)'),
      'Address' => t('Address'),
      'Precinct' => t('Precinct'),
    ];
    $content['table'] = [
      '#type' => 'table',
      '#attributes' => ['class' => ['searchtable']],
      '#header' => $header,
      '#rows' => $rows,
      '#cache.max-age' => 0,
    ];
    $renderer = \Drupal::service('renderer');
    $content = $renderer->render($content);
    $myresponse = new AjaxResponse();
    $selector = '.aclowerleft';
    $myresponse->addCommand(new Htmlcommand($selector,$content));
   return $myresponse;
  }
}
