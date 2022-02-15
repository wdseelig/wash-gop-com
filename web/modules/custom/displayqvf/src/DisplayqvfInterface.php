<?php

namespace Drupal\displayqvf;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a displayqvf entity type.
 */
interface DisplayqvfInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
 * Gets the displayqvf title.
 *
 * @return string
 *   Title of the displayqvf.
 */
  public function getTitle();
  /**
   *
   * @param string $title
   *   The displayqvf title.
   *
   * @return \Drupal\displayqvf\DisplayqvfInterface
   *   The called displayqvf entity.
   */
  public function setTitle($title);

  /**
   * Gets the displayqvf first name.
   *
   * @return string
   *   First Name of the displayqvf.
   */
  public function getFirstName();
  /**
   *
   * @param string $name
   *   The first name in the displayqvf.
   *
   * @return \Drupal\displayqvf\DisplayqvfInterface
   * The called displayqvf entity
   */
  public function setFirstName($name);
  /**
   * Gets the displayqvf creation timestamp.
   *
   * @return int
   *   Creation timestamp of the displayqvf.
   */
  public function getCreatedTime();

  /**
   * Sets the displayqvf creation timestamp.
   *
   * @param int $timestamp
   *   The displayqvf creation timestamp.
   *
   * @return \Drupal\displayqvf\DisplayqvfInterface
   *   The called displayqvf entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the displayqvf status.
   *
   * @return bool
   *   TRUE if the displayqvf is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the displayqvf status.
   *
   * @param bool $status
   *   TRUE to enable this displayqvf, FALSE to disable.
   *
   * @return \Drupal\displayqvf\DisplayqvfInterface
   *   The called displayqvf entity.
   */
  public function setStatus($status);

}
