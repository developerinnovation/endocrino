<?php  

namespace Drupal\ngt_general\Plugin\Config\Form;  

use Drupal\Core\Form\FormStateInterface;  

class CategoryConfigFormClass{  
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_general.adminSettingsCategory',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_form_category';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = \Drupal::config('ngt_general.adminSettingsCategory');  
        $categorys = $this->load_taxonomy('categorias');
        
        foreach ($categorys as $category) {
            $tid = $category->get('tid')->getValue()[0]['value'];
            $name = $category->get('name')->getValue()[0]['value'];

            $form['category_'.$tid] = [  
                '#type' => 'checkbox',
                '#title' => t($name),   
                '#default_value' => $config->get('category_'.$tid),  
            ]; 
        }

        return $form;  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  

        $config = \Drupal::configFactory()->getEditable('ngt_general.adminSettingsCategory');

        $categorys = $this->load_taxonomy('categorias');
        foreach ($categorys as $category) {
            $tid = $category->get('tid')->getValue()[0]['value'];
            $name = $category->get('name')->getValue()[0]['value'];
            $config->set('category_'.$tid, $form_state->getValue('category_'.$tid));
        }
        $config->save();  
    }  
    
    /**
     * load_taxonomy
     *
     * @param  string $machineVocabulary
     * @param  int $tidParent
     * @param  int $level
     * @return array
     */
    public function load_taxonomy($machineVocabulary,$tidParent = 0, $level = 1){
        $tree = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadTree(
                $machineVocabulary,  // This is your taxonomy term vocabulary (machine name).
                $tidParent,                   // This is "tid" of parent. Set "0" to get all.
                $level,                   // Get only 1st level.
                TRUE                 // Get full load of taxonomy term entity.
            );

        return $tree;
    }
} 