<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\SliderMainCourseBlock;

/**
 * Manage config a 'SliderMainCourseBlockClass' block
 */
class SliderMainCourseBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\SliderMainCourseBlock $instance
     * @param $config
     */
    public function setConfig(SliderMainCourseBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\SliderMainCourseBlock $instance
     * @param $config
     */
    public function build(SliderMainCourseBlock &$instance, $configuration){
        
        $build = [
            '#theme' => 'course_slider_main_category_section',
            '#data' => $this->getCourseDest(),
        ];

        return $build;
    }

    /**
     * getLastcouseMain
     *
     * @return array
     */
    public function getCourseDest(){
        $nid = \Drupal::entityQuery('node')
                ->condition('type','curso')
                ->condition('status', 1)
                ->condition('field_destacado', 1)
                ->sort('created' , 'DESC')
                ->range(0, 3)
                ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nid);
        $content = $this->structureContentDest($nodes);
        return $content;
    }
    
    /**
     * structureContentDest
     *
     * @param  array $node
     * @return array
     */
    public function structureContentDest($nodes) {
        $contents = [];
        foreach ($nodes as $node) {
            $content = [
                'nid' => $node->get('nid')->getValue()[0]['value'],
                'title' => $node->get('title')->getValue()[0]['value'],
                'body' => $node->get('field_resumen')->getValue()[0]['value'],
                'enlace' => [
                    'text' => 'Ver curso',
                    'uri' => \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $node->get('nid')->getValue()[0]['value']),
                ],
                'foto_portada' => [
                    'uri' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id']),
                    'uri_313x156' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'313x156'),
                    'uri_604x476' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'604x476'),
                    'uri_374x226' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'374x226'),
                    'target_id' => $node->get('field_foto_portada')->getValue()[0]['target_id'],
                    'alt' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['alt'] : '',
                    'title' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['title'] : '',
                    'width' => $node->get('field_foto_portada')->getValue()[0]['width'],
                    'height' => $node->get('field_foto_portada')->getValue()[0]['height'],
                ],
            ];
            array_push($contents,$content);
        }
        return $contents;
    }

}