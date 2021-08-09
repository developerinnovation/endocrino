<?php
 
/**
* @file 
* Contains \Drupal\ngt_progress\Controller\RenderProgressController.
*/
namespace Drupal\ngt_progress\Controller ;
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

class RenderProgressController extends ControllerBase{ 

    public function render_progress(Request $request){
        

        $result_log = \Drupal::service('ngt_progress.method_general')->loadProgressByUserId();
        return [
            '#theme' => 'render_progress',
            '#data' => !empty($result_log['data']) ? $result_log['data'] : null,
        ];
        
    }

}