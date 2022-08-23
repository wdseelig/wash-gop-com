<?php

namespace Drupal\contactdata;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

class ContactDataAccess extends EntityAccessControlHandler {
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation)
    {
      case 'view':
        $access_result = AccessResult::allowedIf($account
          ->hasPermission('view contactdata'));
        if ($access_result->isAllowed()) {
          return AccessResult::allowed()
          ->cachePerPermissions()
          ->addCacheableDependency($entity);
        }
        else {
        $access_result->setReason('You must have view contact data permissions to see this data');
        printf("You are not able to access contactdata via views");
        exit;
        return $access_result;
      }
      break;
      case 'update':
        $access_result = AccessResult::allowedIf($account
          ->hasPermission('edit contact data'));
        if ($access_result->isAllowed()) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->addCacheableDependency($entity);
        }
        else {
          $access_result->setReason('You must have edit contact data permissions to edit a contact');
          return $access_result;
        }
        break;
      case 'add':
        $access_result = AccessResult::allowedIf($account
          ->hasPermission('create contact data'));
        if ($access_result->isAllowed()) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->addCacheableDependency($entity);
        }
        else {
          $access_result->setReason('You must have create contact data permissions to add a contact');
          return $access_result;
        }
        break;
    }
    return AccessResult::neutral();
  }
}
