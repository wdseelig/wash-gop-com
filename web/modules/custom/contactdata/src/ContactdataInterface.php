<?php

namespace Drupal\contactdata;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a contactdata entity type.
 */
interface ContactdataInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the contactdata creation timestamp.
   *
   * @return int
   *   Creation timestamp of the contactdata.
   */
  public function getCreatedTime();

  /**
   * Sets the contactdata creation timestamp.
   *
   * @param int $timestamp
   *   The contactdata creation timestamp.
   *
   * @return \Drupal\contactdata\ContactdataInterface
   *   The called contactdata entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the contactdata info_id.
   *
   * @return int
   *   InfoID of the contactdata.
   */
  public function getInfoID();

  /**
   * Sets the contactdata info_id.
   *
   * @param int $info_id
   *   The contactdata InfoID.
   *
   * @return \Drupal\contactdata\ContactdataInterface
   *   The called contactdata entity.
   */
  public function setInfoID($info_id);


  /**
   * Gets the contactdata d7cid.
   *
   * @return int
   *   d7cid of the contactdata.
   */
  public function getD7cid();

  /**
   * Sets the contactdata d7cid.
   *
   * @param int $d7cid
   *   The contactdata d7cid.
   *
   * @return \Drupal\contactdata\ContactdataInterface
   *   The called contactdata entity.
   */
  public function setD7cid($d7cid);


}
