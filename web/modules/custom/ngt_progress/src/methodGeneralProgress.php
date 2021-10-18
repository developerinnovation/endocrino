<?php 

namespace Drupal\ngt_progress;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\ngt_progress\Entity\ProgressLog;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

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
        $data = [];
        $user_id = \Drupal::currentUser()->Id();
        $query = \Drupal::database()->select('ngt_progress_log', 'ngt');
        $query->condition('user_id', $user_id);
        $query->fields('ngt', ['id','user_id','node_id','parent_node_id','type']);
        $result = $query->execute();
        $results = $result->fetchAll();
        if(count($results) > 0) {
            return [
                'result' => 'yes',
                'data' => $this->preparerate($results),
            ];
        }else{
            return [
                'result' => 'not',
            ];
        }
    }

    /**
     * preparerate
     *
     * @param  array $node
     * @return array
     */
    public function preparerate($nodes, $showUrl = true){
        $courses = [];
        
        foreach ($nodes as $item) {
            $node = \Drupal::entityManager()->getStorage('node')->load($item->node_id);
            if ($node) {
                $type = $node->get('type')->getValue()[0]['target_id'];
                if ($type == 'curso') {
                    $date = new DrupalDateTime($node->get('field_fecha_de_inicio')->getValue()[0]['value']);
                    $formatted_date = \Drupal::service('date.formatter')->format($date->getTimestamp(), 'custom', 'M d, Y');
                    $modules = isset($node->field_modulo->getValue()[0]['target_id']) ? \Drupal::service('ngt_general.methodGeneral')->load_module_course($node->field_modulo->getValue()): NULL;
                    $video = isset($node->get('field_url_video')->getValue()[0]) ? $node->get('field_url_video')->getValue()[0]['uri'] : '';
                    $video = explode('/', $video);
                    $video = is_array($video) ? end($video) : null;
                    $video = $video != null ? 'https://www.youtube.com/embed/' . $video : null;
                    $course = [
                        'uid' => \Drupal::currentUser()->id(),
                        'nid' => $node->get('nid')->getValue()[0]['value'],
                        'title' => $node->get('title')->getValue()[0]['value'],
                        'body' => isset($node->get('body')->getValue()[0]['value']) ? $node->get('body')->getValue()[0]['value'] : '',
                        'resume' => isset($node->get('field_resumen')->getValue()[0]['value']) ? $node->get('field_resumen')->getValue()[0]['value'] : '',
                        'autor' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_autor_principal')->getValue()),
                        'coordinadores' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_coordinadores')->getValue()),
                        'expertos' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_expertos')->getValue()),
                        'organizador' => \Drupal::service('ngt_general.methodGeneral')->load_organizer($node->get('field_organizador')->getValue()[0]['target_id']),
                        'hours' => $node->get('field_horas')->getValue()[0]['value'],
                        'foto_portada' => [
                            'uri' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id']),
                            'uri_360x196' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'360x196'),
                            'uri_604x476' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'604x476'),
                            'target_id' => $node->get('field_foto_portada')->getValue()[0]['target_id'],
                            'alt' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['alt'] : '',
                            'title' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['title'] : '',
                            'width' => $node->get('field_foto_portada')->getValue()[0]['width'],
                            'height' => $node->get('field_foto_portada')->getValue()[0]['height'],
                        ],
                        'video' => $video,
                        'modules' => $modules,
                        'showUrl' => $showUrl,
                        'formatted_date' => $formatted_date,
                        'type' => 'curso',
                    ];
                    array_push($courses,$course);
                } 
            }
        }
        return $courses;
    }
}