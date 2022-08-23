<?php
namespace Drupal\contactdata\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\contactdata\Plugin\search_api\processor\Property\AddContactDataProperty;

/**
 * Adds the item's URL to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "add_contactdatatags",
 *   label = @Translation("Tag Field"),
 *   description = @Translation("Flattens the contactdata entities tags"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class ContactDataTags extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];


    if (!$datasource) {
      $definition = [
        'label' => $this->t('TagField'),
        'description' => $this->t('A field to store this entities tags as a property'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['contactdata_tags'] = new AddContactDataProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item)
  {
    $x = 1;
    $contact = $item->getOriginalObject();
    $para = $contact->get('field_taginfo');
    $cid = $contact->get('id')->value;
    if ($para) {
      $fields = $item->getFields(FALSE);
      $fields = $this->getFieldsHelper()
        ->filterForPropertyPath($fields, NULL, 'contactdata_tags');
      foreach ($fields as $field) {
        foreach ($contact->get('field_taginfo') as $pid) {
          $tag = $pid->entity->get('field_tag');  //$tag at this point is an entity reference
          $tid = $tag->entity->id();              //$tag->entity is the entity referenced by $tag
          $tagparent = \Drupal::service('entity_type.manager')
            ->getStorage("taxonomy_term")->loadAllParents($tid);
          $tagname = $tag->entity->get('name')->value;
          if ( count($tagparent)  < 2)
            break;
          $tagparentobj = array_values($tagparent)[1];
          $x = 1;
          $y = 1;
          if (!$tagparentobj)
            break;
          $tagparentname = $tagparentobj->get('name')->value;
          $field->addValue($tagparentname . " -- " . $tagname);
          $x = 3;
        }
      }
    }

  }
  }

