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
   *   info_id of the contactdata.
   */
  public function getinfo_id();

  /**
   * Sets the contactdata info_id.
   *
   * @param int $info_id
   *   The contactdata info_id.
   *
   * @return \Drupal\contactdata\ContactdataInterface
   *   The called contactdata entity.
   */
  public function setinfo_id($info_id);


  /**
   * Gets the contactdata d7cid.
   *
   * @return int
   *   d7cid of the contactdata.
   */
  public function getd7cid();

  /**
   * Sets the contactdata d7cid.
   *
   * @param int $d7cid
   *   The contactdata d7cid.
   *
   * @return \Drupal\contactdata\ContactdataInterface
   *   The called contactdata entity.
   */
  public function setd7cid($d7cid);


}
