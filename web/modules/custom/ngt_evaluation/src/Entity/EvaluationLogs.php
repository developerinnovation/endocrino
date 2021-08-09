<?php

namespace Drupal\ngt_evaluation\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the ngt_evaluation_log.s
 *
 * @ingroup ngt_evaluation_logs
 *
 * @ContentEntityType(
 *   id = "ngt_evaluation_logs",
 *   label = @Translation("Evaluations"),
 *   base_table = "ngt_evaluation_logs",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 * )
 */

class EvaluationLogs extends ContentEntityBase implements ContentEntityInterface  {

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

        $fields['course_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('course_id'))
            ->setDescription(t('The course_id.'));

        $fields['module_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('module_id'))
            ->setDescription(t('The id of paragraph module from node course.'));

        $fields['type_evaluation'] = BaseFieldDefinition::create('string')
            ->setLabel(t('type_evaluation'))
            ->setDescription(t('The type evaluation, course or module.'));

        $fields['calification'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('calification'))
            ->setDescription(t('The calification percent.'));   

        $fields['attempts'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('attempts'))
            ->setDescription(t('The attempts for an module or course evaluation.'));

        $fields['total_question'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('total_question'))
            ->setDescription(t('The total question for  module or course evaluation.'));

        $fields['total_question_answered'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('total_question_answered'))
            ->setDescription(t('Total questions answered'));   

        $fields['total_corrrectly_answered'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('total_corrrectly_answered'))
            ->setDescription(t('Total corrrectly answered'));  
        
        $fields['minutes'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('minutes'))
            ->setDescription(t('The minutes used in evaluation'));

        $fields['approved'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('approved'))
            ->setDescription(t('approved evaluation'));

        $fields['token'] = BaseFieldDefinition::create('string')
            ->setLabel(t('token'))
            ->setDescription(t('token for evaluation'));

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));
        
        return $fields;
    }
}