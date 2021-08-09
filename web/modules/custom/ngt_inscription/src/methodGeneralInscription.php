<?php 

namespace Drupal\ngt_inscription;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\ngt_inscription\Entity\InscriptionLog;

class methodGeneralInscription{

    /**
     * Obtiene un registro desde el id
     * @param $id
     * @return entity InscriptionLog
     */
    public function getinscripctionById($id) {
        $inscripction = InscriptionLog::load($id);
        return $inscripction;
    }

    /**
     * registra una nueva reserva
     * @param $user_id
     * @param $node_id
     * @return $id
     */
    public function initReserve($user_id, $node_id) {

        $inscripction = InscriptionLog::create();
        $inscripction->set('node_id', $node_id);
        $inscripction->set('user_id', \Drupal::currentUser()->Id());
        $inscripction->save();
        $id = ($inscripction) ? $inscripction->Id() : NULL;

        return $id;
    }
    
    /**
     * searchReserve
     *
     * @param  mixed $node_id
     * @param  mixed $user_id
     * @return array
     */
    public function searchReserve($node_id, $user_id){
        $query = \Drupal::database()->select('ngt_inscription_log', 'ngt');
        $query->condition('user_id', $user_id);
        $query->condition('node_id', $node_id);
        $query->fields('ngt', ['id']);
        $result = $query->execute();
        $results = $result->fetchAll();
        if(count($results) > 0) {
            return [
                'result' => 'yes',
                'id' => $results[0]->id,
            ];
        }else{
            return [
                'result' => 'not',
            ];
        }
    }

    /**
     * elimina una reserva
     * @param $user_id
     * @param $node_id
     * @param $id
     * @return bool
     */
    public function deleteReserve($user_id, $node_id, $id){

        $query = \Drupal::database()->delete('ngt_inscription_log');
        $query->condition('id', $id);
        $query->condition('user_id', $user_id);
        $query->condition('node_id', $node_id);
        $result = $query->execute();

        if($result){
            return [
                'status' => '200',
            ];
        }

        return [
            'status' => '500',
        ];

    }

    /**
     * loadCourseByUserId
     *
     * @return array
     */
    public function loadCourseByUserId(){
        $user_id = \Drupal::currentUser()->Id();
        $query = \Drupal::database()->select('ngt_inscription_log', 'ngt');
        $query->condition('user_id', $user_id);
        $query->fields('ngt', ['id','user_id','node_id','created']);
        $result = $query->execute();
        $results = $result->fetchAll();
        if(count($results) > 0) {
            return [
                'result' => 'yes',
                'data' => $results,
            ];
        }else{
            return [
                'result' => 'not',
            ];
        }
    }

}