<?php

namespace Drupal\ngt_inscription\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the ngt_inscription_log.
 *
 * @ingroup ngt_inscription_log
 *
 * @ContentEntityType(
 *   id = "ngt_inscription_log",
 *   label = @Translation("Inscription"),
 *   base_table = "ngt_inscription_log",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 * )
 */

class InscriptionLog extends ContentEntityBase implements ContentEntityInterface  {

    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

        // Standard field, used as unique if primary index.
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Inscription entity.'))
            ->setReadOnly(TRUE);

        $fields['user_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('user_id'))
            ->setDescription(t('The user_id.'));

        $fields['node_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('node_id'))
            ->setDescription(t('The node_id.'));

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));
        
        return $fields;
    }
}