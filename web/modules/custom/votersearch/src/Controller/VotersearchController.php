<?php

namespace Drupal\votersearch\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for votersearch routes.
 */
class VotersearchController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
