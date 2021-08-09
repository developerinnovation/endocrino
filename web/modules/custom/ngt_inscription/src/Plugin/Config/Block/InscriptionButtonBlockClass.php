<?php 

namespace Drupal\ngt_inscription\Plugin\Config\Block;

use Drupal\ngt_inscription\Plugin\Block\InscriptionButtonBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;



/**
 * Manage config a 'InscriptionButtonBlockClass' block
 */
class InscriptionButtonBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_inscription\Plugin\Block\InscriptionButtonBlock $instance
     * @param $config
     */
    public function setConfig(InscriptionButtonBlock &$instance, &$config){
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
     * @param \Drupal\ngt_inscription\Plugin\Block\InscriptionButtonBlock $instance
     * @param $config
     */
    public function build(InscriptionButtonBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $roles = \Drupal::currentUser()->getRoles();

        $instance->setValue('config_name', 'InscriptionButtonBlock');
        $instance->setValue('class', 'block-inscription-button');
        $uuid = $instance->uuid('block-inscription-button');
        $instance->setValue('directive', 'data-ng-inscription-button');
        $this->instance->setValue('dataAngular', 'inscription-button-' . $uuid);

       
        $parameters = [
            'theme' => 'inscription_button',
            'library' => 'ngt_inscription/inscription-button',
        ];

        $config = \Drupal::config('ngt_inscription.settings')->get('ngt_inscription');
        $node_id = $configuration['node'];
        $user_id = $configuration['uid'];
        $idReserve = 0;

        if($user_id != '0'){
            $response = \Drupal::service('ngt_inscription.method_general')->searchReserve($node_id, $user_id);
            if($response['result'] == 'yes'){
                $idReserve = $response['id'];
            }
        }
       
        $others = [
            '#dataAngular' => $this->instance->getValue('dataAngular'),
            '#config' => $config,
            '#uuid' => $uuid,
        ];

        $other_config = [
            'pathReserve' => '/ngt/api/v1/inscription/reserve',
            'typeAction' => $idReserve != '0' ? 'unReserve' : 'reserve',
            'config' =>  $config,
            'uid' => $user_id,
            'nid' => $node_id,
            'id' => $idReserve,
            'disableBtn' => (count(array_intersect($roles, ['anonymous','alumnos'])) > 0) == false ? true : false,
        ];

        $config_block = $instance->cardBuildConfigBlock(NULL, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);

        
        return $instance->getValue('build');
    }

}