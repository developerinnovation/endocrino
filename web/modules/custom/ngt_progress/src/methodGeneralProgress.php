<?php 

namespace Drupal\ngt_progress;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\ngt_progress\Entity\ProgressLog;

class methodGeneralProgress{

    /**
     * Obtiene un registro desde el id
     * @param $id
     * @return entity ProgressLog
     */
    public function getprogressById($id) {
        $progress = ProgressLog::load($id);
        return $progress;
    }

    /**
     * registra un inicio de progreso
     * @param $user_id
     * @param $node_id
     * @return $id
     */
    public function initProgress($node_id, $type) {
        $searchProgress = $this->searchProgress($node_id, $type);
        if($searchProgress['result'] == 'not'){
            $parentId = $node_id;
            if($type == 'leccion'){
                $parentId = \Drupal::service('ngt_general.methodGeneral')->get_module_by_lesson($node_id);
            }
            $progress = ProgressLog::create();
            $progress->set('node_id', $node_id);
            $progress->set('parent_node_id', $parentId);
            $progress->set('user_id', \Drupal::currentUser()->Id());
            $progress->set('type', $type);
            $progress->save();
            $id = ($progress) ? $progress->Id() : NULL;
            return $id;
        }
        
    }
    
    /**
     * searchProgress
     *
     * @param  int $node_id
     * @param  string $type
     * @return array
     */
    public function searchProgress($node_id, $type){
        $user_id = \Drupal::currentUser()->Id();
        $query = \Drupal::database()->select('ngt_progress_log', 'ngt');
        $query->condition('user_id', $user_id);
        $query->condition('node_id', $node_id);
        $query->condition('type', $type);
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
     * loadProgressByUserId
     *
     * @return array
     */
    public function loadProgressByUserId(){
        $user_id = \Drupal::currentUser()->Id();
        $query = \Drupal::database()->select('ngt_progress_log', 'ngt');
        $query->condition('user_id', $user_id);
        $query->fields('ngt', ['id','user_id','node_id','parent_node_id','type']);
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