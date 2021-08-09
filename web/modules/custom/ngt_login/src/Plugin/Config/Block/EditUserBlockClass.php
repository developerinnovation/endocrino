<?php 

namespace Drupal\ngt_login\Plugin\Config\Block;

use Drupal\ngt_login\Plugin\Block\EditUserBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;



/**
 * Manage config a 'EditUserBlockClass' block
 */
class EditUserBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_login\Plugin\Block\EditUserBlock $instance
     * @param $config
     */
    public function setConfig(EditUserBlock &$instance, &$config){
        $this->instance = &$instance;
        $this->configuration = &$config;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [];
    }

  
    /**
     * @param \Drupal\ngt_login\Plugin\Block\EditUserBlock $instance
     * @param $config
     */
    public function build(EditUserBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $instance->setValue('config_name', 'EditUserBlock');
        $instance->setValue('class', 'block-edit-user');
        $uuid = $instance->uuid('block-edit-user');
        $instance->setValue('directive', 'data-ng-edit-user');
        $this->instance->setValue('dataAngular', 'edit-user-' . $uuid);

        $config = \Drupal::config('ngt_login.settings');  
        $configGeneral = \Drupal::config('ngt_general.settings'); 


        $image_logo_fid = reset($configGeneral->get('general_settings')['img_logo']);
        $image_logo_file = File::load($image_logo_fid);
        $image_logo_url = isset($image_logo_file) ? $image_logo_file->getFileUri() : '';

        $image_login_background_fid = reset($config->get('ngt_login')['image']);
        $image_login_background_file = File::load($image_login_background_fid);
        $image_login_background_url = isset($image_login_background_file) ? $image_login_background_file->getFileUri() : '';

        $terms_external_url = $configGeneral->get('general_terms_conditions')['terms_external_url'];
        $terms_text = $configGeneral->get('general_terms_conditions')['terms_text']['value'];
        $url_terms_text = '<a target="_blank" href="'. $terms_external_url .'">';
        $terms_text = str_replace('<a>', $url_terms_text, $terms_text);

        $profession = \Drupal::service('ngt_general.methodGeneral')->loadTermByCategory('profesiones');
        $country = \Drupal::service('ngt_general.methodGeneral')->loadTermByCategory('ubicacion_geografica');

        $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

        $dataUser = [
            'mail' => $current_user->get('name')->value,
            'name' => $current_user->get('field_nombre')->value .' '. $current_user->get('field_apellidos')->value,
            'profession' => $current_user->get('field_profesion')->target_id,
            'location' => $current_user->get('field_ubicacion_geografica')->target_id,
        ];

        $parameters = [
            'theme' => 'edit_user',
            'library' => 'ngt_login/edit-user',
        ];

        $others = [
            '#dataAngular' => $this->instance->getValue('dataAngular'),
            '#image_logo_url' => $image_logo_url,
            '#image_login_background_url' => $image_login_background_url,
            '#config' => $config->get('ngt_new_user'),
            '#uuid' => $uuid,
            '#terms_text' => $terms_text,
        ];

        $other_config = [
            'url' => '/prueba',
            'config' => $config->get('ngt_new_user'),
            'pass_criteriar' => explode(PHP_EOL, $config->get('ngt_new_user')['help_text_new_pass']),
            'profession' => $profession,
            'country' => $country,
            'bundleForCountry' => 'ubicacion_geografica',
            'pathMoreTerms' => '/ngt/api/v1/terms/load/parent/{parentId}/vocabulary/{vocabularyBundle}',
            'icon_load_pic' => '../../../modules/custom/ngt_login/asset/image/load-pic.png',
            'pathUpdateUser' => '/ngt/api/v1/login/user/edit/set/update',
            'dataUser' => $dataUser,
        ];

        $url = '/api/v1/';
        $config_block = $instance->cardBuildConfigBlock($url, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);

    
        return $instance->getValue('build');
    }

    /**
     * {@inheritdoc}
     */
    public function blockAccess(AccountInterface $account){
        if ($account->isAnonymous()) {
            return AccessResult::forbidden();
        }
        return AccessResult::allowed();
    }

}