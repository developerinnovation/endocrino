<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeTopCourseBlock;
use Drupal\media\Entity\Media;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'NodeTopCourseBlockClass' block
 */
class NodeTopCourseBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeTopCourseBlock $instance
     * @param $config
     */
    public function setConfig(NodeTopCourseBlock &$instance, &$config){
        $this->instance = &$instance;
        $this->configuration = &$config;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [];
    }

  
    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeTopCourseBlock $instance
     * @param $config
     */
    public function build(NodeTopCourseBlock &$instance, $configuration){
        $nid = $configuration['node'];
        $node = \Drupal\node\Entity\Node::loadMultiple(array($nid));
        $progressId = \Drupal::service('ngt_progress.method_general')->initProgress($nid, 'curso');
        $build = [
            '#theme' => 'node_top_course',
            '#data' => $this->preparerate($node),
        ];

        return $build;
    }

        
    /**
     * preparerate
     *
     * @param  array $node
     * @return array
     */
    public function preparerate($nodes){
        $courses = [];
        
        foreach ($nodes as $node) {
            $date = new DrupalDateTime($node->get('field_fecha_de_inicio')->getValue()[0]['value']);
            $formatted_date = \Drupal::service('date.formatter')->format($date->getTimestamp(), 'custom', 'M d, Y');
            $course = [
                'nid' => $node->get('nid')->getValue()[0]['value'],
                'title' => $node->get('title')->getValue()[0]['value'],
                'body' => $node->get('body')->getValue()[0]['value'],
                'resumen' => isset($node->get('field_resumen')->getValue()[0]['value']) ? $node->get('field_resumen')->getValue()[0]['value'] : '',
                'autor' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_autor_principal')->getValue()),
                'cnt_alumnos' => $node->get('field_cantidad_de_alumnos')->getValue()[0]['value'],
                'cnt_moulos' => count($node->get('field_modulo')->getValue()),
                'categoria' => $node->get('field_categoria')->getValue()[0]['target_id'],
                'expertos' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_coordinadores')->getValue(), 3),
                'fecha_inicio' => $formatted_date,
                'horas' =>$node->get('field_horas')->getValue()[0]['value'],
                'rating' => isset($node->get('field_calificacion')->getValue()[0]['rating']) ? $node->get('field_calificacion')->getValue()[0]['rating'] :'',
            ];
            array_push($courses,$course);
        }
        return $courses;
    }

}