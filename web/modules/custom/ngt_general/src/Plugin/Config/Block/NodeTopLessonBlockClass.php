<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeTopLessonBlock;
use Drupal\media\Entity\Media;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'NodeTopLessonBlockClass' block
 */
class NodeTopLessonBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeTopLessonBlock $instance
     * @param $config
     */
    public function setConfig(NodeTopLessonBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\NodeTopLessonBlock $instance
     * @param $config
     */
    public function build(NodeTopLessonBlock &$instance, $configuration){
        $nid = $configuration['node'];
        $node = \Drupal\node\Entity\Node::loadMultiple(array($nid));
        $progressId = \Drupal::service('ngt_progress.method_general')->initProgress($nid, 'leccion');
        $build = [
            '#theme' => 'node_top_lesson',
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
        $lessons = [];
        
        foreach ($nodes as $node) {
            $nid = $node->get('nid')->getValue()[0]['value'];
            $courseId = \Drupal::service('ngt_general.methodGeneral')->get_module_by_lesson($nid);
            $navLesson = \Drupal::service('ngt_general.methodGeneral')->get_last_prev_lesson($courseId, $nid);
            $course = \Drupal\node\Entity\Node::load($courseId);
            $modules = isset($course->field_modulo->getValue()[0]['target_id']) ? \Drupal::service('ngt_general.methodGeneral')->load_module_course($course->field_modulo->getValue()): NULL;
            
            $resource = isset($node->get('field_recursos')->getValue()[0]['target_id']) ? \Drupal::service('ngt_general.methodGeneral')->load_resource($node->get('field_recursos')->getValue()) : null;
            $lesson = [
                'nid' => $nid,
                'title' => $node->get('title')->getValue()[0]['value'],
                'expertos' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_docente')->getValue()),
                'nextLesson' => $navLesson['next'],
                'prevLesson' => $navLesson['prev'],
                'module' => $this->searchTitleModule($nid, $modules) != NULL ? $this->searchTitleModule($nid, $modules) : 'No hay módulo asociado a la lección actual.',
                'courseTitle' =>  $course->get('title')->getValue()[0]['value'],
                'courseResume' => isset($course->get('field_resumen')->getValue()[0]['value']) ? $course->get('field_resumen')->getValue()[0]['value'] : '',
            ];
            array_push($lessons,$lesson);
        }
        return $lessons;
    }

    public function searchTitleModule($nidLesson, $modules){
        if($modules != NULL){
            foreach ($modules  as $key => $dataModule) {
                if(\Drupal::service('ngt_general.methodGeneral')->in_array_r($nidLesson, $dataModule['lessons'])){
                    return 'M' . $dataModule['numModule'] . ': '. $dataModule['titleModule'];
                    break;
                }
            }
            return NULL;
        }
        return NULL;
    }


}



            