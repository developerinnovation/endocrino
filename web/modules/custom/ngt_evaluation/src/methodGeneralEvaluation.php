<?php 

namespace Drupal\ngt_evaluation;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\ngt_evaluation\Entity\EvaluationLogs;

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

}