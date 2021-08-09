<?php 

namespace Drupal\ngt_evaluation\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\ngt_evaluation\Entity\EvaluationLog;

/**
 * Class class EvaluationRestLogic.
 *
 * @package Drupal\ngt_evaluation
 */
class EvaluationRestLogic {


    /**
     * @return \Drupal\rest\ResourceResponse
     */
    public function post($params) {
        // Remove cache.
        \Drupal::service('page_cache_kill_switch')->trigger();
        $data = [];

        $fields = [
            'user_id' =>  \Drupal::currentUser()->Id(),
            'node_id' => $params['nid'],
            'module_id' => $params['moduleId'],
            'type_evaluation' => 'module',
            'attempts' => '1',
            'total_question' => $params['maxNavValue'],
            'course_id' => $params['idCourse'],
        ];

        $data = \Drupal::service('ngt_evaluation.method_general')->initEvaluation($fields);

        return new ResourceResponse($data);
    }
  
    /**
     * @return \Drupal\rest\ResourceResponse
     */
    public function put($params) {
        // Remove cache.
        \Drupal::service('page_cache_kill_switch')->trigger();

        $idEvaluation = $params['idEvaluation'];
        $nidExamen = $params['nid'];
        $averageMin = $params['average'];
        $answerSend = $params['answer'];
        $idCourse = $params['idCourse'];

        $data = \Drupal::service('ngt_evaluation.method_general')->check_answers_by_evaluation($nidExamen, $answerSend, $averageMin);

        $total_question_answered = count($answerSend);

        $token = md5(uniqid(rand(), true));
        $token = substr($token, 0 , 20);

        $fields = [
            'total_question_answered' => $total_question_answered,
            'total_corrrectly_answered' => $data['countCorrectly'],
            'calification' => $data['averageObtained'],
            'approved' => $data['evaluation'] == 'god' ?  true : false,
            'token' => $data['evaluation'] == 'god' ?  $token : '',
        ];

        if($data['evaluation'] == 'god'){
            $data['token'] = $token;
        }

        $data['urlCourse'] = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $idCourse);
        
        \Drupal::service('ngt_evaluation.method_general')->updateDataTransaction($idEvaluation, $fields);

        return new JsonResponse($data);
    }
}