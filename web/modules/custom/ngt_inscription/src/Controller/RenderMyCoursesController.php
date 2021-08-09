<?php
 
/**
* @file 
* Contains \Drupal\ngt_inscription\Controller\RenderMyCoursesController.
*/
namespace Drupal\ngt_inscription\Controller ;
use Drupal\Core\Controller\ControllerBase ;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Html ;
use Drupal\Core\Database\Connection;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

class RenderMyCoursesController extends ControllerBase{ 

    public function render_my_courses(Request $request){
        

        $result_log = \Drupal::service('ngt_inscription.method_general')->loadCourseByUserId();
        return [
            '#theme' => 'render_my_courses',
            '#data' => !empty($result_log['data']) ? $result_log['data'] : null,
        ];
        
    }

}