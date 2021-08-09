<?php  

namespace Drupal\ngt_social_shared\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SocialSharedForm extends ConfigFormBase {
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_social_shared.settings',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_social_shared_form_settings';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = $this->config('ngt_social_shared.settings');  


        $form['#tree'] = true;

        // redes sociales a activar

        $form['ngt_social_shared_active'] = [  
            '#type' => 'details',
            '#title' => t('Redes sociales activas'),   
            '#open' => true,  
        ]; 

        $form['ngt_social_shared_active']['image'] = [
            '#type' => 'managed_file',
            '#title' => t('Foto general para compartir '),
            '#default_value' => isset($config->get('ngt_social_shared_active')['image']) ? $config->get('ngt_social_shared_active')['image'] : '',
            '#description' => t('Foto general que saldrá cuando comparta la página principal de la plataforma u otra página que no tenga asociada una foto, de extension png jpg jpeg, tamaño no superior a 1300x820 px'),
            '#upload_location' => 's3://file-project',
            '#upload_validators' => [
              'file_validate_image_resolution' => ['1300x820'],
              'file_validate_extensions' => ['png jpg jpeg'],
            ],
        ];


        $form['ngt_social_shared_active']['facebook'] = [  
            '#type' => 'checkbox',
            '#title' => t('Facebook'),   
            '#default_value' => isset($config->get('ngt_social_shared_active')['facebook']) ? $config->get('ngt_social_shared_active')['facebook'] : 1,
        ]; 

        $form['ngt_social_shared_active']['twitter'] = [  
            '#type' => 'checkbox',
            '#title' => t('Twitter'),   
            '#default_value' => isset($config->get('ngt_social_shared_active')['twitter']) ? $config->get('ngt_social_shared_active')['twitter'] : 1,
        ]; 

       
        // parámetros extra redes sociales

        $form['ngt_social_shared_config'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones de parámetros obligatorios/opcionales en opciones de compartir'),   
            '#open' => true,  
        ]; 


        $form['ngt_social_shared_config']['description'] = [  
            '#type' => 'textfield',
            '#title' => t('Descripción'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['description']) ? $config->get('ngt_social_shared_config')['description'] : 'Cursos en línea',
            '#required' => true,
            '#description' => t('Descripción general del sitio, para los cursos y lecciones se tomará el campo resumen automáticamente.')
        ]; 

        $form['ngt_social_shared_config']['facebook_url'] = [  
            '#type' => 'textfield',
            '#title' => t('Path de facebook'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['facebook_url']) ? $config->get('ngt_social_shared_config')['facebook_url'] : 'http://www.facebook.com/sharer.php',
            '#required' => true
        ]; 

        $form['ngt_social_shared_config']['twitter_url'] = [  
            '#type' => 'textfield',
            '#title' => t('Path de twitter'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['twitter_url']) ? $config->get('ngt_social_shared_config')['twitter_url'] : 'https://twitter.com/intent/tweet',
            '#required' => true
        ]; 

        $form['ngt_social_shared_config']['twitter_via'] = [  
            '#type' => 'textfield',
            '#title' => t('@via para twitter'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['twitter_via']) ? $config->get('ngt_social_shared_config')['twitter_via'] : 'Cursos',
            '#required' => true,
            '#description' => t('Por favor no agregar el @ ya que se agrega automáticamente.')
        ]; 

        $form['ngt_social_shared_config']['twitter_hashtags'] = [  
            '#type' => 'textfield',
            '#title' => t('Hashtags en twitter'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['twitter_hashtags']) ? $config->get('ngt_social_shared_config')['twitter_hashtags'] : '#Cursos',
            '#required' => true,
        ];

        $form['ngt_social_shared_config']['twitter_text'] = [  
            '#type' => 'textfield',
            '#title' => t('Tíitulo a compartir en twitter y facebook'),   
            '#default_value' => isset($config->get('ngt_social_shared_config')['twitter_text']) ? $config->get('ngt_social_shared_config')['twitter_text'] : 'Title',
            '#required' => true,
            '#description' => t('Solo se utilizará para la página principal y landing, no aplican para los cursos y lecciones ya que tienen su propio título.')
        ]; 

        
       
        return parent::buildForm($form, $form_state);
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  
        parent::submitForm($form, $form_state);

        $this->config('ngt_social_shared.settings')
        ->set('ngt_social_shared_active', $form_state->getValue('ngt_social_shared_active'))  
        ->set('ngt_social_shared_config', $form_state->getValue('ngt_social_shared_config'))
        ->save();   

        $fid_logo_design = $form_state->getValue('ngt_social_shared_active')['image'];
        if ($fid_logo_design) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_design);
        }

    }  

    /**  
     * {@inheritdoc}  
     */ 
    public function validateFormat(array &$form, FormStateInterface $form_state){
        parent::validateFormat($form, $form_state);
    }

}