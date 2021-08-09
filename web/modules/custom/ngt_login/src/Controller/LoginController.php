<?php
 
/**
* @file 
* Contains \Drupal\ngt_login\Controller\LoginController.
*/
namespace Drupal\ngt_login\Controller ;
use Drupal\Core\Controller\ControllerBase ;

class LoginController extends ControllerBase { 
        
    /**
     * RecoveryPass
     *
     * @return void
     */
    public function RecoveryPass() {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $block_manager = \Drupal::service('plugin.manager.block');
        $config = [];
        $plugin_block = $block_manager->createInstance('ngt_login_recovery_pass', $config);
        $access_result = $plugin_block->access(\Drupal::currentUser());
        if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
            return [];
        }
        $render = $plugin_block->build();
        return $render;
    }
    
    /**
     * RegisterUser
     *
     * @return void
     */
    public function RegisterUser() {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $block_manager = \Drupal::service('plugin.manager.block');
        $config = [];
        $plugin_block = $block_manager->createInstance('ngt_login_register_user', $config);
        $access_result = $plugin_block->access(\Drupal::currentUser());
        if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
            return [];
        }
        $render = $plugin_block->build();
        return $render;
    }
    
    /**
     * EditUser
     *
     * @return void
     */
    public function EditUser(){
        \Drupal::service('page_cache_kill_switch')->trigger();
        $block_manager = \Drupal::service('plugin.manager.block');
        $config = [];
        $plugin_block = $block_manager->createInstance('ngt_login_edit_user', $config);
        $access_result = $plugin_block->access(\Drupal::currentUser());
        if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
            return [];
        }
        $render = $plugin_block->build();
        return $render;
    }
    
}