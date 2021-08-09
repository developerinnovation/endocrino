<?php  

namespace Drupal\ngt_general\Plugin\Config\Form;  

use Drupal\Core\Form\FormStateInterface;  

class BenefitConfigFormClass{  
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_general.adminSettingsBenefit',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_form_benefit';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = \Drupal::config('ngt_general.adminSettingsBenefit');  
        
        $form['beneficio_1'] = [  
            '#type' => 'textarea',  
            '#title' => t('Primer beneficio'),  
            '#description' => t('Beneficio a mostrar en el home'),  
            '#default_value' => $config->get('beneficio_1'),  
        ]; 

        $form['beneficio_2'] = [  
            '#type' => 'textarea',  
            '#title' => t('Segundo beneficio'),  
            '#description' => t('Beneficio a mostrar en el home'),  
            '#default_value' => $config->get('beneficio_2'),  
        ]; 

        $form['beneficio_3'] = [  
            '#type' => 'textarea',  
            '#title' => t('Tercer beneficio'),  
            '#description' => t('Beneficio a mostrar en el home'),  
            '#default_value' => $config->get('beneficio_3'),  
        ]; 

        return $form;  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  

        $config = \Drupal::configFactory()->getEditable('ngt_general.adminSettingsBenefit');
        $config
            ->set('beneficio_1', $form_state->getValue('beneficio_1'))  
            ->set('beneficio_2', $form_state->getValue('beneficio_2'))  
            ->set('beneficio_3', $form_state->getValue('beneficio_3'))  
            ->save();  
    }  
} 