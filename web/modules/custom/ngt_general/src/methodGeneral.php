<?php 

namespace Drupal\ngt_general;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\media\Entity\Media;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;

class methodGeneral{
   
   
    /**
     * @param string $fid
     *   File id.
     */
    public function setFileAsPermanent($fid) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        if (is_array($fid)) {
            $fid = array_shift($fid);
        }

        $file = File::load($fid);
        if (!is_object($file)) {
            return;
        }

        $file->setPermanent();
        $file->save();
        \Drupal::service('file.usage')->add($file, 'ngt', 'ngt', $fid);
    }

        
    /**
     * renderLogo
     *
     * @return void
     */
    public function renderLogo(){
        \Drupal::service('page_cache_kill_switch')->trigger();
        // build uri logo
        $logo = [
            'image_src_general_settings_logo' => '',
            'image_src_img_second_logo' => '',
            'activated_second_logo' => false,
        ];

        $image_src_general_settings_logo = '';
        $image_src_img_second_logo = '';

        $conf = \Drupal::config('ngt_general.settings');
        $img_general_settings_logo = $conf->get('general_settings')['img_logo'];
        $img_second_logo = $conf->get('general_settings')['img_logo_second'];
        $activate_img_logo_second = $conf->get('general_settings')['activate_img_logo_second'];

        $logo['activated_second_logo'] = $activate_img_logo_second == true ? true : false;
        
        if ( is_array($img_general_settings_logo) ) {
            $fid = reset($img_general_settings_logo);  
            $file = File::load($fid);
            isset($file) ? $logo['general_settings_logo'] = $file->getFileUri() : '';
        }

        if ( is_array($img_second_logo) ) {
            $fid = reset($img_second_logo);  
            $file = File::load($fid);
            isset($file) ? $logo['second_logo'] = $file->getFileUri() : '';
        }
        
        return $logo;
    }
    
    /**
     * loadTermByCategory
     *
     * @param  string $name
     * @return array
     */
    public function loadTermByCategory($name, $parent = 0){
        
        $term = [];
        
        $query = \Drupal::entityQuery('taxonomy_term');
        $query->condition('vid', $name);
        $tids = $query->execute();

        if($tids){
            $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
            foreach ($terms as $value) {
                if($value->get('parent')->getValue()[0]['target_id'] == $parent){
                    array_push($term,[
                        'tid' => $value->tid->value,
                        'name' => $value->name->value
                    ]);
                }
            }
        }
        
        return $term;
    }

      /**
     * load_image
     *
     * @param  int $media_field
     * @return url
     */
    public function load_image($media_field, $style = NULL){
        $file = File::load($media_field);
        $url = $file->getFileUri();
        if ($style != NULL){
            $url = ImageStyle::load($style)->buildUrl($url);
        }
        return $url;
    }

    /**
     * load_url_file
     *
     * @param  int $media_field
     * @return string url
     */
    public function load_url_file($media_field){
        $file = File::load($media_field);
        $url = file_create_url($file->getFileUri());
        return $url;
    }
    
    /**
     * load_author
     *
     * @param  array $authors
     * @param  int $limit
     * @return array
     */
    public function load_author($authors, $limit = NULL){
        $expertos = [];
        $i = 1;
        foreach ($authors as $key => $author) {
            $user =   User::load($author['target_id']); 
            $experto = [
                'uid' => $user->get('uid')->getValue()[0]['value'],
                'name_author' => ucfirst($user->get('field_nombre')->getValue()[0]['value'])." ".ucfirst($user->get('field_apellidos')->getValue()[0]['value']),
                'picture_uri' => $this->load_image($user->get('user_picture')->getValue()[0]['target_id'],'200x200'),
                'picture_uri_200x200' => $this->load_image($user->get('user_picture')->getValue()[0]['target_id'],'medium'),
                'uri' => \Drupal::service('path.alias_manager')->getAliasByPath('/user/'.$user->get('uid')->getValue()[0]['value']),
                'profile' => $user->get('field_perfil')->getValue()[0]['value'],
            ];
            array_push($expertos,$experto);
            if ($limit != NULL){
                if($i == $limit){
                    break;
                }
                $i++;
            }
        }
        
        return $expertos;
    }
    
    /**
     * load_resource
     *
     * @param  array $resources
     * @return array
     */
    public function load_resource($resources){
        $recursos = [];
        $resourcesArray = $resources;
        foreach ($resourcesArray as $key => $resource) {
            $file = File::load($resource['target_id']);
            $url = file_create_url($file->getFileUri());
            $filename = $file->get('filename')->getValue()[0]['value'];
            $filenameArray = explode('.', $filename);
            array_pop($filenameArray);
            $filenameArrayData = $filenameArray;
            $title = implode('', $filenameArrayData);
            $typeFileData = $file->get('filename')->getValue()[0]['value'];
            $fileDataExplode = explode('.', $typeFileData);
            $typeFile = end($fileDataExplode);
            $recurso = [
                'title' => $title,
                'description' => $resource['description'],
                'extension' => $typeFile,
                'url' => $url,
            ];
            array_push($recursos,$recurso);
        }
        return $recursos;
    }
    
    /**
     * load_module_course
     *
     * @param  mixed $paragraph
     * @return void
     */
    public function load_module_course($paragraph){
        $modules = [];
        $i = 1;
        $paragraphArray = $paragraph;
        foreach ( $paragraphArray as $element ) {
            $module = \Drupal\paragraphs\Entity\Paragraph::load( $element['target_id'] );
            $lessons = isset($module->get('field_leccion')->getValue()[0]['target_id']) ? $this->load_lesson_module($module->get('field_leccion')->getValue()) : NULL;
            $nid = $module->get('parent_id')->getValue()[0]['value'];
            $quiz = isset($module->get('field_examen')->getValue()[0]['target_id']) ? $this->load_quiz($module->get('field_examen')->getValue(), $i, $nid) : NULL;
            array_push($modules, [
                'nidModule' => $nid,
                'numModule' => $i,
                'moduleId' => 'Módulo '. $i,
                'titleModule' => $module->get('field_titulo_del_modulo')->getValue()[0]['value'],
                'lessons' => $lessons,
                'quiz' => $quiz,
            ]);
            $i++;
        }
        return $modules;
    }

    
    /**
     * load_lesson_module
     *
     * @param  mixed $lesson
     * @return void
     */
    public function load_lesson_module($lessons){
        $lessonByModule = [];
        $lessonsArray = $lessons;
        if($lessons != NULL){
            foreach ($lessonsArray as $key => $lesson) {
                $node = \Drupal\node\Entity\Node::load($lesson['target_id']);
                $url = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $node->get('nid')->getValue()[0]['value']);
                array_push($lessonByModule, [
                    'title' => $node->get('title')->getValue()[0]['value'],
                    'body' => isset($node->get('body')->getValue()[0]['value']) ? $node->get('body')->getValue()[0]['value'] : '',
                    'url' => $url,
                    'nid' => $node->get('nid')->getValue()[0]['value'],
                ]);
            }
        }
        return $lessonByModule;
    }
    
    /**
     * load_quiz
     *
     * @param  mixed $quiz
     * @return void
     */
    public function load_quiz($quiz, $idModule, $nidCourse){
        $quizByModuleCourse = [];
        $quizArray = $quiz;
        if($quiz != NULL){
            foreach ($quizArray as $key => $data) {
                $node = \Drupal\node\Entity\Node::load($data['target_id']);
                $url = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $node->get('nid')->getValue()[0]['value']);
                $time = time();
                $url = $url . '?token='. $idModule .'-'.md5($time) . '-'. $nidCourse;
                array_push($quizByModuleCourse, [
                    'title' => $node->get('title')->getValue()[0]['value'],
                    'url' => $url,
                    'nid' => $node->get('nid')->getValue()[0]['value'],
                ]);
            }
        }
        return $quizByModuleCourse;
    }
    
    /**
     * get_module_by_lesson
     *
     * @param  mixed $idLesson
     * @return void
     */
    public function get_module_by_lesson($idLesson){
        $query = 'SELECT leccion.field_leccion_target_id, curso.parent_id FROM paragraph__field_leccion leccion
                INNER JOIN paragraphs_item_field_data curso
                ON leccion.entity_id = curso.id
                WHERE leccion.field_leccion_target_id = ' . $idLesson;
        
        $result = [];
        $db = \Drupal::database();
        $select = $db->query($query);
        $result = $select->fetchAll();
        
        if($result[0]){
            return $result[0]->parent_id;
        }
        return NULL;
    }

    /**
     * in_array_r
     *
     * @param  mixed $needle
     * @param  mixed $haystack
     * @param  mixed $strict
     * @return void
     */
    public function in_array_r($needle, $haystack, $strict = false) {
        $haystackArray = $haystack;
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * get_last_prev_lesson
     *
     * @param  int $courseId
     * @param  int $lessonId
     * @return array
     */
    public function get_last_prev_lesson($courseId, $lessonId){

        $query = 'SELECT leccion.field_leccion_target_id FROM paragraphs_item_field_data curso
            INNER JOIN paragraph__field_leccion leccion
            ON leccion.entity_id = curso.id
            WHERE curso.parent_id = ' . $courseId;

        $result = NULL;

        $path = [
            'next' => NULL,
            'prev' => NULL,
        ];

        $db = \Drupal::database();
        $select = $db->query($query);
        $result = $select->fetchAll();

        if($result){
            $lessons = [];
            foreach ($result as $leccion) {
                array_push($lessons, $leccion->field_leccion_target_id);
            }

            $position = array_search($lessonId, $lessons); 
            if($lessons[$position - 1] != NULL) {
                $path['prev'] =  \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $lessons[$position-1]);
            } 
            
            if($lessons[$position +1 ] != NULL) {
                $path['next'] = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $lessons[$position+1]);
            } 
               
        }
        return $path;
    }

}
