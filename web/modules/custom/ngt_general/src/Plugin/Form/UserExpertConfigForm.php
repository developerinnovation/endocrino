<?php  

namespace Drupal\ngt_general\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class UserExpertConfigForm extends ConfigFormBase {  

    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_general.settings_user_expert',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_form_general_settings_user_expert';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = $this->config('ngt_general.settings_user_expert');  

        $form['#tree'] = true;

        $form['expert'] = [  
            '#type' => 'details',
            '#title' => t('Configuración general bloque de expertos en Home'),   
            '#open' => false,  
        ]; 

        $form['expert']['active'] = [  
            '#type' => 'checkbox',
            '#title' => t('Activar bloque de expertos'),   
            '#default_value' => isset($config->get('expert')['active']) ? $config->get('expert')['active'] : 1,  
        ]; 

        $form['expert']['txt_profile'] = [  
            '#type' => 'textfield',  
            '#title' => 'Texto del botón ver perfiles',  
            '#description' => t('Ingrese el texto a mostrar en botón tipo enlace para ir a perfiles'),  
            '#default_value' => isset($config->get('expert')['txt_profile']) ? $config->get('expert')['txt_profile'] : 'Ver perfiles',    
            '#required' => true
        ]; 

        $form['expert']['ctn'] = [  
            '#type' => 'textfield',  
            '#title' => 'Cantidad de expertos a mostrar',  
            '#description' => t('Ingrese el número de expertos a mostrar'),  
            '#default_value' => isset($config->get('expert')['ctn']) ? $config->get('expert')['ctn'] : 3,    
            '#required' => true
        ]; 
        
        $form['expert']['txt_title'] = [  
            '#type' => 'textarea',  
            '#title' => 'Título del bloque',  
            '#description' => t('Texto a mostrar en home, arriba del listado de expertos'),  
            '#default_value' => isset($config->get('expert')['txt_title']) ? $config->get('expert')['txt_title'] : 'Quienes somos',    
            '#required' => true
        ]; 

        $form['expert']['txt_subtitle'] = [  
            '#type' => 'textarea',  
            '#title' => t('Subtitulo'),  
            '#description' => t('Texto a mostrar debajo del t´tiulo del bloque'),  
            '#default_value' => isset($config->get('expert')['txt_subtitle']) ? $config->get('expert')['txt_subtitle'] : 'Conoce a nuestro equipo de expertos',    
            '#required' => true
        ]; 

        $form['expert']['active_linkl'] = [  
            '#type' => 'checkbox',
            '#title' => t('Activar enlace de ver perfiles'),   
            '#default_value' => isset($config->get('expert')['active_linkl']) ? $config->get('expert')['active_linkl'] : 0,  
        ]; 

        $form['expert']['profiles_url'] = [
            '#type' => 'url',
            '#title' => t('URL a redireccionar'),
            '#description' => t('Escriba la URL utilizada en el enlace de ver perfiles'),
            '#default_value' => isset($config->get('expert')['profiles_url']) ? $config->get('expert')['profiles_url'] : '',
        ];


        $form['user_list'] = [  
            '#type' => 'details',
            '#title' => t('Asignación de expertos en Home'),   
            '#open' => false,  
        ]; 


        $ctn = $config->get('expert')['ctn'];

        for ($i=0; $i < $ctn; $i++) { 
            $user = '';
            $uid = isset($config->get('user_list')['expert-'.$i]) ? $config->get('user_list')['expert-'.$i] : '';
            if($uid != ''){
                $user = \Drupal\user\Entity\User::load($uid);
            }
            
            $form['user_list']['expert-' . $i] = [
                '#title' => t('Experto #@num', ['@num' => $i + 1]),
                '#type' => 'entity_autocomplete',
                '#target_type' => 'user',
                '#description' => t('Ingrese el nombre o correo del experto(a)'),
                '#default_value' => $user,
                '#selection_settings' => [
                    'include_anonymous' => FALSE,
                    'filter' => [
                        'role' => ['experto_coordinador'],
                    ],
                ],
            ];
        }

        return parent::buildForm($form, $form_state);
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  
        
        parent::submitForm($form, $form_state);
        $this->config('ngt_general.settings_user_expert')
            ->set('expert', $form_state->getValue('expert'))  
            ->set('user_list', $form_state->getValue('user_list'))  
            ->save();  
    }  

}