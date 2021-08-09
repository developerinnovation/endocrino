<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\ExpertHomeBlock;
use Drupal\user\Entity\User;

/**
 * Manage config a 'ExpertHomeBlockClass' block
 */
class ExpertHomeBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\ExpertHomeBlock $instance
     * @param $config
     */
    public function setConfig(ExpertHomeBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\ExpertHomeBlock $instance
     * @param $config
     */
    public function build(ExpertHomeBlock &$instance, $configuration){
        $config = \Drupal::config('ngt_general.settings_user_expert');  
        $data = [
            'expert' => [
                'txt_title' => $config->get('expert')['txt_title'],
                'txt_subtitle' => $config->get('expert')['txt_subtitle'],
                'active_block' => $config->get('expert')['active'],
                'txt_profile' => $config->get('expert')['txt_profile'],
                'profiles_url' => $config->get('expert')['profiles_url'],
                'active_link' => $config->get('expert')['active_linkl'],
            ],
            'user_list' => []
        ];

        $user_list = $config->get('user_list');

        foreach ($user_list as $uid) {
            if($uid != NULL) {
                $user =   User::load($uid); 
                $list = [
                    'picture' => \Drupal::service('ngt_general.methodGeneral')->load_image($user->get('user_picture')->getValue()[0]['target_id'],'200x200'),
                    'name' => ucfirst($user->get('field_nombre')->getValue()[0]['value'])." ".ucfirst($user->get('field_apellidos')->getValue()[0]['value']),
                    'profile' => $user->get('field_perfil')->getValue()[0]['value'],
                ];
                array_push($data['user_list'], $list);
            }
        }

        $build = [
            '#theme' => 'expert_home',
            '#data' => $data,
        ];

        return $build;
    }

}