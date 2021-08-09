<?php 

namespace Drupal\ngt_login;

use Drupal\file\Entity\File;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\ngt_general\Base64Image;

class methodGeneralLogin{

  /**
     * validateUserByMail
     *
     * @param  mixed $mail
     * @return void
     */
    public function validateUserByMail($mail){
        \Drupal::service('page_cache_kill_switch')->trigger();
        $user = user_load_by_mail($mail);
        $conf = \Drupal::config('ngt_login.settings');
        if($user){
            $message = $conf->get('ngt_forgot_password')['code_send'];
            $dataTmpStore = $this->createKeyDinamic($mail, $user);
            $data = [
                'status' => '200',
                'message' => $message,
            ];
        }else{
            $message = $conf->get('ngt_forgot_password')['error_code_send'];
            $data = [
                'status' => '500',
                'message' => $message,
            ];
        }
        return $data;
    }
    
    /**
     * createKeyDinamic
     * 
     * Permite generar un cóodigo de 6 cifras
     *
     * @return void
     */
    public function createKeyDinamic($mail, $user = NULL){
        $tokens = [];
        mt_srand(time());
        $mt_rand = mt_rand(100000,50000000);
        $keyDinamic = substr($mt_rand, '4', '3').substr($mt_rand, '2', '3');
        $this->saveTmpStore($mail, $keyDinamic);
        $data = [
            'code' => $keyDinamic,
            'mail' => $mail,
        ];
        $tokens['code'] = $keyDinamic;
        $template = 'notification_get_code';
        \Drupal::service('ngt.send')->send_notification($tokens, $template, $user);
        return $data;
    }

    
    /**
     * saveTmpStore
     *
     * @param  mixed $mail
     * @param  mixed $keyDinamic
     * @return void
     */
    public function saveTmpStore($mail, $keyDinamic){
        $keyTmpStore = sha1($keyDinamic.$mail);
        $tmpStore = \Drupal::service('tempstore.shared')->get('ngt_general.keyDinamic');
        $dataToSave = [
            'hash' => $keyTmpStore,
            'mail' => $mail,
            'keyDinamic' => $keyDinamic,
        ];
        $dataToSaveJson = json_encode($dataToSave);
        $tmpStore->set($keyTmpStore, $dataToSaveJson);
    }
    
    /**
     * verifyCode
     *
     * @param  mixed $mail
     * @param  mixed $keyDinamic
     * @return void
     */
    public function verifyCode($mail, $keyDinamic, $clearCode = false){
        \Drupal::service('page_cache_kill_switch')->trigger();
        $dataTmpStore = null;
        $keyTmpStore = sha1($keyDinamic.$mail);
        $conf = \Drupal::config('ngt_login.settings');
        $message_error = $conf->get('ngt_forgot_password')['error_code'];
        if($keyTmpStore){
            $tmpStore = \Drupal::service('tempstore.shared')->get('ngt_general.keyDinamic');
            $dataTmpStore = (array)json_decode($tmpStore->get($keyTmpStore));
            if($dataTmpStore != null){
                if ($clearCode){
                    $tmpStore->delete($keyTmpStore);
                }
                $data = [
                    'status' => '200',
                    'data' => $dataTmpStore,
                ];
            }else{
                $data = [
                    'status' => '500',
                    'message' => $message_error, 
                ];
            }
            return $data;
        }
        
        
        
        $data = [
            'status' => '500',
            'message' => $message_error, 
        ];
        
        return $data;
    }
    
