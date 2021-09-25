<?php 

namespace Drupal\ngt_evaluation;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\ngt_evaluation\Entity\EvaluationLogs;
use GuzzleHttp\Client;

class methodGeneralEvaluation{

    /**
     * Obtiene un registro desde el id
     * @param $id
     * @return entity EvaluationLogs
     */
    public function getEvaluationById($id) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $evaluation = EvaluationLogs::load($id);
        return $evaluation;
    }

    /**
     * registra el inicio de euna evaluación
     * @param $fields array
     * @return entity EvaluationLogs
     */
    public function initEvaluation($fields = []) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $evaluation = EvaluationLogs::create();
        foreach ($fields as $key => $value) {
            $evaluation->set($key, $value);
        }
        $evaluation->save();
        $id = ($evaluation) ? $evaluation->Id() : NULL;

        if($id != NULL){
            return [
                'status' => '200',
                'id' => $id,
            ];
        }
        return [
            'status' => '500',
        ];
    }
    
    /**
     * Actualiza los datos de la evaluación
     * @param $id
     * @param $fields array
     * @return entity EvaluationLogs
     */
    public function updateDataTransaction($id, $fields = []) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $evaluation = EvaluationLogs::load($id);
            foreach ($fields as $key => $value) {
            $evaluation->set($key, $value);
        }
        $evaluation->save();
        return $evaluation->Id();
    }
    
    /**
     * check_answers_by_evaluation
     *
     * @param  mixed $nid
     * @param  mixed $answers
     * @return void
     */
    public function check_answers_by_evaluation($nid, $answers, $averageMin){
        \Drupal::service('page_cache_kill_switch')->trigger();
        $node = \Drupal\node\Entity\Node::load($nid);
        $questions = $node->field_pregunta->getValue();
        $correctly = [];
        if($questions != NULL){
            foreach ($questions as $question) {
                $q = \Drupal\paragraphs\Entity\Paragraph::load( $question['target_id'] );
                array_push($correctly, $q->get('field_respuesta_correcta')->getValue()[0]['value']);
            }

            $countCorrectly = $this->comparate_answers($answers, $correctly);
            $totalQuestions = count($questions);
            $totalAnswersMin = $totalQuestions * $averageMin / 100;
            $averageObtained = $countCorrectly / $totalQuestions * 100;

            
            $data = [
                'status' => 200,
                'totalAnswersMin' => $totalAnswersMin,
                'totalQuestions' => $totalQuestions,
                'countCorrectly' => $countCorrectly,
                'averageObtained' => $averageObtained,
                'evaluation' => '',
                'token' => '',
                'urlCourse' => '',
            ];

            if($countCorrectly >= $totalAnswersMin){
                $data['evaluation'] = 'god';
            }else{
                $data['evaluation'] =  'bad';
            }

            return $data;
        }

        return [
            'status' => 500,
        ];
    }
    
    /**
     * comparate_answers
     *
     * @param  mixed $answersByAlumno
     * @param  mixed $answersCorrectly
     * @return void
     */
    public function comparate_answers($answersByAlumno, $answersCorrectly){
        $countCorrectly = 0;
        foreach ($answersByAlumno as $key => $value) {
           if($value == $answersCorrectly[$key]){
             $countCorrectly += 1; 
           }
        }

        return $countCorrectly;
    }

    public function getAllCertificate(){
        \Drupal::service('page_cache_kill_switch')->trigger();
        
        $certificates = [];
        $approved = 1;
        
        $uuid = \Drupal::currentUser()->Id();
        $user = \Drupal\user\Entity\User::load($uuid);
        $nombre = $user->get('field_nombre')->getValue()[0]['value'];
        $apellidos = $user->get('field_apellidos')->getValue()[0]['value'];

        $query = \Drupal::database()->select('ngt_evaluation_logs', 'n');
        
        $query->addField('n', 'id');
        $query->addField('n', 'user_id');
        $query->addField('n', 'node_id');
        $query->addField('n', 'course_id');
        $query->addField('n', 'module_id');
        $query->addField('n', 'calification');
        $query->addField('n', 'total_corrrectly_answered');
        $query->addField('n', 'token');
        $query->addField('n', 'changed');
        
        $query->condition('n.user_id', $uuid);
        $query->condition('n.approved', $approved);
        $results = $query->execute()->fetchAll();
        
        if ($results) {
            foreach ($results as $value) {

                $path_pdf = NULL;
                $log_id = $value->id;
                $course_id = $value->course_id;
                $examen_id = $value->node_id;
                $module_id = $value->module_id;
                $certificate_token = $value->token;
                $change = $value->changed;

                $course = \Drupal::entityManager()->getStorage('node')->load($course_id);
                $title_course = $course->get('title')->getValue()[0]['value'];

                $host = \Drupal::request()->getSchemeAndHttpHost();
                $path_consult_certificate = '/render/pdf/certificate/' . $course_id .'/'. $examen_id .'/'. $module_id .'/'. $uuid .'/'. $certificate_token .'/'. $log_id;
                // $data_certificate = \Drupal::service('ngt_evaluation.evaluation_get_pdf')->generatePdfDirect($course_id, $examen_id, $module_id, $uuid, $certificate_token, $log_id);
                // if($data_certificate){
                //     $path_pdf =  $host . $data_certificate['certificate']['browser_path'];
                // }
                array_push ($certificates, [
                    'title_course' => ucfirst($title_course),
                    'id_certificate' => strtoupper($certificate_token),
                    'url_download' => $host.$path_consult_certificate,
                    'name_user' => ucfirst($nombre) .' '.ucfirst($apellidos),
                    'date' => \Drupal::service('date.formatter')->format(intval($change), 'certificado', ''),
                ]);
            }
        }

        return $certificates;
    }

}