<?php 

namespace Drupal\ngt_login\Plugin\Config\Block;

use Drupal\ngt_login\Plugin\Block\RecoveryPassBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;



/**
 * Manage config a 'RecoveryPassBlockClass' block
 */
class RecoveryPassBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_login\Plugin\Block\RecoveryPassBlock $instance
     * @param $config
     */
    public function setConfig(RecoveryPassBlock &$instance, &$config){
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
     * @param \Drupal\ngt_login\Plugin\Block\RecoveryPassBlock $instance
     * @param $config
     */
    public function build(RecoveryPassBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $instance->setValue('config_name', 'RecoveryPassBlock');
        $instance->setValue('class', 'block-recovery-pass');
        $uuid = $instance->uuid('block-recovery-pass');
        $instance->setValue('directive', 'data-ng-recovery-pass');
        $this->instance->setValue('dataAngular', 'recovery-pass-' . $uuid);

        $config = \Drupal::config('ngt_login.settings');  
        $configGeneral = \Drupal::config('ngt_general.settings'); 


        $image_logo_fid = reset($configGeneral->get('general_settings')['img_logo']);
        $image_logo_file = File::load($image_logo_fid);
        $image_logo_url = isset($image_logo_file) ? $image_logo_file->getFileUri() : '';

        $image_login_background_fid = reset($config->get('ngt_login')['image']);
        $image_login_background_file = File::load($image_login_background_fid);
        $image_login_background_url = isset($image_login_background_file) ? $image_login_background_file->getFileUri() : '';

        $parameters = [
            'theme' => 'recovery_pass',
            'library' => 'ngt_login/recovery-pass',
        ];

        $others = [
            '#dataAngular' => $this->instance->getValue('dataAngular'),
            '#image_logo_url' => $image_logo_url,
            '#image_login_background_url' => $image_login_background_url,
            '#config' => $config->get('ngt_forgot_password'),
            '#uuid' => $uuid,
        ];

        $other_config = [
            'pathGetCode' => '/ngt/api/v1/login/recovery/pass/get/code',
            'pathVerifyCode' => '/ngt/api/v1/login/recovery/pass/verify/code',
            'pathSetNewPass' => '/ngt/api/v1/login/recovery/pass/set/new',
            'config' => $config->get('ngt_forgot_password'),
            'pass_criteriar' => explode(PHP_EOL, $config->get('ngt_forgot_password')['help_text_new_pass']),
        ];

        $config_block = $instance->cardBuildConfigBlock(NULL, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);

        
        return $instance->getValue('build');
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