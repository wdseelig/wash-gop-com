<?php

namespace Drupal\trialentity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the trialentity entity type.
 */
class TrialentityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view trialentity');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ['edit trialentity', 'administer trialentity'], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ['delete trialentity', 'administer trialentity'], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ['create trialentity', 'administer trialentity'], 'OR');
  }

}
