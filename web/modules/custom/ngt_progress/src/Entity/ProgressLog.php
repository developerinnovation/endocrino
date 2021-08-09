<?php

namespace Drupal\ngt_progress\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the ngt_progress_log.
 *
 * @ingroup ngt_progress_log
 *
 * @ContentEntityType(
 *   id = "ngt_progress_log",
 *   label = @Translation("Progress"),
 *   base_table = "ngt_progress_log",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 * )
 */

class ProgressLog extends ContentEntityBase implements ContentEntityInterface  {

    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

        // Standard field, used as unique if primary index.
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Progress entity.'))
            ->setReadOnly(TRUE);

        $fields['user_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('user_id'))
            ->setDescription(t('The user_id.'));

        $fields['type'] = BaseFieldDefinition::create('string')
            ->setLabel(t('type'))
            ->setDescription(t('The type progress.'));

        $fields['node_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('node_id'))
            ->setDescription(t('The node_id.'));

        $fields['parent_node_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('parent_node_id'))
            ->setDescription(t('The parent node_id.'));

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));
        
        return $fields;
    }
}