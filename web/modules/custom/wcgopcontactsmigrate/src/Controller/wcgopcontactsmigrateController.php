<?php

namespace Drupal\wcgopcontactsmigrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\paragraphs\Entity;
use Drupal\Core\Entity\Sql;

/**
 * Returns responses for wcgopcontactsmigrate routes.
 */
class WcgopcontactsmigrateController extends ControllerBase
{

  /**
   * Builds the response.
   */
  public function build()
  {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  public function transfer()
  {

    $result = \Drupal::database()->select('crm_core_contact', 'c')
      ->fields('c')
      ->condition('c.type', 'v2individual')
      ->execute();
    foreach ($result as $record) {

      $contact = \Drupal::entityTypeManager()
        ->getStorage('contactdata')   // or other entity type name
        ->create();
      $contact->info_id = $record->info_id;
      $contact->d7cid = $record->contact_id;
      $contact->save();
    }
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Finished transferring D7 contacts to D9'),
    ];
    return $build;
  }

  public function transferqvf()
  {

    // Get the fields that have been added to the contactdata entity (ignore the booleans)
    /*@TODO Also should ignore all non-QVF fields that are in the contact e.g., phoneinfo  */
    $db = \Drupal::database();
    $query = $db->query("SHoW COLUMNS FROM wcgopindividualinfo ");
    $result = $query->fetchAll();

    foreach ($result as $key => $value) {
      $infoarray[] = strtolower($value->Field);
      $y = 1;
    }
    /* this is where I should use the infoarray to modify getarray  (now being done) */
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

    /* This is a place to pause to check to make sure that getarray is correct */
    $x = 1;
    // Get an array of the info_id's of all of the contacts
    $query = \Drupal::database()->select('contactdata', 'c')
      ->fields('c', ['info_id']);
    $result = $query->execute()->fetchAll();
    // Add QVF info to the contact
    foreach ($result as $record) {
      $infoid = $record->info_id;
      $query = \Drupal::entityQuery('contactdata')
        ->condition('info_id', $infoid);
      $cid = $query->execute();
      $contact = \Drupal::entityTypeManager()->getStorage('contactdata')->loadMultiple($cid);
      $mycontact = array_values($contact)[0];
      // Now get the fields for which we need data from wcgopindividualinfo
      $result = \Drupal::database()->select('wcgopindividualinfo', 'w')
        ->fields('w', $getarray)
        ->condition('w.info_id', $infoid)
        ->execute();
      $record = $result->fetchAll();
      foreach ($record as $myobj) {
        $myarr = (array)$myobj;
        /*@TODO Put the test of the _admin field in here  */
        foreach ($myarr as $key => $value) {
          $fieldname = 'field_' . $key;
          $mycontact->$fieldname = $value;
        }
      }
      $mycontact->save();
    }
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Finished the QVF transfer routine'),
    ];
    return $build;
  }

  public function experiment()
  {
    $result = \Drupal::database()->select('wcgopindividualinfo', 'w')
      ->fields('w', ['info_id', 'LastName', 'FirstName', 'PrimaryAddress1'])
      ->condition('w.LastName', 'Seelig', 'LIKE')
      ->execute();
    $record = $result->fetchAllAssoc('info_id', \PDO::FETCH_ASSOC);
    $rows = [];
    foreach ($record as $value) {
      $link = Link::fromTextandUrl(
        t($value['LastName'] . ', ' . $value['FirstName']),
        Url::fromUri('internal:/admin/content/contactdata/2752/edit'));
      $rows[] = [$link, $value['PrimaryAddress1'], $value['info_id']];
      $x = 1;
    }
    $header = [
      'Name (Last, First)' => t('Name (Last, First)'),
      'Address' => t('Address'),
      'info_id' => t('info_id'),
    ];
    return [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#cache.max-age' => 0,
    ];
  }

  public function transferphoneinfo()
  {
    /*First get all of the contacts that have an info_id  */
    $result = \Drupal::database()->select('contactdata', 'cd')
      ->fields('cd', ['id', 'info_id'])
      ->condition('cd.info_id', 0, '>')
      ->execute();
    /*Now loop through all contacts, pulling down the QVF phone info  */
    foreach ($result as $record) {
      /* This is for testing purposes and will go once we're confident in the code */
      if ($record->id > 100002) {
        continue;
      }
      /* Start by getting all paragraphs attached to this contactdata entity */
      $cid = $record->id;
      $info = $record->info_id;
      /*
       * Get all phoneinfo paragraphs attached to this contact ...
       */

      $query = \Drupal::entityQuery('paragraph');
      $andgroup = $query->andConditionGroup()
        ->condition('parent_id', $cid)
        ->condition('type', 'phoneinfo');
      $pid = $query->condition($andgroup)->execute();
      // loadMultiple requires below integers so convert the query's strings
      $intpid = array_map(
        function ($value) {
          return (int)$value;
        },
        $pid
      );
      /*
       * Now get all of the wcgopindividualinfo phone fields for this info_id
       */
      $infofields = (array)\Drupal::database()->select('wcgopindividualinfo', 'wc')
        ->fields('wc', ['info_id', 'WorkPhone', 'HomePhone', 'CellularPhone'])
        ->condition('wc.info_id', $info, 'LIKE')
        ->execute()->fetchAll();
      $infofields = (array)$infofields[0];
      /*
       * Now load all of the paragrphs we found aove
       */
      $intpid = array_values($intpid);
      $para = \Drupal::entityTypeManager()->getStorage('paragraph')
        ->loadMultiple($intpid);
      $para = array_values($para);


      /* This is the core of the transfer: check use each info field to update the paragraph
       * that holds its data. and update its value.  If there is no paragraph to contain its
       * data, then create one.
      */
      foreach ($infofields as $colname => $colvalue) {
        /*
         * Looping here on the 3 phone fields
         */
        $paraset = false;
        if ((strlen($colvalue) > 0) && ($colname !== "info_id")) { // Only look at the paragraphs if we have an info value
          foreach ($para as $contactpara) {
            /*
             * Looping here on the phoneinfo paragraphs that we identified above
             * Don't bother to loop if ther are no phoneinfo paragrphs for this contact.
             */
            if (count([$para]))
              break;
            $tid = (int)$contactpara->field_phonesourceandtype->getString();
            $taxitem = \Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')->load($tid);
            $taxterm = $taxitem->name->value;
            if ($colname == $taxterm) {
              /*
               * This is the case where the column name in info matches the name of the
               * paragraph field.  In this case we're just updating the phone number part of
               * that paragraph
               */
              $taxterm = 'field_' . $taxterm;
              $contactpara->$taxterm = $colvalue;
              $contactpara->save();
              $paraset = true;
              break;
            }
          }
          if (!$paraset) {
            /*
             * This is the case where we do not have an existing paragraph and need to create a new one.
             * We create a paragraph and then add that paragraph's id to the entity reference field on the
             * contact itself.  First create a new paragaph ...
             */
            $cid = (int)$cid;
            $para = \Drupal::entityTypeManager()->getStorage('paragraph')
              ->create(['type' => 'phoneinfo', 'parent_id' => $cid,
                'parent_type' => 'contactdata', 'parent_field_name' => 'field_phoneinfo']);
            /*
             * Now get the tid of the taxonomy term that matches this info field
             */
            $query = \Drupal::entityQuery('taxonomy_term');
            $andgroup = $query->andConditionGroup()
              ->condition('vid', 'phonesourceandtype')
              ->condition('name', $colname);
            $term = $query->condition($andgroup)->execute();
            $tid =(int)reset($term);
            /*
             * Add the phone source tid and the phone number to the paragraph
             */
            $para->field_phonesourceandtype = $tid;
            $para->field_phonenumber = $colvalue;
            $para->save();
            /*
             * When we created the paragraph above, we told it what contact ($cid) it was attached
             * to.  Now we have to do the reverse and let the contact know that this paragraph is
             * attached to it. The contactdata->field_phoneinfo to which we're adding the paragaraph's
             * id is an entity reference field.  Note that we have to reload the contact each time we
             * add another paragraph to it.
             */
            $infofield = 'field_phoneinfo';
            $contact = \Drupal::entityTypeManager()->getStorage('contactdata')->load($cid);
            $cinfovalues = $contact->$infofield->getValue();
            $cinfoalues[] = [
              'target_id'  => $para->id(),
              'target_revision_id' => $para->id(),
            ];
            $contact->$infofield->setValue($cinfovalues);
            $contact->save();
          } // End of the add a new paragraph code
        } // End of the "info table had a phone number" code
      } //  End of the loop through the phone fields
    }// End of the loop through the contacts
    /*
     * Now make Symfony happy by returning a render array
     */
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Finished transferring phone info to D9'),
    ];
    return $build;
  }  // End of the transferphoneinfo method
  public function linkpara() {
    /*
     * This method is here because the transferphoneinfo method above, although it
     * created the paragraphs, did not populate the entity reference field to let
     * the contact editor know which paragraphs are attactched to this contact
     */
    /*
     * First get a list of the contacts who have phone number paragraphs
     */
    $query = \Drupal::database()->select('paragraphs_item_field_data', 'pi')
      ->fields('pi', ['parent_id'])
      ->condition('pi.id', 41, '>');
    $result = $query->distinct()->execute()->fetchAll();
    /*
     * Now get all of the paragraphs for this contact
     */
    foreach ($result as $record) {
      $cid = (int)$record->parent_id;
      $query2 = \Drupal::database()->select('paragraphs_item_field_data', 'pi')
        ->fields('pi', ['id', 'parent_id']);
      $qryandcondition = $query->andConditionGroup()
        ->condition('pi.parent_id', $cid, 'LIKE')
        ->condition('pi.id', 4218, '>');
      $pids = $query2->condition($qryandcondition)->execute()->fetchAll();
      foreach ($pids as $para) {
        $pid = (int)$para->id;
         if (!in_array($pid, [919, 920])) {  // Some paragraphs are already linked
            $contact = \Drupal::entityTypeManager()->getStorage('contactdata')->load($cid);
            $phinfo = 'field_phoneinfo';
            $phdata = $contact->$phinfo->getValue();
            $phdata[] = [
              'target_id' => $pid,
              'target_revision_id' => $pid,
            ];
            $contact->$phinfo->setValue($phdata);
            $contact->save();

         } // End of test to eliminate a few paragraphs that are already there
      } // End of inner loop on paragraphs for this contact
    } // End of loop on contacts with phone paragraphs
    /*
     * Now make Symfony happy by returning a render array
     */
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Finished linking paragrpahs to contacts'),
    ];
    return $build;
  }// End of the linkpara function
  public function transferemail()
  {
    /*
     * First get the id and email address of all contacts
     */
    $query = \Drupal::database()->select('wcgopindividualinfo', 'wc');
    $query->join('contactdata', 'cd', 'wc.info_id = cd.info_id');
    $query
      ->fields('wc', ['info_id', 'email'])
      ->fields('cd', ['id']);
    $andcondition = $query->andConditionGroup()
      ->condition('wc.email', 'NULL', '<>')
      ->condition('wc.email', '', '<>')
      ->condition('wc.email', 'none', '<>');
    $result = $query->condition($andcondition)->execute()->fetchAll();
    foreach ($result as $record)
    {
      $paraset = false;
      $info = $record->info_id;
      $cid = (int)$record->id;
      $email = $record->email;
      $query = \Drupal::entityQuery('paragraph');
      $andgroup = $query->andConditionGroup()
        ->condition('parent_id', $cid)
        ->condition('type', 'emailinfo');
      $pid = $query->condition($andgroup)->execute();
      // loadMultiple requires below integers so convert the query's strings
      $intpid = array_map(
        function ($value) {
          return (int)$value;
        },
        $pid
      );
      if (count($intpid)) {
        /*
         * This is the case where we already have an emailinfo paragraph attached to this
         * contact, so we just have to update the address field.  This is presumably only
         * rarely going to happen because we're just going to bring the legacy database email
         * fields into the contacts database once.
         */
        $para = \Drupal::entityTypeManager()->getStorage('paragraph')
          ->loadMultiple($intpid);
        foreach ($para as $contactpara) {
          $emailfield = 'field_emailaddress';
          $contactpara->$emailfield = $email;
          $contactpara->save();
          $paraset = true;
          break;
        }
      }
      if ($paraset)
        continue;
      else {
        /*
         * This is the case where we do not yet have an emailinfo paragraph for this contact
         * so we have to make one.
         */
        $para = \Drupal::entityTypeManager()->getStorage('paragraph')
          ->create(
            ['type' => 'emailinfo', 'parent_id' => $cid,
              'parent_type' => 'contactdata', 'parent_field_name' => 'field_emailinfo']);
        $para->field_emailsourceandtype = 224; //This is the "Legacy" email source
        $para->field_emailaddress = $email;
        $para->save();
        /*
        * When we created the paragraph above, we told it what contact ($cid) it was attached
      * to.  Now we have to do the reverse and let the contact know that this paragraph is
      * attached to it. The contactdata->field_emailinfo to which we're adding the paragaraph's
      * id is an entity reference field.  Note that we have to reload the contact each time we
      * add another paragraph to it.
       */
        $paraid = (int)$para->id();
        $para = \Drupal::entityTypeManager()->getStorage('paragraph')->load($paraid);
        $paraid = (int)$para->id();
        $contact = \Drupal::entityTypeManager()->getStorage('contactdata')->load($cid);
        $infofield = 'field_emailinfo';
        $cinfovalues = $contact->$infofield->getValue();
        $cinfovalues[] = [
          'target_id' => $paraid,
          'target_revision_id' => $paraid,
        ];
        $contact->$infofield->setValue($cinfovalues);
        $contact->save();
      } // End of the case where we make a new paragraph and tie it to this contact
    } // Loop over all contact email addresses
  } // End of transferemail
  public function transfertags()
  {
    $query = \Drupal::database()->select('contactdata', 'cd')
      ->fields('cd', ['id', 'd7cid'])
      ->execute();
    $result = $query->fetchAll();
    foreach ($result as $record)
    /*
     * Loop through all contacts
     */
    {
      $cid = (int)$record->id;
      $d7id = (int)$record->d7cid;
      if ($cid < 2946)
        continue;
      $tagquery = \Drupal::database()->select('field_data_field_tagswithhhsv2', 'tags')
        ->fields('tags', ['entity_id', 'field_tagswithhhsv2_tid'])
        ->condition('entity_id', $d7id);
      $tagsforcontact = $tagquery->execute()->fetchAll();
      foreach ($tagsforcontact as $tag)
      /*
       * Loop through all tags for this contact
       */
      {
        $x = 1;
        $tid = (int)$tag->field_tagswithhhsv2_tid;
        $para = \Drupal::entityTypeManager()->getStorage('paragraph')
          ->create(
            ['type' => 'taginfo', 'parent_id' => $cid,
              'parent_type' => 'contactdata', 'parent_field_name' => 'field_taginfo']);
        $para->field_tagsource = 228; //This is the "Legacy" tag source
        $para->field_tag = $tid ;
        $para->save();
        /*
         * This is where we start on Sunday 2/13/2022.  We now have to inform the contact via its entity
         * reference fields that this contact is attached to it. [This is for the contact edit form]
         */
        /*
       * When we created the paragraph above, we told it what contact ($cid) it was attached
     * to.  Now we have to do the reverse and let the contact know that this paragraph is
     * attached to it. The contactdata->field_emailinfo to which we're adding the paragaraph's
     * id is an entity reference field.  Note that we have to reload the contact each time we
     * add another paragraph to it.
      */
        $paraid = (int)$para->id();
        $contact = \Drupal::entityTypeManager()->getStorage('contactdata')->load($cid);
        $infofield = 'field_taginfo';
        $cinfovalues = $contact->$infofield->getValue();
        $cinfovalues[] = [
          'target_id' => $paraid,
          'target_revision_id' => $paraid,
        ];
        $contact->$infofield->setValue($cinfovalues);
        $contact->save();
      } // End of loop for all tags for this contact
    } // End of loop through all contacts
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Finished transferring tags to D9'),
    ];
    return $build;
  }// End of transfertags
} // End of the wcgopcontactsmigratgeController class

