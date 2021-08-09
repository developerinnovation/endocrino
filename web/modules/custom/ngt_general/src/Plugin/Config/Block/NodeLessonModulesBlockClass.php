<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeLessonModulesBlock;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'NodeLessonModulesBlockClass' block
 */
class NodeLessonModulesBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeLessonModulesBlock $instance
     * @param $config
     */
    public function setConfig(NodeLessonModulesBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\NodeLessonModulesBlock $instance
     * @param $config
     */
    public function build(NodeLessonModulesBlock &$instance, $configuration){
        $nid = $configuration['node'];
        
        $build = [
            '#theme' => 'node_lesson_modules',
            '#data' => $this->preparerate($nid),
        ];

        return $build;
    }
    
    /**
     * preparerate
     *
     * @param  int $nid
     * @return array
     */
    public function preparerate($nid){
        $moduleList = [];
        
        if($nid != NULL){
            $courseId = \Drupal::service('ngt_general.methodGeneral')->get_module_by_lesson($nid);
            
            if($courseId != NULL){
                $course = \Drupal\node\Entity\Node::load($courseId);
                $modules = isset($course->field_modulo->getValue()[0]['target_id']) ? \Drupal::service('ngt_general.methodGeneral')->load_module_course($course->field_modulo->getValue()): NULL;
                if($modules != NULL){
                    foreach ($modules  as $key => $dataModule) {
                    array_push($moduleList, [
                            'titleModule' => $dataModule['titleModule'],
                            'class' => \Drupal::service('ngt_general.methodGeneral')->in_array_r($nid, $dataModule['lessons']) ?  'active' : 'no-active',
                            'numModule' => $dataModule['numModule'],
                            'moduleId' => 'M' . $dataModule['numModule'],
                            'urlCourse' =>\Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $dataModule['nidModule']),
                    ]);
                    }
                }
                
            }
        }
        return $moduleList;
    }

}