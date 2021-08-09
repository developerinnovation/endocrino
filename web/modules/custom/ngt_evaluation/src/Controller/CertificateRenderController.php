<?php
 
/**
* @file 
* Contains \Drupal\ngt_evaluation\Controller\CertificateRenderController.
*/
namespace Drupal\ngt_evaluation\Controller ;
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

class CertificateRenderController extends ControllerBase{ 

    public function render_certificate(Request $request, $courserId, $evaluationId, $moduleId, $userId, $tokenId, $logId){
        
        $user = \Drupal\user\Entity\User::load($userId);
        $nombre = $user->get('field_nombre')->getValue()[0]['value'];
        $apellidos = $user->get('field_apellidos')->getValue()[0]['value'];

        $config = \Drupal::config('ngt_evaluation.settings'); 
        $message = $config->get('ngt_evaluation_certificate')['message'];
        $plantillaBody = $config->get('ngt_evaluation_certificate')['body'];


        $course = \Drupal::entityManager()->getStorage('node')->load($courserId);
        $title_course = $course->get('title')->getValue()[0]['value'];


        $result_log = \Drupal::service('ngt_evaluation.method_general')->getEvaluationById($logId);

        $created = $result_log->get('created')->getValue()[0]['value'];
        $type_evaluation = $result_log->get('type_evaluation')->getValue()[0]['value'];

        if($type_evaluation == 'module'){
            $type_approved = 'Módulo '. $moduleId . ' del '. $title_course;
        }else{
            $type_approved = '';
        }

        $token_service = \Drupal::token();
        $tokens = [
            'logo' => $this->load_file('img_logo_certificate'),
            'background' => $this->load_file('backgound'),
            'signature_president'  => $this->load_file('signature_president'),
            'signture_coordinator_1' => '',
            'signture_coordinator_2' => '',
            'token' => strtoupper($tokenId),
            'type_approved' => $type_approved,
            'message' => $message,
            'name_user' => ucfirst($nombre) .' '.ucfirst($apellidos),
            'date' => intval($created),
        ];
        
        $plantillaHtml = $token_service->replace($plantillaBody['value'],$tokens);
        
        $renderable = [
            '#theme' => 'certificate_render',
            '#logo' => $this->load_file('img_logo_certificate'),
            '#background' => $this->load_file('backgound'),
            '#signature_president'  => $this->load_file('signature_president'),
            '#signture_coordinator_1' => NULL,
            '#signture_coordinator_2' => NULL,
            '#token' => strtoupper($tokenId),
            '#type_approved' => $type_approved,
            '#message' => $message,
            '#name_user' => ucfirst($nombre) .' '.ucfirst($apellidos),
            '#date' => intval($created),
            '#cache' => ['max-age' => 0],
        ];

        $output = \Drupal::service('renderer')->renderRoot($renderable);
        $response = new Response();
        $response->setContent($output);
        return $response;

        
    }


    public function load_file($key){
        $config = \Drupal::config('ngt_evaluation.settings');  
        $logo_general_fid = reset($config->get('ngt_evaluation_certificate')[$key]);
        $logo_general_file = File::load($logo_general_fid);
        $logo_general_url = isset($logo_general_file) ? $logo_general_file->getFileUri() : '';
        return file_create_url($logo_general_url);
    }

}