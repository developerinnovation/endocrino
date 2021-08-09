<?php  

namespace Drupal\ngt_inscription\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InscriptionForm extends ConfigFormBase {
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_inscription.settings',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_inscription_form_settings';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = $this->config('ngt_inscription.settings');  


        $form['#tree'] = true;

        // configuración general 

        $form['ngt_inscription_config'] = [  
            '#type' => 'details',
            '#title' => t('Activar o inactivar reserva de cupo'),   
            '#open' => false,  
        ]; 

        $form['ngt_inscription_config']['button'] = [  
            '#type' => 'checkbox',
            '#title' => t('Activar la reserva de cupo en cursos'),   
            '#default_value' => isset($config->get('ngt_inscription_config')['button']) ? $config->get('ngt_inscription_config')['button'] : 1,
        ]; 

        // inscripción de cursos

        $form['ngt_inscription'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones del botón para inscripción de cursos'),   
            '#open' => false,  
        ]; 

        $form['ngt_inscription']['inscription'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto del botón para reservar cupo'),   
            '#default_value' => isset($config->get('ngt_inscription')['inscription']) ? $config->get('ngt_inscription')['inscription'] : t('Reservar cupo'),
            '#required' => true
        ]; 

        $form['ngt_inscription']['uninscription'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto del botón cancelar reserva de cupo'),   
            '#default_value' => isset($config->get('ngt_inscription')['uninscription']) ? $config->get('ngt_inscription')['uninscription'] : t('Cancelar reservar'),
            '#required' => true
        ]; 

        $form['ngt_inscription']['gotoLogin'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto del botón para redirigir al login'),   
            '#default_value' => isset($config->get('ngt_inscription')['gotoLogin']) ? $config->get('ngt_inscription')['gotoLogin'] : t('Deseo reservar'),
            '#required' => true
        ];

        $form['ngt_inscription']['messageReserve'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo reserva correcta'),   
            '#default_value' => isset($config->get('ngt_inscription')['messageReserve']) ? $config->get('ngt_inscription')['messageReserve'] : t('Se ha realizado la inscripción de forma correcta.'),
            '#required' => true
        ];

        $form['ngt_inscription']['messageUnReserve'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo al cancelar reserva'),   
            '#default_value' => isset($config->get('ngt_inscription')['messageUnReserve']) ? $config->get('ngt_inscription')['messageUnReserve'] : t('Se canceló la inscripción de forma correcta.'),
            '#required' => true
        ];

        $form['ngt_inscription']['errorMessage'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo error al reservar'),   
            '#default_value' => isset($config->get('ngt_inscription')['errorMessage']) ? $config->get('ngt_inscription')['errorMessage'] : t('Se presentó un error al realizar la reserva, por favor intente nuevamente.'),
            '#required' => true
        ];

        $form['ngt_inscription']['errorMessageUnreserve'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo error al cancelar reserva'),   
            '#default_value' => isset($config->get('ngt_inscription')['errorMessageUnreserve']) ? $config->get('ngt_inscription')['errorMessageUnreserve'] : t('Se presentó un error al realizar cancelar reserva, por favor intente nuevamente.'),
            '#required' => true
        ];

        $form['ngt_inscription']['userAnonime'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo usuario anónimo'),   
            '#default_value' => isset($config->get('ngt_inscription')['userAnonime']) ? $config->get('ngt_inscription')['userAnonime'] : t('Para reservar el cupo del presente curso, deberá iniciar sesión en la plataforma o crear una cuenta, por favor haga clic en "Deseo reservar" para redirigirlo al login.'),
            '#required' => true
        ]; 

        $form['ngt_inscription']['userRegister'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje informativo usuario registrado sin reservar curso'),   
            '#default_value' => isset($config->get('ngt_inscription')['userRegister']) ? $config->get('ngt_inscription')['userRegister'] : t('Para ver las lecciones del presente curso deberá reservar su cupo.'),
            '#required' => true
        ];

        return parent::buildForm($form, $form_state);
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  
        parent::submitForm($form, $form_state);

        $this->config('ngt_inscription.settings')
        ->set('ngt_inscription', $form_state->getValue('ngt_inscription'))  
        ->save();   

    }  

    /**  
     * {@inheritdoc}  
     */ 
    public function validateFormat(array &$form, FormStateInterface $form_state){
        parent::validateFormat($form, $form_state);
    }

}