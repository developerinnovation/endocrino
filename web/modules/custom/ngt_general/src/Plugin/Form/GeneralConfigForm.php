<?php  

namespace Drupal\ngt_general\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class GeneralConfigForm extends ConfigFormBase {  

    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_general.settings',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_form_general_settings';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = $this->config('ngt_general.settings');  

        $form['#tree'] = true;

        $form['general_settings'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones generales'),   
            '#open' => false,  
        ]; 

        $form['general_settings']['img_logo'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general_settings')['img_logo'],  
            '#description' => t('Logo general de la plataforma'),
            '#required' => true
        ]; 

        $form['general_settings']['img_logo_mobile'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo mobile'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general_settings')['img_logo_mobile'],  
            '#description' => t('Logo dispositivos móvil de la plataforma'),
            '#required' => true
        ]; 

        $form['general_settings']['activate_img_logo_second'] = [  
            '#type' => 'checkbox',
            '#title' => t('Utilizar logo secundario'),   
            '#default_value' => $config->get('general_settings')['activate_img_logo_second'],   
            '#description' => t('Permite indicar a la plataforma si debe utilizar un logo secundario'),
        ]; 

        $form['general_settings']['img_logo_second'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo secundario'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general_settings')['img_logo_second'],  
            '#description' => t('Logo secundario de la plataforma'),
            '#required' => false
        ]; 
        

        $form['general_settings']['img_logo_design_by'] = [  
            '#type' => 'managed_file',
            '#upload_location' => 's3://file-project',
            '#title' => t('Logo diseñado por'),
            '#upload_validators' => [
                'file_validate_extensions' => ['png svg']
            ],
            '#default_value' => $config->get('general_settings')['img_logo_design_by'],  
            '#description' => t('Logo compañía desarrolladora'),
            '#required' => false
        ]; 

        // text header home

        $form['home_text_header'] = [  
            '#type' => 'details',
            '#title' => t('Configuración text header home'),   
            '#open' => false,  
        ]; 

        $form['home_text_header']['active_message'] = [  
            '#type' => 'checkbox',
            '#title' => t('Activar texto home header '),   
            '#default_value' => $config->get('home_text_header')['active_message'],   
            '#description' => t('Permite indicar a la plataforma si debe mostrar el texto en el header del home'),
        ];

        $form['home_text_header']['txt'] = [  
            '#type' => 'textarea',  
            '#title' => 'Texto home header',  
            '#description' => t('Texto a mostrar en home, en el header'),  
            '#default_value' => isset($config->get('home_text_header')['txt']) ? $config->get('home_text_header')['txt'] : 'Investigación en Reumatología | Proyecto originado por un subsidio de ILAR (International League of Associations for Rheumatology)',    
            '#required' => true
        ]; 
        

        // otras config

        $form['other_settings'] = [  
            '#type' => 'details',
            '#title' => t('Otras configuraciones'),   
            '#open' => false,  
        ]; 

        $form['other_settings']['txt_curso'] = [  
            '#type' => 'textarea',  
            '#title' => 'Texto home cursos',  
            '#description' => t('Texto a mostrar en home, arriba de curso destacado'),  
            '#default_value' => isset($config->get('other_settings')['txt_curso']) ? $config->get('other_settings')['txt_curso'] : 'Aenean commodo ligula eget dolor, aenean massa, Cum sociis natoque penatibus et magnis dis parturient montes, nascetur',    
            '#required' => true
        ]; 

        $form['other_settings']['newsletter'] = [  
            '#type' => 'textfield',
            '#title' => t('Invitación suscripción newsletter'),   
            '#default_value' => isset($config->get('other_settings')['newsletter']) ? $config->get('other_settings')['newsletter'] : 'Suscríbete a nuestro newsletter',
            '#required' => true
        ]; 

        $form['other_settings']['txt_newsletter'] = [  
            '#type' => 'textarea',  
            '#title' => t('Texto newsletter'),  
            '#description' => t('Texto a mostrar en formulario de newsletter'),  
            '#default_value' => isset($config->get('other_settings')['txt_newsletter']) ? $config->get('other_settings')['txt_newsletter'] : 'Aenean commodo ligula eget dolor, aenean.',    
            '#required' => true
        ]; 

        $form['other_settings']['input_newsletter'] = [  
            '#type' => 'textfield',  
            '#title' => t('Texto placeholder input newsletter'),   
            '#default_value' => isset($config->get('other_settings')['input_newsletter']) ? $config->get('other_settings')['input_newsletter'] : 'Correo electrónico',    
            '#required' => true
        ]; 

        $form['other_settings']['btn_newsletter'] = [  
            '#type' => 'textfield',  
            '#title' => t('Texto botón suscribirme a newsletter'),   
            '#default_value' => isset($config->get('other_settings')['txt_newsletter']) ? $config->get('other_settings')['txt_newsletter'] : 'Suscribirme',    
            '#required' => true
        ]; 

        $form['other_settings']['txt_footer'] = [  
            '#type' => 'textarea',  
            '#title' => t('Texto footer'),  
            '#description' => t('Texto a mostrar en footer'),  
            '#default_value' => isset($config->get('other_settings')['txt_footer']) ? $config->get('other_settings')['txt_footer'] : 'Aenean commodo ligula eget dolor, aenean massa, cum sociis natoque penatibus et magnis dis parturient montes, nascetur',  
            '#required' => true
        ]; 


        // terminos y condiciones

        $form['general_terms_conditions'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones de Política de Privacidad y Tratamiento de Datos'),   
            '#open' => false,  
        ]; 

        $entity = isset($config->get('general_terms_conditions')['terms_internal_page']) ? \Drupal\node\Entity\Node::load($config->get('general_terms_conditions')['terms_internal_page']) : '';
        
        $form['general_terms_conditions']['terms_internal_page'] = [
            '#title' => t('Página interna'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#default_value' => $entity,
            '#description' => t('Selecciona una página básica a mostrar'),
        ];

        $form['general_terms_conditions']['terms_external_url'] = [
            '#type' => 'url',
            '#title' => t('URL externa'),
            '#description' => t('Escriba la URL externa que se cargará en una nueva pestaña del navegador o en el correo electrónico que reciba'),
            '#default_value' => $config->get('general_terms_conditions')['terms_external_url'],
        ];

        $form['general_terms_conditions']['terms_text'] = [
            '#type' => 'text_format',
            '#format' => 'full_html',
            '#title' => t('Política de privacidad y tratamiento de datos'),
            '#description' => t('Utilice la etiqueta "a" para establecer el link correspondiente a Terminos y Condiciones '),
            '#required' => TRUE,
            '#default_value' => t('He leido y acepto la <a>Política de Privacidad y Tratamiento de Datos.</a>'),
        ];


        // terminos y condiciones

        $form['script_settings'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones de script o códigos externos'),   
            '#open' => false,  
        ]; 

        $form['script_settings']['external'] = [  
            '#type' => 'textarea',  
            '#title' => t('Script externos'),  
            '#description' => t('Script adicione auqí totos los códigos o script externos'),  
            '#default_value' => isset($config->get('script_settings')['external']) ? $config->get('script_settings')['external'] : '',    
            '#required' => true
        ]; 

        return parent::buildForm($form, $form_state);
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  
        
        parent::submitForm($form, $form_state);
        
        $this->config('ngt_general.settings')
            ->set('general_settings', $form_state->getValue('general_settings'))  
            ->set('general_terms_conditions', $form_state->getValue('general_terms_conditions'))  
            ->set('home_text_header', $form_state->getValue('home_text_header'))
            ->set('other_settings', $form_state->getValue('other_settings'))  
            ->set('script_settings', $form_state->getValue('script_settings'))
            ->save();  
            
        $fid_logo_general_settings = $form_state->getValue('general_settings')['img_logo'];
        $fid_logo_mobile_settings = $form_state->getValue('general_settings')['img_logo_mobile'];
        $fid_logo_second = $form_state->getValue('general_settings')['img_logo_second'];
        $fid_logo_design = $form_state->getValue('general_settings')['img_logo_design_by'];
        if ($fid_logo_general_settings) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_general_settings);
        }
        if ($fid_logo_second) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_second);
        }
        if ($fid_logo_design) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_design);
        }
        if ($fid_logo_mobile_settings) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_design);
        }
    }  

}