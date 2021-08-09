<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeMoreCourseBlock;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'NodeMoreCourseBlockClass' block
 */
class NodeMoreCourseBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeMoreCourseBlock $instance
     * @param $config
     */
    public function setConfig(NodeMoreCourseBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\NodeMoreCourseBlock $instance
     * @param $config
     */
    public function build(NodeMoreCourseBlock &$instance, $configuration){
        $nid = $configuration['node'];
        $nodes = $this->getMoreContent($nid);
        $preparerate = $this->preparerate($nodes);
        
        $build = [
            '#theme' => 'node_more_course',
            '#data' => $preparerate,
        ];

        return $build;
    }

    /**
     * getLastcouseMain
     *
     * @return array
     */
    public function getMoreContent($nid){
        $nid = \Drupal::entityQuery('node')
                ->condition('type','curso')
                ->condition('status', 1)
                ->sort('created' , 'DESC')
                ->range(0, 3)
                ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nid);
        return $nodes;
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
                'autor' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_autor_principal')->getValue()),
                'fecha_inicio' => $formatted_date,
                'horas' =>$node->get('field_horas')->getValue()[0]['value'],
                'rating' => isset($node->get('field_calificacion')->getValue()[0]['rating']) ? $node->get('field_calificacion')->getValue()[0]['rating'] :'',
                'foto_portada' => [
                    'uri' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id']),
                    'uri_360x196' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'360x196'),
                    'uri_604x476' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'604x476'),
                    'target_id' => $node->get('field_foto_portada')->getValue()[0]['target_id'],
                    'alt' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['alt'] : '',
                    'title' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['title'] : '',
                    'width' => $node->get('field_foto_portada')->getValue()[0]['width'],
                    'height' => $node->get('field_foto_portada')->getValue()[0]['height'],
                ],
            ];
            array_push($courses,$course);
        }
        return $courses;
    }
   

}