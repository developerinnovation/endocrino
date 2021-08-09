<?php 

namespace Drupal\ngt_general\Plugin\Config\Form;

use Drupal\Core\Form\FormStateInterface;

class GeneralConfigFormClass{
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_general.adminSettingsGeneral',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_form_general';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = \Drupal::config('ngt_general.adminSettingsGeneral');  

        $form['general'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones generales'),   
            '#open' => false,  
        ]; 

        $form['general']['img_logo'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general')['img_logo'],  
            '#description' => t('Logo general de la plataforma'),
            '#required' => true
        ]; 

        $form['general']['activate_img_logo_second'] = [  
            '#type' => 'checkbox',
            '#title' => t('Utilizar logo secundario'),   
            '#default_value' => $config->get('general')['activate_img_logo_second'],   
            '#description' => t('Permite indicar a la plataforma si debe utilizar un logo secundario'),
        ]; 

        $form['general']['img_logo_second'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo secundario'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general')['img_logo_second'],  
            '#description' => t('Logo secundario de la plataforma'),
            '#required' => false
        ]; 
        
        $form['general']['txt_curso'] = [  
            '#type' => 'textarea',  
            '#title' => 'Texto home cursos',  
            '#description' => t('Texto a mostrar en home, arriba de curso destacado'),  
            '#default_value' => $config->get('general')['txt_curso'],    
            '#required' => true
        ]; 

        $form['general']['txt_newsletter'] = [  
            '#type' => 'textarea',  
            '#title' => t('Texto newsletter'),  
            '#description' => t('Texto a mostrar en formulario de newsletter'),  
            '#default_value' => $config->get('general')['txt_newsletter'],    
            '#required' => true
        ]; 

        $form['general']['txt_footer'] = [  
            '#type' => 'textarea',  
            '#title' => t('Texto footer'),  
            '#description' => t('Texto a mostrar en footer'),  
            '#default_value' => $config->get('general')['txt_footer'],  
            '#required' => true
        ]; 


        // terminos y condiciones

        $form['terms_conditions'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones de Política de Privacidad y Tratamiento de Datos'),   
            '#open' => false,  
        ]; 

        $entity = isset($config->get('terms_conditions')['terms_internal_page']) ? \Drupal\node\Entity\Node::load($config->get('terms_conditions')['terms_internal_page']) : '';
        
        $form['terms_conditions']['terms_internal_page'] = [
            '#title' => t('Página interna'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#default_value' => $entity,
            '#description' => t('Selecciona una página básica a mostrar'),
        ];

        $form['terms_conditions']['terms_external_url'] = [
            '#type' => 'url',
            '#title' => t('URL externa'),
            '#description' => t('Escriba la URL externa que se cargará en una nueva pestaña del navegador o en el correo electrónico que reciba'),
            '#default_value' => $config->get('terms_conditions')['terms_external_url'],
        ];

        $form['terms_conditions']['terms_text'] = [
            '#type' => 'text_format',
            '#format' => 'full_html',
            '#title' => t('Política de privacidad y tratamiento de datos'),
            '#description' => t('Utilice la etiqueta "a" para establecer el link correspondiente a Terminos y Condiciones '),
            '#required' => TRUE,
            '#default_value' => t('He leido y acepto la <a>Política de Privacidad y Tratamiento de Datos.</a>'),
        ];


        return $form;
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  

        $config = \Drupal::configFactory()->getEditable('ngt_general.adminSettingsGeneral');
        $config
            ->set('general', $form_state->getValue('general'))  
            ->set('terms_conditions', $form_state->getValue('terms_conditions'))  
            ->save();  
            
        $fid_logo_general = $form_state->getValue('general')['img_logo'];
        $fid_logo_second = $form_state->getValue('general')['img_logo_second'];
        if ($fid_logo_general) {
            $this->setFileAsPermanent($fid_logo_general);
        }
        if ($fid_logo_second) {
            $this->setFileAsPermanent($fid_logo_second);
        }
    }  

    /**
     * @param string $fid
     *   File id.
     */
    public function setFileAsPermanent($fid) {
        if (is_array($fid)) {
            $fid = array_shift($fid);
        }

        $file = \Drupal\file\Entity\File::load($fid);
        if (!is_object($file)) {
            return;
        }

        $file->setPermanent();
        $file->save();
        \Drupal::service('file.usage')->add($file, 'ngt', 'ngt', $fid);
    }

    public function renderLogo(){
        // build uri logo
        $logo = [
            'image_src_general_logo' => '',
            'image_src_img_second_logo' => '',
            'activated_second_logo' => false,
        ];

        $image_src_general_logo = '';
        $image_src_img_second_logo = '';

        $conf = \Drupal::config('ngt.adminSettingsGeneral');
        $img_general_logo = $conf->get('general')['img_logo'];
        $img_second_logo = $conf->get('general')['img_logo_second'];
        $activate_img_logo_second = $conf->get('general')['activate_img_logo_second'];

        $logo['activated_second_logo'] = $activate_img_logo_second == true ? true : false;
        
        if ( is_array($img_general_logo) ) {
            $fid = reset($img_general_logo);  
            $file = File::load($fid);
            isset($file) ? $logo['general_logo'] = $file->getFileUri() : '';
        }

        if ( is_array($img_second_logo) ) {
            $fid = reset($img_second_logo);  
            $file = File::load($fid);
            isset($file) ? $logo['second_logo'] = $file->getFileUri() : '';
        }
        
        return $logo;
    }
}