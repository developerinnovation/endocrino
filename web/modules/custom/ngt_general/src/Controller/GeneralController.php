<?php
 
 /**
 * @file 
 * Contains \Drupal\ngt_general\Controller\GeneralController.
 */
 namespace Drupal\ngt_general\Controller ;
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
 use Drupal\Core\Routing\RouteMatchInterface;

 class GeneralController extends ControllerBase{ 
 
     public function section_course(RouteMatchInterface $route_match, $tid = NULL){
        return [
            '#theme' => 'course_section',
            '#main_category' => \Drupal::config('ngt_general.adminSettingsCategory')->getRawData(),
            '#category_id' => $tid,
        ];
         
     }
 
 }