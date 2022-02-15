<?php

namespace Drupal\displayqvf\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the displayqvf entity edit forms.
 */
class DisplayqvfForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New displayqvf %label has been created.', $message_arguments));
      $this->logger('displayqvf')->notice('Created new displayqvf %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The displayqvf %label has been updated.', $message_arguments));
      $this->logger('displayqvf')->notice('Updated new displayqvf %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.displayqvf.canonical', ['displayqvf' => $entity->id()]);
  }

}
