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
    $form['content']['resetbutton'] = [
      '#type'=> 'item',
      '#markup' => '<img src="/files/images/site_editors/RestartButton.png" class="clrscreen">',
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
      $url->setOptions(['attributes' => ['class' => ['c-link', 'r-elem']]]);
      $link = Link::fromTextAndUrl($value->get('field_lastname')->value . ', ' .
        $value->get('field_firstname')->value, $url);
      $link = $link->toString();
      /*TODO  Add additional info to $row*/
      $rows[] = [Markup::create($link), $value->get('field_primaryaddress1')->value,
        $value->get('field_precinctname')->value];
    }
    /*
     * This is now code that selects all of the relevant voters from the wcgopindividualinfo
     * table who are NOT yet contacts.  We will add these to the $rows variable with a link that points
     * to code that creates and displays a form that will allow these voters to be added as a contact
     */

    $query = \Drupal::database()->select('wcgopindividualinfo', 'w');
    $query->leftJoin('contactdata', 'cd','cd.info_id = w.info_id');
    $query->fields('w', ['LastName', 'FirstName', 'PrimaryAddress1', 'PrecinctName', 'info_id']);
    $query->fields('cd', ['id', 'info_id']);
    $andGroup = $query->andConditionGroup()
      ->condition('w.LastName', '%' . $ln . '%' , 'LIKE')
      ->condition('w.FirstName', '%'. $fn . '%' , 'LIKE')
      ->condition('w.primaryaddress1', '%' . $addr . '%' , 'LIKE' )
      ->condition('cd.id', NULL, 'IS NULL');
    $noncontacts = $query->condition($andGroup)->execute()->fetchAll();
    foreach ($noncontacts as $value)
    {
      $url = Url::fromRoute('admincontacts.voterinfo',
        array( 'infoid' => $value->info_id));
      $url->setRouteParameter("acmain", "1");
      $url->setOptions(['attributes' => ['class' => ['c-link', 'b-elem']]]);
      $link = Link::fromTextAndUrl($value->LastName . ', ' . $value->FirstName,$url);
      $link = $link->toString();
      /*TODO  Add additional info to $row*/
      $rows[] = [Markup::create($link), $value->PrimaryAddress1, $value->PrecinctName];
    }
    usort($rows, function($a, $b) {
      $first = explode('>', $a[0]->getGeneratedLink());
      $second = explode('>', $b[0]->getGeneratedLink());
     return(strnatcasecmp($first[1],$second[1]));
    });

    $header = [
      'Name (Last, First)' => t('Name (Last, First)'),
      'Address' => t('Address'),
      'Precinct' => t('Precinct'),
    ];
    $content['table'] = [
      '#prefix' => '<div class=searchtable>',
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#cache.max-age' => 0,
      '#suffix' => '</div>',
    ];





    $renderer = \Drupal::service('renderer');
    $content = $renderer->render($content);
    $myresponse = new AjaxResponse();
    $selector = '.aclowerleft';
    $myresponse->addCommand(new Htmlcommand($selector,$content));
   return $myresponse;
  }
}
