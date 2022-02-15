<?php
/**
 * @file
 * Contains \Drupal\myvotersearch\votersearchController.
 */

namespace Drupal\myvotersearch\Controller;

use Drupal\Core\Controller\ControllerBase;

class VoterGateway extends ControllerBase
{
  public function content($fromform, $firstname, $lastname, $address)
  {
    $build = \Drupal::formBuilder()->getForm('Drupal\myvotersearch\Form\VoterSearchForm');
    return $build;
  }
}
