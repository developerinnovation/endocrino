<?php 

namespace Drupal\ngt_general\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class class CourseCategoryRestLogic.
 *
 * @package Drupal\ngt_general
 */
class CourseCategoryRestLogic {

    /**
     * @return \Drupal\rest\ResourceResponse
     */
    public function get($category,$rangeInitial,$rangeStop) {
        // Remove cache.
        \Drupal::service('page_cache_kill_switch')->trigger();
        $data = [];
        $meth = 'getLastcourseCategory';
        
        if($category == 'all'){ 
            $meth = 'getLastcourse';
        }
        $data = $this->$meth($category,$rangeInitial,$rangeStop);

        return new ModifiedResourceResponse($data);
    }

    /**
     * getLastcouseCategory
     *
     * @return array
     */
    public function getLastcourseCategory($tid,$rangeInitial,$rangeStop){
        $nid = \Drupal::entityQuery('node')
                ->condition('type','curso')
                ->condition('field_categoria.entity:taxonomy_term.tid', $tid)
                ->condition('status', 1)
                ->sort('created' , 'DESC')
                ->range($rangeInitial,$rangeStop)
                ->execute();
        $node = \Drupal\node\Entity\Node::loadMultiple($nid);
        $courses =  \Drupal::service('ngt_general.course_main')->structureCourse($node);
        return $courses;
    }

    /**
     * getLastcourse
     *
     * @return array
     */
    public function getLastcourse($tid,$rangeInitial,$rangeStop){
        $nid = \Drupal::entityQuery('node')
                ->condition('type','curso')
                ->condition('status', 1)
                ->sort('created' , 'DESC')
                ->range($rangeInitial,$rangeStop)
                ->execute();
        $node = \Drupal\node\Entity\Node::loadMultiple($nid);
        $courses =  \Drupal::service('ngt_general.course_main')->structureCourse($node);
        return $courses;
    }
  
}