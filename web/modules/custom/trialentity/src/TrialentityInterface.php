<?php

namespace Drupal\trialentity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a trialentity entity type.
 */
interface TrialentityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the trialentity title.
   *
   * @return string
   *   Title of the trialentity.
   */
  public function getTitle();

  /**
   * Sets the trialentity title.
   *
   * @param string $title
   *   The trialentity title.
   *
   * @return \Drupal\trialentity\TrialentityInterface
   *   The called trialentity entity.
   */
  public function setTitle($title);

  /**
   * Gets the trialentity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the trialentity.
   */
  public function getCreatedTime();

  /**
   * Sets the trialentity creation timestamp.
   *
   * @param int $timestamp
   *   The trialentity creation timestamp.
   *
   * @return \Drupal\trialentity\TrialentityInterface
   *   The called trialentity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the trialentity status.
   *
   * @return bool
   *   TRUE if the trialentity is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the trialentity status.
   *
   * @param bool $status
   *   TRUE to enable this trialentity, FALSE to disable.
   *
   * @return \Drupal\trialentity\TrialentityInterface
   *   The called trialentity entity.
   */
  public function setStatus($status);

}
