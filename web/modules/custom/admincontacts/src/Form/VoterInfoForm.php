<?php

namespace Drupal\admincontacts\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Provides a admincontacts form.
 */
class VoterInfoForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admincontacts_voter_info';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $infoid = NULL) {
    /*
     * This form show a few fields from the wcgopoindividualinfo file and provides the administrator with
     * the option of adding this wcgopindividualinfo entry to our contacts list.  We start by finding all
     * of the fields in the wcgipindividualinfo table that have also been added to the contact.
     */
    $db = \Drupal::database();
    $query = $db->query("SHoW COLUMNS FROM wcgopindividualinfo ");
    $result = $query->fetchAll();

    foreach ($result as $key => $value) {
      $infoarray[] = strtolower($value->Field);
    }
    $entityfields = \Drupal::service('entity_field.manager')
      ->getFieldDefinitions('contactdata', 'contactdata');
    $fnames = array_keys($entityfields);
    $getarray = array();
    foreach ($fnames as $key => $value) {
      if ((strpos($value, 'field') !== false) && (strpos($value, 'admin') === false)) {
        $trialfield = str_replace('field_', '', $value);
        if (in_array($trialfield, $infoarray)) {
          $getarray[] = $trialfield;
        }
      }
    }
    /*
     * Now get the wcgopindividual info fields that are added to the contact
     */
    $result = \Drupal::database()->select('wcgopindividualinfo', 'w')
      ->fields('w', $getarray)
      ->condition('w.info_id', $infoid)
      ->execute();
    $record = $result->fetchObject();

    $form['message'] = [
      '#type' => 'item',
      '#markup' => '<h2>Click the "Add this Contact" button below to create a new contact</h2>',
    ];
    $form['contactname'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $record->lastname . ', ' . $record->firstname . '<br><br>',
      '#prefix' => '<div class="cname"',
      '#suffix' => '</div',
    ];
    $form['contactaddr'] = [
      '#type' => 'item',
      '#prefix' => '<div class="caddr"',
      '#markup' => '<h2>' . $record->primaryaddress1 . '<br>' . $record->primarycity . ', ' .
        $record->primaryzip . '<br>' . $record->precinctname . '</h2>' ,
      '#suffix' => '</div',
    ];
    $form['infoid'] = [
      '#type' => 'hidden',
      '#value' => $infoid,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add this Contact'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*
     * Note that there is no form validation here because wre just using the submit
     * button to create a content from an existing wcgopindividualinfo record.
     */
    $zz = 1;

    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
{
  $contactinfoid = $form_state->getValue('infoid');
  $db = \Drupal::database();
  $query = $db->query("SHOW COLUMNS FROM wcgopindividualinfo ");
  $result = $query->fetchAll();

  foreach ($result as $key => $value) {
    $infoarray[] = strtolower($value->Field);
  }
  $entityfields = \Drupal::service('entity_field.manager')
    ->getFieldDefinitions('contactdata', 'contactdata');
  $fnames = array_keys($entityfields);
  $getarray = array();
  foreach ($fnames as $key => $value) {
    if ((strpos($value, 'field') !== false) && (strpos($value, 'admin') === false)) {
      $trialfield = str_replace('field_', '', $value);
      if (in_array($trialfield, $infoarray)) {
        $getarray[] = $trialfield;
      }
    }
  }
  $newcontact = \Drupal::entityTypeManager()->getStorage('contactdata')->create();
  $newcontact->info_id = (int)$contactinfoid;
  $result = \Drupal::database()->select('wcgopindividualinfo', 'w')
    ->fields('w', $getarray)
    ->condition('w.info_id', (int)$contactinfoid)
    ->execute();
  $record = $result->fetchAll();
  foreach ($record as $myobj) {
    $myarr = (array)$myobj;
    foreach ($myarr as $key => $value) {
      $fieldname = 'field_' . $key;
      $newcontact->$fieldname = $value;
    }
  }
  $newcontact->save();
  $ncid = $newcontact->id();

  /*
   * We now dispatch to the edit page of this new contact
   */
  $url = Url::fromRoute(
    'entity.contactdata.canonical',
    array('contactdata' => $ncid));
  $response = new RedirectResponse($url->toString());
  $request = \Drupal::request();
  // Save the session so things like messages get saved.
  $request->getSession()->save();
  $response->prepare($request);
  // Make sure to trigger kernel events.
  \Drupal::service('kernel')->terminate($request, $response);
  $response->send();
}
}

