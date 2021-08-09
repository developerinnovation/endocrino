<?php 

namespace Drupal\ngt_login\Plugin\Config\Block;

use Drupal\ngt_login\Plugin\Block\RegisterUserBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;



/**
 * Manage config a 'RegisterUserBlockClass' block
 */
class RegisterUserBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_login\Plugin\Block\RegisterUserBlock $instance
     * @param $config
     */
    public function setConfig(RegisterUserBlock &$instance, &$config){
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
     * @param \Drupal\ngt_login\Plugin\Block\RegisterUserBlock $instance
     * @param $config
     */
    public function build(RegisterUserBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $instance->setValue('config_name', 'RegisterUserBlock');
        $instance->setValue('class', 'block-register-user');
        $uuid = $instance->uuid('block-register-user');
        $instance->setValue('directive', 'data-ng-register-user');
        $this->instance->setValue('dataAngular', 'register-user-' . $uuid);

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

        $parameters = [
            'theme' => 'register_user',
            'library' => 'ngt_login/register-user',
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
            'pathNewUser' => '/ngt/api/v1/login/user/create/set/new',
        ];

        $url = '/api/v1/';
        $config_block = $instance->cardBuildConfigBlock($url, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);

    
        return $instance->getValue('build');
    }

    public function loadCountry(){

    }

    /**
     * {@inheritdoc}
     */
    public function blockAccess(AccountInterface $account){
        if ($account->isAnonymous()) {
            return AccessResult::allowed();
        }
        return AccessResult::forbidden();
    }

}