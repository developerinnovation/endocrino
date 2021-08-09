<?php  

namespace Drupal\ngt_notification\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class notificationSettingsForm extends ConfigFormBase { 
    
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_notification.settings',  
        ];  
    }  

    /**
     * getFormId
     *
     */
    public function getFormId(){
        return 'ngt_notification_settings_form';
    }

    /**
     * buildForm
     *
     * @param  mixed $form
     * @param  mixed $form_state
     */
    public function buildForm(array $form, FormStateInterface $form_state){

        $config = $this->config('ngt_notification.settings');  

        $form['#tree'] = true;

        $form['notification_images'] = [
            '#type' => 'details',
            '#title' => t('Images'),
            '#open' => FALSE,
        ];

        $form['notification_images']['logo'] = [
            '#type' => 'managed_file',
            '#title' => t('Logo'),
            '#upload_location' => 's3://file-project',
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg jpg jpeg']
            ],
            '#default_value' => $config->get('notification_images')['logo'],
            '#description' => t('Logo general para correos'),
        ];

        // temple new user

        $form['notification_new_user'] = [
            '#type' => 'details',
            '#title' => t('Template para notificar la creación de un nuevo usuario'),
            '#open' => FALSE,
        ];

        $form['notification_new_user']['subject'] = [
            '#type' => 'textfield',
            '#maxlength' => 180,
            '#title' => t('Asunto'),
            '#default_value' => isset($config->get('notification_new_user')['subject']) ? $config->get('notification_new_user')['subject'] : t('Creación de nuevo usuario'),
        ];

        $form['notification_new_user']['body'] = [
            '#type' => 'text_format',
            '#title' => t('Body'),
            '#format' => 'full_html',
            '#default_value' => $config->get('notification_new_user')['body']['value'],
            '#description' => t('Mensaje en formato HTMl.'),
        ];

        // temple recovery pass - get code
        
        $form['notification_get_code'] = [
            '#type' => 'details',
            '#title' => t('Template para notificar código temporal para recuperar contraseña'),
            '#open' => FALSE,
        ];

        $form['notification_get_code']['subject'] = [
            '#type' => 'textfield',
            '#maxlength' => 180,
            '#title' => t('Asunto'),
            '#default_value' => isset($config->get('notification_get_code')['subject']) ? $config->get('notification_get_code')['subject'] : t('Recuperar contraseña'),
        ];

        $form['notification_get_code']['body'] = [
            '#type' => 'text_format',
            '#title' => t('Body'),
            '#format' => 'full_html',
            '#default_value' => $config->get('notification_get_code')['body']['value'],
            '#description' => t('Mensaje en formato HTMl.'),
        ];


        return parent::buildForm($form, $form_state);
    }
    
    /**
     * submitForm
     *
     * @param  mixed $form
     * @param  mixed $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state){

        parent::submitForm($form, $form_state);

        $fid = $form_state->getValue('notification_images')['logo'];
        if($fid){
            \Drupal::service('ngt.methodGeneral')->setFileAsPermanent($fid);
        }
        
        $this->config('ngt_notification.settings')
            ->set('notification_images', $form_state->getValue('notification_images'))  
            ->set('notification_new_user', $form_state->getValue('notification_new_user'))
            ->set('notification_get_code', $form_state->getValue('notification_get_code'))
            ->save();   

    }
    
    /**  
     * {@inheritdoc}  
     */ 
    public function validateFormat(array &$form, FormStateInterface $form_state){
        parent::validateFormat($form, $form_state);
    }

}