    /**
     * setNewPass
     *
     * @param  mixed $mail
     * @param  mixed $key_dinamic
     * @param  mixed $pass
     * @return void
     */
    public function setNewPass($mail, $key_dinamic, $pass){
        \Drupal::service('page_cache_kill_switch')->trigger();
        $user = user_load_by_mail($mail);

        if($user){
            $verifyCode = $this->verifyCode($mail, $key_dinamic, true);
            if($verifyCode['status'] == '200'){
                $user->setPassword($pass);
                $user->save();
                $data = [
                    'status' => '200',
                ];
            }else{
                $data = [
                    'status' => '500', 
                ];
            }
        }else{
            $data = [
                'status' => '500', 
            ];
        }
        return $data;
    }

    
    /**
     * setNewUser
     *
     * @param  mixed $params
     * @return void
     */
    public function setNewUser($params){

        $havePic = ($params['pic'] != '') ? true : false;
        if($havePic){
            // $file = $this->preparate_image_profile($params);
            // $fid = $file->id();
        }

        $mail = $params['email'];
        $pass = $params['repeat_pass'];

        $name = $params['name'];
        $name = explode(' ', $name);
        $count_name = count($name);

        switch ($count_name) {
            case 3:
                    $field_nombre =  $name[0] . ' ' . $name[1];
                    $field_apellidos =  $name[2];
                break;
            
            case 4:
                    $field_nombre =  $name[0] . ' ' . $name[1];
                    $field_apellidos =  $name[2] . ' ' . $name[3];
                break;
            default:
                    $field_nombre =  $name[0];
                    $field_apellidos =  $name[1];
                break;
        }


        $field_ubicacion_geografica =  $params['countrySelect'];
        $field_profesion =  $params['professionSelect'];
        $field_fecha_nacimiento =  $params['date'];

        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $user = \Drupal\user\Entity\User::create();

        $user->setPassword($pass);
        $user->enforceIsNew();
        $user->setEmail($mail);
        $user->setUsername($mail);
      
        $user->set('field_ubicacion_geografica', $field_ubicacion_geografica);
        $user->set('field_profesion', $field_profesion);
        $user->set('field_nombre', $field_nombre);
        $user->set('field_apellidos', $field_apellidos);
        $user->set('field_fecha_nacimiento', $field_fecha_nacimiento);

        if($havePic){
            if($fid != 0){
                $user->set('user_picture', $fid);
            }
        }        

        $user->set('init', 'email');
        $user->set('langcode', $language);
        $user->set('preferred_langcode', $language);
        $user->set('preferred_admin_langcode', $language);
        $user->addRole('alumnos');
        $user->activate();
        $user->save();


        $tokens['email'] = $mail;
        $tokens = [
            'email' => $mail,
            'link' => \Drupal::request()->getHost() .'/user/login',
        ];
        $template = 'notification_new_user';
        \Drupal::service('ngt.send')->send_notification($tokens, $template, $user);

        return [
            'status' => '200',
            'data' => $user,
        ];
    }
    
    /**
     * preparate_image_profile
     *
     * @param  mixed $params
     * @return void
     */
    public function preparate_image_profile($params){
        
        $path = 'pictures/' . date('Y'). '/'. date('m'). '/'. date('d');
        $img = new Base64Image($params['pic']);
        $upload_location = '://';
        $img->setFileDirectory($path, $upload_location);
        $file = file_save_data($img->getFileData(), 's3://pictures/' . $path . '/' . $img->getFileName() , FILE_EXISTS_REPLACE);
        // $img->setImageStyleImages($path);
        if($file){
            return $file->id();
        }
        return 0;
    }
    
    /**
     * setEditUser
     *
     * @param  mixed $params
     * @return void
     */
    public function setEditUser($params){
        $havePic = ($params['pic'] != '') ? true : false;
        if($havePic){
            // $file = $this->preparate_image_profile($params);
            // $fid = $file->id();
        }

        $mail = $params['email'];
        $pass = $params['repeat_pass'];

        $name = $params['name'];
        $name = explode(' ', $name);
        $count_name = count($name);

        switch ($count_name) {
            case 3:
                    $field_nombre =  $name[0] . ' ' . $name[1];
                    $field_apellidos =  $name[2];
                break;
            
            case 4:
                    $field_nombre =  $name[0] . ' ' . $name[1];
                    $field_apellidos =  $name[2] . ' ' . $name[3];
                break;
            default:
                    $field_nombre =  $name[0];
                    $field_apellidos =  $name[1];
                break;
        }


        $field_ubicacion_geografica =  $params['countrySelect'];
        $field_profesion =  $params['professionSelect'];
        $field_fecha_nacimiento =  $params['date'];

        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $user = \Drupal\user\Entity\User::create();

        $user->setPassword($pass);
        $user->enforceIsNew();
        $user->setEmail($mail);
        $user->setUsername($mail);
      
        $user->set('field_ubicacion_geografica', $field_ubicacion_geografica);
        $user->set('field_profesion', $field_profesion);
        
        if($field_nombre != ''){
            $user->set('field_nombre', $field_nombre);
        }
        
        if($field_apellidos != ''){
            $user->set('field_apellidos', $field_apellidos);
        }

        if($field_fecha_nacimiento != ''){
            $user->set('field_fecha_nacimiento', $field_fecha_nacimiento);
        }
        
        

        if($havePic){
            if($fid != 0){
                $user->set('user_picture', $fid);
            }
        }        
        $user->save();

        $tokens = [
            'email' => $mail,
        ];
        $template = 'notification_edit_user';
        \Drupal::service('ngt.send')->send_notification($tokens, $template, $user);

        return [
            'status' => '200',
            'data' => $user,
        ];
    }
}