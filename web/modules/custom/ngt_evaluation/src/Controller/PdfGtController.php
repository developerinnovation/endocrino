<?php

namespace Drupal\ngt_evaluation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxy;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Render\Renderer;
use Drupal\Component\Utility\Html ;
use Drupal\Core\Database\Connection;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class PdfGtController.
 *
 * @package Drupal\ngt_evaluation\Controller
 */
class PdfGtController extends ControllerBase {

  /**
   * @var configurationInstance
   *   Drupal\pdf_generator\Plugin\DomPdfGenerator
   */
  protected $configurationInstance;

  /**
   * PdfGtController constructor.
   */
  public function __construct() {
    $this->configurationInstance = \Drupal::service('pdf_generator.dompdf_generator');
    $this->api = '';
    //$response = $service->response($title, $content);
  }

  /**
   * Returns a redirect response object for the specified route.
   *
   * @param string $provider
   *   The name of the provider(COMCEL, SICESA, NAVEGA).
   * @param string $option
   *   Option value is refer to user come from menu or direct access (detalle, menu)
   * @param string $url
   *   URL to redirect User
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response object that may be returned by the controller.
   */

  public function generatePdf(Request $request, $courserId, $evaluationId, $moduleId, $userId, $tokenId, $logId) {
    $data = [];
    $time = new \DateTime("now");

    $current_user = \Drupal::currentUser();
    $created = $time->format('d/m/Y');

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
    
    $renderable = [
        'theme' => 'certificate_render',
        'logo' => $this->load_file('img_logo_certificate'),
        'background' => $this->load_file('backgound'),
        'signature_president'  => $this->load_file('signature_president'),
        'signture_coordinator_1' => NULL,
        'signture_coordinator_2' => NULL,
        'token' => strtoupper($tokenId),
        'type_approved' => $type_approved,
        'message' => $message,
        'name_user' => ucfirst($nombre) .' '.ucfirst($apellidos),
        'date' => \Drupal::service('date.formatter')->format(intval($created), 'certificado', ''),
        'cache' => ['max-age' => 0],
    ];

    try {
      $responsePdf = $this->configurationInstance->postResponse("certificado", $renderable, FALSE, [], 'A4', 'landscape', NULL, NULL);
    } catch (\Throwable $th) {
      $response = [
        'code' => 500,
        'errors' => 'Error al generar el pdf'
      ];
      return new JsonResponse((object) $response);
    }

    $response = [
      'code' => 200,
      'certificate' => $responsePdf,
    ];

    return new JsonResponse((object) $response);
  }

  public function load_file($key){
    $config = \Drupal::config('ngt_evaluation.settings');  
    $logo_general_fid = reset($config->get('ngt_evaluation_certificate')[$key]);
    $logo_general_file = File::load($logo_general_fid);
    $logo_general_url = isset($logo_general_file) ? $logo_general_file->getFileUri() : '';
    return file_create_url($logo_general_url);
  }
  
  /**
   * generatePdfDirect
   *
   * @param  mixed $courserId
   * @param  mixed $evaluationId
   * @param  mixed $moduleId
   * @param  mixed $userId
   * @param  mixed $tokenId
   * @param  mixed $logId
   * @return void
   */
  public function generatePdfDirect($courserId, $evaluationId, $moduleId, $userId, $tokenId, $logId) {
    $data = [];
    $time = new \DateTime("now");

    $current_user = \Drupal::currentUser();
    $created = $time->format('d/m/Y');

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
    
    $renderable = [
        'theme' => 'certificate_render',
        'logo' => $this->load_file('img_logo_certificate'),
        'background' => $this->load_file('backgound'),
        'signature_president'  => $this->load_file('signature_president'),
        'signture_coordinator_1' => NULL,
        'signture_coordinator_2' => NULL,
        'token' => strtoupper($tokenId),
        'type_approved' => $type_approved,
        'message' => $message,
        'name_user' => ucfirst($nombre) .' '.ucfirst($apellidos),
        'date' => \Drupal::service('date.formatter')->format(intval($created), 'certificado', ''),
        'cache' => ['max-age' => 0],
    ];

    try {
      $responsePdf = $this->configurationInstance->postResponse("certificado", $renderable, FALSE, [], 'A4', 'landscape', NULL, NULL);
    } catch (\Throwable $th) {
      $response = [
        'code' => 500,
        'errors' => 'Error al generar el pdf'
      ];
      return new JsonResponse((object) $response);
    }

    $response = [
      'code' => 200,
      'certificate' => $responsePdf,
    ];

    return $response;
  }

}