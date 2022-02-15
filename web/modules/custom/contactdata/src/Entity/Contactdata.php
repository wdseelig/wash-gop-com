<?php

namespace Drupal\contactdata\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\contactdata\ContactdataInterface;
use Drupal\user\UserInterface;

/**
 * Defines the contactdata entity class.
 *
 * @ContentEntityType(
 *   id = "contactdata",
 *   label = @Translation("contactData"),
 *   label_collection = @Translation("contactDatas"),
 *   handlers = {
 *
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\contactdata\ContactDataAccess",
 *     "list_builder" = "Drupal\contactdata\ContactdataListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\contactdata\Form\ContactdataForm",
 *       "edit" = "Drupal\contactdata\Form\ContactdataForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "contactdata",
 *   admin_permission = "administer contactdata",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/contactdata/add",
 *     "canonical" = "/contactdata/{contactdata}",
 *     "edit-form" = "/admin/content/contactdata/{contactdata}/edit",
 *     "delete-form" = "/admin/content/contactdata/{contactdata}/delete",
 *     "collection" = "/admin/content/contactdata"
 *   },
 *   field_ui_base_route = "entity.contactdata.settings"
 * )
 */
class Contactdata extends ContentEntityBase implements ContactdataInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new contactdata entity is created, set the uid entity reference to
   * the current user as the creator of the entity.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += ['uid' => \Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getinfo_id() {
    return $this->info_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setinfo_id($info_id) {
    $this->set('info_id', $info_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getd7cid() {
    return $this->d7cid;
  }

  /**
   * {@inheritdoc}
   */
  public function setd7cid($d7cid) {
    $this->set('d7cid', $d7cid);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The user ID of the contactdata author.'))
      ->setSetting('target_type', 'user')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the contactdata was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the contactdata was last edited.'));

    $fields['info_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('InfoID'))
      ->setDescription(t('The contact identifer in wcgopindividualinfo'));

    $fields['d7cid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('D7cid'))
      ->setDescription(t('The D7 cid for this contact'));

    return $fields;
  }

}
