<?php

namespace Drupal\admincontacts\Controller;

use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

/**
 * Returns responses for admincontacts routes.
 */
class AdmincontactsController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build()
  {


    $build = \Drupal::formBuilder()->getForm('Drupal\admincontacts\Form\VoterSearchForm');
    $myrender = \Drupal::service('renderer');
    $content = $myrender->render($build);
    $myresponse = new AjaxResponse();
    $selector = 'acupperleft';
    $myresponse->addCommand(new HtmlCommand($selector, $content));
    // return $myresponse;
    return [
      '#theme' => 'admincontacts_main',
      '#searchform' => $build,
    ];
  }
    public function acdisplay($nojs,$cid)
    {
      /**
       * Displays the edit form for the selected contact
       */
      $storage = \Drupal::entityTypeManager()->getStorage('contactdata');
      $cd = $storage->load($cid);

      $form = \Drupal::service('entity.form_builder')->getForm($cd, 'view');

      $content = \Drupal::service('renderer')->render($form);
      $ajaxresponse = new AjaxResponse();
      $selector = '.acrightcontainer';
      $ajaxresponse->addCommand(new Htmlcommand($selector,$content));
      return $ajaxresponse;
    }
    public function voterinfo($infoid)
    {
      /**
       * Display a form with information about a voter in the info table who is not a contact
       */
      $form = \Drupal::formBuilder()->getForm('Drupal\admincontacts\Form\VoterInfoForm',$infoid);
      return $form;
    }

}
